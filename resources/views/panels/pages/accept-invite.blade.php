<div>
    @if($error)
        <div class="fi-prose">
            <h3>{{ $this->getHeading() }}</h3>

            @if($this->getSubheading())
                <p>{{ $this->getSubheading() }}</p>
            @endif
        </div>

        <div style="margin-top: 1.5rem">
            @if($error === \App\Filament\Pages\AcceptInvite::ERROR_EMAIL_MISMATCH)
                <x-filament::button
                    :href="filament()->getLogoutUrl()"
                    tag="a"
                    color="gray"
                    icon="heroicon-o-arrow-right-start-on-rectangle"
                >
                    {{ __('organization.accept_invite.logout_button') }}
                </x-filament::button>
            @else
                <x-filament::button
                    :href="filament()->getUrl()"
                    tag="a"
                    color="gray"
                >
                    {{ __('organization.accept_invite.dashboard_button') }}
                </x-filament::button>
            @endif
        </div>
    @elseif($invite)
        <div class="fi-prose">
            <h3>{{ $this->getHeading() }}</h3>

            <p class="lead">
                {!! __('organization.accept_invite.invited_body', [
                    'user' => '<strong>' . e($invite->user?->name ?? __('organization.accept_invite.default_user')) . '</strong>',
                    'organization' => '<strong>' . e($invite->organization->name) . '</strong>'
                ]) !!}
            </p>
        </div>

        <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; margin-top: 1.5rem">
            {{ $this->declineAction }}
            {{ $this->acceptAction }}
        </div>
    @endif

    <x-filament-actions::modals />
</div>
