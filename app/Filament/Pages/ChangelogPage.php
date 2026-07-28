<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Resources\Changelog\ChangelogResource;
use App\Models\Changelog;
use App\Support\Changelog\KeepAChangelogParser;
use App\Support\Changelog\VersionGrouper;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;

class ChangelogPage extends Page
{
    protected const PER_PAGE = 8;

    public ?array $data = [];

    public int $limit = self::PER_PAGE;

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::OutlinedDocumentText;
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Sistema';
    }

    public static function getNavigationLabel(): string
    {
        return 'Atualizações';
    }

    public static function getNavigationSort(): ?int
    {
        return 90;
    }

    public function getTitle(): string
    {
        return 'Atualizações';
    }

    public function mount(): void
    {
        $this->data = [
            'search' => '',
            'version' => null,
        ];

        $this->limit = self::PER_PAGE;

        if (Filament::auth()->check()) {
            $user = Filament::auth()->user();
            if (method_exists($user, 'markChangelogAsRead')) {
                $user->markChangelogAsRead();
            }
            if (method_exists($user, 'unreadNotifications')) {
                $user->unreadNotifications()
                    ->where(function ($query): void {
                        $query->whereNotNull('data->viewData->changelog_version')
                            ->orWhere('data->title', 'like', '%Nova atualização disponível%');
                    })
                    ->update(['read_at' => now()]);
            }
        }
    }

    public static function canAccess(): bool
    {
        return Filament::auth()->check();
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return 'changelog';
    }

    public function resetLimit(): void
    {
        $this->limit = self::PER_PAGE;
    }

    public function loadMore(): void
    {
        $this->limit += self::PER_PAGE;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('manage')
                ->label('Gerenciar Registro')
                ->icon(Heroicon::OutlinedPencilSquare)
                ->color('gray')
                ->visible(fn (): bool => ChangelogResource::canViewAny())
                ->url(fn (): string => ChangelogResource::getUrl()),
        ];
    }

    protected function getEntries(): Collection
    {
        $entries = Changelog::query()->orderBy('sort')->get();

        if ($entries->isEmpty() && file_exists(base_path('CHANGELOG.md'))) {
            $parser = new KeepAChangelogParser;
            $raw = $parser->parse(file_get_contents(base_path('CHANGELOG.md')));

            return collect($raw)->map(fn (array $item) => new Changelog($item));
        }

        return $entries;
    }

    public function content(Schema $schema): Schema
    {
        $entries = $this->getEntries();

        if ($entries->isEmpty()) {
            return $schema->components([
                Section::make()->schema([
                    Text::make('Nenhum registro de changelog encontrado.')->color('gray'),
                ]),
            ]);
        }

        $components = [];

        if ($toolbar = $this->toolbar($entries)) {
            $components[] = $toolbar;
        }

        $components[] = Grid::make(1)
            ->key('changelog-list')
            ->schema(fn (Get $get): array => $this->list(
                (string) ($get('search') ?? ''),
                $get('version'),
            ));

        return $schema
            ->statePath('data')
            ->components($components);
    }

    protected function toolbar(Collection $entries): ?Grid
    {
        $fields = [
            TextInput::make('search')
                ->hiddenLabel()
                ->placeholder('Buscar nas atualizações...')
                ->prefixIcon(Heroicon::MagnifyingGlass)
                ->live(debounce: 350)
                ->afterStateUpdated(fn () => $this->resetLimit())
                ->columnSpan(['default' => 1, 'sm' => 2]),

            Select::make('version')
                ->hiddenLabel()
                ->placeholder('Todas as versões')
                ->options($this->versionOptions($entries))
                ->native(false)
                ->searchable()
                ->live()
                ->afterStateUpdated(fn () => $this->resetLimit())
                ->columnSpan(1),
        ];

        return Grid::make(['default' => 1, 'sm' => 3])
            ->schema($fields);
    }

    protected function versionOptions(Collection $entries): array
    {
        return (new VersionGrouper)->group($entries)
            ->keys()
            ->mapWithKeys(fn (string $version): array => [$version => $this->versionHeading($version)])
            ->all();
    }

    protected function list(string $search, ?string $version): array
    {
        $grouper = new VersionGrouper;

        $entries = $this->filter($this->getEntries(), $search);

        $groups = $grouper->group($entries);

        if ($version !== null && $version !== '') {
            $groups = $groups->only([$version]);
        }

        if ($groups->isEmpty()) {
            return [
                Section::make()->schema([
                    Text::make('Nenhuma atualização encontrada para o filtro atual.')->color('gray'),
                ]),
            ];
        }

        $total = $groups->count();

        $sections = $groups
            ->take($this->limit)
            ->map(fn (Collection $group, string $v): Section => $this->versionSection($v, $group, $grouper, $search))
            ->values()
            ->all();

        if ($total > $this->limit) {
            $sections[] = $this->loadMoreSentinel();
        }

        return $sections;
    }

    protected function filter(Collection $entries, string $search): Collection
    {
        $search = trim($search);

        if ($search === '') {
            return $entries;
        }

        $needle = mb_strtolower($search);

        return $entries->filter(function (Changelog $entry) use ($needle): bool {
            return str_contains(mb_strtolower((string) $entry->description), $needle)
                || str_contains(mb_strtolower((string) $entry->version), $needle);
        })->values();
    }

    protected function loadMoreSentinel(): Grid
    {
        return Grid::make(1)
            ->key('changelog-load-more-'.$this->limit)
            ->extraAttributes([
                'x-intersect.margin.600px.once' => '$wire.loadMore()',
                'class' => 'flex justify-center py-2',
            ])
            ->schema([
                Text::make('Carregando mais atualizações...')
                    ->color('gray')
                    ->size('sm'),
            ]);
    }

    protected function versionSection(string $version, Collection $group, VersionGrouper $grouper, string $search = ''): Section
    {
        $body = $grouper->byType($group)
            ->map(function (array $bucket): string {
                $lines = $bucket['entries']
                    ->map(fn (Changelog $e): string => '- '.trim($e->description))
                    ->implode("\n");

                return '**'.$bucket['type']->getLabel().'**'."\n\n".$lines;
            })
            ->implode("\n\n");

        return Section::make()
            ->heading($this->versionHeading($version))
            ->afterHeader($this->dateBadge($group))
            ->schema([
                TextEntry::make('body_'.md5($version.'|'.$search))
                    ->hiddenLabel()
                    ->state($body)
                    ->markdown(),
            ]);
    }

    protected function versionHeading(string $version): string
    {
        if (strtolower($version) === 'unreleased' || strtolower($version) === 'não lançado') {
            return 'Não Lançado';
        }

        return preg_match('/^\d/', $version) ? 'v'.$version : $version;
    }

    protected function dateBadge(Collection $group): ?Text
    {
        $date = optional($group->first())->released_at;

        if (! $date) {
            return null;
        }

        return Text::make($date->format('d/m/Y'))
            ->badge()
            ->color('warning')
            ->weight(FontWeight::Medium);
    }
}
