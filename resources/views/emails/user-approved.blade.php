<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conta Aprovada</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .content {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
        }
        .button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎉 Sua conta foi aprovada!</h1>
    </div>

    <div class="content">
        <p>Olá <strong>{{ $user->name }}</strong>,</p>

        <p>É com grande prazer que informamos que sua conta foi aprovada e está ativa no sistema <strong>{{ config('app.name') }}</strong>.</p>

        <p>Agora você pode acessar sua conta usando as credenciais que definiu durante o cadastro:</p>

        <ul>
            <li><strong>Email:</strong> {{ $user->email }}</li>
            <li><strong>Senha:</strong> A senha que você definiu no cadastro</li>
        </ul>

        <p>Clique no botão abaixo para fazer login:</p>

        <a href="{{ $loginUrl }}" class="button">Fazer Login</a>

        <p>Se você tiver alguma dúvida ou precisar de ajuda, não hesite em entrar em contato conosco.</p>

        <p>Bem-vindo ao {{ config('app.name') }}!</p>
    </div>

    <div class="footer">
        <p>Este é um email automático, por favor não responda.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.</p>
    </div>
</body>
</html>

