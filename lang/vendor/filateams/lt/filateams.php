<?php

declare(strict_types=1);

return [
    'pages' => [
        'edit_team'   => ['label' => 'Komandos nustatymai'],
        'create_team' => ['label' => 'Sukurti komandą'],
    ],

    'fields' => [
        'team_name'     => ['label' => 'Komandos pavadinimas'],
        'name'          => ['label' => 'Vardas'],
        'email'         => ['label' => 'El. paštas'],
        'email_address' => ['label' => 'El. pašto adresas'],
        'role'          => ['label' => 'Vaidmuo'],
        'invited_by'    => ['label' => 'Pakvietė'],
        'expires'       => ['label' => 'Galioja iki'],
    ],

    'sections' => [
        'delete_team' => ['heading' => 'Ištrinti komandą'],
    ],

    'tables' => [
        'members' => [
            'heading' => 'Komandos nariai',
        ],
        'invitations' => [
            'heading'     => 'Laukiantys pakvietimai',
            'empty_state' => [
                'heading'     => 'Nėra laukiančių pakvietimų',
                'description' => 'Pakvieskite komandos narius paspausdami mygtuką aukščiau.',
            ],
        ],
    ],

    'actions' => [
        'delete_team' => [
            'label'              => 'Ištrinti komandą',
            'modal_heading'      => 'Ištrinti komandą',
            'modal_description'  => 'Ar tikrai norite ištrinti šią komandą? Šio veiksmo negalima atšaukti.',
            'modal_submit_label' => 'Ištrinti komandą',
        ],
        'change_role'       => ['label' => 'Keisti vaidmenį'],
        'remove_member'     => ['label' => 'Pašalinti'],
        'leave_team'        => ['label' => 'Palikti komandą'],
        'invite_member'     => ['label' => 'Pakviesti narį'],
        'cancel_invitation' => ['label' => 'Atšaukti'],
    ],

    'notifications' => [
        'cannot_delete_personal_team' => ['title' => 'Negalima ištrinti asmeninės komandos.'],
        'role_updated'                => ['title' => 'Vaidmuo atnaujintas.'],
        'member_removed'              => ['title' => 'Narys pašalintas.'],
        'left_team'                   => ['title' => 'Jūs palikote komandą.'],
        'invitation_sent'             => ['title' => 'Pakvietimas išsiųstas adresu :email.'],
        'invitation_cancelled'        => ['title' => 'Pakvietimas atšauktas.'],
    ],

    'validation' => [
        'team_name' => [
            'reserved'       => 'Šis komandos pavadinimas yra rezervuotas ir negali būti naudojamas.',
            'route_conflict' => 'Šis komandos pavadinimas konfliktuoja su esamu maršrutu ir negali būti naudojamas.',
        ],
        'invitation' => [
            'already_member' => 'Šis vartotojas jau yra komandos narys.',
            'pending_exists' => 'Šiam el. pašto adresui jau yra laukiantis pakvietimas.',
        ],
    ],

    'mail' => [
        'invitation' => [
            'subject'       => 'Jūs buvote pakviesti prisijungti prie :team',
            'line_invited'  => ':inviter pakvietė jus prisijungti prie :team komandos.',
            'action_accept' => 'Priimti pakvietimą',
            'line_expiry'   => 'Šis pakvietimas galioja iki :date.',
        ],
    ],

    'flash' => [
        'invitation_expired'     => 'Šio pakvietimo galiojimo laikas baigėsi.',
        'invitation_wrong_email' => 'Šis pakvietimas buvo išsiųstas kitu el. pašto adresu.',
        'no_team'                => 'Norėdami pasiekti šį išteklių, turite būti komandos narys.',
        'not_member_of_any_team' => 'Jūs nesate jokios komandos narys.',
    ],

    'personal_team_name' => ':name komanda',

    'roles' => [
        'owner'  => 'Savininkas',
        'admin'  => 'Administratorius',
        'member' => 'Narys',
    ],
];
