---
name: scaffold-listener
description: Standardizes Events and Listeners for decoupled architecture.
---

# Laravel Listener Pattern Skill

Use this skill when implementing Event-Driven Architecture.

## Workflow

1.  **Create Event**: `php artisan make:event [EventName]`
2.  **Create Listener**: `php artisan make:listener [ListenerName] --event=[EventName]`

## Rules

### 1. Queued Listeners

- If the listener performs IO (Email, API, Notification), it **MUST** implement `ShouldQueue`.

```php
class SendWelcomeEmail implements ShouldQueue
{
    // ...
}
```

### 2. Dependency Injection

- Inject dependencies in `__construct`.
- Access event data in `handle(EventName $event)`.

### 3. Verification

- Ensure the mapping exists. Laravel 11/12 detects it automatically if the listener type-hints the event.
