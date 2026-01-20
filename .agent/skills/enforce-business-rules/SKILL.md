---
name: enforce-business-rules
description: Validates code against the project's critical business logic (Multi-tenancy, RBAC, IDOR, SaaS Limits).
---

# Enforce Business Rules

Use this skill BEFORE writing complex logic or AFTER generating code to ensure it adheres to the projects "Ironclad Business Laws".

## 1. Tenancy Laws (Multi-Tenancy)

**Context**: Single Database, Tenant-per-record (`tenant_id`).

- **Rule T1 (Scoped Queries)**: NEVER query models directly without considering the Tenant Scope. Filament does this automatically, but custom Controllers/Jobs must manually apply `where('tenant_id', $tenant->id)`.
- **Rule T2 (Team Resolver)**: We use `spatie/laravel-permission` with **Teams**.
    - The `team_id` IS the `tenant_id`.
    - DO NOT use global roles for tenant-specific users. Use `RoleType::USER` or `RoleType::OWNER` scoped to the tenant.

## 2. Authorization Laws (RBAC)

**Context**: Hierarchical Access (`Admin` > `Owner` > `User`).

- **Rule A1 (Policy First)**: authorization logic lives in **Policies**, NOT Controllers.
- **Rule A2 (The `before` Filter)**: Every Policy MUST implement `before($user)` to grant unrestricted access to `RoleType::ADMIN` and `RoleType::OWNER`.
    ```php
    public function before(User $user): ?bool {
        if ($user->hasRole(RoleType::ADMIN->value)) return true;
        if ($user->isOwnerOfTenant(Filament::getTenant())) return true;
        return null;
    }
    ```
- **Rule A3 (Enum Permissions)**: usage of `Permission::for('resource')` is MANDATORY. Do not hardcode strings like `'update users'`.

## 3. Security Laws (IDOR & Data)

- **Rule S1 (UUID Mandatory)**: All public-facing IDs (URLs, API) MUST use UUIDs.
    - Model must use `App\Traits\UuidTrait`.
    - Migration must have `$table->uuid('uuid')->unique();`.
    - Route Key must be `return 'uuid';`.
- **Rule S2 (Route Binding)**: Always use Implicit Route Binding scoped to the tenant to prevent IDOR.
    - Bad: `Media::find($id)`
    - Good: `$tenant->media()->where('uuid', $uuid)->firstOrFail()`

## 4. SaaS Laws (Limits & Plans)

- **Rule P1 (Centralized Logic)**: Limits (e.g., "Max 50 videos") should be checked via a unified Service/Trait, not hardcoded.

## Workflow for Agents

1.  **Identify Context**: Are we in Admin Panel (Global) or App Panel (Tenant)?
2.  **Check Tenancy**: If App Panel, ensure `tenant_id` is handled.
3.  **Check IDs**: proper usage of `UuidTrait`?
4.  **Check Auth**: `Policy` created with `before` method?

## Verification Checklist

- [ ] Does the Model have `UuidTrait`?
- [ ] Does the Policy have `before()` handling Admin/Owner?
- [ ] Is `tenant_id` being populated on creation?
- [ ] Are permissions using `Permission::VIEW->for('x')`?
