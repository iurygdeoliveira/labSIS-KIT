### Multi-organização (single database), FilaTeams e “teams” do Spatie Permission

Em single database, os dados de todas as organizações vivem nas mesmas tabelas; o isolamento lógico é feito por **chave estrangeira para a organização ativa**. No domínio da aplicação isso é o modelo **`Team`** (tabela `teams`), com vínculo N:N em **`team_members`** (pacote `laraveldaily/filateams`). Exemplos: `media_items.team_id`, `videos.team_id` apontam para `teams.id`.

O **RBAC** (quem pode o quê) é um problema separado do “dono do registro”. O Spatie Permission com **teams** adiciona `team_id` em `roles`, `model_has_roles` e `model_has_permissions`. Aqui, **`team_id` referencia `teams.id`** — o mesmo identificador da organização no FilaTeams. Assim, o mesmo usuário pode ser `Owner` no Team A e apenas `User` no Team B, com permissões distintas por contexto, sem colisão de nomes de role.

A **UI de equipes** (switcher no header, criar equipe, `/user/{slug}/settings` com membros, papéis no pivot, convites com expiração) vem do **FilaTeams**, integrado ao painel Filament `user` via `tenant(Team::class, slugAttribute: 'slug')`, `tenantRegistration` e `tenantProfile`. O Filament continua usando o vocabulário interno **`tenant`** na API (`getTenant()`, parâmetro de rota `{tenant:slug}`, `canAccessTenant(Model $tenant)`): na prática o modelo é sempre **`Team`**.

O **`SpatieTeamResolver`** liga o tenant Filament ao `PermissionRegistrar`: no painel **admin** (sem organização selecionada) o contexto global usa `team_id = 0`. No painel **user**, ao navegar em `/user/{slug}/…`, o resolver usa o `id` do `Team` atual como `team_id` do Spatie. Checagens como `hasRole`, `can` e `hasPermissionTo` passam a considerar esse contexto.

O **`TeamSyncMiddleware`** alinha o resolver ao **slug** da rota (ou ao primeiro team ativo do usuário), e **invalida** relações em memória (`roles`, `permissions`) e o cache do Spatie ao trocar de equipe na mesma requisição — evitando vazamento de permissões entre teams.

O pivot **`team_members.role`** (valores do enum de app `AppTeamRole`: `owner`, `member`) é espelhado para o Spatie pelo **`MembershipObserver`**: a fonte de verdade para autorização de negócio continua sendo **Spatie** (`RoleType::OWNER`, `RoleType::USER` por `team_id`).

Em resumo: **`team_id` em dados de domínio** e **`team_id` no Spatie** apontam para a mesma entidade **`teams`**. O FilaTeams cuida de **membros e convites**; o Spatie cuida de **permissões**; o middleware e o resolver mantêm o **contexto** coerente.

## Sistema multi-organização (teams)

Cada **team** representa uma organização com membros, slug único para URLs (`/user/{slug}`), flag `is_active` e integração com o painel.

### Arquitetura

**Modelos e pacotes principais**

- **`App\Models\Team`**: estende o `Team` do FilaTeams; campos extras (`is_active`); relação com mídias/vídeos.
- **`App\Models\User`**: `HasTeams` + `HasTeamMembership` (FilaTeams) + `HasRoles` (Spatie); implementa os contratos Filament que ainda falam em “tenant” mas operam em `Team`.
- **`App\Models\Membership`**: pivot `team_members` (role no pivot sincronizada com Spatie).
- **`App\Models\Role`**: roles do Spatie com `team_id` → `teams.id`.

**Isolamento**

- Papéis e permissões são avaliados no contexto do team atual (`team_id` do Spatie).
- Dados de domínio ligados à organização usam `team_id` onde aplicável.
- Policies e queries do painel `user` devem considerar `Filament::getTenant()` como instância de `Team`.

### Hierarquia de acesso

1. **Admin (global)**: `team_id = 0` no resolver; acesso ao painel admin e a recursos globais conforme role `Admin`.
2. **Owner (por team)**: no pivot / Spatie como Owner daquele `teams.id`; permissões amplas dentro do team (inclui UI FilaTeams de gestão quando autorizado).
3. **User (por team)**: colaborador com permissões granulares definidas no cluster **Permissions** e nas roles Spatie daquele `team_id`.

### Sincronização de contexto Spatie

O middleware mantém o resolver alinhado ao team da rota:

```php
resolve(\App\Tenancy\SpatieTeamResolver::class)->setPermissionsTeamId($teamId);
// Em seguida: unset relations + forgetCachedPermissions() quando aplicável
```

Arquivo de referência: `app/Http/Middleware/TeamSyncMiddleware.php`.

## Controle de acesso aos painéis

### Método `canAccessPanel`

Arquivo: `app/Models/User.php`.

**Regras resumidas**

1. **Painel `auth`**: sempre permitido (login unificado).
2. **Suspensos / e-mail não verificado**: bloqueados nos painéis de aplicação.
3. **Painel `admin`**: apenas role global `Admin`.
4. **Painel `user`**: role global `User`, ou **owner em algum team**, ou **membro de algum team** (`teams()`).

A coleção retornada por `getTenants()` (contrato Filament) é na prática a lista de **`Team`** ativos aos quais o usuário pertence.

## Referências no código

- [Model: Team](/app/Models/Team.php)
- [Resolver: SpatieTeamResolver](/app/Tenancy/SpatieTeamResolver.php)
- [Middleware: TeamSyncMiddleware](/app/Http/Middleware/TeamSyncMiddleware.php)
- [Observer: MembershipObserver](/app/Observers/MembershipObserver.php)
- [Config: filateams](/config/filateams.php)
- [Painel: UserPanelProvider](/app/Providers/Filament/UserPanelProvider.php)
