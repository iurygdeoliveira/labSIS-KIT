<?php

declare(strict_types=1);

return [
    'pages' => [
        'edit_team'   => ['label' => 'Team Settings'],
        'create_team' => ['label' => 'Create Team'],
    ],

    'fields' => [
        'team_name'     => ['label' => 'Team Name'],
        'name'          => ['label' => 'Name'],
        'email'         => ['label' => 'Email'],
        'email_address' => ['label' => 'Email Address'],
        'role'          => ['label' => 'Role'],
        'invited_by'    => ['label' => 'Invited By'],
        'expires'       => ['label' => 'Expires'],
    ],

    'sections' => [
        'delete_team' => ['heading' => 'Delete Team'],
    ],

    'tables' => [
        'members' => [
            'heading' => 'Team Members',
        ],
        'invitations' => [
            'heading'     => 'Pending Invitations',
            'empty_state' => [
                'heading'     => 'No pending invitations',
                'description' => 'Invite team members by clicking the button above.',
            ],
        ],
    ],

    'actions' => [
        'delete_team' => [
            'label'              => 'Delete Team',
            'modal_heading'      => 'Delete Team',
            'modal_description'  => 'Are you sure you want to delete this team? This action cannot be undone.',
            'modal_submit_label' => 'Delete Team',
        ],
        'change_role'       => ['label' => 'Change Role'],
        'remove_member'     => ['label' => 'Remove'],
        'leave_team'        => ['label' => 'Leave Team'],
        'invite_member'     => ['label' => 'Invite Member'],
        'cancel_invitation' => ['label' => 'Cancel'],
    ],

    'notifications' => [
        'cannot_delete_personal_team' => ['title' => 'Cannot delete personal team.'],
        'role_updated'                => ['title' => 'Role updated.'],
        'member_removed'              => ['title' => 'Member removed.'],
        'left_team'                   => ['title' => 'You have left the team.'],
        'invitation_sent'             => ['title' => 'Invitation sent to :email.'],
        'invitation_cancelled'        => ['title' => 'Invitation cancelled.'],
    ],

    'validation' => [
        'team_name' => [
            'reserved'       => 'This team name is reserved and cannot be used.',
            'route_conflict' => 'This team name conflicts with an existing route and cannot be used.',
        ],
        'invitation' => [
            'already_member' => 'This user is already a team member.',
            'pending_exists' => 'A pending invitation already exists for this email.',
        ],
    ],

    'mail' => [
        'invitation' => [
            'subject'       => "You've been invited to join :team",
            'line_invited'  => ':inviter has invited you to join the :team team.',
            'action_accept' => 'Accept Invitation',
            'line_expiry'   => 'This invitation will expire on :date.',
        ],
    ],

    'flash' => [
        'invitation_expired'     => 'This invitation has expired.',
        'invitation_wrong_email' => 'This invitation was sent to a different email address.',
        'no_team'                => 'You must be a member of a team to access this resource.',
        'not_member_of_any_team' => 'You are not a member of any team.',
    ],

    'personal_team_name' => ":name's Team",

    'roles' => [
        'owner'  => 'Owner',
        'admin'  => 'Admin',
        'member' => 'Member',
    ],
];
