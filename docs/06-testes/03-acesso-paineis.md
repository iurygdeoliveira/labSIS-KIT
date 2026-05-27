# 🛡️ Testes de Controle de Acesso e Redirecionamento

Este documento detalha os testes de integração responsáveis por verificar as regras de acesso aos diferentes painéis do sistema (Admin, User) e a lógica de redirecionamento inteligente.

Arquivo: [`tests/Feature/PanelAccessTest.php`](../../tests/Feature/PanelAccessTest.php)

## Estrutura e Dados

Estes testes utilizam o **`DatabaseSeeder`** completo (`beforeEach`) para garantir um ambiente consistente com roles e permissions reais configuradas pelo `UserSeeder` — incluindo usuários seed (`admin@labsis.dev.br`, `beltrano@labsis.dev.br`, `sicrano@labsis.dev.br`) e teams (`Team A`, etc.).

## Cenários Cobertos

### 1. Visitantes (Guests)

- **Redirecionamento**: Usuários não autenticados que tentam acessar `/admin` ou `/user` são redirecionados para `/__compat-login` (rota de compatibilidade que encaminha para `/login`).

### 2. Administradores (Admin)

- **Acesso ao painel**: Usuário `admin@labsis.dev.br` acessa `/admin` com sucesso (200).
- **Listagem de usuários**: Admin acessa `/admin/users` no Filament.
- **Redirecionamento inteligente**: Admin autenticado em `/login` é redirecionado para `/admin`.

### 3. Usuários Comuns (User)

- **Bloqueio ao painel Admin**: `beltrano@labsis.dev.br` recebe **403 Forbidden** ao acessar `/admin`.
- **Acesso ao painel do team**: Usuário acessa `/user/{slug}` do team (`Team A` → slug do seed).
- **Redirecionamento inteligente**: `sicrano@labsis.dev.br` autenticado em `/login` é redirecionado para `/user/{slug}` do primeiro team retornado por `getTenants()`.

> **Terminologia Filament:** a API usa `getTenants()` e `getTenant()`, mas o model subjacente é sempre `App\Models\Team` com identificador de rota **`slug`** (não UUID).

## Middleware Testado

Estes testes validam indiretamente o funcionamento de:

- [`RedirectToProperPanelMiddleware`](../../app/Http/Middleware/RedirectToProperPanelMiddleware.php) — redirecionamentos por painel e status
- [`User::canAccessPanel()`](../../app/Models/User.php) — autorização final nos painéis Filament

## Executando estes testes

```bash
./vendor/bin/sail artisan test tests/Feature/PanelAccessTest.php
```

## Referências

- [Test: PanelAccessTest.php](../../tests/Feature/PanelAccessTest.php)
- [Middleware: RedirectToProperPanelMiddleware](../../app/Http/Middleware/RedirectToProperPanelMiddleware.php)
- [Seeder: DatabaseSeeder](../../database/seeders/DatabaseSeeder.php)
- [Tenancy e Teams](../02-autenticacao-e-seguranca/tenancy-e-teams.md)
