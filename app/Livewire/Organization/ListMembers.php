<?php

namespace App\Livewire\Organization;

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\TableComponent;
use Illuminate\Support\Facades\DB;

class ListMembers extends TableComponent
{
    public function table(Table $table): Table
    {
        $tenant = Filament::getTenant();
        $currentUserId = Filament::auth()->user()->id;
        $roleEnum = config('filament-tenant-members.role_enum', OrganizationRole::class);

        $pivotRole = null;
        if ($tenant instanceof Organization) {
            $pivotRole = $tenant->users()
                ->where('users.id', $currentUserId)
                ->first()
                ?->pivot
                ?->getAttribute('role');
        }

        $currentRole = $pivotRole ? $roleEnum::tryFrom($pivotRole) : null;

        $query = $tenant instanceof Organization
            ? $tenant->users()->getQuery()
                ->select('users.*', 'organization_user.role as pivot_role')
                // @phpstan-ignore-next-line
                ->orderByRaw($roleEnum::orderBySql('organization_user.role'))
                ->orderBy('users.name')
            : User::query()->whereRaw('1 = 0');

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('name')
                    ->label(__('organization.fields.name'))
                    ->weight(FontWeight::Medium)
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('organization.fields.email'))
                    ->weight(FontWeight::Light)
                    ->color('gray')
                    ->searchable(),
                TextColumn::make('pivot_role')
                    ->label(__('organization.fields.role'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => $roleEnum::tryFrom($state ?? '')?->getLabel() ?? $state ?? '-')
                    ->color(fn (?string $state) => $roleEnum::tryFrom($state ?? '')?->getColor() ?? 'gray')
                    ->grow(false),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('changeRole')
                        ->label(__('organization.actions.change_role.label'))
                        ->icon('heroicon-o-user-circle')
                        ->modalHeading(fn ($record) => __('organization.actions.change_role.modal_heading', ['name' => $record->name]))
                        ->modalSubmitActionLabel(__('organization.actions.change_role.modal_submit_label'))
                        ->schema([
                            Select::make('role')
                                ->label(__('organization.fields.new_role'))
                                ->options($roleEnum::assignableOptions())
                                ->required()
                                ->default(fn ($record) => $record->pivot_role),
                        ])
                        ->modalWidth(Width::Small)
                        ->action(function ($record, array $data) use ($roleEnum): void {
                            $tenant = Filament::getTenant();
                            if ($tenant instanceof Organization) {
                                $tenant->users()->updateExistingPivot($record->id, [
                                    'role' => $data['role'],
                                ]);
                            }

                            $role = $roleEnum::tryFrom($data['role']);

                            Notification::make()
                                ->title(__('organization.notifications.role_updated_title'))
                                ->body(__('organization.notifications.role_updated_body', [
                                    'name' => $record->name,
                                    'role' => $role?->getLabel() ?? $data['role'],
                                ]))
                                ->success()
                                ->send();
                        }),
                    Action::make('transferOwnership')
                        ->label(__('organization.actions.transfer_ownership.label'))
                        ->icon('heroicon-o-arrow-right-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading(__('organization.actions.transfer_ownership.modal_heading'))
                        ->modalDescription(function ($record) use ($roleEnum): string {
                            $defaultRoleName = $roleEnum::assignableOptions()[config('filament-tenant-members.default_role', 'user')] ?? config('filament-tenant-members.default_role', 'user');

                            return __('organization.actions.transfer_ownership.modal_description', [
                                'name' => $record->name,
                                'role' => $defaultRoleName,
                            ]);
                        })
                        ->action(function ($record) use ($currentUserId, $roleEnum): void {
                            $tenant = Filament::getTenant();

                            if (! $tenant instanceof Organization) {
                                return;
                            }

                            DB::transaction(function () use ($tenant, $record, $currentUserId, $roleEnum): void {
                                $tenant->users()->updateExistingPivot($record->id, [
                                    'role' => $roleEnum::ownerValue(),
                                ]);

                                $tenant->users()->updateExistingPivot($currentUserId, [
                                    'role' => config('filament-tenant-members.default_role', 'user'),
                                ]);
                            });

                            Notification::make()
                                ->title(__('organization.notifications.ownership_transferred_title'))
                                ->body(__('organization.notifications.ownership_transferred_body', ['name' => $record->name]))
                                ->success()
                                ->send();
                        })
                        ->visible(fn () => $currentRole?->isProtected() ?? false),
                    Action::make('remove')
                        ->label(__('organization.actions.remove.label'))
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading(__('organization.actions.remove.modal_heading'))
                        ->modalDescription(fn ($record) => __('organization.actions.remove.modal_description', ['name' => $record->name]))
                        ->action(function ($record): void {
                            $tenant = Filament::getTenant();
                            if ($tenant instanceof Organization) {
                                $tenant->users()->detach($record->id);
                            }

                            Notification::make()
                                ->title(__('organization.notifications.member_removed_title'))
                                ->body(__('organization.notifications.member_removed_body', ['name' => $record->name]))
                                ->success()
                                ->send();
                        }),
                ])
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->tooltip(__('organization.actions.tooltip'))
                    ->visible(function ($record) use ($currentUserId, $currentRole, $roleEnum): bool {
                        if ($record->id === $currentUserId) {
                            return false;
                        }

                        $targetRole = $roleEnum::tryFrom($record->pivot_role ?? '');

                        if ($targetRole?->isProtected()) {
                            return false;
                        }

                        return $currentRole?->canManageMembers() ?? false;
                    }),
                Action::make('leave')
                    ->label(__('organization.actions.leave.label'))
                    ->icon('heroicon-o-arrow-right-start-on-rectangle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(__('organization.actions.leave.modal_heading'))
                    ->modalDescription(__('organization.actions.leave.modal_description'))
                    ->action(function () use ($currentUserId): void {
                        $tenant = Filament::getTenant();
                        if ($tenant instanceof Organization) {
                            $tenant->users()->detach($currentUserId);
                        }

                        Notification::make()
                            ->title(__('organization.notifications.left_organization'))
                            ->success()
                            ->send();

                        $this->redirect(Filament::getUrl());
                    })
                    ->visible(function ($record) use ($currentUserId, $currentRole): bool {
                        return $record->id === $currentUserId
                            && ! ($currentRole?->isProtected() ?? false);
                    }),
            ]);
    }

    public function render(): string
    {
        return <<<'HTML'
        <div>{{ $this->table }}</div>
        HTML;
    }
}
