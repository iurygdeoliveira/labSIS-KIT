<x-mail::message>
# {{ __('organization.mail.invitation.heading') }}

{{ __('organization.mail.invitation.body', [
    'user' => $invite->user->name ?? 'Um membro do time',
    'organization' => $invite->organization->name,
    'role' => $invite->role->getLabel(),
]) }}

{{ __('organization.mail.invitation.expiry', ['date' => $invite->expires_at->format('d/m/Y H:i')]) }}

<x-mail::button :url="route('invite.accept', $invite->token)">
{{ __('organization.mail.invitation.button') }}
</x-mail::button>

{{ __('organization.mail.invitation.ignore') }}
</x-mail::message>
