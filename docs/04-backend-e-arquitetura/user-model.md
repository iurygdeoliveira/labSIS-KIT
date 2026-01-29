# Arquitetura do Model User

Este documento detalha a arquitetura, responsabilidades e a lógica interna do model `App\Models\User`. Como entidade central da aplicação, o Usuário interage com autenticação, multi-tenancy, permissões e armazenamento híbrido de dados.

## 1. Visão Geral

O model `User` é um model Eloquent padrão armazenado no banco de dados relacional (**PostgreSQL**). Ele implementa interfaces essenciais para o funcionamento do **Filament** e do sistema de autenticação multi-fator da aplicação.

### Interfaces Principais

-   `FilamentUser`: Permite acesso aos painéis administrativos.
-   `HasTenants`: Habilita a lógica de multi-tenancy do Filament.
-   `HasMedia`: Integração com Spatie Media Library para avatares.
-   `HasAppAuthentication` / `HasAppAuthenticationRecovery`: Suporte a 2FA customizado.

## 2. Organização do Código

Para manter a classe legível e manutenível, o código foi organizado em seções lógicas separadas por comentários visuais:

1.  **Setup & Configuration**:
    -   `casts`: Definição de tipos (ex: senhas hash, campos criptografados).
    -   `registerMediaConversions`: Configuração de otimizações de imagem e thumbnails (vazio por padrão neste model).
    -   (Nota: A configuração do disco S3 para avatares é feita diretamente no componente de upload do Filament, não no model via `registerMediaCollections`).
2.  **Relationships**: Todos os relacionamentos Eloquent (SQL e MongoDB).
3.  **Scopes**: Filtros de consulta reutilizáveis (ex: `withRolesForTenant`).
4.  **Filament / Access Control**: Métodos para controlar quem pode entrar em qual painel (`admin`, `user`) e em qual `tenant`.
5.  **State Checks & Notifications**: Métodos de verificação de estado (`isSuspended`, `isApproved`).
6.  **Tenant & Role Logic**: Lógica de negócio complexa para gerenciar papéis dentro de um contexto de Tenant (times).

## 3. Arquitetura Híbrida (SQL + MongoDB)

Uma das características mais importantes deste model é sua capacidade de se relacionar com logs armazenados no **MongoDB**, mantendo seus dados principais no **PostgreSQL**.

### Trait `HybridRelations`

O model utiliza a trait `MongoDB\Laravel\Eloquent\HybridRelations`. Isso permite que o Eloquent entenda relacionamentos entre bancos de dados diferentes (SQL Parent -> MongoDB Child).

```php
use \MongoDB\Laravel\Eloquent\HybridRelations;
```

### Logs de Autenticação (Correção de Driver)

O pacote `rappasoft/laravel-authentication-log` foi customizado para gravar logs no MongoDB. Para isso, o relacionamento `authentications()` foi sobrescrito no `User.php`.

**Problema Original:** A trait padrão do pacote tentava instanciar o model de Log usando uma conexão SQL padrão, causando erro `prepare() on null` ao tentar salvar no Mongo.

**Solução:** Sobrescrevemos o método para apontar explicitamente para `App\Models\AuthenticationLog` (que estende o Model do Mongo), garantindo que o driver correto seja carregado.

```php
public function authentications()
{
    // Força o uso do Model MongoDB customizado
    return $this->morphMany(\App\Models\AuthenticationLog::class, 'authenticatable')->latest('login_at');
}
```

## 4. Multi-Tenancy e Permissões

O sistema utiliza uma abordagem de **Multi-Tenancy com Times** (Tenants) onde os papéis (Roles) são atribuídos ao usuário _dentro do contexto de um Tenant_.

### Estrutura de Dados

-   **Users**: Tabela global.
-   **Tenants**: Tabela global.
-   **Roles**: Spatie Permissions (globais, mas atribuídas com `team_id`).
-   **Pivot (`model_has_roles`)**: Contém a coluna extra `team_id`.

### Métodos de Helper

A seção **Tenant & Role Logic** fornece métodos para abstrair a complexidade dessa query:

-   `isOwnerOfTenant($tenant)`: Verifica se o usuário tem o papel de 'Owner' no contexto daquele tenant.
-   `isUserOfTenant($tenant)`: Verifica se o usuário tem o papel de 'User' no contexto daquele tenant.
-   `getRolesForTenant($tenant)`: Retorna todos os papéis que o usuário possui naquele tenant específico.

Toda a lógica cruza `user_id` + `role_id` + `team_id` (Tenant).

### Distribuição Natural de Carga (Load Distribution)

