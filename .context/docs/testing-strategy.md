---
status: filled
generated: 2026-01-15
---

# Testing Strategy

## Frameworks

-   **Pest 4**: Framework de testes principal
-   **Pest Browser Plugin**: Testes E2E com Playwright
-   **PHPUnit 12**: Base do Pest

## Test Types

### Unit Tests (`tests/Unit/`)

Testam classes isoladamente sem dependências externas.

```php
it('calculates total correctly', function () {
    $calculator = new Calculator();
    expect($calculator->sum(2, 3))->toBe(5);
});
```

### Feature Tests (`tests/Feature/`)

Testam fluxos completos com banco de dados.

```php
it('creates user successfully', function () {
    $data = ['name' => 'John', 'email' => 'john@example.com'];

    $this->post('/users', $data)
        ->assertCreated();

    assertDatabaseHas('users', $data);
});
```

### Browser Tests (`tests/Browser/`) - Pest 4

Testam UI com navegador real (Playwright).

```php
it('allows login', function () {
    $user = User::factory()->create();

    $page = visit('/login');
    $page->fill('email', $user->email)
         ->fill('password', 'password')
         ->click('Login')
         ->assertSee('Dashboard')
         ->assertNov aJavascriptErrors();
});
```

## Filament Testing

Usar helpers Livewire:

```php
livewire(ListUsers::class)
    ->assertCanSeeTableRecords($users)
    ->searchTable($users->first()->name)
    ->assertCanSeeTableRecords($users->take(1));

livewire(CreatePost::class)
    ->fillForm(['title' => 'Test'])
    ->call('create')
    ->assertNotified();
```

## Running Tests

```bash
# Todos os testes
./vendor/bin/sail artisan test --compact

# Arquivo específico
./vendor/bin/sail artisan test tests/Feature/UserTest.php

# Filtro por nome
./vendor/bin/sail artisan test --filter=test_user_can_login

# Com coverage
./vendor/bin/sail artisan test --coverage

# Browser tests
./vendor/bin/sail artisan test tests/Browser/
```

## Quality Gates

### Antes de Merge

-   ✅ **Todos os testes passando**
-   ✅ **Coverage >80%** (feature tests)
-   ✅ **Larastan level 5** sem erros
-   ✅ **Pint** formatado

### CI/CD

```bash
# Pipeline completo
./vendor/bin/sail bin pint --test  # Fail se não formatado
./vendor/bin/sail composer analyse  # Larastan
./vendor/bin/sail artisan test --compact
```

## Mocking & Datasets

### Mocking (Pest)

```php
use function Pest\Laravel\mock;

it('sends notification', function () {
    $mock = mock(NotificationService::class);
    $mock->shouldReceive('send')->once();

    // ... teste
});
```

### Datasets

Para testes de validação:

```php
it('validates email', function (string $email, bool $valid) {
    $response = $this->post('/register', ['email' => $email]);

    $valid ? $response->assertOk() : $response->assertInvalid('email');
})->with([
    ['valid@email.com', true],
    ['invalid', false],
    ['', false],
]);
```

## Factories

Sempre usar factories para criar models em testes:

```php
// ✅ Correto
$user = User::factory()->create();
$tenant = Tenant::factory()->create();

// ❌ Errado
$user = User::create([...]);
```

## Troubleshooting

### Testes Falhando Aleatoriamente

-   Usar `RefreshDatabase` trait
-   Verificar seeds rodando em ordem
-   Limpar cache: `./vendor/bin/sail artisan config:clear`

### Browser Tests Lentos

-   Usar `$page->pause()` para debugar
-   Screenshots: `$page->screenshot('/path/to/save.png')`
-   Headless mode (padrão, mais rápido)

---

_Estratégia validada com Pest 4.3 + Laravel 12_
