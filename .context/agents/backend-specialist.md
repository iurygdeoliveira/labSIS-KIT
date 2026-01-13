# Backend Specialist - Playbook

## Role
Especialista em Laravel 12 e arquitetura do LabSIS-KIT.

## ⚠️ Regras de Ouro (Contexto Real)

### 1. Criação de Models
- **SEMPRE** use `App\Traits\UuidTrait`.
- **SEMPRE** implemente `SoftDeletes` se for entidade de negócio.
- **SEMPRE** defina a propriedade `$casts` para Enums e Dates.
- Se o model pertencer a um Tenant, adicione `database/migrations` field `team_id` ou `tenant_id` e a relação `BelongsTo`.

### 2. Services Patterns
- Não escreva lógica pesada em Controllers ou Resources do Filament.
- Use `App\Services` (ex: ver `MediaService.php`).
- Injete services via Constructor Injection.

### 3. Database & Migrations
- Stack: **PostgreSQL**. Use tipos nativos do Postgres quando útil (jsonb, uuid).
- Migrations de tabelas pivot devem seguir ordem alfabética (`tenant_user`).
- FKs: Use `constrained()->cascadeOnDelete()`.

### 4. Filament Resources
- Use Clusters quando apropriado (ex: `Permissions` cluster).
- Defina `getNavigationGroup()` para manter o menu organizado.
- Use `Select::make('tenant_id')->relationship('tenant', 'name')` para donos de recursos.

## 🔍 Snippets Comuns

**Novo Model Padrão:**
\`\`\`php
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feature extends Model
{
    use HasFactory, SoftDeletes, UuidTrait;

    protected $guarded = ['id'];
}
\`\`\`

---
*Gerado especificamente para a stack Laravel 12 + Filament 4 deste projeto.*
