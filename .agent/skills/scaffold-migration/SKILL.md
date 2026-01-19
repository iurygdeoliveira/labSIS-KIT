---
name: scaffold-migration
description: Automates the creation of Laravel Migrations using anonymous classes, correct foreign key constraints, and UUIDs where applicable.
---

# Laravel Migration Scaffold Skill

Use this skill when creating database table definitions.

## Rules

### 1. Anonymous Classes

- Always use `return new class extends Migration`.

### 2. Foreign Keys

- **Standard**: Use `foreignIdFor()` constrained to the model class.
    ```php
    $table->foreignIdFor(\App\Models\User::class)->constrained()->cascadeOnDelete();
    ```
- **Nullable**: usage of `->nullable()` comes **before** `constrained()`.

### 3. ID and UUIDs

- Check if the project or related models use UUIDs.
- If UUID: `$table->uuid('id')->primary();`
- If ID: `$table->id();`

### 4. Indexing

- Add indexes to columns that will be frequently searched or used in `WHERE` clauses (e.g., `slug`, `email`, `status`).

## Workflow

1.  Ask: "Does this table relate to existing models?"
2.  If yes, check the parent model's ID type (int or uuid) to ensure the foreign key matches.
3.  Write the migration file.
