# Plano: Enforcamento de Permissões via Roles (Single Source of Truth)

## Contexto
Atualmente, o projeto utiliza o pacote `spatie/laravel-permission` com suporte a multi-tenancy (teams). Embora o sistema esteja operando de forma que as permissões sejam atribuídas a Roles, queremos garantir que isso seja uma regra absoluta do sistema, eliminando a possibilidade de "exceções" (permissões diretas para usuários).

## Objetivo
Transformar as Roles na **única fonte de verdade** para permissões. O usuário herda permissões exclusivamente das Roles que possui no contexto de um Tenant.

---

## 1. Alterações na Model `User`
Para garantir a integridade via código e manter a compatibilidade com o pacote, vamos blindar o modelo de usuário.

### Ações:
- Sobrescrever os métodos de atribuição direta de permissões para lançar uma exceção.
- Manter o uso da trait `HasRoles`.

```php
// app/Models/User.php

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles {
        givePermissionTo as protected traitGivePermissionTo;
        syncPermissions as protected traitSyncPermissions;
        revokePermissionTo as protected traitRevokePermissionTo;
    }

    /**
     * Bloqueia a atribuição direta de permissões a usuários.
     * Permissões devem ser atribuídas exclusivamente a Roles.
     */
    public function givePermissionTo(...$permissions)
    {
        throw new \Exception("Atribuição direta de permissões desabilitada. Utilize Roles.");
    }

    public function syncPermissions(...$permissions)
    {
        throw new \Exception("Sincronização direta de permissões desabilitada. Utilize Roles.");
    }

    public function revokePermissionTo($permission)
    {
        throw new \Exception("Revogação direta de permissões desabilitada. Utilize Roles.");
    }
}
```

## 2. Auditoria e Validação de Fluxos Existentes

### UI (Filament)
- **UserResource:** Verificado. O `UserForm` atual não possui campos de permissões ou roles diretas.
- **Permissions Cluster:** Verificado. As páginas de permissão (`UsersPermissions`, `MediaPermissions`, etc.) já operam sobre a instância de `Role`.
- **Tenants:** Validar se a vinculação de usuários a tenants através de roles está seguindo o padrão correto.

### Seeders e Testes
- **UserSeeder:** Verificado. Já atribui permissões às Roles (`Admin`, `Owner`).
- **Testes:** Criar um teste unitário que garanta que tentar dar uma permissão direta a um usuário resulte em falha/exceção.

## 3. Benefícios
1.  **Integridade:** Elimina "shadow permissions" onde usuários possuem acessos que não deveriam ter por seu cargo.
2.  **Manutenibilidade:** Ao alterar uma Role, todos os usuários são afetados instantaneamente, sem necessidade de varrer permissões individuais.
3.  **Performance:** Otimiza o uso do cache do Spatie, focando apenas na hierarquia Role-Permission.

## 4. Próximos Passos
1.  Aplicar as alterações na model `User`.
2.  Rodar a suite de testes para garantir que nada quebrou.
3.  Implementar o teste de restrição de permissão direta.
