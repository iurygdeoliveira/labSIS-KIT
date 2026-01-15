# labSIS-KIT

## Propósito

SaaS starter kit pronto para produção utilizando Laravel 12 e Filament 4, com arquitetura multi-painel, autenticação unificada e gestão de mídia.

## Tech Stack

-   **Backend**: PHP 8.5.1, Laravel 12.46
-   **Admin**: Filament 4.5.2 (SDUI framework)
-   **Frontend**: Livewire 3.7, Flux UI 2.10, Tailwind CSS 4 (CSS-first)
-   **Database**: PostgreSQL (via Docker)
-   **Cache**: Redis
-   **Ambiente**: Laravel Sail 1.52 (Docker)
-   **Qualidade**: Pest 4.3 (Tests + Browser), Larastan 3.8 (Level 5), Rector 2.3, Pint 1.27

## Arquitetura Core

### Multi-Tenancy

-   **Model**: `App\Models\Tenant`
-   **Pivot**: `tenant_user` (N:N com User)
-   **Isolamento**: Via coluna `team_id`, não multi-database
-   **Scope**: Global scope aplicado em models relacionados

### Padrões de Código

-   **UUIDs obrigatórios**: Trait `UuidTrait` em todos os models
-   **Services layer**: Lógica complexa em `app/Services/`
-   **Form Requests**: Validações sempre em classes dedicadas (array-based)
-   **Policies**: Uma por model em `app/Policies/` para RBAC
-   **Type hints**: Obrigatório em métodos/parâmetros/return types
-   **Strict typing**: `declare(strict_types=1);` em todos os arquivos

### Laravel 12 Specifics

-   **Middleware**: Registrar em `bootstrap/app.php`
-   **Service Providers**: Usar `AppServiceProvider`, evitar criar novos
-   **Console**: Auto-discovery de commands em `app/Console/Commands/`
-   **Casts**: Preferir método `casts()` sobre propriedade

### Filament 4 Specifics

-   **Schemas**: Componentes de layout em `Filament\Schemas\Components`
-   **Actions**: Todas estendem `Filament\Actions\Action`
-   **Resources**: Em `app/Filament/Resources` (CRUD para models)
-   **Testing**: Livewire helpers (`livewire(ListUsers::class)->assert...`)

## Comandos de Desenvolvimento

```bash
# Start
vendor/bin/sail up -d

# Code style (antes de commit!)
vendor/bin/sail bin pint --dirty

# Static analysis (Level 5 obrigatório)
vendor/bin/sail composer analyse

# Tests
vendor/bin/sail artisan test --compact

# Tinker
vendor/bin/sail artisan tinker

# Assets
vendor/bin/sail npm run dev  # Hot reload
vendor/bin/sail npm run build  # Production
```

## Convenções

-   **PSR-12** para formatação
-   **Conventional Commits** em português (`feat(tenants): adiciona CRUD`)
-   **Tabelas pivot**: Ordem alfabética (`project_role`, not `role_project`)
-   **Factories**: Sempre usar em testes (nunca `Model::create()` direto)
-   **Eager loading**: Prevenir N+1 queries

## MCPs Disponíveis

-   **Laravel Boost**: Contexto Laravel (versões, schema, docs por versão)
-   **Serena**: Navegação semântica LSP (find_symbol, rename, etc)
-   **AI-Context**: Playbooks em `.context/` (agents, docs, plans)

## Recursos Implementados

-   Multi-tenancy (Tenants + isolamento lógico)
-   RBAC (Roles/Permissions via Spatie)
-   Gestão de mídias (MediaService + FFmpeg)
-   Autenticação unificada (2FA)
-   Audit log de acessos
-   Templates de e-mail
-   Landing page

---

**Última atualização**: 2026-01-15
