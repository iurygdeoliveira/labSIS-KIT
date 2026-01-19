---
name: optimize-performance
description: Guidelines for analyzing and optimizing application performance (N+1, caching, Computed properties).
---

# Performance Optimizer Skill

Use this skill when the user reports "slow pages" or asks to "optimize" code.

## Checklist

### 1. Database (The Usual Suspect)

- **N+1 Detection**: Look for loops calling relationships.
    - _Bad_: `@foreach ($users as $user) {{ $user->posts->count() }} @endforeach`
    - _Fix_: `User::withCount('posts')->get()`
- **Indexes**: Ensure searching columns (slugs, foreign keys, status) are indexed.

### 2. Livewire / Filament

- **Computed Properties**: Use `#[Computed]` for expensive calculations that don't need to run on every dehydrate.
- **Lazy Loading**: Use `lazy()` on heavy components.

```php
#[Computed]
public function heavyData()
{
    return ...;
}
```

### 3. Caching

- **Cache Facade**: `Cache::remember('key', 60, fn() => ...)` for unrelated data.
- **Model Caching**: If generic, use the model's `booted` method to clear cache on updates.

### 4. Cloudflare / HTTP

- Check headers for `Cache-Control`.
