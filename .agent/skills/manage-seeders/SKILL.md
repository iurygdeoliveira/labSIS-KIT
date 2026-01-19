---
name: manage-seeders
description: Manages Database Seeders with advanced support for JSON data sources, idempotency checks, and relationship mapping.
---

# Database Seeder Manager Skill

Use this skill to create or refactor Seeders, especially when moving from hardcoded data to JSON files or factories.

## Core Rules

### 1. Idempotency (Run Multiple Times Safe)

- Seeders MUST be safe to run multiple times without creating duplicate dirty data.
- **Use `updateOrCreate`** or `firstOrCreate` based on a unique key (e.g., `slug`, `email`, `code`).

### 2. JSON Data Source Pattern

When populating data from JSON files (e.g., `database/data/*.json`):

1.  **Read File**: Use `file_get_contents` and `json_decode`.
2.  **Iterate**: Loop through the array.
3.  **Persist**: Use `updateOrCreate`.

```php
$json = file_get_contents(database_path('data/categories.json'));
$categories = json_decode($json, true);

foreach ($categories as $data) {
    Category::updateOrCreate(
        ['slug' => $data['slug']], // Unique Key
        [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]
    );
}
```

### 3. Removing IDs

- **Never** force `id` from JSON unless it's a critical system constant. Let the database Auto-Increment handle IDs.
- If relationships rely on specific "codes" in the JSON, look up the related ID by that code, do not hardcode the foreign ID.

### 4. Registration

- Always check if the Seeder is called in `DatabaseSeeder.php`. If not, register it.
