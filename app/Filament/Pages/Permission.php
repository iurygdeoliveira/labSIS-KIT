<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\Permission as PermissionEnum;
use App\Enums\RoleType;
use App\Models\Role;
use App\Models\Tenant;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Permission\PermissionRegistrar;

class Permission extends Page
{
    use Forms\Concerns\InteractsWithForms;

    protected string $view = 'filament.pages.permission';

    protected static ?string $slug = 'role-permissions';

    protected static ?string $navigationLabel = 'Permissões';

    protected static ?string $title = 'Permissões';

    protected static string|\UnitEnum|null $navigationGroup = 'Configurações';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?int $navigationSort = 1;

    public ?string $selectedRole = null; // RoleType values: Owner | User

    public string $guard = 'web';

    public bool $selectAll = false;

    /**
     * Estrutura: [tenantId][resource] = ['delete' => bool, 'create' => bool, 'update' => bool, 'view' => bool]
     *
     * @var array<int, array<string, array<string, bool>>>
     */
    public array $permissions = [];

    /**
     * Estrutura: [tenantId][resource] = bool
     *
     * @var array<int, array<string, bool>>
     */
    public array $resourceEnabled = [];

    /**
     * Mapa inicial de resources. Pode ser expandido conforme necessidade.
     *
     * @var array<string, string>
     */
    protected array $resourcesMap = [];

