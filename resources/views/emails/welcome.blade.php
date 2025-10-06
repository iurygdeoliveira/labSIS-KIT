<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bem-vindo</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #3b82f6; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9fafb; }
        .button { display: inline-block; padding: 12px 24px; background: #3b82f6; color: white; text-decoration: none; border-radius: 6px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Bem-vindo ao {{ config('app.name') }}!</h1>
        </div>
        <div class="content">
            <p>Olá {{ $user->name }},</p>
            
            <p>Sua conta foi criada com sucesso! Agora você pode acessar nossa plataforma.</p>
            
            @if($password)
                <p><strong>Suas credenciais de acesso:</strong></p>
                <ul>
                    <li><strong>Email:</strong> {{ $user->email }}</li>
                    <li><strong>Senha:</strong> {{ $password }}</li>
                </ul>
                <p><em>Recomendamos que você altere sua senha no primeiro acesso.</em></p>
            @endif
            
            <p style="text-align: center; margin: 30px 0;">
                <a href="{{ $loginUrl }}" class="button">Acessar Plataforma</a>
            </p>
            
            <p>Se você tiver alguma dúvida, entre em contato conosco.</p>
            
            <p>Obrigado,<br>Equipe {{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>
