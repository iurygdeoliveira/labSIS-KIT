# Antigravity Skills Protocol

## Philosophy
We follow the **Action-Object** pattern (`verb-noun`) for all agent skills to map user intent directly to tools. This ensures a consistent "Language of Command".

## The Skill Catalog
The following skills are available in `.agent/skills` and MUST be used when the user intent matches:

### Manage (Operations)
- `manage-git`: Git workflow (commit, push) with conventional commits.
- `manage-seeders`: Database seeding with JSON sources and idempotency.

### Scaffold (Structure)
- `scaffold-model`: Eloquent Models (strict types, modern casting).
- `scaffold-migration`: Database Migrations (anonymous classes).
- `scaffold-factory`: Model Factories (states, sequences).
- `scaffold-controller`: Controllers (FormRequests, Resource responses).
- `scaffold-filament-resource`: Filament v4 Resources (Clusters, Tabs, Spatie Media).
- `scaffold-filament-page`: Custom Filament Pages (Navigation Groups).
- `scaffold-service`: Service classes for business logic.
- `scaffold-policy`: Authorization Policies.
- `scaffold-observer`: Eloquent Observers.
- `scaffold-listener`: Event Listeners.
- `scaffold-middleware`: HTTP Middleware (bootstrap/app.php).

### Quality & Testing
- `audit-security`: Security checklist (XSS, CSP, IDOR).
- `optimize-performance`: Performance tuning (N+1, Caching).
- `generate-tests`: Pest v4 generation (Unit, Feature, Arch).
- `debug-browser`: Pest v4 Browser testing (Dusk/Panther).

### Frontend
- `style-components`: Modular CSS for Filament components.
- `style-tailwind`: Tailwind v4 configuration (@theme).
- `optimize-livewire`: Livewire 3 optimization (Computed, Locked).

### Documentation
- `write-documentation`: Creating and updating project docs.

## Integration Rule (The Triad)
When a user asks to [ACTION] a [OBJECT]:

1.  **IDENTIFY**: Look up the corresponding Skill above.
2.  **READ**: Read the `SKILL.md` file to understand the *Rules* and *Process*.
3.  **EXECUTE**: Use **Serena** tools (`find_symbol`, `insert_after_symbol`) to apply the changes surgically based on the Skill's rules.

> **Golden Rule**: The Skill tells you **WHAT** to do. Serena tells you **WHERE** to do it.
