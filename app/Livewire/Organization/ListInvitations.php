<?php

namespace App\Livewire\Organization;

use App\Enums\OrganizationRole;
use App\Events\OrganizationInviteCreated;
use App\Models\OrganizationInvite;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\TableComponent;
use Livewire\Attributes\On;

class ListInvitations extends TableComponent
{
    #[On('invites-changed')]
    public function refresh(): void {}

    public function table(Table $table): Table
    {
        $tenant = Filament::getTenant();
        $expiresDays = config('filament-tenant-members.invite_expires_days', 7);
        $roleEnum = config('filament-tenant-members.role_enum', OrganizationRole::class);

        return $table
            ->query(
                OrganizationInvite::query()
                    ->with('user')
                    ->where('organization_id', $tenant?->getKey())
                    ->pending()
            )
            ->columns([
                TextColumn::make('email')
                    ->label(__('organization.fields.email'))
                    ->searchable(),
                TextColumn::make('role')
                    ->label(__('organization.fields.role'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => $roleEnum::tryFrom($state ?? '')?->getLabel() ?? $state ?? '-'),
                TextColumn::make('user.name')
                    ->label(__('organization.fields.invited_by'))
                    ->placeholder('-'),
                TextColumn::make('expires_at')
                    ->label(__('organization.fields.expires_at'))
                    ->dateTime('d/m/Y H:i'),
            ])
            ->recordActions([
                Action::make('resend')
                    ->icon('heroicon-o-paper-airplane')
                    ->label(__('organization.actions.resend.label'))
                    ->requiresConfirmation()
                    ->modalHeading(__('organization.actions.resend.modal_heading'))
                    ->modalDescription(fn ($record) => __('organization.actions.resend.modal_description', ['email' => $record->email]))
                    ->visible(fn ($record) => $record->isResendable())
                    ->action(function ($record) use ($expiresDays): void {
                        if (! $record->isResendable()) {
                            Notification::make()
                                ->title(__('organization.notifications.resend_cooldown'))
                                ->warning()
                                ->send();

                            return;
                        }

                        $record->update([
                            'expires_at' => now()->addDays($expiresDays),
                        ]);

                        OrganizationInviteCreated::dispatch($record);

                        Notification::make()
                            ->title(__('organization.notifications.resend_success_title'))
                            ->body(__('organization.notifications.resend_success_body', ['email' => $record->email]))
                            ->success()
                            ->send();
                    }),
                ActionGroup::make([
                    DeleteAction::make()
                        ->label(__('organization.actions.cancel.label'))
                        ->after(fn () => $this->dispatch('invites-changed')),
                ]),
            ]);
    }

    public function render(): string
    {
        return <<<'HTML'
        <div>{{ $this->table }}</div>
        HTML;
    }
}
