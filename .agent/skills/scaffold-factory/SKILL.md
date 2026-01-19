---
name: scaffold-factory
description: Automates the creation of Laravel Factories with standardized Faker usage and essential state methods.
---

# Laravel Factory Scaffold Skill

Use this skill when defining test data generators for Models.

## Conventions

### 1. Sensible Faker Data

- Use `$this->faker` methods that match the column type contextually.
- **Email**: `safeEmail()` (never real emails).
- **Text**: `paragraph()` or `sentence()` depending on length.
- **Enums**: `fake()->randomElement(Enum::cases())`.

### 2. State Methods

- Create state methods for every boolean flag or status enum in the model.
- This makes tests readable: `User::factory()->active()->admin()->create()`.

```php
public function admin(): static
{
    return $this->state(fn (array $attributes) => [
        'role' => UserRole::Admin,
    ]);
}

public function suspended(): static
{
    return $this->state(fn (array $attributes) => [
        'suspended_at' => now(),
    ]);
}
```

### 3. Relationships

- For "BelongsTo" relationships, create the parent factory by default in the definition.

```php
'user_id' => User::factory(),
```
