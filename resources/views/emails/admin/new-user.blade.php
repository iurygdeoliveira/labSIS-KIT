<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Novo Usuário Cadastrado</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f59e0b; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9fafb; }
        .button { display: inline-block; padding: 12px 24px; background: #f59e0b; color: white; text-decoration: none; border-radius: 6px; }
        .user-info { background: white; padding: 15px; border-radius: 6px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Novo Usuário Cadastrado</h1>
        </div>
        <div class="content">
            <p>Olá {{ $admin->name }},</p>
            
            <p>Um novo usuário foi cadastrado no sistema:</p>
            
            <div class="user-info">
                <p><strong>Nome:</strong> {{ $newUser->name }}</p>
                <p><strong>Email:</strong> {{ $newUser->email }}</p>
                <p><strong>Data de Cadastro:</strong> {{ $newUser->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Email Verificado:</strong> {{ $newUser->hasVerifiedEmail() ? 'Sim' : 'Não' }}</p>
            </div>
            
            <p style="text-align: center; margin: 30px 0;">
                <a href="{{ $userUrl }}" class="button">Ver Usuário</a>
            </p>
            
            <p>Equipe {{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>
