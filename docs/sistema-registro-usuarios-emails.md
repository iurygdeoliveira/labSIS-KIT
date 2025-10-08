# Sistema de Registro de Usuários e Envio de Emails

## Visão Geral

O sistema implementa um fluxo completo de registro de usuários com criação automática de tenants, atribuição de roles e envio de emails de boas-vindas e notificações para administradores. O sistema utiliza eventos e listeners do Laravel para garantir desacoplamento e facilidade de manutenção.

## Arquitetura do Sistema

### Componentes Principais

1. **Página de Registro Personalizada** (`app/Filament/Pages/Auth/Register.php`)
2. **Sistema de Eventos** (Events e Listeners)
3. **Serviço de Email** (`app/Services/EmailService.php`)
4. **Templates de Email** (Blade templates)
5. **Configuração de Email** (Mailpit para desenvolvimento)

## Fluxo de Registro de Usuários

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
// Campos obrigatórios
- Nome completo
- E-mail (único)
- Senha (mínimo 8 caracteres)
- Confirmação de senha
- Nome do Tenant (único)
```

**Validações:**
- Email único na tabela `users`
- Nome do tenant único na tabela `tenants`
- Senha com confirmação
- Todos os campos obrigatórios

### 3. Processo de Criação

Quando o usuário submete o formulário, o método `handleRegistration()` executa:

```php
// 1. Criar usuário (sem email verificado)
$user = User::create($userData); // email_verified_at será null

// 2. Criar tenant
$tenant = Tenant::create($tenantData);

// 3. Associar usuário ao tenant
$user->tenants()->attach($tenant->id);

// 4. Disparar eventos
event(new UserRegistered($user, $data['password'])); // ← Evento customizado
event(new TenantCreated($user, $tenant));        // ← Evento customizado

// 5. Login automático (gerenciado pelo BaseRegister do Filament)
// Filament::auth()->login($user); // ← Executado automaticamente pelo Filament
```

### 4. Verificação de Email

**Comportamento Importante**: O usuário é criado **sem email verificado** (`email_verified_at = null`). Isso significa que:

- ✅ O usuário pode fazer login em `http://localhost/login`
- ❌ O usuário **não pode** acessar os painéis `admin` ou `user`
- 🔔 Uma notificação de **perigo** é exibida informando que o email precisa ser verificado
- 📧 Um email de verificação é enviado automaticamente

### 5. Redirecionamento

Após o registro bem-sucedido, o usuário é redirecionado para:
```
/user/{tenant-uuid}/dashboard
```

## Sistema de Eventos

### Eventos Disparados

#### 1. `UserRegistered` (Customizado)
**Arquivo:** `app/Events/UserRegistered.php`

**Dados:**
- `User $user` - Usuário criado
- `?string $password` - Senha em texto plano (opcional)

**Listeners:**
- `SendWelcomeEmail` - Envia email de boas-vindas
- `SendEmailVerificationNotification` - Envia verificação de email
- `NotifyAdminNewUser` - Notifica administradores

#### 2. `TenantCreated` (Customizado)
**Arquivo:** `app/Events/TenantCreated.php`

**Dados:**
- `User $user` - Usuário proprietário
- `Tenant $tenant` - Tenant criado

**Listeners:**
- `AssociateUserAsOwner` - Atribui role Owner para o tenant

### Comportamento de Login com Email Não Verificado

Quando um usuário tenta fazer login com email não verificado:

1. **Verificação no Login** (`app/Filament/Pages/Auth/Login.php`):
   ```php
   // Verifica se o email não foi verificado
   if ($user instanceof User && !$user->hasVerifiedEmail()) {
       $this->notifyDanger(
           'Email não verificado',
           'Você precisa verificar seu email antes de acessar o painel. Verifique sua caixa de entrada e clique no link de verificação.',
           15,
           true // Notificação persistente
       );
       
       return null; // Bloqueia o login
   }
   ```

2. **Resultado:**
   - ❌ Login é **bloqueado**
   - 🔔 Notificação de **perigo** é exibida
   - 📧 Usuário é orientado a verificar o email

### Listeners Implementados

#### 1. `SendWelcomeEmail`
**Arquivo:** `app/Listeners/SendWelcomeEmail.php`

**Funcionalidade:**
- Envia email de boas-vindas para o novo usuário
- Inclui credenciais de acesso
- Inclui nome do tenant
- Link para login

#### 2. `NotifyAdminNewUser`
**Arquivo:** `app/Listeners/NotifyAdminNewUser.php`

**Funcionalidade:**
- Busca todos os usuários com role 'admin'
- Envia notificação para cada administrador
- Inclui dados do novo usuário
- Link para visualizar o usuário

#### 3. `AssociateUserAsOwner`
**Arquivo:** `app/Listeners/AssociateUserAsOwner.php`

**Funcionalidade:**
- Atribui role 'Owner' para o tenant criado
- Garante que o usuário tenha permissões de proprietário

## Serviço de Email

### EmailService
**Arquivo:** `app/Services/EmailService.php`

