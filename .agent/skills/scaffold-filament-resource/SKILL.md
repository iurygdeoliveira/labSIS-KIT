---
name: scaffold-filament-resource
description: Guidelines for creating Filament 4 Resources, Pages, and clusters with strict UI standards.
---

# Filament Resource (v4) Skill

Use this skill when building separate administrative interfaces. It enforces Filament 4 patterns and the project specific design system.

## When to use this skill

- When creating CRUDs ("Create a resource for Products").
- When adding custom pages to the admin panel.
- When organizing existing resources into **Clusters**.

## Recommended Tools

- **laravel_boost_search_docs**: MANDATORY when using plugins (e.g., `['awcodes/curator', 'filament tables']`) to avoid v3 syntax.
- **laravel_boost_list_routes**: To verify if the resource slug collides with existing routes.

## Workflow

### 1. Generation

Run the command with the `--view` flag to generate the page classes immediately:

```bash
php artisan make:filament-resource [Model] --generate --view
```

### 2. Implementation Rules

#### A. Resource Class (`app/Filament/Resources/...Resource.php`)

- **Navigation**:
    - Use `protected static ?string $navigationIcon = 'heroicon-o-[name]';`
    - If part of a cluster (e.g., `UserRole`), add `protected static ?string $cluster = UserRoleCluster::class;`.
- **Global Search**: Configure `$globalSearchAttribute = 'name';` to enable search bar integration.

#### B. Form Schema (`form()`)

- **Container**: Generally wrap fields in `Section::make()`.
- **Columns**: Use `SelectColumn` for relations (avoid extensive queries in `options()`, prefer `relationship()`).
- **Input Patterns**:
    - `TextInput::make('price')->numeric()->prefix('R$')`
    - `DatePicker::make('published_at')->native(false)` (for consistent UI).

#### C. Table Schema (`table()`)

- **Columns**:
    - **Actions**: ALWAYS import from `Filament\Actions` (NOT `Filament\Tables\Actions`).
    - **Visuals**: Use `BadgeColumn` for status (colors: `primary`, `success`, `warning`, `danger`, `gray`).
    - **Interaction**: Use `SelectColumn` or `ToggleColumn` for quick inline edits.
- **Filters**:
    - Use `SelectFilter` or `TernaryFilter` (for booleans).
- **Actions**:
    - `EditAction::make()`
    - `DeleteAction::make()`
    - `BulkActionGroup::make([...])`

### 3. Clusters & Architecture (Filament v4)

- **Prefer Clusters**: For grouped features (e.g., `Financial/Invoices`, `Financial/Main`), organize resources inside a Cluster.
- **Directory**: `app/Filament/Clusters/[ClusterName]/Resources`.
- **Breadcrumbs**: Use `protected static ?string $clusterBreadcrumb` to customize.

### 4. UI Patterns

- **Tabs as SubNavigation**: Use `protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;` for tabbed layouts.
- **Spatie Media**: Use `SpatieMediaLibraryImageColumn` and `SpatieMediaLibraryFileUpload` for handling media.

### 3. UI Standardization (Project Styles)

- **Colors**: Never hardcode hex codes. Use Filament semantic colors (`Color::Success`, `Color::Primary`).
- **Icons**: Use Heroicons set (e.g., `heroicon-o-check-circle`).

### 4. Custom Pages

If creating a custom page (`make:filament-page`):

- Extend `Filament\Pages\Page`.
- Define `protected static string $view = 'filament.pages.[name]';`.
- Use `Flux UI` components in the blade view if available, or Filament Blade components (`<x-filament::button>`).
