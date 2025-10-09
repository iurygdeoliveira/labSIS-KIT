# Fluxo de Registro de Novos Usuários

## 📋 Índice

- [Visão Geral](#visão-geral)
- [Arquitetura do Sistema](#arquitetura-do-sistema)
- [Fluxo de Registro](#fluxo-de-registro)
- [Sistema de Eventos](#sistema-de-eventos)
- [Templates de Email](#templates-de-email)
- [Configuração de Email](#configuração-de-email)
- [Aprovação de Usuários](#aprovação-de-usuários)
- [Verificação do Sistema](#verificação-do-sistema)
- [Troubleshooting](#troubleshooting)
- [Conclusão](#conclusão)

## Visão Geral

O sistema implementa um fluxo simplificado de registro de usuários com criação automática de tenants, atribuição de roles e envio de emails de notificação. O sistema utiliza eventos e listeners do Laravel para garantir desacoplamento e facilidade de manutenção.

## Arquitetura do Sistema

### Componentes Principais

1. **Página de Registro Personalizada** (`app/Filament/Pages/Auth/Register.php`)
2. **Sistema de Eventos** (Events e Listeners)
3. **Templates de Email** (Blade templates)
4. **Configuração de Email** (Mailpit para desenvolvimento)
5. **Sistema de Aprovação** (Toggle na tabela de usuários)

### Arquivos Utilizados

- **`app/Filament/Pages/Auth/Register.php`** - Página de registro personalizada
- **`app/Events/UserRegistered.php`** - Evento disparado no registro
- **`app/Events/UserApproved.php`** - Evento disparado na aprovação
- **`app/Listeners/NotifyAdminNewUser.php`** - Listener para notificar admin
- **`app/Listeners/SendUserApprovedEmail.php`** - Listener para notificar usuário aprovado
- **`app/Mail/NewUserNotificationMail.php`** - Email para administrador
- **`app/Mail/UserApprovedMail.php`** - Email para usuário aprovado
- **`app/Providers/AppServiceProvider.php`** - Registro de listeners
- **`app/Filament/Resources/Users/Tables/UsersTable.php`** - Toggle de aprovação

## Fluxo de Registro

### 1. Acesso à Página de Registro

**URL:** `/register`

**Provider:** `AuthPanelProvider` (não o AdminPanelProvider)

O sistema utiliza o `AuthPanelProvider` para gerenciar o registro, que está configurado para:
- Path: `/` (raiz)
- ID: `auth`
- Habilita registro com `->registration()`

### 2. Formulário de Registro

O formulário personalizado (`Register.php`) inclui:

```php
public function form(Schema $schema): Schema
{
    return $schema
        ->schema([
            TextInput::make('name')
                ->label('Nome completo')
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->label('E-mail')
                ->email()
                ->required()
                ->maxLength(255)
                ->unique(User::class),
            TextInput::make('password')
                ->label('Senha')
                ->password()
                ->required()
                ->minLength(8)
                ->confirmed()
                ->revealable()
                ->dehydrateStateUsing(fn (string $state): string => Hash::make($state)),
            TextInput::make('password_confirmation')
                ->label('Confirmar senha')
                ->password()
                ->required()
                ->revealable()
                ->dehydrated(false),
            TextInput::make('tenant_name')
                ->label('Nome do Tenant')
                ->required()
                ->maxLength(255)
                ->unique(Tenant::class, 'name'),
        ])
        ->columns(1);
}
```

**Validações:**
- Email único na tabela `users`
- Nome do tenant único na tabela `tenants`
- Senha com confirmação
- Todos os campos obrigatórios

### 3. Processo de Criação

Quando o usuário submete o formulário, o método `handleRegistration()` executa:

```php
protected function handleRegistration(array $data): Model
{
    try {
        $userData = $this->prepareUserData($data);
        $tenantData = $this->prepareTenantData($data);

        $user = $this->createUser($userData);
        $tenant = $this->createTenant($tenantData);

        $this->associateUserWithTenant($user, $tenant);
        
        // Disparar evento de usuário registrado
        event(new \App\Events\UserRegistered($user));

        $this->showSuccessNotification();

        return $user;
    } catch (QueryException $e) {
        $this->handleDatabaseException($e);
        throw $e;
    } catch (Exception $e) {
        $this->handleGenericException($e);
        throw $e;
    }
}
```

**Dados do Usuário:**
```php
protected function prepareUserData(array $data): array
{
    return [
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => $data['password'],
        'is_suspended' => true, // Usuário fica suspenso até aprovação
        'is_approved' => false, // Usuário não aprovado por padrão
        'email_verified_at' => null, // Email não verificado por padrão
    ];
}
```

### 4. Status do Usuário

**Comportamento Importante**: O usuário é criado **suspenso e não aprovado**:
- ✅ O usuário pode fazer login em `http://localhost/login`
- ❌ O usuário **não pode** acessar os painéis `admin` ou `user`
- 🔔 Uma notificação de **perigo** é exibida informando que precisa de aprovação
- 📧 Um email é enviado para o administrador

## Sistema de Eventos

### Eventos Disparados

#### 1. `UserRegistered` (Registro)
**Arquivo:** `app/Events/UserRegistered.php`

```php
<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public User $user,
        public ?string $password = null
    ) {}
}
```

**Listeners:**
- `NotifyAdminNewUser` - Envia email para administrador

#### 2. `UserApproved` (Aprovação)
**Arquivo:** `app/Events/UserApproved.php`

```php
<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public User $user
    ) {}
}
```

**Listeners:**
- `SendUserApprovedEmail` - Envia email para usuário aprovado

### Registro de Listeners

**Arquivo:** `app/Providers/AppServiceProvider.php`

```php
private function configEvents(): void
{
    // Registrar listeners manualmente para evitar duplicação
    $this->app['events']->listen(UserRegistered::class, NotifyAdminNewUser::class);
    $this->app['events']->listen(UserApproved::class, SendUserApprovedEmail::class);
}
```

### Listeners Implementados

#### 1. `NotifyAdminNewUser`
**Arquivo:** `app/Listeners/NotifyAdminNewUser.php`

```php
<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Mail\NewUserNotificationMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class NotifyAdminNewUser
{
    public function handle(UserRegistered $event): void
    {
        // Buscar apenas o admin específico
        $admin = User::where('email', 'admin@labsis.dev.br')->first();

        if ($admin) {
            Mail::to($admin->email)->send(new NewUserNotificationMail($admin, $event->user));
        }
    }
}
```

**Funcionalidade:**
- Busca o administrador específico (admin@labsis.dev.br)
- Envia notificação com dados do novo usuário
- Inclui link para visualizar o usuário

#### 2. `SendUserApprovedEmail`
**Arquivo:** `app/Listeners/SendUserApprovedEmail.php`

```php
<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\UserApproved;
use App\Mail\UserApprovedMail;
use Illuminate\Support\Facades\Mail;

class SendUserApprovedEmail
{
    public function handle(UserApproved $event): void
    {
        Mail::to($event->user->email)->send(new UserApprovedMail($event->user));
    }
}
```

**Funcionalidade:**
- Envia email de aprovação para o usuário
- Inclui credenciais de acesso
- Inclui link para login

## Templates de Email

### 1. Email para Administrador
**Arquivo:** `resources/views/emails/admin/new-user.blade.php`

**Conteúdo:**
- Dados do novo usuário
- Data de cadastro
- Status de verificação de email
- Link para visualizar o usuário

### 2. Email de Aprovação
**Arquivo:** `resources/views/emails/user-approved.blade.php`

**Conteúdo:**
- Saudação personalizada
- Informação de aprovação
- Credenciais de acesso
- Link para login

## Configuração de Email

### Desenvolvimento (Mailpit)

**Arquivo:** `docker-compose.yml`

```yaml
mailpit:
    image: 'axllent/mailpit:latest'
    ports:
        - '${FORWARD_MAILPIT_PORT:-1025}:1025'    # SMTP
        - '${FORWARD_MAILPIT_DASHBOARD_PORT:-8025}:8025'  # Web UI
    networks:
        - sail
```

**Acesso:**
- **SMTP:** `localhost:1025`
- **Web UI:** `http://localhost:8025`

### Configuração do Laravel

**Arquivo:** `.env`

```env
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

## Aprovação de Usuários

### Toggle de Aprovação

**Arquivo:** `app/Filament/Resources/Users/Tables/UsersTable.php`

```php
private static function getApprovalColumn()
{
    return ToggleColumn::make('is_approved')
        ->onColor('primary')
        ->offColor('danger')
        ->onIcon('heroicon-c-check')
        ->offIcon('heroicon-c-x-mark')
        ->label('Aprovar')
        ->afterStateUpdated(function (User $record, $state) {
            // Se o usuário foi aprovado
            if ($state) {
                // Remover suspensão
                $record->is_suspended = false;

                // Se o email não está verificado, verificar automaticamente
                if (! $record->hasVerifiedEmail()) {
                    $record->markEmailAsVerified();
                }

                $record->save();

                // Disparar evento de aprovação
                event(new \App\Events\UserApproved($record));
            }
        });
}
```

### Processo de Aprovação

1. **Admin acessa** a lista de usuários
2. **Ativa o toggle** de aprovação
3. **Sistema remove** a suspensão
4. **Verifica email** automaticamente
5. **Dispara evento** `UserApproved`
6. **Email é enviado** para o usuário


## Troubleshooting

### Problemas Comuns

1. **Emails duplicados**
   - Verificar se há listeners duplicados registrados
   - Limpar cache: `php artisan config:clear && php artisan event:clear`

2. **Emails não enviados**
   - Verificar configuração do Mailpit
   - Verificar se eventos estão registrados
   - Verificar logs do Laravel

3. **Usuário não aprovado**
   - Verificar se o toggle está funcionando
   - Verificar se o evento está sendo disparado


## Conclusão

O sistema implementa um fluxo simplificado e eficiente de registro de usuários com:

- ✅ **Registro simplificado** com validações adequadas
- ✅ **Criação automática** de tenants
- ✅ **Atribuição automática** de roles
- ✅ **Sistema de aprovação** via toggle
- ✅ **Emails de notificação** para admin e usuário
- ✅ **Sistema de eventos** desacoplado
- ✅ **Configuração de desenvolvimento** com Mailpit

O sistema está pronto para uso e pode ser facilmente estendido com novas funcionalidades conforme necessário.