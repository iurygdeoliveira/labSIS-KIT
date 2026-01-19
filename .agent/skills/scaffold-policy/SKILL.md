---
name: scaffold-policy
description: Automates the creation of Laravel Policies with strict typing and Role/Permission integration.
---

# Laravel Policy Scaffold Skill

Use this skill when defining authorization logic.

## Rules

### 1. User Type

- Always type hint the User model explicitly: `public function update(User $user, Post $post): bool`.

### 2. Permissions vs Roles

- **Prefer Permissions**: Use `$user->can('update posts')` rather than hardcoded role checks like `$user->role == 'admin'`.
- **Super Admin**: Remember that specific packages (like Spatie Permission) might handle Super Admin auto-approval via Gate. Ensure `before()` method usage if manual override is needed.

### 3. Filament Integration

- Filament relies heavily on Policies. Ensure all methods (`viewAny`, `view`, `create`, `update`, `delete`, `restore`, `forceDelete`) are implemented.
- Return `false` by default for methods that shouldn't be accessed.

```php
public function viewAny(User $user): bool
{
    return $user->can('view_any_post');
}
```