    public function mount(): void
    {
        $this->guard = config('auth.defaults.guard', 'web');
        $this->resourcesMap = $this->discoverResourcesMap();
        $this->initializeStateForActiveTenants();

        // Esta página gerencia apenas a role de usuário; define como padrão
        $this->selectedRole = RoleType::USER->value;
        $this->loadRolePermissions();

        // Preenche o campo "guard" no estado do formulário para exibição
        // $this->form->fill(['guard' => $this->guard]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Configurações')
                ->columns(3)
                ->schema([
                    Select::make('selectedRole')
                        ->label('Role')
                        ->options([
                            RoleType::USER->value => RoleType::USER->getLabel(),
                        ])
                        ->default(RoleType::USER->getLabel())
                        ->native(false)
                        ->required()
                        ->searchable(false)
                        ->preload(),

                    TextInput::make('guard')
                        ->label('Guard')
                        ->readOnly()
                        ->dehydrated(false)
                        ->formatStateUsing(fn () => $this->guard),

                    Toggle::make('selectAll')
                        ->label('Habilitar todas as permissões')
                        ->onColor('primary')
                        ->offColor('danger')
                        ->onIcon('heroicon-c-check')
                        ->offIcon('heroicon-c-x-mark')
                        ->reactive()
                        ->afterStateUpdated(fn (bool $state) => $this->toggleAll($state))
                        ->inline(false),
                ]),

            Tabs::make('tenants')
                ->tabs(fn () => $this->makeTenantTabs()),
        ])->statePath('data');
    }

    /** @return array<int, Tabs\Tab> */
    protected function makeTenantTabs(): array
    {
        $tenants = Tenant::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return $tenants->map(function (Tenant $tenant) {
            $tenantId = (int) $tenant->id;

            return Tab::make($tenant->name)
                ->schema([
                    Grid::make(12)->schema(
                        collect($this->resourcesMap)->map(function (string $label, string $resourceSlug) use ($tenantId) {
                            return Grid::make(1)
                                ->schema([
                                    Toggle::make("resourceEnabled.{$tenantId}.{$resourceSlug}")
                                        ->onColor('primary')
                                        ->offColor('danger')
                                        ->onIcon('heroicon-c-check')
                                        ->offIcon('heroicon-c-x-mark')
                                        ->reactive()
                                        ->afterStateUpdated(fn (bool $state) => $this->toggleResource($tenantId, $resourceSlug, $state)),

                                    Fieldset::make('Permissões')->schema([
                                        Toggle::make("permissions.{$tenantId}.{$resourceSlug}.delete")
                                            ->label(PermissionEnum::DELETE->getLabel())
                                            ->onColor('primary')
                                            ->offColor('danger')
                                            ->onIcon('heroicon-c-check')
                                            ->offIcon('heroicon-c-x-mark')
                                            ->reactive(),
                                        Toggle::make("permissions.{$tenantId}.{$resourceSlug}.create")
                                            ->label(PermissionEnum::CREATE->getLabel())
                                            ->onColor('primary')
                                            ->offColor('danger')
                                            ->onIcon('heroicon-c-check')
                                            ->offIcon('heroicon-c-x-mark')
                                            ->reactive(),
                                        Toggle::make("permissions.{$tenantId}.{$resourceSlug}.update")
                                            ->label(PermissionEnum::UPDATE->getLabel())
                                            ->onColor('primary')
                                            ->offColor('danger')
                                            ->onIcon('heroicon-c-check')
                                            ->offIcon('heroicon-c-x-mark')
                                            ->reactive(),
                                        Toggle::make("permissions.{$tenantId}.{$resourceSlug}.view")
                                            ->label(PermissionEnum::VIEW->getLabel())
                                            ->onColor('primary')
                                            ->offColor('danger')
                                            ->onIcon('heroicon-c-check')
                                            ->offIcon('heroicon-c-x-mark')
                                            ->reactive(),
                                    ])->columns(2),
                                ])
                                ->columnSpan(4);
                        })->values()->all()
                    )->columns(12),
                ]);
        })->all();
    }

    protected function initializeStateForActiveTenants(): void
    {
        $tenantIds = Tenant::query()
            ->where('is_active', true)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        foreach ($tenantIds as $tenantId) {
            foreach ($this->resourcesMap as $slug => $label) {
                $this->resourceEnabled[$tenantId][$slug] = false;
                $this->permissions[$tenantId][$slug] = [
                    PermissionEnum::DELETE->value => false,
                    PermissionEnum::CREATE->value => false,
                    PermissionEnum::UPDATE->value => false,
                    PermissionEnum::VIEW->value => false,
                ];
            }
        }
    }

    public function loadRolePermissions(): void
    {
        if ($this->selectedRole === null) {
            return;
        }

        $this->initializeStateForActiveTenants();

        $tenantIds = array_keys($this->resourceEnabled);

        foreach ($tenantIds as $tenantId) {
            app(PermissionRegistrar::class)->setPermissionsTeamId($tenantId);

            $role = $this->resolveTenantRole((int) $tenantId);

            foreach (array_keys($this->resourcesMap) as $slug) {
                $anyEnabled = false;

                foreach ([PermissionEnum::DELETE, PermissionEnum::CREATE, PermissionEnum::UPDATE, PermissionEnum::VIEW] as $action) {
                    $name = "{$slug}.{$action->value}";
                    $has = $role->hasPermissionTo($name, $this->guard);
                    $this->permissions[$tenantId][$slug][$action->value] = $has;
                    $anyEnabled = $anyEnabled || $has;
                }

                $this->resourceEnabled[$tenantId][$slug] = $anyEnabled;
            }
        }
    }

    public function save(): void
    {
        if ($this->selectedRole === null) {
            Notification::make()->danger()->title('Selecione uma Role')->send();

            return;
        }

        foreach ($this->permissions as $tenantId => $resources) {
            app(PermissionRegistrar::class)->setPermissionsTeamId((int) $tenantId);

            $role = $this->resolveTenantRole((int) $tenantId);

            foreach ($resources as $slug => $actions) {
                foreach ($actions as $action => $enabled) {
                    $permissionName = "{$slug}.{$action}";

                    SpatiePermission::findOrCreate($permissionName, $this->guard);

                    if ($enabled) {
                        if (! $role->hasPermissionTo($permissionName, $this->guard)) {
                            $role->givePermissionTo($permissionName);
                        }
                    } else {
                        if ($role->hasPermissionTo($permissionName, $this->guard)) {
                            $role->revokePermissionTo($permissionName);
                        }
                    }
                }
            }
        }

        Notification::make()->success()->title('Permissões atualizadas')->send();
    }

    protected function toggleAll(bool $state): void
    {
        foreach ($this->resourceEnabled as $tenantId => $resources) {
            foreach (array_keys($resources) as $slug) {
                $this->toggleResource((int) $tenantId, $slug, $state);
            }
        }
    }

    protected function toggleResource(int $tenantId, string $slug, bool $state): void
    {
        foreach ([PermissionEnum::DELETE, PermissionEnum::CREATE, PermissionEnum::UPDATE, PermissionEnum::VIEW] as $action) {
            $this->permissions[$tenantId][$slug][$action->value] = $state;
        }

        $this->resourceEnabled[$tenantId][$slug] = $state;
    }

    /**
     * Resolve a role para o tenant atual com base em selectedRole (Owner/User).
     */
    protected function resolveTenantRole(int $tenantId): Role
    {
        if ($this->selectedRole === RoleType::OWNER->value) {
            return RoleType::ensureOwnerRoleForTeam($tenantId, $this->guard);
        }

        return RoleType::ensureUserRoleForTeam($tenantId, $this->guard);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Salvar')
                ->submit('save')
                ->action('save'),
        ];
    }

    /**
     * Descobre dinamicamente os Filament Resources e gera o mapa slug => label.
     * Usa a convenção: slug kebab-case a partir do nome da classe (ex: UserResource => users).
     * O label usa o pluralModelLabel do Resource quando disponível; caso contrário, um fallback legível.
     *
     * @return array<string,string>
     */
    protected function discoverResourcesMap(): array
    {
        $resourcesDir = app_path('Filament/Resources');

        if (! is_dir($resourcesDir)) {
            return [];
        }

        $map = [];

        foreach (scandir($resourcesDir) as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $path = $resourcesDir.DIRECTORY_SEPARATOR.$entry;

            if (is_dir($path)) {
                // Diretório de um Resource (ex.: Users, Media, Tenants)
                $resourceClass = sprintf('App\\Filament\\Resources\\%s\\%sResource', $entry, rtrim($entry, 's'));

                // Alguns resources podem estar diretamente dentro do diretório (Media/MediaResource.php já existe)
                if (! class_exists($resourceClass)) {
                    $resourceClass = sprintf('App\\Filament\\Resources\\%s\\%sResource', $entry, $entry);
                }

                if (! class_exists($resourceClass)) {
                    // Tenta formato alternativo: App\Filament\Resources\Media\MediaResource
                    $resourceClass = sprintf('App\\Filament\\Resources\\%sResource', $entry);
                }

                if (! class_exists($resourceClass)) {
                    continue;
                }

                // Gera slug a partir do nome do resource (ex.: UserResource => users)
                $base = class_basename($resourceClass);
                $slug = (string) str($base)->beforeLast('Resource')->kebab()->plural();

                // Ignora explicitamente o resource de tenants
                if ($slug === 'tenants' || $resourceClass === 'App\\Filament\\Resources\\Tenants\\TenantResource') {
                    continue;
                }

                // Tenta obter o plural label do Resource se existir o método estático
                $label = method_exists($resourceClass, 'getPluralModelLabel')
                    ? (string) $resourceClass::getPluralModelLabel()
                    : (string) str($slug)->replace('-', ' ')->ucfirst();

                $map[$slug] = $label;
            }
        }

        // Ordena por label para UX consistente
        asort($map);

        return $map;
    }
}
