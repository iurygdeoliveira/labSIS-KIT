# Customização de E-mails e Fluxo de Reset de Senha

O LabSIS KIT possui um sistema robusto para customização de templates de e-mail, garantindo que todas as comunicações enviadas pelo sistema sigam a identidade visual do projeto.

## Estrutura de Templates

Os templates de e-mail estão localizados em `resources/views/vendor/mail/html`.

### Layout Base (`website-layout.blade.php`)

Foi criado um layout base que replica a identidade visual do website (Header e Footer). Todos os e-mails do sistema devem estender este layout para manter a consistência.

```blade
@component('vendor.mail.html.website-layout')
    {{-- Conteúdo do e-mail --}}
@endcomponent
```

### Template de Redefinição de Senha (`password-reset.blade.php`)

Este template sobrescreve o padrão do Laravel para fornecer uma experiência mais personalizada. Ele aceita as seguintes variáveis:

-   `$user`: O modelo do usuário (para personalização, ex: `Olá, {{ $user->name }}`).
-   `$token`: O token de redefinição.
-   `$url`: A URL _assinada_ para redefinição de senha.

## Fluxo de Envio (Reset de Senha)

O fluxo padrão do Filament para reset de senha foi customizado para garantir o uso do nosso template e notificação.

### 1. Notificação Customizada

Criamos a classe `App\Notifications\Auth\ResetPasswordNotification` que estende a notificação padrão do Laravel/Filament. Ela foi ajustada para:

1.  Utilizar a view `vendor.mail.html.password-reset`.
2.  Aceitar uma propriedade pública `$url` (necessária para links assinados corretamente).

### 2. Sobrescrita da Página de Solicitação

No painel administrativo, a página de solicitação de reset (`App\Filament\Pages\Auth\RequestPasswordReset`) sobrescreve o método `request()` padrão.

Isso é necessário porque o Filament, por padrão, instancia uma notificação interna que não utiliza nosso template customizado. Nossa implementação:

1.  Gera a URL de reset assinada corretamente usando `Filament::getResetPasswordUrl($token, $user)`.
2.  Instancia nossa `ResetPasswordNotification`.
3.  Injecta a URL gerada na notificação.
4.  Envia a notificação para o usuário.

**Trecho de código (RequestPasswordReset.php):**

```php
$notification = new \App\Notifications\Auth\ResetPasswordNotification($token);
$notification->url = \Filament\Facades\Filament::getResetPasswordUrl($token, $user);

$user->notify($notification);
```

## Visualização de Templates (Preview)

O LabSIS KIT inclui uma funcionalidade para visualizar e testar os templates de e-mail diretamente pelo painel administrativo.

-   **Acesso:** Menu `Emails` -> `Preview`.
-   **Funcionalidade:** O preview utiliza os dados do usuário logado para renderizar o template em tempo real, permitindo verificar a formatação, variáveis e layout sem precisar disparar um e-mail real.
