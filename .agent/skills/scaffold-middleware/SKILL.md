---
name: scaffold-middleware
description: Standardizes Middleware creation and registration for Laravel 11/12 (bootstrap/app.php).
---

# Laravel Middleware Pattern Skill

Use this skill when implementing request filtering or modification logic.

## Rules

### 1. Modern Registration (Laravel 12)

- **Do NOT** look for `app/Http/Kernel.php` (it is gone).
- Register middleware in `bootstrap/app.php` using the `->withMiddleware()` callback.
- Use `append()` for global or `alias()` for route-specific.

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->append(EnsureTokenIsValid::class);
    $middleware->alias([
        'admin' => EnsureUserIsAdmin::class,
    ]);
})
```

### 2. Structure

- Implement the `handle` method with proper typing.

```php
public function handle(Request $request, Closure $next): Response
{
    if (...) {
        return redirect('/home');
    }
    return $next($request);
}
```
