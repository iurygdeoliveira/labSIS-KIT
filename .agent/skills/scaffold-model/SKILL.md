---
name: scaffold-model
description: Automates the creation of Laravel Models with strict typing, modern casting (method-based), and fully typed relationships.
---

# Laravel Model Scaffold Skill

Use this skill to create or modify Eloquent Models.

## Rules

### 1. Structure

- **Strict Types**: Always `declare(strict_types=1);`.
- **Final**: Models should be `final` unless they are explicitly designed for inheritance.
- **Fillable**: Prefer `$fillable` over `$guarded` for explicit security.

### 2. Modern Casting (Laravel 12)

- Use the `casts(): array` **method** (Laravel 11+), NOT the `$casts` property.
- Use Enums for status columns and native types.

```php
protected function casts(): array
{
    return [
        'status' => TicketStatus::class,
        'published_at' => 'datetime',
        'is_active' => 'boolean',
        'options' => 'array',
    ];
}
```

### 3. Relationships

- **Always** add return types to relationship methods (`BelongsTo`, `HasMany`, etc.).
- Import the relation classes (`Illuminate\Database\Eloquent\Relations\BelongsTo`).

```php
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}
```

### 4. Scopes

- Use `Builder` type hint for scopes.

```php
public function scopeActive(Builder $query): void
{
    $query->where('is_active', true);
}
```