Uma característica **arquitetural importante** desta estrutura é que ela promove **distribuição natural de carga** sem necessidade de particionamento da tabela `users`. Isso ocorre porque:

#### 1. Separação de Responsabilidades em Tabelas Pivot

Ao invés de armazenar todas as permissões diretamente na tabela `users`, o sistema utiliza **tabelas pivot normalizadas**:

```mermaid
graph TD
    A[users<br/>1M registros] --> B[tenant_user<br/>Pivot N:M]
    A --> C[model_has_roles<br/>Pivot com team_id]
    B --> D[tenants<br/>10K registros]
    C --> E[roles<br/>100 registros]
    
    style A fill:#e3f2fd
    style B fill:#fff3e0
    style C fill:#fff3e0
    style D fill:#f3e5f5
    style E fill:#e8f5e9
```

**Benefícios de Performance:**

- **Índices Menores**: Cada pivot tem índices específicos (`user_id`, `tenant_id`, `role_id`, `team_id`), que são muito mais compactos que um índice composto gigante na tabela `users`.
- **Queries Mais Eficientes**: PostgreSQL consegue otimizar JOINs em tabelas menores com maior eficiência do que escanear uma tabela monolítica com milhões de linhas.
- **Cache Hit Rate Maior**: Tabelas pivot frequentemente acessadas (ex: verificação de permissões) cabem inteiramente na memória (Shared Buffers do PostgreSQL), acelerando leituras.

#### 2. Exemplo Prático: Query de Autorização

Quando o Filament verifica se um usuário pode acessar um Tenant, a query executada é:

```sql
SELECT tenants.* 
FROM tenants
INNER JOIN tenant_user ON tenant_user.tenant_id = tenants.id
WHERE tenant_user.user_id = 12345
  AND tenants.is_active = true;
```

**Por que isso é eficiente?**

- A tabela `tenant_user` possui um índice composto em `(user_id, tenant_id)`.
- PostgreSQL usa **Index-Only Scan** quando possível, sem precisar tocar na tabela `users`.
- Mesmo com 1 milhão de usuários, a pivot `tenant_user` pode ter apenas 3-5 milhões de registros (se cada usuário pertence a 3-5 tenants em média), mas com índices muito menores.

#### 3. Comparação: Monolítico vs. Multi-Tenant Pivot

| Abordagem | Estrutura | Performance em 1M usuários |
|:----------|:----------|:---------------------------|
| **Monolítica** | `users` com JSON de `tenant_ids` e `permissions` | ❌ Índices GIN/JSONB são lentos para queries complexas. Tabela única de 1M+ linhas. |
| **Multi-Tenant Pivot** (labSIS-KIT) | `users` + `tenant_user` + `model_has_roles` | ✅ Cada tabela é especializada. Índices menores e queries otimizadas por JOIN. |
| **Particionamento** | `users` particionada por `created_at` ou `hash(id)` | ⚠️ Não resolve o problema de RBAC. Complexidade alta sem ganho real para este caso de uso. |

#### 4. Quando a Distribuição Atual NÃO é Suficiente?

A arquitetura pivot atinge seus limites quando:

- **Dezenas de milhões de usuários**: A tabela `model_has_roles` pode crescer desproporcionalmente (ex: 100M+ registros se cada usuário tem múltiplos papéis em múltiplos tenants).
- **Queries analíticas pesadas**: Relatórios que precisam agregar dados de todos os tenants simultaneamente podem ser lentos.

**Solução nesses casos:**
- **Particionamento da pivot `model_has_roles`** por `team_id` (Range ou Hash).
- **Read Replicas** para queries analíticas (separar carga de leitura e escrita).
- **Caching de Permissões** com Redis (evitar consultas repetidas ao banco).



## 5. Integração com Filament

O método `canAccessPanel(Panel $panel)` centraliza a lógica de autorização:

-   **Painel Admin**: Requer Role `Super Admin` (RoleType::ADMIN).
-   **Painel User**: Requer Role `User` OU ser dono de algum Tenant OU pertencer a algum Tenant.
-   **Bloqueios Globais**: Usuários suspensos ou sem e-mail verificado são bloqueados em todos os painéis (exceto 'auth').

## Diagrama de Relacionamento Simplificado

```mermaid
classDiagram
    class User {
        +UUID id
        +String email
        +authentications() [MongoDB]
        +tenants()
    }
    class Tenant {
        +UUID id
        +String name
    }
    class AuthenticationLog {
        <<MongoDB>>
        +IP address
        +UserAgent
    }

    User "1" --> "*" Tenant : BelongsToMany
    User "1" --> "*" AuthenticationLog : MorphMany (Hybrid)
```

## Referências

- [Model: User](/labsis-kit/app/Models/User.php)

