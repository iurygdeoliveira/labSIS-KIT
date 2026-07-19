<?php

namespace App\Filament\Pages\Organization;

use App\Filament\Clusters\TenantSettings;
use App\Traits\Filament\HasConfigurableNavigationSort;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\Rule;

/**
 * @property Schema $form
 */
class GeneralSettings extends Page
{
    use HasConfigurableNavigationSort;

    protected static ?string $cluster = TenantSettings::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('organization.general_settings.navigation_label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('organization.general_settings.title');
    }

    public function mount(): void
    {
        $this->form->fill(
            Filament::getTenant()->only(['name', 'slug']),
        );
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $tenant = Filament::getTenant();

        return $schema
            ->statePath('data')
            ->components([
                TextInput::make('name')
                    ->label(__('organization.fields.name'))
                    ->required(),
                TextInput::make('slug')
                    ->label(__('organization.fields.slug'))
                    ->required()
                    ->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
                    ->rules([Rule::unique($tenant->getTable(), 'slug')->ignore($tenant->getKey())])
                    ->validationMessages(['regex' => __('organization.validation.slug_regex')]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(__('organization.actions.save.label'))
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Filament::getTenant()->update($data);

        Notification::make()
            ->title(__('organization.notifications.saved'))
            ->success()
            ->send();
    }
}
