# Sistema de Roles e Permissions

## 📋 Índice

- [Introdução](#introdução)
- [Por que usar Roles e Permissões?](#por-que-usar-roles-e-permissões)
- [Pacote Utilizado (Spatie)](#pacote-utilizado-spatie)
- [Arquitetura no Projeto](#arquitetura-no-projeto)
- [Sistema Multi-Tenant](#sistema-multi-tenant)
- [Implementação](#implementação)
- [Policies de Autorização](#policies-de-autorização)
- [Gerenciamento no Filament](#gerenciamento-no-filament)
  - [PermissionsCluster (Permissões por Role)](#permissionscluster-permissões-por-role)
  - [UserRoleCluster (Funções por Usuário)](#userrolecluster-funções-por-usuário)
- [Isolamento de Dados por Tenant](#isolamento-de-dados-por-tenant)
- [Boas Práticas](#boas-práticas)
- [Problemas Comuns](#problemas-comuns)
- [Conclusão](#conclusão)

## Introdução

Este documento descreve como o sistema de **roles** (papeis) e **permissions** (permissões) foi implementado neste projeto, integrando a experiência de UI/UX do Filament com o controle fino de autorização na aplicação.

## Por que usar Roles/Permissions?

Sistemas reais possuem diferentes perfis de usuários e responsabilidades. Sem um controle de acesso granular, riscos como ações indevidas, vazamento de informações e inconsistências de negócio tornam-se comuns.

Com roles e permissions você consegue:

- Garantir que somente usuários autorizados executem ações sensíveis;
- Segregar funções (ex.: Administrador vs Usuário comum);
- Evoluir a segurança de forma incremental sem reescrever a aplicação;
- Integrar facilmente com políticas (Policies) e middlewares de autorização.

## Pacote Utilizado (Spatie)

Utilizamos o pacote `spatie/laravel-permission`, referência no ecossistema Laravel para gerenciamento de roles e permissions.

- **Instalação e orientações oficiais**: consulte a documentação da Spatie para detalhes de instalação e configuração inicial: `https://spatie.be/docs/laravel-permission/v6/installation-laravel`.

Após a instalação, o pacote registra tabelas para `roles`, `permissions` e pivots que relacionam usuários a essas entidades. Ele também provê traits e APIs simples como `assignRole()`, `hasRole()` e `can()`.

## Arquitetura no Projeto

O sistema de roles e permissões foi projetado para funcionar em um ambiente **multi-tenant**, onde cada tenant possui suas próprias roles e permissões isoladas. Os principais arquivos que centralizam a configuração e aplicação das permissões são:

### Arquivos Centrais

- **`app/Enums/RoleType.php`**: enum que define as roles disponíveis no sistema (`Admin`, `Owner`, `User`).
- **`app/Enums/Permission.php`**: enum que define e padroniza todas as permissions do sistema (`create`, `view`, `update`, `delete`).
- **`app/Models/User.php`**: modelo do usuário com trait `HasRoles` e lógica de acesso aos painéis.
- **`app/Models/Tenant.php`**: modelo do tenant com relacionamentos para usuários e mídias.
- **`app/Models/TenantUser.php`**: modelo pivot para relacionamento many-to-many entre usuários e tenants.
- **`app/Models/Role.php`**: modelo de role estendido do Spatie com relacionamento para tenant.
- **`app/Tenancy/SpatieTeamResolver.php`**: resolver customizado para definir o team_id baseado no tenant atual.

### Policies de Autorização

- Na pasta `app/Policies` encontramos as policies de autorização.

### Clusters do Filament

- **`app/Filament/Clusters/Permissions/`**: cluster para gerenciar permissões por role.
- **`app/Filament/Clusters/UserRole/`**: cluster para gerenciar as roles atribuídas a cada usuário.

### Providers de Painel

- **`app/Providers/Filament/UserPanelProvider.php`**: configuração do painel de usuários com suporte a multi-tenancy.
- **`app/Providers/Filament/AdminPanelProvider.php`**: configuração do painel administrativo global.

### Estratégia de Permissões

- As permissions são semeadas a partir de `app/Enums/Permission.php` no `UserSeeder`.
- Roles são criadas dinamicamente por tenant usando métodos estáticos do `RoleType`.
- O sistema utiliza hierarquia de autorização: **Admin** (global) → **Owner** (por tenant) → **User** (por tenant).
- Policies implementam lógica de `before()` para verificar Admin e Owner antes de verificar permissões específicas.

## Sistema Multi-Tenant

O projeto implementa um sistema multi-tenant onde cada tenant possui suas próprias roles e permissões isoladas. Esta arquitetura permite que diferentes organizações utilizem o sistema de forma independente e segura.

### Hierarquia de Acesso

1. **Admin (Global)**: Acesso total a todos os tenants e recursos
2. **Owner (Por Tenant)**: Acesso total dentro do tenant específico
3. **User (Por Tenant)**: Acesso baseado em permissões específicas dentro do tenant

### Isolamento de Dados

- Cada tenant possui suas próprias roles (`Owner`, `User`) criadas dinamicamente
- Permissões são aplicadas no contexto do tenant atual
- O `SpatieTeamResolver` garante que as permissões sejam verificadas no tenant correto
- Policies implementam verificação de tenant antes de aplicar permissões

### Controle de Acesso aos Painéis

- **Admin Panel**: Apenas usuários com role `Admin` global
- **User Panel**: Usuários com role `User`, `Owner` em algum tenant, ou vinculados a tenants

## Implementação

### 1. Enum de Roles

O enum `RoleType` define três tipos de roles no sistema:

- **`ADMIN`**: Administrador global com acesso total
- **`OWNER`**: Proprietário de um tenant específico
- **`USER`**: Usuário comum de um tenant específico

O enum inclui métodos estáticos para criação dinâmica de roles:
- `ensureGlobalRoles()`: Cria role Admin global
- `ensureOwnerRoleForTeam()`: Cria role Owner para um tenant específico
- `ensureUserRoleForTeam()`: Cria role User para um tenant específico

**Arquivo**: `app/Enums/RoleType.php`

### 2. Enum de Permissões

O enum `Permission` define as permissões básicas do sistema:

- **`VIEW`**: Visualizar recursos
- **`CREATE`**: Criar recursos
- **`UPDATE`**: Editar recursos
- **`DELETE`**: Apagar recursos

O método `for(string $resource)` gera permissões específicas por recurso (ex.: `media.view`, `users.create`).

**Arquivo**: `app/Enums/Permission.php`

### 3. Modelo de Usuário

O modelo `User` implementa múltiplas interfaces e traits:

- `HasRoles`: Trait do Spatie para gerenciamento de roles
- `HasTenants`: Interface do Filament para multi-tenancy
- `FilamentUser`: Interface para controle de acesso aos painéis

Métodos importantes:
- `canAccessPanel()`: Controla acesso aos painéis baseado em roles
- `isOwnerOfTenant()`: Verifica se é Owner de um tenant específico
- `isUserOfTenant()`: Verifica se é User de um tenant específico
- `assignRoleInTenant()`: Atribui role no contexto de um tenant

**Arquivo**: `app/Models/User.php`

### 4. Modelos de Tenant

- **`Tenant`**: Representa uma organização/empresa no sistema
- **`TenantUser`**: Modelo pivot para relacionamento many-to-many
- **`Role`**: Estende o modelo do Spatie com relacionamento para tenant

**Arquivos**: `app/Models/Tenant.php`, `app/Models/TenantUser.php`, `app/Models/Role.php`

### 5. Resolver de Team

O `SpatieTeamResolver` define como o Spatie identifica o contexto atual para permissões:

- Usa o tenant atual do Filament como `team_id`
- Fallback para `0` quando não há tenant selecionado
- Permite override manual para casos específicos

**Arquivo**: `app/Tenancy/SpatieTeamResolver.php`

## Policies de Autorização

As policies implementam a lógica de autorização hierárquica do sistema, garantindo que Admin e Owner tenham acesso apropriado antes de verificar permissões específicas.

### Estrutura das Policies

Todas as policies seguem o mesmo padrão:

```php
public function before(User $user): ?bool
{
    // Admin tem acesso total
    if ($user->hasRole(RoleType::ADMIN->value)) {
        return true;
    }

    // Owner tem acesso total no tenant atual
    $currentTenant = Filament::getTenant();
    if ($currentTenant instanceof Tenant && $user->isOwnerOfTenant($currentTenant)) {
        return true;
    }

    // Deixa para os métodos específicos verificarem permissões
    return null;
}
```

### Métodos de Autorização

Cada policy implementa os métodos padrão do Laravel:
- `viewAny()`: Listar recursos
- `view()`: Visualizar recurso específico
- `create()`: Criar recursos
- `update()`: Editar recursos
- `delete()`: Apagar recursos
- `deleteAny()`: Apagar múltiplos recursos

Todos os métodos verificam permissões usando `Permission::for($resource)`.

**Arquivos**: `app/Policies/MediaItemPolicy.php`, `app/Policies/UserPolicy.php`

## Gerenciamento no Filament

A administração é feita por dois clusters principais: `PermissionsCluster` (permissões por role) e `UserRoleCluster` (roles por usuário). Ambos implementam controle de acesso baseado em roles e são visíveis apenas para Admin e Owner.


## Isolamento de Dados por Tenant

O sistema implementa isolamento completo de dados por tenant, garantindo que cada organização veja apenas seus próprios dados e configurações.



## Conclusão

O sistema de roles e permissões implementado oferece uma solução para controle de acesso em ambiente multi-tenant. A integração entre Spatie Laravel Permission, Filament e o sistema de tenancy garante:

- **Segurança**: Isolamento completo de dados entre tenants
- **Flexibilidade**: Hierarquia de acesso clara e configurável
- **Usabilidade**: Interface intuitiva para gerenciamento de permissões
- **Manutenibilidade**: Código organizado e bem documentado

Os clusters `PermissionsCluster` e `UserRoleCluster` proporcionam uma experiência administrativa completa, permitindo que administradores e proprietários gerenciem permissões de forma eficiente e segura, respeitando o isolamento de dados por tenant.

## Referências

- [Enum: RoleType](/app/Enums/RoleType.php)
- [Enum: Permission](/app/Enums/Permission.php)
- [Model: Role](/app/Models/Role.php)
- [Policy: UserPolicy](/app/Policies/UserPolicy.php)


