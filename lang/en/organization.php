<?php

return [
    'settings' => [
        'navigation_label' => 'Settings',
    ],
    'general_settings' => [
        'navigation_label' => 'General',
        'title' => 'General Settings',
    ],
    'register' => [
        'label' => 'Register Organization',
    ],
    'members' => [
        'title' => 'Members',
        'navigation_label' => 'Members',
        'tabs' => [
            'members' => 'Members',
            'pending_invitations' => 'Pending Invitations',
        ],
    ],
    'fields' => [
        'name' => 'Name',
        'slug' => 'Slug',
        'email' => 'Email',
        'role' => 'Role',
        'invited_by' => 'Invited by',
        'expires_at' => 'Expires at',
        'new_role' => 'New Role',
        'created_at' => 'Created at',
    ],
    'validation' => [
        'slug_regex' => 'The slug may only contain lowercase letters, numbers, and hyphens.',
        'unique' => 'This email already has a pending invitation.',
        'exists' => 'This email belongs to an existing member.',
        'invalid_invitation' => 'This invitation is invalid or has expired.',
    ],
    'notifications' => [
        'saved' => 'Saved successfully.',
        'sent_single' => 'Invitation sent successfully.',
        'sent_multiple' => ':count invitations sent successfully.',
        'none_sent_title' => 'No invitations sent.',
        'none_sent_body' => 'All provided emails already have pending invitations.',
        'resend_cooldown' => 'Please wait before resending.',
        'resend_success_title' => 'Invitation resent',
        'resend_success_body' => 'Invitation resent to :email.',
        'role_updated_title' => 'Role updated',
        'role_updated_body' => ':name is now :role.',
        'ownership_transferred_title' => 'Ownership transferred',
        'ownership_transferred_body' => ':name is now the owner.',
        'member_removed_title' => 'Member removed',
        'member_removed_body' => ':name has been removed from the organization.',
        'left_organization' => 'You have left the organization.',
        'invitation_declined_title' => 'Invitation declined',
        'invitation_declined_body' => 'You have declined the invitation to :organization.',
        'invitation_accepted_title' => 'Invitation accepted',
        'invitation_accepted_body' => 'You have joined :organization.',
        'invitation_unavailable_title' => 'This invitation is no longer available.',
    ],
    'actions' => [
        'save' => [
            'label' => 'Save',
        ],
        'invite' => [
            'label' => 'Invite Users',
            'add_another' => 'Add another',
            'modal_heading' => 'Invite Users',
            'modal_description' => 'Invite new members to your organization by email.',
            'modal_submit_label' => 'Send invitations',
        ],
        'resend' => [
            'label' => 'Resend',
            'modal_heading' => 'Resend invitation',
            'modal_description' => 'Resend the invitation to :email?',
        ],
        'cancel' => [
            'label' => 'Cancel invitation',
        ],
        'change_role' => [
            'label' => 'Change role',
            'modal_heading' => 'Change role for :name',
            'modal_submit_label' => 'Save',
        ],
        'transfer_ownership' => [
            'label' => 'Transfer ownership',
            'modal_heading' => 'Transfer ownership',
            'modal_description' => 'Are you sure you want to transfer ownership to :name? You will be demoted to :role.',
        ],
        'remove' => [
            'label' => 'Remove',
            'modal_heading' => 'Remove member',
            'modal_description' => 'Are you sure you want to remove :name from this organization?',
        ],
        'tooltip' => 'Actions',
        'leave' => [
            'label' => 'Leave',
            'modal_heading' => 'Leave organization',
            'modal_description' => 'Are you sure you want to leave this organization? You will lose access immediately.',
        ],
        'decline' => [
            'label' => 'Decline',
            'modal_heading' => 'Decline invitation',
            'modal_description' => 'Are you sure? You can ask to be invited again later.',
        ],
        'accept' => [
            'label' => 'Accept invitation',
        ],
    ],
    'roles' => [
        'owner' => 'Owner',
        'admin' => 'Admin',
        'user' => 'User',
    ],
    'mail' => [
        'invitation' => [
            'subject' => 'You\'ve been invited to join :organization',
            'heading' => 'You\'ve been invited',
            'body' => ':user has invited you to join :organization as :role.',
            'expiry' => 'This invitation expires on :date.',
            'button' => 'Accept Invitation',
            'ignore' => 'If you weren\'t expecting this invitation, you can ignore this email.',
        ],
    ],
    'accept_invite' => [
        'title' => 'Invitation',
        'invalid_heading' => 'Invalid invitation',
        'invalid_subheading' => 'This invitation is invalid or has expired.',
        'mismatch_heading' => 'Email mismatch',
        'mismatch_subheading' => 'This invitation was sent to a different email address. Please log in with the correct account.',
        'invited_heading' => 'You\'ve been invited',
        'logout_button' => 'Log out and try again',
        'dashboard_button' => 'Go to dashboard',
        'invited_body' => ':user has invited you to join :organization.',
        'default_user' => 'A team member',
    ],
];
