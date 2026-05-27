# Sistema de Login Unificado (Filament + Laravel)

## 📋 Índice

- [Introdução](#introdução)
- [Arquitetura Geral](#arquitetura-geral)
- [Sistema de Aprovação de Usuários](#sistema-de-aprovação-de-usuários)
- [Providers do Filament](#providers-do-filament)
    - [BasePanelProvider](#basepanelprovider)
    - [AuthPanelProvider](#authpanelprovider)
    - [AdminPanelProvider](#adminpanelprovider)
    - [UserPanelProvider](#userpanelprovider)
- [Middlewares](#middlewares)
    - [RedirectToProperPanelMiddleware](#redirecttoproperpanelmiddleware)
    - [TeamSyncMiddleware](#teamsyncmiddleware)
- [Páginas de Autenticação](#páginas-de-autenticação)
    - [Login Customizado](#login-customizado)
    - [Registro com Tenant](#registro-com-tenant)
    - [VerificationPending](#verificationpending)
    - [AccountSuspended](#accountsuspended)
- [Sistema Multi-Tenant](#sistema-multi-tenant)
- [Controle de Acesso aos Painéis](#controle-de-acesso-aos-painéis)
- [Fluxo de Funcionamento](#fluxo-de-funcionamento)
- [Problemas Comuns](#problemas-comuns)
- [Conclusão](#conclusão)

## Introdução

Este documento explica a implementação do **Sistema de Login Unificado** da aplicação, que centraliza toda a autenticação em um painel dedicado (Auth) e implementa uma metodologia de aprovação de usuários

O sistema foi projetado para funcionar em ambiente **multi-tenant**, onde cada organização possui seus próprios usuários e permissões isoladas, com um fluxo de aprovação que garante que apenas usuários autorizados tenham acesso aos painéis de aplicação.

## Arquitetura Geral

O sistema de login unificado implementa uma arquitetura em camadas que separa claramente as responsabilidades:

- **Painel Auth**: Centraliza login, registro, recuperação de senha e páginas de status
- **Sistema de Aprovação**: Usuários são criados suspensos e precisam ser aprovados por administradores
- **Multi-Tenancy**: Cada usuário pode pertencer a múltiplos tenants com diferentes roles
- **Controle de Acesso**: Baseado em roles (Admin, Owner, User) e status de aprovação
- **Redirecionamento por role**: Usuários são direcionados automaticamente para o painel correto

### Componentes Principais

- **Providers**: `BasePanelProvider`, `AuthPanelProvider`, `AdminPanelProvider`, `UserPanelProvider`
- **Middlewares**: Controle de acesso, redirecionamento e sincronização de tenants
- **Páginas de Status**: `VerificationPending` e `AccountSuspended`

## Sistema de Aprovação de Usuários

O sistema implementa um fluxo de aprovação em duas etapas para garantir segurança e controle:

### 1. Registro de Usuário

Quando um usuário se registra:

- **Usuário é criado suspenso** (`is_suspended = true`)
- **Usuário não é aprovado** (`is_approved = false`)
- **Team é criado automaticamente** no registro; o usuário recebe role Owner via `MembershipObserver`
- **Associação é estabelecida** entre usuário e tenant
- **Evento é disparado** para notificar administradores

### 2. Processo de Aprovação

**Administradores podem**:

- Visualizar usuários pendentes de aprovação
- Aprovar ou rejeitar usuários
- Suspender usuários já aprovados
- Gerenciar roles e permissões por tenant

**Usuários aprovados podem**:

- Acessar os painéis de aplicação
- Ser redirecionados automaticamente para o tenant correto
- Ter suas permissões sincronizadas por tenant

### 3. Estados do Usuário

- **Suspenso**: Usuário não pode fazer login mas é redirecionado para página de suspensão
- **Não Aprovado**: Usuário pode fazer login mas é redirecionado para página de verificação pendente
- **Aprovado**: Usuário tem acesso completo aos painéis conforme suas permissões

## Providers do Filament

### BasePanelProvider

Arquivo: `app/Providers/Filament/BasePanelProvider.php`

Centraliza as configurações comuns de todos os painéis:

**Configurações de UI**:

- Tema: `viteTheme`, `darkMode`, `defaultThemeMode`
- Layout: `sidebarWidth`, `maxContentWidth`, `spa()`
- Cores: Paleta personalizada com cor primária `#014029`

**Segurança e Autenticação**:

- MFA (2FA) via `AppAuthentication::make()->recoverable()`
- Middlewares essenciais: cookies, sessão, CSRF, bindings
- Middlewares customizados: `RedirectToProperPanelMiddleware`, `TeamSyncMiddleware`
- Autenticação de guests gerenciada automaticamente pelo Filament

**Plugins Compartilhados**:

- `BriskTheme`: Tema visual personalizado
- `FilamentEditProfilePlugin`: Edição de perfil com avatar, e-mail e 2FA
- `EasyFooterPlugin`: Rodapé com links e informações

**Métodos Abstratos**:

- `getPanelId()`: Define o ID único do painel
- `getPanelPath()`: Define o caminho da URL do painel

### AuthPanelProvider

Arquivo: `app/Providers/Filament/AuthPanelProvider.php`

Painel público para autenticação centralizada:

**Funcionalidades**:

- Login unificado com página customizada
- Registro de usuários com criação automática de tenant
- Recuperação de senha
- Páginas de status: `VerificationPending` e `AccountSuspended`

**Configuração**:

- ID: `auth`
- Path: `/` (raiz)
- Guard: `web`
- Login customizado: `App\Filament\Pages\Auth\Login`
- Registro customizado: `App\Filament\Pages\Auth\Register`

### AdminPanelProvider

Arquivo: `app/Providers/Filament/AdminPanelProvider.php`

Painel administrativo global:

**Características**:

- Painel padrão (`->default()`)
- Sem tenancy (`->tenant(null, false)`)
- Descoberta automática de resources, pages, clusters e widgets
- Configuração de componentes via `FilamentComponentsConfigurator`

**Recursos**:

- Dashboard administrativo
- Widgets: `AccountWidget`, `FilamentInfoWidget`, `SystemStats`
- Acesso apenas para usuários com role `Admin`

### UserPanelProvider

Arquivo: `app/Providers/Filament/UserPanelProvider.php`

Painel de usuários com suporte a multi-tenancy:

**Multi-Tenancy** (vocabulário Filament; model = `Team`):

- Tenant: `Team::class` com slug em `slug` (`slugAttribute: 'slug'`)
- Menu de tenant habilitado (`->tenantMenu(true)`)
- Relacionamento de propriedade: `teams`
- Registro/perfil de team via FilaTeams (`CreateTeamPage`, `EditTeam`)

**Recursos**:

- Resources específicos: `UserResource`, `MediaResource`
- Descoberta de clusters para gerenciamento de permissões
- Middleware `TeamSyncMiddleware` para sincronização de permissões

**Controle de Acesso**:

- Usuários com role `User` em qualquer tenant
- Usuários com role `Owner` em qualquer tenant
- Usuários vinculados a tenants ativos

## Middlewares

### RedirectToProperPanelMiddleware

Arquivo: `app/Http/Middleware/RedirectToProperPanelMiddleware.php`

Gerencia redirecionamentos inteligentes baseados em status e roles:

**Verificações de Status**:

- **Usuários não aprovados**: Redirecionados para `VerificationPending` (exceto Admin)
- **Usuários suspensos**: Redirecionados para `AccountSuspended` (exceto Admin)
- **Rotas de logout**: Sempre permitidas

**Redirecionamento por Painel**:

- **Painel Auth**: Redireciona para painel apropriado baseado em roles
- **Painel Admin**: Apenas usuários com role `Admin`
- **Painel User**: Usuários com roles `User` ou `Owner`, ou seja, vinculados a tenants

**Lógica de Redirecionamento**:

```php
// Se no painel auth, redireciona para painel correto
if ($panel->getId() === 'auth') {
    if ($user->canAccessPanel(Filament::getPanel('admin'))) {
        return redirect()->to('/admin');
    }
    if ($user->canAccessPanel(Filament::getPanel('user'))) {
        $firstTeam = $user->getTenants(Filament::getPanel('user'))->first();
        return redirect()->to('/user/'.$firstTeam->slug);
    }
}
```

### TeamSyncMiddleware

Arquivo: `app/Http/Middleware/TeamSyncMiddleware.php`

Sincroniza permissões com o tenant atual:

**Funcionamento**:

- Apenas ativo no painel `user`
- Sincroniza `SpatieTeamResolver` com o tenant da rota
- Fallback para primeiro tenant ativo do usuário
- Define `team_id` como `0` quando não há tenant

**Processo de Sincronização**:

1. Extrai UUID do tenant da rota
2. Verifica se usuário pode acessar o tenant
3. Configura `SpatieTeamResolver` com o ID do tenant
4. Se não há tenant na rota, usa primeiro tenant ativo
5. Se não há tenant ativo, define `team_id = 0`

## Páginas de Autenticação

### Login Customizado

Arquivo: `app/Filament/Pages/Auth/Login.php`

Implementa verificações de status antes da autenticação:

**Verificações Implementadas**:

- **Usuário suspenso**: Login permitido, redireciona para `AccountSuspended`
- **Usuário não aprovado**: Login permitido, redireciona para `VerificationPending`
- **Usuário válido**: Continua com autenticação padrão do Filament

**Fluxo de Autenticação**:

```php
// Verifica se usuário existe
$user = $authGuard->getProvider()->retrieveByCredentials($credentials);

// Bloqueia usuários suspensos
if ($user->isSuspended()) {
    $authGuard->login($user);
    $this->redirect(route('filament.auth.account-suspended'));
    return null;
}

// Redireciona usuários não aprovados
if (!$user->isApproved()) {
    $authGuard->login($user);
    $this->redirect(route('filament.auth.verification-pending'));
    return null;
}
```

### Registro com Tenant

Arquivo: `app/Filament/Pages/Auth/Register.php`

Cria usuário e tenant automaticamente:

**Campos do Formulário**:

- Nome completo
- E-mail (único)
- Senha (mínimo 8 caracteres, confirmada)
- Nome do Tenant (único)

**Processo de Registro**:

1. **Cria usuário** com status suspenso e não aprovado
2. **Cria tenant** com nome fornecido
3. **Associa usuário ao tenant** via tabela pivot
4. **Dispara evento** `UserRegistered` para notificar administradores
5. **Exibe notificação** de sucesso

**Configurações de Usuário**:

- `is_suspended = true`: Usuário suspenso até aprovação
- `is_approved = false`: Usuário não aprovado por padrão
- `email_verified_at = null`: E-mail não verificado

### VerificationPending

Arquivo: `app/Filament/Pages/Auth/VerificationPending.php`

Página exibida para usuários não aprovados:

**Características**:

- Página simples (`SimplePage`)
- Não aparece na navegação (`$shouldRegisterNavigation = false`)
- View: `filament.pages.auth.verification-pending`
- Suporte a tenancy para URLs corretas

**Funcionalidade**:

- Informa ao usuário que sua conta está pendente de aprovação
- Permite logout para tentar novamente mais tarde
- Bloqueia acesso a outros painéis até aprovação

### AccountSuspended

Arquivo: `app/Filament/Pages/Auth/AccountSuspended.php`

Página exibida para usuários suspensos:

**Características**:

- Página simples (`SimplePage`)
- Não aparece na navegação (`$shouldRegisterNavigation = false`)
- View: `filament.pages.auth.account-suspended`
- Suporte a tenancy para URLs corretas

**Funcionalidade**:

- Informa ao usuário que sua conta está suspensa
- Orienta contato com suporte
- Bloqueia completamente o acesso aos painéis

## Controle de Acesso aos Painéis

### Método canAccessPanel

Arquivo: `app/Models/User.php`

Implementa lógica de controle de acesso baseada em status e roles:

**Regras de Acesso**:

1. **Painel Auth**: Sempre permitido (viabiliza login unificado)
2. **Usuários Suspensos**: Bloqueados em todos os painéis
3. **E-mail Não Verificado**: Bloqueados em painéis de aplicação
4. **Painel Admin**: Apenas usuários com role `Admin`
5. **Painel User**: Usuários com roles `User` ou `Owner`, ou vinculados a tenants

**Implementação**:

```php
public function canAccessPanel(Panel $panel): bool
{
    // Painel auth sempre permitido
    if ($panel->getId() === 'auth') {
        return true;
    }

    // Usuários suspensos bloqueados
    if ($this->isSuspended()) {
        return false;
    }

    // E-mail deve ser verificado
    if (!$this->hasVerifiedEmail()) {
        return false;
    }

    // Painel admin: apenas Admin
    if ($panel->getId() === 'admin') {
        return $this->hasRole(RoleType::ADMIN->value);
    }

    // Painel user: User, Owner ou vinculado a tenants
    if ($panel->getId() === 'user') {
        return $this->hasRole(RoleType::USER->value) ||
               $this->hasOwnerRoleInAnyTeam() ||
               $this->teams()->exists();
    }

    return false;
}
```

### Métodos de Verificação de Roles

**Verificação por Team** (tenant Filament):

- `isOwnerOfTeam(Team $team)`: Verifica se é Owner do team
- `isUserOfTeam(Team $team)`: Verifica se é User do team
- `hasAnyRoleInTeam(Team $team)`: Verifica se tem qualquer role no team

**Verificação Global**:

- `hasOwnerRoleInAnyTeam()`: Verifica se é Owner em algum team
- `getRolesForTeam(Team $team)`: Retorna todas as roles do usuário no team

## Fluxo de Funcionamento

### 1. Registro de Usuário

1. **Usuário acessa `/register`**
2. **Preenche formulário** com dados pessoais e nome do tenant
3. **Sistema cria usuário suspenso** (`is_suspended = true`, `is_approved = false`)
4. **Sistema cria team** (`App\Models\Team`) com nome fornecido
5. **Sistema associa usuário ao team** via `Membership` (role `owner`); `MembershipObserver` sincroniza role Spatie
6. **Evento `UserRegistered` é disparado** para notificar administradores
7. **Usuário é redirecionado** para página de sucesso

### 2. Processo de Login

1. **Usuário acessa `/login`**
2. **Preenche credenciais** (e-mail e senha)
3. **Sistema verifica se usuário existe**
4. **Se usuário suspenso**: Login bloqueado, redireciona para `AccountSuspended`
5. **Se usuário não aprovado**: Login permitido, redireciona para `VerificationPending`
6. **Se usuário válido**: Continua com autenticação padrão

### 3. Redirecionamento Pós-Login

1. **`LoginResponse` processa** usuário autenticado
2. **`RedirectToProperPanelMiddleware` verifica** status e roles
3. **Se Admin**: Redireciona para `/admin`
4. **Se User/Owner**: Redireciona para `/user/{team-slug}`
5. **Se não aprovado**: Redireciona para `VerificationPending`

### 4. Acesso aos Painéis

1. **Filament** gerencia automaticamente o redirecionamento de usuários não autenticados
2. **`RedirectToProperPanelMiddleware`** verifica permissões e redireciona para painéis apropriados
3. **`TeamSyncMiddleware`** sincroniza permissões por tenant (apenas painel User)
4. **`canAccessPanel()`** valida acesso final baseado em status e roles

### 5. Processo de Aprovação

1. **Administrador visualiza** usuários pendentes
2. **Administrador aprova** usuário (`is_approved = true`)
3. **Administrador pode suspender** usuário (`is_suspended = true`)
4. **Usuário aprovado** pode acessar painéis conforme permissões

## Conclusão

O **Sistema de Login Unificado** implementado oferece uma solução robusta para autenticação e controle de acesso em ambiente multi-tenant. A integração entre Filament, sistema de roles/permissões e multi-tenancy garante:

- **Segurança**: Sistema de aprovação em duas etapas
- **Flexibilidade**: Suporte completo a multi-tenancy
- **Usabilidade**: Redirecionamento inteligente baseado em roles
- **Manutenibilidade**: Código organizado e bem documentado
- **Escalabilidade**: Arquitetura preparada para crescimento

O sistema centraliza toda a autenticação no painel `auth`, implementa verificações de status rigorosas e garante que usuários sejam direcionados automaticamente para o painel correto baseado em suas permissões e contexto de tenant. A separação clara de responsabilidades entre Providers, Middlewares e métodos de controle de acesso torna o sistema previsível, seguro e fácil de manter.

## Referências

- [Provider: AuthPanelProvider](/app/Providers/Filament/AuthPanelProvider.php)
- [Provider: AdminPanelProvider](/app/Providers/Filament/AdminPanelProvider.php)
- [Middleware: RedirectToProperPanelMiddleware](/app/Http/Middleware/RedirectToProperPanelMiddleware.php)
- [Page: Login](/app/Filament/Pages/Auth/Login.php)
