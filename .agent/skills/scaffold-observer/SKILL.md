---
name: scaffold-observer
description: Standardizes Model Observers for handling side-effects of model events.
---

# Laravel Observer Pattern Skill

Use this skill when you need to trigger logic based on Eloquent Model events (created, updated, deleted, etc.).

## When to use

- Sending emails after user registration.
- Logging changes for audit entry.
- Updating summary tables.

## Rules

### 1. Silent Handling

- Observers should handle exceptions gracefully or let them bubble up depending on criticality.
- Avoid putting slow blocking logic (like API calls) directly in Observer. Dispatch a Job/Event instead.

### 2. Registration

- Ensure the Observer is registered in the model attribute `#[ObservedBy(UserObserver::class)]` (Laravel 10+ standard) or in `AppServiceProvider`.
- **Prefer Attributes**:

```php
#[ObservedBy(UserObserver::class)]
class User extends Authenticatable
{
    // ...
}
```

### 3. Methods

- Type hint the model in the observer methods.

```php
public function created(User $user): void
{
    // ...
}
```
