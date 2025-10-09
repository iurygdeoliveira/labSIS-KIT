### Tenancy (single database) e “teams” do Spatie Permission

Quando falamos de multi-tenant em single database, estamos dizendo que todos os dados vivem nas mesmas tabelas, mas cada registro “pertence” a um tenant específico através de uma coluna como `tenant_id`. Isso resolve o isolamento dos DADOS: cada mídia, cada vídeo, cada item de negócio aponta para o seu tenant por meio do `tenant_id`.

Controle de acesso (RBAC) é um problema diferente do dado em si. Em vez de isolar registros, precisamos isolar quais papéis e permissões um usuário possui em cada tenant. É aqui que entram os “teams” do Spatie Permission: eles adicionam um escopo (`team_id`) nas relações de papéis e permissões. Na prática, isso permite que o mesmo usuário tenha, por exemplo, o papel “User” no Tenant A e nenhum papel no Tenant B, ou ainda que tenha permissão de “editar mídias” em um tenant e não no outro. Tudo isso sem colisão de nomes nem gambiarras, porque cada vínculo de role/permission fica salvo com o `team_id` correto.

Neste projeto, o `SpatieTeamResolver` integra esse conceito com o Filament. Quando você está no painel do admin (sem tenant selecionado), o resolver retorna `0` como `team_id`, que usamos como “time global”. Já no dia a dia, você navega no painel do usuário e escolhe o tenant pelo menu de tenant. Ao selecionar um tenant nesse menu, o resolver passa a retornar o `id` desse tenant como `team_id`. Assim, qualquer checagem de autorização feita pelo Spatie (como `hasRole`, `can`, `hasPermissionTo`) automaticamente considera o tenant selecionado no painel do usuário. Você não precisa ficar filtrando “na unha”: o escopo do time já está embutido nas consultas do pacote.

Em resumo, o `tenant_id` mantém os REGISTROS no seu condomínio certo, enquanto o `team_id` mantém os CRACHÁS (roles) e CHAVES (permissions) válidos apenas dentro daquele condomínio. É por isso que usamos as duas coisas ao mesmo tempo: `tenant_id` garante isolação de dados; “teams” garantem isolação de regras de acesso. Essa combinação dá segurança e previsibilidade: cada tenant enxerga seus dados e aplica suas próprias regras, enquanto o painel admin (time 0) continua com uma visão e um controle globais, sem conflitar com o que acontece dentro dos tenants.

## Sistema Multi-Tenant

O sistema implementa um ambiente multi-tenant completo onde cada organização possui seus próprios usuários, dados e permissões isoladas.

### Arquitetura Multi-Tenant

**Modelos Principais**:
- **`Tenant`**: Representa uma organização/empresa
- **`User`**: Usuários que podem pertencer a múltiplos tenants
- **`TenantUser`**: Tabela pivot para relacionamento many-to-many
- **`Role`**: Roles específicas por tenant (Owner, User)

**Isolamento de Dados**:
- Cada tenant possui suas próprias roles e permissões
- Usuários podem ter diferentes roles em diferentes tenants
- Permissões são verificadas no contexto do tenant atual
- Dados são filtrados automaticamente por tenant

### Hierarquia de Acesso

1. **Admin (Global)**: Acesso total a todos os tenants e recursos
2. **Owner (Por Tenant)**: Acesso total dentro do tenant específico
3. **User (Por Tenant)**: Acesso baseado em permissões específicas dentro do tenant

### Sincronização de Permissões

O `TeamSyncMiddleware` garante que as permissões sejam verificadas no contexto correto:

```php
// Sincroniza permissões com tenant atual
$resolver = app(SpatieTeamResolver::class);
$resolver->setPermissionsTeamId($tenant->getKey());
```

## Controle de Acesso aos Painéis

### Método canAccessPanel

Arquivo: `app/Models/User.php`

Implementa lógica de controle de acesso baseada em status e roles:

**Regras de Acesso**:

1. **Painel Auth**: Sempre permitido (viabiliza login unificado)
2. **Usuários Suspensos**: Bloqueados em todos os painéis
3. **E-mail Não Verificado**: Bloqueados em painéis de aplicação
4. **Painel Admin**: Apenas usuários com role `Admin`
5. **Painel User**: Usuários com roles `User` ou `Owner`, ou vinculados a tenants

**Implementação**:
```php
public function canAccessPanel(Panel $panel): bool
{
    // Painel auth sempre permitido
    if ($panel->getId() === 'auth') {
        return true;
    }

    // Usuários suspensos bloqueados
    if ($this->isSuspended()) {
        return false;
    }

    // E-mail deve ser verificado
    if (!$this->hasVerifiedEmail()) {
        return false;
    }

    // Painel admin: apenas Admin
    if ($panel->getId() === 'admin') {
        return $this->hasRole(RoleType::ADMIN->value);
    }

    // Painel user: User, Owner ou vinculado a tenants
    if ($panel->getId() === 'user') {
        return $this->hasRole(RoleType::USER->value) ||
               $this->hasOwnerRoleInAnyTenant() ||
               $this->tenants()->exists();
    }

    return false;
}
```



