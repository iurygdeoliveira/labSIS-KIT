<?php

namespace App\Filament\Pages\Organization;

use App\Enums\OrganizationRole;
use App\Events\OrganizationInviteCreated;
use App\Filament\Clusters\TenantSettings;
use App\Livewire\Organization\ListInvitations;
use App\Livewire\Organization\ListMembers;
use App\Models\OrganizationInvite;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;

class Members extends Page
{
    protected static ?string $cluster = TenantSettings::class;

    public int $activeTab = 0;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?int $navigationSort = 2;

    public function getTitle(): string|Htmlable
    {
        return __('organization.members.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('organization.members.navigation_label');
    }

    public static function canAccess(): bool
    {
        $tenant = Filament::getTenant();
        if (! $tenant) {
            return false;
        }

        $pivotRole = $tenant->users()
            ->where('users.id', Filament::auth()->id())
            ->first()
            ?->pivot
            ?->role;

        return $pivotRole !== null;
    }

    protected function getHeaderActions(): array
    {
        $tenant = Filament::getTenant();
        $roleEnum = config('filament-tenant-members.role_enum', OrganizationRole::class);

        $pivotRole = $tenant->users()
            ->where('users.id', Filament::auth()->id())
            ->first()
            ?->pivot
            ?->role;

        $currentRole = $pivotRole ? $roleEnum::tryFrom($pivotRole) : null;
        $maxInvites = config('filament-tenant-members.max_invites_per_batch', 5);

        return [
            Action::make('invite')
                ->label(__('organization.actions.invite.label'))
                ->icon('heroicon-o-user-plus')
                ->visible(fn (): bool => $currentRole?->canInviteMembers() ?? false)
                ->schema([
                    Repeater::make('emailAddresses')
                        ->label(__('organization.actions.invite.label'))
                        ->hiddenLabel()
                        ->minItems(1)
                        ->maxItems($maxInvites)
                        ->defaultItems(1)
                        ->deletable(fn ($state): bool => is_array($state) && count($state) > 1)
                        ->reorderable(false)
                        ->addActionLabel(__('organization.actions.invite.add_another'))
                        ->schema([
                            Grid::make()
                                ->columns(3)
                                ->schema([
                                    TextInput::make('email')
                                        ->required()
                                        ->columnSpan(2)
                                        ->placeholder('email@example.com')
                                        ->email()
                                        ->maxLength(254)
                                        ->distinct()
                                        ->rule(
                                            Rule::unique(OrganizationInvite::class, 'email')
                                                ->where('organization_id', $tenant?->id)
                                                ->whereNull('accepted_at')
                                                ->where(fn ($query) => $query->where('expires_at', '>', now()))
                                        )
                                        ->validationMessages([
                                            'unique' => __('organization.validation.unique'),
                                        ])
                                        ->rules([
                                            fn () => function (string $attribute, mixed $value, \Closure $fail) use ($tenant) {
                                                if ($tenant && $tenant->users()->where('email', $value)->exists()) {
                                                    $fail(__('organization.validation.exists'));
                                                }
                                            },
                                        ]),
                                    Select::make('role')
                                        ->label(__('organization.fields.role'))
                                        ->options($roleEnum::assignableOptions())
                                        ->required()
                                        ->default(config('filament-tenant-members.default_role', 'user')),
                                ]),
                        ]),
                ])
                ->modalHeading(__('organization.actions.invite.modal_heading'))
                ->modalDescription(__('organization.actions.invite.modal_description'))
                ->modalSubmitActionLabel(__('organization.actions.invite.modal_submit_label'))
                ->action(fn (array $data) => $this->createInvites($data)),
        ];
    }

    public function content(Schema $schema): Schema
    {
        $tenant = Filament::getTenant();

        return $schema->components([
            Tabs::make()
                ->livewireProperty('activeTab')
                ->tabs([
                    Tab::make(__('organization.members.tabs.members'))
                        ->icon('heroicon-m-users')
                        ->schema([
                            Livewire::make(ListMembers::class)->key('list-members'),
                        ]),
                    Tab::make(__('organization.members.tabs.pending_invitations'))
                        ->icon('heroicon-m-envelope')
                        ->badge(
                            OrganizationInvite::query()
                                ->where('organization_id', $tenant?->id)
                                ->pending()
                                ->count() ?: null
                        )
                        ->schema([
                            Livewire::make(ListInvitations::class)->key('list-invitations'),
                        ]),
                ])
                ->contained(false),
        ]);
    }

    protected function createInvites(array $data): void
    {
        $emailAddresses = $data['emailAddresses'] ?? [];
        $tenant = Filament::getTenant();
        $expiresDays = config('filament-tenant-members.invite_expires_days', 7);

        if (! $tenant) {
            return;
        }

        $invites = DB::transaction(function () use ($emailAddresses, $tenant, $expiresDays): array {
            $created = [];

            foreach ($emailAddresses as $item) {
                $emailAddress = $item['email'] ?? null;
                $role = $item['role'] ?? config('filament-tenant-members.default_role', 'user');

                if (! $emailAddress) {
                    continue;
                }

                $exists = OrganizationInvite::query()
                    ->where('organization_id', $tenant->id)
                    ->where('email', $emailAddress)
                    ->pending()
                    ->lockForUpdate()
                    ->exists();

                if ($exists) {
                    continue;
                }

                $created[] = OrganizationInvite::create([
                    'organization_id' => $tenant->id,
                    'user_id' => Filament::auth()->id(),
                    'email' => $emailAddress,
                    'token' => Str::uuid(),
                    'role' => $role,
                    'expires_at' => now()->addDays($expiresDays),
                ]);
            }

            return $created;
        });

        foreach ($invites as $invite) {
            OrganizationInviteCreated::dispatch($invite);
        }

        $count = count($invites);

        if ($count > 0) {
            Notification::make()
                ->title($count === 1
                    ? __('organization.notifications.sent_single')
                    : __('organization.notifications.sent_multiple', ['count' => $count]))
                ->success()
                ->send();

            $this->dispatch('invites-changed');
        } else {
            Notification::make()
                ->title(__('organization.notifications.none_sent_title'))
                ->body(__('organization.notifications.none_sent_body'))
                ->warning()
                ->send();
        }
    }

    #[On('invites-changed')]
    public function refreshContent(): void {}
}
