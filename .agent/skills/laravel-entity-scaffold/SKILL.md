---
name: laravel-entity-scaffold
description: Automates the creation of Laravel entities (Model, Migration, Factory, Seeder) with strict standards.
---

# Laravel Entity Scaffold Skill

Use this skill to create new database entities, ensuring all layers (Database, Model, Test Data) are consistent and follow Laravel 12 / PHP 8.5 standards.

## When to use this skill

-   When requested to "create a model", "add a table", or "scaffold an entity".
-   When needing to populate the database with test data for a new feature.

## Recommended Tools

-   **serena_insert_after_symbol**: To register Seeders in `DatabaseSeeder.php`.
-   **laravel_boost_database_schema**: To verify referencing table types (e.g., `id` vs `uuid`) before creating new foreign keys.

## Workflow

### 1. Verification (Pre-Flight)

Before running commands, ask:

-   "Does this entity depend on existing tables?" -> Use `laravel_boost_database_schema` to check FK types.
-   "Is this a pivot table?" -> Follow naming hierarchy (alphabetical order).

### 2. Execution

Run the artisan command with all flags:

```bash
php artisan make:model [EntityName] -mfs
```

_(Validation: This creates Model, Migration, Factory, and Seeder)_

### 3. Implementation Rules

#### A. Model (`app/Models/[Entity].php`)

-   **Strict Types**: `declare(strict_types=1);` at the top.
-   **Fillables**: Always define `$fillable` or `guarded`.
-   **Casts**: Use the `casts()` method (Laravel 11+ style), not the property.
-   **Relationships**: Return types are mandatory (e.g., `: BelongsTo`).

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Ticket extends Model
{
    // ...

    protected function casts(): array
    {
        return [
            'status' => TicketStatus::class, // Enum casting
            'is_resolved' => 'boolean',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
```

#### B. Migration (`database/migrations/....php`)

-   **Anonymous Class**: `return new class extends Migration`.
-   **Foreign Keys**: Use `foreignIdFor(Model::class)->constrained()->cascadeOnDelete()` whenever possible.
-   **UUIDs**: If the project uses UUIDs (check `Project` model), use `c->uuid('id')->primary()`.

#### C. Factory (`database/factories/[Entity]Factory.php`)

-   **Definitions**: Use sensible fakers (`$this->faker->company` for names).
-   **States**: Create methods for pivotal states (e.g., `published()`, `archived()`).

```php
public function published(): static
{
    return $this->state(fn (array $attributes) => [
        'published_at' => now(),
    ]);
}
```

#### D. Seeder (`database/seeders/[Entity]Seeder.php`)

-   **Usage**: Only call the Factory.
-   **Output**: `return;` void type.

### 4. Registration (Post-Flight)

-   **Action**: Register the new Seeder in `database/seeders/DatabaseSeeder.php`.
-   **Tool**: Use `serena_insert_after_symbol` seeking `$this->call([` to append the new class.
