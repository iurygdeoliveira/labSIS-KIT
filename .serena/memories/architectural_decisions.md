# Decisões Arquiteturais (ADRs)

## 1. UUIDs como Primary Keys

### Decision
Todos os models usam UUIDs (v4) em vez de auto-increment integers.

### Rationale
- **Segurança**: IDs não sequenciais previnem enumeração
- **Distribuição**: Compatível com sistemas distribuídos
- **Unicidade**: Garante IDs únicos sem coordenação central

### Implementation
```php
use App\Traits\UuidTrait;

class User extends Model
{
    use UuidTrait;
    
    protected $keyType = 'string';
    public $incrementing = false;
}
```

### Trade-offs
- ✅ Maior segurança
- ✅ Escalabilidade distribuída
- ❌ 36 bytes vs 4 bytes (storage)
- ❌ Índices ligeiramente mais lentos

---

## 2.Services Layer para Lógica de Negócios

### Decision
Lógica complexa vive em Services (`app/Services/`), não Controllers.

### Rationale
- **Separação de concerns**: Controllers apenas orquestram
- **Reutilização**: Services podem ser injetados em Commands, Jobs, etc
- **Testabilidade**: Mais fácil mockar Services

### Implementation
```php
// MediaService.php
class MediaService
{
    public function processUpload(UploadedFile $file): Media
    {
        // Lógica complexa aqui
    }
}

// MediaController.php
public function store(Request $request, MediaService $service)
{
    $media = $service->processUpload($request->file('upload'));
    return redirect()->route('media.show', $media);
}
```

### Trade-offs
- ✅ Controllers magros
- ✅ Código reutilizável
- ❌ Mais arquivos para navegar

---

## 3. Multi-tenancy via Column (team_id)

### Decision
Isolamento lógico via coluna `team_id`, não multi-database.

### Rationale
- **Simplicidade**: Um banco, migrations simples
- **Performance**: Melhor uso de cache
- **Backups**: Um dump, fácil restore

### Implementation
- Model `Tenant` central
- Pivot table `tenant_user` (N:N)
- Scope global aplicado em models

### Trade-offs
- ✅ Simples de gerenciar
- ✅ Migrations unificadas
- ❌ Risco de data leakage (policies críticas!)
- ❌ Não suporta DB customizado por tenant

---

## 4. Form Requests Obrigatórios

### Decision
Toda validação usa Form Requests, nunca inline em controllers.

### Rationale
- **Reutilização**: Mesmas rules em API/Web
- **Clareza**: Validações em arquivo dedicado
- **Type safety**: IDE autocomplete

### Implementation
```php
class CreateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'unique:users'],
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
```

### Trade-offs
- ✅ Código organizado
- ✅ Fácil testar
- ❌ Mais arquivos

---

## 5. Policies Centralizadas

### Decision
Cada Model tem uma Policy correspondente em `app/Policies`.

### Rationale  
- **RBAC granular**: Permissões por ação
- **Prevenção IDOR**: Validação automática via Filament
-   **Auditoria**: Lógica de autorização centralizada

### Implementation
```php
class UserPolicy
{
    public function update(User $actor, User $target): bool
    {
        return $actor->isAdmin() || $actor->id === $target->id;
    }
}
```

### Trade-offs
- ✅ Segurança garantida
- ✅ Fácil auditar
- ❌ Boilerplate inicial

---

**Última atualização**: 2026-01-15
