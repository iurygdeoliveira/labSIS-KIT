# Stack Tecnológica e Versões

> **Nota**: Este projeto utiliza versões "Bleeding Edge" (mais recentes disponíveis) para garantir longevidade e performance máxima.

## 核心 Core Stack

| Componente       | Versão  | Notas                                              |
| ---------------- | ------- | -------------------------------------------------- |
| **PHP**          | `8.5.1` | Requer Property Hooks e novos recursos de tipagem. |
| **Laravel**      | `12.46` | Framework base.                                    |
| **Filament**     | `4.5`   | Painel Administrativo e TALL Stack wrapper.        |
| **Livewire**     | `3.7`   | Camada de interatividade reativa.                  |
| **Tailwind CSS** | `4.1`   | Motor de estilização (config via CSS nativo).      |

## 🗄️ Backend e Dados

-   **Banco de Dados**: PostgreSQL (Recomendado) ou MySQL 8+.
-   **ORM**: Eloquent com suporte nativo a UUIDs.
-   **Tenancy**: Single-database tenancy (coluna `team_id`) com isolamento lógico via Scopes.
-   **Cache/Queue**: Redis (Recomendado para produção) ou Database (Dev).

## 🛡️ Segurança e Auth

-   **Laravel Sanctum**: Autenticação API e SPA.
-   **Spatie Permission**: RBAC (Roles e Permissions) granular.
-   **Authentication Log**: `rappasoft/laravel-authentication-log` para auditoria de acessos.

## 🎨 Frontend e UI

-   **Flux UI**: Componentes Livewire modernos (`livewire/flux`).
-   **Filament Theme**: Sistema customizado em `resources/css/filament/admin/`.
    -   Suporte nativo a **Dark Mode**.
    -   Arquitetura de cores CSS Variables (`colors.css`, `mapping.css`).

## 🧪 Qualidade e Testes

-   **Pest PHP**: v4.3 (Testes Unitários e Feature).
-   **Larastan**: v3.8 (Análise estática nível 5+).
-   **Laravel Pint**: Padronização de código (PSR-12 expandido).
-   **Rector**: Upgrades automáticos e refatoração segura.

## 🐳 Infraestrutura (Dev)

-   **Laravel Sail**: Ambiente Dockerizado padronizado.
    -   Serviços: `laravel.test` (App), `pgsql`, `redis`, `mailpit`, `minio` (S3 local).
