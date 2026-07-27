@php
    use Filament\Support\Enums\Alignment;
    use Filament\Support\View\Components\BadgeComponent;
    use Illuminate\View\ComponentAttributeBag;

    $tabs = $this->categoryTabs;
    $notifications = $this->getNotifications();
    $unreadNotificationsCount = $this->getUnreadNotificationsCount();
    $hasNotifications = $notifications->count();
    $hasAnyNotifications = $this->hasAnyNotifications();
    $isPaginated = $notifications instanceof \Illuminate\Contracts\Pagination\Paginator && $notifications->hasPages();
    $pollingInterval = $this->getPollingInterval();
    $emptyState = $this->getCategoryEmptyState($activeCategory);
@endphp

<div class="fi-no-database">
    <x-filament::modal
        :alignment="$hasAnyNotifications ? null : Alignment::Center"
        close-button
        :description="$hasAnyNotifications ? null : __('filament-notifications::database.modal.empty.description')"
        :heading="$hasAnyNotifications ? null : __('filament-notifications::database.modal.empty.heading')"
        :icon="$hasAnyNotifications ? null : \Filament\Support\Icons\Heroicon::OutlinedBellSlash"
        :icon-alias="
            $hasAnyNotifications
            ? null
            : \Filament\Notifications\View\NotificationsIconAlias::DATABASE_MODAL_EMPTY_STATE
        "
        :icon-color="$hasAnyNotifications ? null : 'gray'"
        id="database-notifications"
        slide-over
        :sticky-header="$hasAnyNotifications"
        teleport="body"
        width="md"
        class="fi-no-database"
        :attributes="
            new \Illuminate\View\ComponentAttributeBag([
                'wire:poll.' . $pollingInterval => $pollingInterval ? '' : false,
            ])
        "
    >
        @if ($trigger = $this->getTrigger())
            <x-slot name="trigger">
                {{ $trigger->with(['unreadNotificationsCount' => $unreadNotificationsCount]) }}
            </x-slot>
        @endif

        @if ($hasAnyNotifications)
            <x-slot name="header">
                <div class="fi-notification-center-header">
                    <div>
                        <h2 class="fi-modal-heading">
                            {{ __('filament-notifications::database.modal.heading') }}

                            @if ($unreadNotificationsCount)
                                <span
                                    {{
                                        (new ComponentAttributeBag)->color(BadgeComponent::class, 'primary')->class([
                                            'fi-badge fi-size-xs',
                                        ])
                                    }}
                                >
                                    {{ $unreadNotificationsCount }}
                                </span>
                            @endif
                        </h2>

                        <div class="fi-ac">
                            @if ($unreadNotificationsCount && $this->markAllNotificationsAsReadAction?->isVisible())
                                {{ $this->markAllNotificationsAsReadAction }}
                            @endif

                            @if ($hasNotifications && $this->clearNotificationsAction?->isVisible())
                                {{ $this->clearNotificationsAction }}
                            @endif
                        </div>
                    </div>

                    {{-- See notification-center.css for why the border/shadow, clipping,
                         and width containment are split across two wrappers here. --}}
                    <div class="fi-notification-center-tabs-outer">
                        <div class="fi-notification-center-tabs-inner">
                            <x-filament::tabs
                                label="Categorias de notificação"
                                style="box-shadow: none;"
                            >
                                @foreach ($tabs as $tab)
                                    <x-filament::tabs.item
                                        :active="$activeCategory === $tab->id"
                                        :icon="$tab->icon"
                                        :badge="$tab->count > 0 ? $tab->count : null"
                                        :badge-color="$tab->color ?? 'gray'"
                                        wire:click="setActiveCategory('{{ $tab->id }}')"
                                        wire:key="notification-center-tab-{{ $tab->id }}"
                                    >
                                        {{ $tab->label }}
                                    </x-filament::tabs.item>
                                @endforeach
                            </x-filament::tabs>
                        </div>
                    </div>
                </div>
            </x-slot>

            @if ($hasNotifications)
                @foreach ($notifications as $notification)
                    <div
                        @class([
                            'fi-no-notification-read-ctn' => ! $notification->unread(),
                            'fi-no-notification-unread-ctn' => $notification->unread(),
                        ])
                    >
                        {{ $this->getNotification($notification)->inline() }}
                    </div>
                @endforeach
            @else
                <x-filament::empty-state
                    :heading="$emptyState['heading']"
                    :description="$emptyState['description']"
                    :icon="\Filament\Support\Icons\Heroicon::OutlinedBellSlash"
                    icon-color="gray"
                    :contained="false"
                />
            @endif

            @if ($broadcastChannel = $this->getBroadcastChannel())
                @script
                    <script>
                        window.addEventListener('EchoLoaded', () => {
                            window.Echo.private(@js($broadcastChannel)).listen(
                                '.database-notifications.sent',
                                () => {
                                    setTimeout(
                                        () => $wire.call('$refresh'),
                                        500,
                                    )
                                },
                            )
                        })

                        if (window.Echo) {
                            window.dispatchEvent(new CustomEvent('EchoLoaded'))
                        }
                    </script>
                @endscript
            @endif

            @if ($isPaginated)
                <x-slot name="footer">
                    <x-filament::pagination :paginator="$notifications" />
                </x-slot>
            @endif
        @endif
    </x-filament::modal>
</div>
