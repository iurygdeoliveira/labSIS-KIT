<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verificação de Email</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #10b981; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9fafb; }
        .button { display: inline-block; padding: 12px 24px; background: #10b981; color: white; text-decoration: none; border-radius: 6px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Verifique seu email</h1>
        </div>
        <div class="content">
            <p>Olá {{ $user->name }},</p>
            
            <p>Para completar seu cadastro, clique no botão abaixo para verificar seu endereço de email:</p>
            
            <p style="text-align: center; margin: 30px 0;">
                <a href="{{ $verificationUrl }}" class="button">Verificar Email</a>
            </p>
            
            <p>Se o botão não funcionar, copie e cole o link abaixo no seu navegador:</p>
            <p style="word-break: break-all; background: #e5e7eb; padding: 10px; border-radius: 4px;">
                {{ $verificationUrl }}
            </p>
            
            <p>Este link expira em 24 horas.</p>
            
            <p>Obrigado,<br>Equipe {{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>
