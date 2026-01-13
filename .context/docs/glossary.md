---
status: template
generated: 2026-01-13
---

# Glossary & Domain Concepts

## Core Terms

**Tenant**  
Organization/client in the multi-tenant system. Maps to `teams` table via Spatie Laravel Permission.

**Role**  
User role within a tenant. Types: `ADMIN`, `OWNER`, `USER`.

**Permission**  
Granular action right. Format: `{resource}.{action}` (e.g., `users.view`).

**Media**  
Uploaded file (image/video) managed by Spatie Media Library.

**Panel**  
Filament UI instance. Types: `admin` (global) and tenant-specific panels.

## Acronyms

- **UUID**: Universally Unique Identifier
- **CRUD**: Create, Read, Update, Delete
- **RBAC**: Role-Based Access Control
- **FFmpeg**: Fast Forward MPEG (video processing)
- **MCP**: Model Context Protocol (AI tooling)

## Personas

### Admin
Full system access across all tenants. Manages global settings, tenants, and users.

### Owner
Tenant administrator. Manages users and permissions within their tenant.

### User
Regular tenant member with restricted permissions based on assigned role.

## Domain Rules

- UUIDs are primary keys for all models
- Soft deletes enabled on `User`, `Tenant` models
- Media files < 100MB (configurable)
- Permissions scoped to tenant via `team_id`

---
*Add project-specific terms.*
