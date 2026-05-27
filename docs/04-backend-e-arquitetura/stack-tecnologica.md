# Stack Tecnológica e Versões

> **Nota**: Este projeto utiliza versões "Bleeding Edge" (mais recentes disponíveis) para garantir longevidade e performance máxima.

## Core Stack

| Componente       | Versão | Notas                                         |
| ---------------- | ------ | --------------------------------------------- |
| **PHP**          | `8.5`  | Requer novos recursos de linguagem.           |
| **Laravel**      | `13.x` | Framework base.                               |
| **Filament**     | `5.x`  | Painel Administrativo e TALL Stack wrapper.   |
| **Livewire**     | `4.x`  | Camada de interatividade reativa.             |
| **Tailwind CSS** | `4.0`  | Motor de estilização (config via CSS nativo). |

## 🗄️ Backend e Dados

-   **Banco de Dados Relacional**: PostgreSQL (Recomendado) ou MySQL 8+.
-   **Banco de Dados NoSQL**: MongoDB Atlas Local (para logs de auditoria e dados não estruturados).
-   **ORM**: Eloquent com suporte nativo a UUIDs.
-   **MongoDB Driver**: `mongodb/laravel-mongodb` v5.5 (integração oficial MongoDB).
-   **Tenancy**: Multi-teams via `laraveldaily/filateams` (coluna `team_id`) com isolamento lógico via Scopes e Spatie Permission.
-   **Cache/Queue**: Redis (Recomendado para produção) ou Database (Dev).

## 🛡️ Segurança e Auth

-   **Laravel Sanctum**: Autenticação API e SPA.
-   **Spatie Permission**: RBAC (Roles e Permissions) granular.
-   **Authentication Log**: Model customizado `App\Models\AuthenticationLog` persistido em **MongoDB** (eventos `Login`, `Logout` e `Failed` registrados via listener).

## 🎨 Frontend e UI

-   **Flux UI**: Componentes Livewire modernos (`livewire/flux`).
-   **Filament Theme**: Sistema customizado em `resources/css/filament/admin/`.
    -   Suporte nativo a **Dark Mode**.
    -   Arquitetura de cores CSS Variables (`colors.css`, `mapping.css`).

## 🧪 Qualidade e Testes

-   **Pest PHP**: v4.x (Testes Unitários e Feature).
-   **Larastan**: v3.x (Análise estática nível 5).
-   **Filacheck Pro**: Validação de convenções Filament v5 (`laraveldaily/filacheck-pro`).
-   **Laravel Pint**: Padronização de código (PSR-12 expandido).
-   **Rector**: Upgrades automáticos e refatoração segura.

## 🐳 Infraestrutura (Dev)

-   **Laravel Sail**: Ambiente Dockerizado padronizado.
    -   Serviços: `laravel.test` (App), `pgsql`, `redis`, `mailpit`, `minio` (S3 local).

## Referências

- [Config: composer.json](../../composer.json)
- [Lock: composer.lock](../../composer.lock)
