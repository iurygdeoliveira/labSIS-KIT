<?php

declare(strict_types=1);

return [
    'pages' => [
        'edit_team'   => ['label' => 'Configurações do time'],
        'create_team' => ['label' => 'Criar time'],
    ],

    'fields' => [
        'team_name'     => ['label' => 'Nome do time'],
        'name'          => ['label' => 'Nome'],
        'email'         => ['label' => 'E-mail'],
        'email_address' => ['label' => 'Endereço de e-mail'],
        'role'          => ['label' => 'Papel'],
        'invited_by'    => ['label' => 'Convidado por'],
        'expires'       => ['label' => 'Expira em'],
    ],

    'sections' => [
        'delete_team' => ['heading' => 'Excluir time'],
    ],

    'tables' => [
        'members' => [
            'heading' => 'Membros do time',
        ],
        'invitations' => [
            'heading'     => 'Convites pendentes',
            'empty_state' => [
                'heading'     => 'Nenhum convite pendente',
                'description' => 'Convide membros clicando no botão acima.',
            ],
        ],
    ],

    'actions' => [
        'delete_team' => [
            'label'              => 'Excluir time',
            'modal_heading'      => 'Excluir time',
            'modal_description'  => 'Tem certeza de que deseja excluir este time? Esta ação não pode ser desfeita.',
            'modal_submit_label' => 'Excluir time',
        ],
        'change_role'       => ['label' => 'Alterar papel'],
        'remove_member'     => ['label' => 'Remover'],
        'leave_team'        => ['label' => 'Sair do time'],
        'invite_member'     => ['label' => 'Convidar membro'],
        'cancel_invitation' => ['label' => 'Cancelar'],
    ],

    'notifications' => [
        'cannot_delete_personal_team' => ['title' => 'Não é possível excluir o time pessoal.'],
        'role_updated'                => ['title' => 'Papel atualizado.'],
        'member_removed'              => ['title' => 'Membro removido.'],
        'left_team'                   => ['title' => 'Você saiu do time.'],
        'invitation_sent'             => ['title' => 'Convite enviado para :email.'],
        'invitation_cancelled'        => ['title' => 'Convite cancelado.'],
    ],

    'validation' => [
        'team_name' => [
            'reserved'       => 'Este nome de time é reservado e não pode ser usado.',
            'route_conflict' => 'Este nome de time conflita com uma rota existente e não pode ser usado.',
        ],
        'invitation' => [
            'already_member' => 'Este usuário já é membro do time.',
            'pending_exists' => 'Já existe um convite pendente para este e-mail.',
        ],
    ],

    'mail' => [
        'invitation' => [
            'subject'       => 'Você foi convidado para entrar no time :team',
            'line_invited'  => ':inviter convidou você para entrar no time :team.',
            'action_accept' => 'Aceitar convite',
            'line_expiry'   => 'Este convite expira em :date.',
        ],
    ],

    'flash' => [
        'invitation_expired'     => 'Este convite expirou.',
        'invitation_wrong_email' => 'Este convite foi enviado para outro endereço de e-mail.',
        'no_team'                => 'Você precisa ser membro de um time para acessar este recurso.',
        'not_member_of_any_team' => 'Você não é membro de nenhum time.',
    ],

    'personal_team_name' => 'Time de :name',

    'roles' => [
        'owner'  => 'Proprietário',
        'admin'  => 'Administrador',
        'member' => 'Membro',
    ],
];
