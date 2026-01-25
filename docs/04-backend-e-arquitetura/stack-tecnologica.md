# Stack Tecnológica e Versões

> **Nota**: Este projeto utiliza versões "Bleeding Edge" (mais recentes disponíveis) para garantir longevidade e performance máxima.

## 核心 Core Stack

| Componente       | Versão | Notas                                         |
| ---------------- | ------ | --------------------------------------------- |
| **PHP**          | `8.5`  | Requer novos recursos de linguagem.           |
| **Laravel**      | `12.0` | Framework base.                               |
| **Filament**     | `4.0`  | Painel Administrativo e TALL Stack wrapper.   |
| **Livewire**     | `3.0`  | Camada de interatividade reativa.             |
| **Tailwind CSS** | `4.0`  | Motor de estilização (config via CSS nativo). |

## 🗄️ Backend e Dados

-   **Banco de Dados Relacional**: PostgreSQL (Recomendado) ou MySQL 8+.
-   **Banco de Dados NoSQL**: MongoDB Atlas Local (para logs de auditoria e dados não estruturados).
-   **ORM**: Eloquent com suporte nativo a UUIDs.
-   **MongoDB Driver**: `mongodb/laravel-mongodb` v5.5 (integração oficial MongoDB).
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

-   **Pest PHP**: v4.0 (Testes Unitários e Feature).
-   **Larastan**: v3.0 (Análise estática nível 5).
-   **Laravel Pint**: Padronização de código (PSR-12 expandido).
-   **Rector**: Upgrades automáticos e refatoração segura.

## 🐳 Infraestrutura (Dev)

-   **Laravel Sail**: Ambiente Dockerizado padronizado.
    -   Serviços: `laravel.test` (App), `pgsql`, `redis`, `mailpit`, `minio` (S3 local).

## Referências

- [Config: composer.json](file:///home/iury/Projetos/labSIS-KIT/composer.json)
