# 🔐 Testes de Autenticação

Este documento descreve a **cobertura atual** de testes relacionados a autenticação e cadastro no projeto.

> **Estado atual:** não existe `tests/Feature/AuthenticationTest.php`. Fluxos de login Livewire, registro completo e recuperação de senha **ainda não possuem suite dedicada** — parte do comportamento pós-login é coberta indiretamente por [`PanelAccessTest.php`](../../tests/Feature/PanelAccessTest.php).

## O que está coberto hoje

### Redirecionamentos com usuário autenticado (`PanelAccessTest`)

| Cenário | Arquivo | Descrição |
|---------|---------|-----------|
| Admin acessa `/login` | `PanelAccessTest.php` | Redireciona para `/admin` |
| User com team acessa `/login` | `PanelAccessTest.php` | Redireciona para `/user/{slug}` do primeiro team |
| Visitante acessa painéis | `PanelAccessTest.php` | Redireciona para `/__compat-login` (compat → `/login`) |

Esses testes validam o fluxo **após** autenticação via `actingAs()`, não o formulário Livewire de login em si.

### Cache e side-effects de aprovação (`FilamentStatsCacheTest`)

| Cenário | Descrição |
|---------|-----------|
| Aprovação de usuário | Atualiza badges em `FilamentStatsCache` via `UserObserver` |

Relacionado indiretamente ao fluxo de aprovação pós-registro — ver [Fluxo de Registro](../02-autenticacao-e-seguranca/fluxo-de-registro-de-novos-usuarios.md).

## Lacunas conhecidas (não automatizadas)

Os cenários abaixo estão documentados em [Login Unificado](../02-autenticacao-e-seguranca/login-unificado.md) e [Fluxo de Registro](../02-autenticacao-e-seguranca/fluxo-de-registro-de-novos-usuarios.md), mas **não têm teste Feature dedicado**:

- Renderização e submit do formulário Livewire de **login** (`App\Filament\Pages\Auth\Login`)
- Renderização e submit do formulário de **registro** (criação de `User`, `Team`, `Membership`)
- Credenciais inválidas no login
- Fluxo de **recuperação de senha** (request + reset)
- Usuário suspenso / não aprovado bloqueado nos painéis

## Executando testes relacionados

```bash
# Redirecionamentos pós-auth (parcial)
./vendor/bin/sail artisan test tests/Feature/PanelAccessTest.php

# Invalidação de cache ao aprovar usuário
./vendor/bin/sail artisan test tests/Feature/FilamentStatsCacheTest.php

# Filtrar por cenário de login
./vendor/bin/sail artisan test --filter="redirecionado para o painel"
```

## Sugestão para nova suite `AuthenticationTest`

Quando implementar testes de auth completos, prefira **Livewire Testing** contra as pages do `AuthPanelProvider`:

```php
use App\Filament\Pages\Auth\Login;
use App\Models\User;
use Livewire\Livewire;

it('usuário aprovado autentica via Livewire', function (): void {
    $user = User::factory()->create([
        'is_approved' => true,
        'is_suspended' => false,
    ]);

    Livewire::test(Login::class)
        ->fillForm([
            'email' => $user->email,
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertHasNoFormErrors();
});
```

## Referências

- [PanelAccessTest.php](../../tests/Feature/PanelAccessTest.php)
- [FilamentStatsCacheTest.php](../../tests/Feature/FilamentStatsCacheTest.php)
- [03 - Controle de Acesso](./03-acesso-paineis.md)
- [Fluxo de Registro](../02-autenticacao-e-seguranca/fluxo-de-registro-de-novos-usuarios.md)
- [Login Unificado](../02-autenticacao-e-seguranca/login-unificado.md)