**Métodos:**
- `sendWelcomeEmail()` - Email de boas-vindas
- `sendEmailVerification()` - Verificação de email
- `sendNewUserNotification()` - Notificação para admins
- `sendPasswordReset()` - Reset de senha (futuro)

### Templates de Email

#### 1. Email de Boas-vindas
**Arquivo:** `resources/views/emails/welcome.blade.php`

**Conteúdo:**
- Saudação personalizada
- Credenciais de acesso (se fornecidas)
- Link para login
- Instruções de segurança

#### 2. Notificação para Administradores
**Arquivo:** `resources/views/emails/admin/new-user.blade.php`

**Conteúdo:**
- Dados do novo usuário
- Data de cadastro
- Status de verificação de email
- Link para visualizar o usuário

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

**Arquivo:** `config/mail.php`

```php
'mailers' => [
    'smtp' => [
        'transport' => 'smtp',
        'host' => env('MAIL_HOST', 'mailpit'),
        'port' => env('MAIL_PORT', 1025),
        'encryption' => env('MAIL_ENCRYPTION', null),
        'username' => env('MAIL_USERNAME'),
        'password' => env('MAIL_PASSWORD'),
        'timeout' => null,
        'local_domain' => env('MAIL_EHLO_DOMAIN'),
    ],
],
```

## Registro de Eventos

### EventServiceProvider
**Arquivo:** `app/Providers/EventServiceProvider.php`

```php
protected $listen = [
    UserRegistered::class => [
        SendWelcomeEmail::class,
        SendEmailVerificationNotification::class,
        NotifyAdminNewUser::class,
    ],
    TenantCreated::class => [
        AssociateUserAsOwner::class,
    ],
];
```

**Registro:** `bootstrap/providers.php`

## Fluxo Completo de Execução

### 1. Usuário Acessa `/register`
- AuthPanelProvider carrega página personalizada
- Formulário com validações é exibido

### 2. Submissão do Formulário
- Validação dos dados
- Criação do usuário
- Criação do tenant
- Associação usuário-tenant

### 3. Disparo de Eventos
- `UserRegistered` → 3 listeners executam
- `TenantCreated` → 1 listener executa
- Emails são enviados via fila

### 4. Login Automático
- Usuário é autenticado automaticamente
- Redirecionamento para painel do usuário

### 5. Emails Enviados
- **Para o usuário:** Email de boas-vindas
- **Para admins:** Notificação de novo usuário
- **Verificação:** Email de verificação (se necessário)

## Verificação do Sistema

### 1. Teste de Registro
1. Acesse `http://localhost/register`
2. Preencha o formulário
3. Verifique se o usuário foi criado
4. Verifique se o tenant foi criado
5. Verifique se a associação foi feita

### 2. Verificação de Emails
1. Acesse `http://localhost:8025` (Mailpit)
2. Verifique se o email de boas-vindas foi enviado
3. Verifique se a notificação para admin foi enviada

### 3. Verificação de Roles
1. Verifique se o usuário tem role 'Owner' para o tenant
2. Verifique se pode acessar o painel do usuário

### 4. Verificação de Redirecionamento
1. Após registro, deve ser redirecionado para `/user/{uuid}/dashboard`
2. Não deve ser redirecionado para página raiz

## Troubleshooting

### Problemas Comuns

1. **Usuário redirecionado para página raiz**
   - Verificar se email foi verificado
   - Verificar se tem role apropriada
   - Verificar se tem tenant associado

2. **Emails não enviados**
   - Verificar configuração do Mailpit
   - Verificar se eventos estão registrados
   - Verificar logs do Laravel

3. **Role não atribuída**
   - Verificar se listener `AssociateUserAsOwner` está executando
   - Verificar se role 'Owner' existe no sistema

### Logs Importantes

```bash
# Logs do Laravel
tail -f storage/logs/laravel.log

# Logs do Mailpit
docker logs labsis-kit-mailpit-1

# Verificar filas
./vendor/bin/sail artisan queue:work
```

## Configurações de Ambiente

### Variáveis Necessárias

```env
# Email
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@labsis.dev.br"
MAIL_FROM_NAME="${APP_NAME}"

# Mailpit
FORWARD_MAILPIT_PORT=1025
FORWARD_MAILPIT_DASHBOARD_PORT=8025
```

## Melhorias Futuras

1. **Templates de Email Responsivos**
2. **Configuração de SMTP para Produção**
3. **Sistema de Templates de Email Personalizáveis**
4. **Notificações Push**
5. **Sistema de Convites por Email**
6. **Configurações de Tenant Personalizáveis**

## Conclusão

O sistema implementa um fluxo completo e robusto de registro de usuários com:
- ✅ Criação automática de tenants
- ✅ Atribuição automática de roles
- ✅ Envio de emails de boas-vindas
- ✅ Notificações para administradores
- ✅ Redirecionamento correto
- ✅ Sistema de eventos desacoplado
- ✅ Configuração de desenvolvimento com Mailpit

O sistema está pronto para uso e pode ser facilmente estendido com novas funcionalidades conforme necessário.
