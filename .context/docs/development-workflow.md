---
status: filled
generated: 2026-01-15
---

# Development Workflow

## Branching & Releases

-   **Main branch**: Production-ready code
-   **Develop branch**: Integration branch for features
-   Feature branches: `feature/nome-funcionalidade`
-   Hotfix branches: `hotfix/descricao`

## Local Development

### Setup

```bash
./vendor/bin/sail up -d
./vendor/bin/sail composer install
./vendor/bin/sail npm install
./vendor/bin/sail artisan migrate --seed
```

### Running

```bash
./vendor/bin/sail npm run dev  # Frontend hot-reload
./vendor/bin/sail artisan serve # Backend (if not using Sail HTTP)
```

### Building

```bash
./vendor/bin/sail npm run build
./vendor/bin/sail bin pint  # Code style
```

## Convenções de Código (Laravel 12)

### Estrutura

-   **Middleware**: Registrar em `bootstrap/app.php` usando `Application::configure()->withMiddleware()`
-   **Service Providers**: Usar `AppServiceProvider`, evitar criar novos providers
-   **Console Commands**: Auto-discovery em `app/Console/Commands/`

### Models

-   **UUIDs obrigatórios**: Todos os models devem usar `UuidTrait`
-   **Casts**: Preferir método `casts()` sobre propriedade `$casts`
-   **Type hints**: Obrigatório em relationships

```php
public function tenant(): BelongsTo
{
    return $this->belongsTo(Tenant::class);
}
```

### Controllers & Validation

-   **Form Requests obrigatórios**: Nunca validação inline em controllers
-   **Validation rules**: Array-based (não strings)

```php
public function rules(): array
{
    return [
        'email' => ['required', 'email', 'unique:users'],
    ];
}
```

### Services Layer

-   **Lógica complexa**: Extrair para Services (`app/Services/`)
-   **Controllers**: Apenas orquestração, não business logic

```php
// ✅ Correto
public function store(CreateUserRequest $request, UserService $service)
{
    $user = $service->create($request->validated());
    return redirect()->route('users.show', $user);
}

// ❌ Errado
public function store(Request $request)
{
    // Muita lógica aqui...
}
```

## Qualidade de Código

### Antes de Commit

```bash
# 1. Formatar código
./vendor/bin/sail bin pint --dirty

# 2. Análise estática (Level 5 obrigatório)
./vendor/bin/sail composer analyse

# 3. Testes relacionados
./vendor/bin/sail artisan test --filter=NovaFeature
```

### Code Review Expectations

-   All PRs require 1 approval
-   Must pass:
    ✅ Pint (code style)
    ✅ Larastan level 5 (static analysis)
    ✅ Pest tests
-   No merge conflicts
-   Documented breaking changes

## Filament 4 Specifics

### Criando Resources

```bash
# Sempre usar flag --view para evitar prompts
./vendor/bin/sail artisan make:filament-resource Post --view
```

### Testing Filament

```php
// Usar livewire() helpers
livewire(ListUsers::class)
    ->assertCanSeeTableRecords($users);
```

## Commits

Seguir **Conventional Commits** em português:

```
feat(tenants): adiciona CRUD de tenants
fix(auth): corrige logout em múltiplos painéis
docs(readme): atualiza instruções de instalação
```

## Onboarding Tasks

1. Read `/docs` folder
2. Run reset environment (migrate:fresh --seed)
3. Review `.context/agents` for specialized workflows
4. Pick "good first issue" from project board

---

_Workflow testado e aprovado para Laravel 12 + Filament 4._
