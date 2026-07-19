<?php

return [
    'settings' => [
        'navigation_label' => 'Configurações',
    ],
    'general_settings' => [
        'navigation_label' => 'Geral',
        'title' => 'Configurações Gerais',
    ],
    'register' => [
        'label' => 'Cadastrar Organização',
    ],
    'members' => [
        'title' => 'Membros',
        'navigation_label' => 'Membros',
        'tabs' => [
            'members' => 'Membros',
            'pending_invitations' => 'Convites Pendentes',
        ],
    ],
    'fields' => [
        'name' => 'Nome',
        'slug' => 'Slug',
        'email' => 'E-mail',
        'role' => 'Função',
        'invited_by' => 'Convidado por',
        'expires_at' => 'Expira em',
        'new_role' => 'Nova Função',
        'created_at' => 'Criado em',
    ],
    'validation' => [
        'slug_regex' => 'O slug deve conter apenas letras minúsculas, números e hífens.',
        'unique' => 'Este e-mail já possui um convite pendente.',
        'exists' => 'Este e-mail já pertence a um membro da organização.',
        'invalid_invitation' => 'Este convite é inválido ou expirou.',
    ],
    'notifications' => [
        'saved' => 'Configurações salvas com sucesso.',
        'sent_single' => 'Convite enviado com sucesso.',
        'sent_multiple' => ':count convites enviados com sucesso.',
        'none_sent_title' => 'Nenhum convite enviado.',
        'none_sent_body' => 'Todos os e-mails informados já possuem convites pendentes.',
        'resend_cooldown' => 'Por favor, aguarde antes de reenviar.',
        'resend_success_title' => 'Convite reenviado',
        'resend_success_body' => 'Convite reenviado para :email.',
        'role_updated_title' => 'Função atualizada',
        'role_updated_body' => ':name agora é :role.',
        'ownership_transferred_title' => 'Propriedade transferida',
        'ownership_transferred_body' => ':name agora é o proprietário.',
        'member_removed_title' => 'Membro removido',
        'member_removed_body' => ':name foi removido da organização.',
        'left_organization' => 'Você saiu da organização.',
        'invitation_declined_title' => 'Convite recusado',
        'invitation_declined_body' => 'Você recusou o convite para entrar na organização :organization.',
        'invitation_accepted_title' => 'Convite aceito',
        'invitation_accepted_body' => 'Você entrou para a organização :organization.',
        'invitation_unavailable_title' => 'Este convite não está mais disponível.',
    ],
    'actions' => [
        'save' => [
            'label' => 'Salvar',
        ],
        'invite' => [
            'label' => 'Convidar Usuários',
            'add_another' => 'Adicionar outro',
            'modal_heading' => 'Convidar Usuários',
            'modal_description' => 'Convide novos membros para sua organização por e-mail.',
            'modal_submit_label' => 'Enviar Convites',
        ],
        'resend' => [
            'label' => 'Reenviar',
            'modal_heading' => 'Reenviar convite',
            'modal_description' => 'Reenviar o convite para :email?',
        ],
        'cancel' => [
            'label' => 'Cancelar convite',
        ],
        'change_role' => [
            'label' => 'Alterar função',
            'modal_heading' => 'Alterar função de :name',
            'modal_submit_label' => 'Salvar',
        ],
        'transfer_ownership' => [
            'label' => 'Transferir propriedade',
            'modal_heading' => 'Transferir propriedade',
            'modal_description' => 'Tem certeza que deseja transferir a propriedade para :name? Você será rebaixado para :role.',
        ],
        'remove' => [
            'label' => 'Remover',
            'modal_heading' => 'Remover membro',
            'modal_description' => 'Tem certeza que deseja remover :name desta organização?',
        ],
        'tooltip' => 'Ações',
        'leave' => [
            'label' => 'Sair',
            'modal_heading' => 'Sair da organização',
            'modal_description' => 'Tem certeza que deseja sair desta organização? Você perderá o acesso imediatamente.',
        ],
        'decline' => [
            'label' => 'Recusar',
            'modal_heading' => 'Recusar convite',
            'modal_description' => 'Tem certeza? Você poderá pedir para ser convidado novamente mais tarde.',
        ],
        'accept' => [
            'label' => 'Aceitar convite',
        ],
    ],
    'roles' => [
        'owner' => 'Proprietário',
        'admin' => 'Administrador',
        'user' => 'Membro',
    ],
    'mail' => [
        'invitation' => [
            'subject' => 'Você foi convidado para entrar na organização :organization',
            'heading' => 'Você foi convidado',
            'body' => ':user convidou você para entrar na organização :organization como :role.',
            'expiry' => 'Este convite expira em :date.',
            'button' => 'Aceitar Convite',
            'ignore' => 'Se você não esperava este convite, pode ignorar este e-mail.',
        ],
    ],
    'accept_invite' => [
        'title' => 'Convite',
        'invalid_heading' => 'Convite inválido',
        'invalid_subheading' => 'Este convite é inválido ou expirou.',
        'mismatch_heading' => 'E-mail divergente',
        'mismatch_subheading' => 'Este convite foi enviado para outro endereço de e-mail. Por favor, faça login com a conta correta.',
        'invited_heading' => 'Você foi convidado',
        'logout_button' => 'Sair e tentar novamente',
        'dashboard_button' => 'Ir para o painel de controle',
        'invited_body' => ':user convidou você para entrar na organização :organization.',
        'default_user' => 'Um membro da organização',
    ],
];
