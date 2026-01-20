---
name: scaffold-filament-resource
description: Guidelines for creating Filament 4 Resources with strict Separation of Concerns (SoC) and UI standards.
---

# Scaffold Filament Resource

Use this skill when building administrative interfaces. YOU MUST enforce the **Separation of Concerns (SoC)** architecture mandated by this project.

## Context

This skill ensures that all Filament Resources follow the project's strict architectural patterns, specifically delegating logic to dedicated classes rather than defining it inline.

## Tools

- `scaffold-controller`: For related controller logic if needed.
- `scaffold-model`: Ensure the model exists first.

## Rules

### 1. 🚨 Critical: Separation of Concerns

**NEVER define schema logic inline.**
The `Resource` class must strictly act as a configuration hub.

| Responsibility      | Location                                          | Convention                                                 |
| :------------------ | :------------------------------------------------ | :--------------------------------------------------------- |
| **Resource Config** | `app/Filament/Resources/{Name}Resource.php`       | Navigation, Model, Pages, Labels.                          |
| **Form Logic**      | `.../Resources/{Name}/Schemas/{Name}Form.php`     | `public static function configure(Schema $schema): Schema` |
| **Table Logic**     | `.../Resources/{Name}/Tables/{Name}Table.php`     | `public static function configure(Table $table): Table`    |
| **Infolist Logic**  | `.../Resources/{Name}/Schemas/{Name}Infolist.php` | `public static function configure(Schema $schema): Schema` |

### 2. UI Standardization

- **Icons**: ALWAYS use `Filament\Support\Icons\Heroicon`. Ex: `Heroicon::BuildingOffice`.
- **Colors**: Use `filament/support` Enums. Ex: `Color::Primary`.

### 3. Namespace & Imports

- **Filament v4 Structural Components**:
    - Structural components like `Section`, `Group`, `Grid`, `Split` must be imported from `Filament\Schemas\Components`.
    - DO NOT import them from `Filament\Forms\Components`.

### 4. Correct Typing (Filament v4)

- **Navigation Properties**:
    - ❌ **WRONG**: `protected static ?string $navigationGroup = 'Administração';`
    - ✅ **CORRECT**: `protected static string|\UnitEnum|null $navigationGroup = 'Administração';`
    - **Reason**: In Filament v4, properties like `$navigationGroup`, `$navigationIcon`, and `$navigationLabel` accept both strings and UnitEnums, allowing the use of typed enums for greater type safety.

- **Authentication**:
    - ❌ **WRONG**: `$user = auth()->user();`
    - ✅ **CORRECT**: `$user = Filament::auth()->user();`
    - **Reason**: Always use `Filament::auth()` within Resources/Pages/Widgets to ensure the correct authentication context for the panel. This is especially important in multi-panel and multi-tenancy scenarios.

### 5. Code Standards & Naming

- **Comments Language**:
    - **ALL code comments MUST be written in Brazilian Portuguese (pt-BR)**.
    - This includes inline comments, block comments, and PHPDoc descriptions.
    - Example: `// Busca todos os usuários ativos` ✅ NOT `// Fetch all active users` ❌
- **Action Definitions**:
    - Use `->recordActions([...])` instead of `->actions([...])` for row actions.
    - Use `->toolbarActions([...])` instead of `->bulkActions([...])` or `->headerActions([...])`.
- **Clean Code Guidelines**:
    - **No Empty Arrays**: ONLY call `->recordActions()` or `->toolbarActions()` if they contain items. If empty, OMIT the method entirely.
    - **Filter Schemas**: Do NOT define filter forms inline (`->form([...])`). Extract them to the main Form class (e.g., `UserForm::filterSchema()`).
    - **Deprecated Methods**: Use `->schema()` instead of `->form()` for Filters definition (Filament v4).

## Workflow

1.  **Generate**: Create the Resource (`php artisan make:filament-resource`).
2.  **Scaffold Support Classes**: Create `Schemas/{Name}Form.php` and `Tables/{Name}Table.php`.
3.  **Delegate**: Update the Resource to return `UserForm::configure($schema)` and `UserTable::configure($table)`.
4.  **Refine**: Implement the logic inside the support classes using strict types and project UI standards.
