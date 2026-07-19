<?php

namespace App\Filament\Pages\Organization;

use App\Enums\OrganizationRole;
use App\Models\Organization;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegisterOrganization extends RegisterTenant
{
    public static function getLabel(): string
    {
        return __('organization.register.label');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('organization.fields.name'))
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                        if (($get('slug') ?? '') !== Str::slug($old)) {
                            return;
                        }

                        $set('slug', Str::slug($state));
                    }),

                TextInput::make('slug')
                    ->label(__('organization.fields.slug'))
                    ->required()
                    ->unique()
                    ->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
                    ->validationMessages(['regex' => __('organization.validation.slug_regex')]),
            ]);
    }

    protected function handleRegistration(array $data): Model
    {
        $organizationModel = config('filament-tenant-members.models.organization', Organization::class);
        $roleEnum = config('filament-tenant-members.role_enum', OrganizationRole::class);

        $data['slug'] = $this->ensureUniqueSlug($data['slug']);

        return DB::transaction(function () use ($data, $organizationModel, $roleEnum) {
            $organization = $organizationModel::create($data);
            $organization->users()->attach(Filament::auth()->user(), ['role' => $roleEnum::ownerValue()]);

            return $organization;
        });
    }

    protected function ensureUniqueSlug(string $slug): string
    {
        $model = config('filament-tenant-members.models.organization', Organization::class);

        if (! $model::where('slug', $slug)->exists()) {
            return $slug;
        }

        do {
            $candidate = $slug.'-'.Str::random(5);
        } while ($model::where('slug', $candidate)->exists());

        return $candidate;
    }
}
