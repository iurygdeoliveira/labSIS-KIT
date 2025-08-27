# Login Unificado (Filament + Laravel)

## 📋 Índice

- [Introdução](#introdução)
- [Arquitetura Geral](#arquitetura-geral)
- [Providers do Filament](#providers-do-filament)
  - [BasePanelProvider](#basepanelprovider)
  - [AuthPanelProvider](#authpanelprovider)
  - [AdminPanelProvider](#adminpanelprovider)
  - [UserPanelProvider](#userpanelprovider)
- [Middlewares](#middlewares)
  - [RedirectGuestsToCentralLoginMiddleware](#redirectgueststocentralloginmiddleware)
  - [RedirectToProperPanelMiddleware](#redirecttoproperpanelmiddleware)
- [Página de Login Customizada](#página-de-login-customizada)
- [Redirecionamento pós-login](#redirecionamento-pós-login)
- [Autorização de Acesso aos Painéis (canAccessPanel)](#autorização-de-acesso-aos-painéis-canaccesspanel)
- [Registro dos Providers](#registro-dos-providers)
- [Fluxo de Funcionamento](#fluxo-de-funcionamento)
- [Testando](#testando)
- [Problemas Comuns](#problemas-comuns)
- [Conclusão](#conclusão)

## Introdução

Este documento explica a implementação do “Login Unificado” da aplicação. Todo o fluxo de autenticação foi centralizado em um painel dedicado (Auth), enquanto os painéis de aplicação (Admin e User) recebem o usuário já autenticado e autorizado.

## Arquitetura Geral

- O login, o registro e a recuperação de senha acontecem no painel `auth`.
- As configurações compartilhadas de todos os painéis foram consolidadas em um `BasePanelProvider`.
- Os painéis `admin` e `user` herdam do `BasePanelProvider` e mantêm apenas suas particularidades.
- Middlewares controlam o acesso de convidados e o redirecionamento de usuários autenticados para o painel adequado.

## Providers do Filament

### BasePanelProvider  
Arquivo: `app/Providers/Filament/BasePanelProvider.php`

Centraliza as configurações comuns:
- Aparência e UI: `colors`, `viteTheme`, `sidebarWidth`, `maxContentWidth`, `darkMode`, `defaultThemeMode`.
- MFA (2FA) via `AppAuthentication::make()->recoverable()`.
- Middlewares essenciais (cookies, sessão, CSRF, bindings, hooks do Filament) e dois middlewares da aplicação (ver seção Middlewares).
- Plugins compartilhados: `BriskTheme` e `FilamentEditProfilePlugin` (avatar, e-mail, 2FA, etc.).
- Força cada painel filho a definir `getPanelId()` e `getPanelPath()`.

Trecho exemplificativo:
```php
return $panel
    ->id($this->getPanelId())
    ->path($this->getPanelPath())
    ->spa()
    ->databaseTransactions()
    ->darkMode(false)
    ->defaultThemeMode(ThemeMode::Light)
    ->multiFactorAuthentication(AppAuthentication::make()->recoverable())
    ->colors([
        'primary' => '#014029',
        // ... demais cores
    ])
    ->viteTheme('resources/css/filament/admin/theme.css')
    ->sidebarWidth('15rem')
    ->maxContentWidth(Width::Full)
    ->middleware([
        // middlewares comuns + de acesso/redirect
    ])
    ->authMiddleware([
        Authenticate::class,
    ]);
```

### AuthPanelProvider  
Arquivo: `app/Providers/Filament/AuthPanelProvider.php`

- Painel público para autenticação de usuário (login unificado), registro, reset e verificações de e-mail.
- Usa explicitamente a página de login customizada para tratar contas suspensas.

Trecho:
```php
return $panel
    ->id('auth')
    ->path('')
    ->viteTheme('resources/css/filament/admin/theme.css')
    ->authGuard('web')
    ->login(\App\Filament\Pages\Auth\Login::class)
    ->registration()
    ->passwordReset()
    ->emailVerification()
    ->emailChangeVerification();
```

### AdminPanelProvider  
Arquivo: `app/Providers/Filament/AdminPanelProvider.php`

- Painel do usuário administrador.
- Herda as configs do `BasePanelProvider` e descobre resources/pages/widgets do admin.

Trecho:
```php
$panel = parent::panel($panel)
    ->default()
    ->bootUsing(fn () => FilamentComponentsConfigurator::configure())
    ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
    ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
    ->pages([
        Dashboard::class,
    ])
    ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
    ->widgets([
        AccountWidget::class,
        FilamentInfoWidget::class,
        DependencyWidget::class,
    ]);
```

### UserPanelProvider  
Arquivo: `app/Providers/Filament/UserPanelProvider.php`

- Painel do usuário comum.
- Herda as configs do `BasePanelProvider` e descobre resources/pages/widgets do namespace `User`.

Trecho:
```php
$panel = parent::panel($panel)
    ->discoverResources(in: app_path('Filament/User/Resources'), for: 'App\\Filament\\User\\Resources')
    ->discoverPages(in: app_path('Filament/User/Pages'), for: 'App\\Filament\\User\\Pages')
    ->pages([
        Dashboard::class,
    ])
    ->discoverWidgets(in: app_path('Filament/User/Widgets'), for: 'App\\Filament\\User\\Widgets')
    ->widgets([
        AccountWidget::class,
        FilamentInfoWidget::class,
    ]);
```

## Middlewares

### RedirectGuestsToCentralLoginMiddleware  
Arquivo: `app/Http/Middleware/RedirectGuestsToCentralLoginMiddleware.php`

- Se autenticado: permite acesso.
- Se rota pública (login, register, password-reset, email verify/change): permite.
- Caso contrário: redireciona convidados para `/login`.

Trecho:
```php
if (Filament::auth()->check()) {
    return $next($request);
}

$path = $request->path();
$isPublicAuthRoute = $path === 'login'
    || $path === 'register'
    || str_starts_with($path, 'password-reset')
    || str_starts_with($path, 'email/verify')
    || str_starts_with($path, 'email/change');

if ($isPublicAuthRoute) {
    return $next($request);
}

return redirect()->to('/login');
```

### RedirectToProperPanelMiddleware  
Arquivo: `app/Http/Middleware/RedirectToProperPanelMiddleware.php`

- Se autenticado e no painel `auth`: redireciona para `/admin` ou `/user` conforme permissão.
- Se não pode acessar o painel atual: calcula o painel correto e redireciona.

Trecho:
```php
$panel = Filament::getCurrentPanel();

if ($panel && $panel->getId() === 'auth') {
    if ($user->canAccessPanel(Filament::getPanel('admin'))) {
        return redirect()->to('/admin');
    }
    if ($user->canAccessPanel(Filament::getPanel('user'))) {
        return redirect()->to('/user');
    }
}

if (! $user->canAccessPanel($panel)) {
    if ($user->canAccessPanel(Filament::getPanel('admin'))) {
        return redirect()->to('/admin');
    }
    if ($user->canAccessPanel(Filament::getPanel('user'))) {
        return redirect()->to('/user');
    }
}
```

## Página de Login Customizada  
Arquivo: `app/Filament/Pages/Auth/Login.php`

- Mantém o comportamento padrão do Filament.
- Apenas impede login de usuários suspensos, exibindo uma notificação amigável.

Trecho:
```php
if ($user instanceof User && $user->isSuspended()) {
    $this->notifyDanger('Conta suspensa', 'Sua conta está suspensa. Entre em contato com o suporte para mais informações.');
    return null;
}
```

## Redirecionamento pós-login  
Arquivo: `app/Http/Responses/LoginResponse.php`

- Após autenticar, redireciona para `/admin` ou `/user` conforme o papel (role). Caso não haja papel conhecido, redireciona para `route('home')`.

## Autorização de Acesso aos Painéis (canAccessPanel)  
Arquivo: `app/Models/User.php`

- Permite acesso ao painel `auth` (viabiliza o login unificado).
- Bloqueia usuários suspensos.
- Exige e-mail verificado nos painéis de aplicação.
- Autoriza `admin`/`user` conforme os respectivos roles.

Trecho:
```php
if ($panel->getId() === 'auth') {
    return true;
}

if ($this->isSuspended()) {
    return false;
}

if (! $this->hasVerifiedEmail()) {
    return false;
}
```

## Registro dos Providers  
Arquivo: `bootstrap/providers.php`

Os três providers do Filament são registrados aqui:

```php
return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AuthPanelProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\Filament\UserPanelProvider::class,
];
```

## Fluxo de Funcionamento

1. Visitante acessa `/login` → painel `auth` exibe o formulário de autenticação.
2. Tentativa de login:
   - Usuário suspenso: notificação “Conta suspensa” e login bloqueado.
   - Usuário válido: autentica normalmente.
3. `LoginResponse` redireciona para `/admin` ou `/user` conforme o papel.
4. Convidado tentando `/admin` ou `/user`: redirecionado para `/login` pelo `RedirectGuestsToCentralLoginMiddleware`.
5. Usuário autenticado tentando `/login`: redirecionado ao painel correto pelo `RedirectToProperPanelMiddleware`.


## Conclusão

O “Login Unificado” centraliza a autenticação, reduz complexidade nos demais painéis e melhora a experiência do usuário, pois não é necessário acessar diferentes URLs para acessar os painéis de admin e user. A separação de responsabilidades entre Providers, Middlewares e o `canAccessPanel()` torna o sistema coeso, previsível e fácil de manter.
