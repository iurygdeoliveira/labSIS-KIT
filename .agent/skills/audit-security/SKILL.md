---
name: audit-security
description: Automated security checklist and audit for Laravel/Filament applications, focusing on XSS, CSP, and IDOR.
---

# Security Audit Skill

Use this skill to inspect code for common vulnerability patterns in the context of this specific project stack.

## When to use this skill

- When the user asks to "check for vulnerabilities" or "audit security".
- Before deploying critical features involving user input or file handling.
- When reviewing Blade templates or Controllers.

## Audit Checklist

### 1. Cross-Site Scripting (XSS) in Blade

**Pattern to Search:** `!!` (unescaped output)

- **Rule**: `{{ }}` is safe. `{!! !!}` is dangerous.
- **Action**: Use `grep_search` for `{!!`.
- **Validation**: Ensure variables inside `{!! !!}` are explicitly sanitized (e.g., using `HtmlString` from a trusted source or `Purifier`).
- **Exception**: Intentionally raw HTML from the CMS (must be sanitized on save).

### 2. Content Security Policy (CSP)

**File**: `app/Http/Middleware/SecurityHeadersMiddleware.php`

- **Check**: Are we allowing `unsafe-inline` unnecessarily?
- **Check**: Are external domains (S3, R2, Analytics) whitelisted?
- **Action**: Verify if `img-src`, `script-src` includes necessary domains (e.g., `*.r2.cloudflarestorage.com`).

### 3. IDOR (Insecure Direct Object References)

**Context**: Controllers/Livewire Components accepting IDs.

- **Rule**: Never trust an ID from the client without checking ownership/policy.
- **Check**:
    - Does the route use Route Model Binding with scoping? (e.g., `->scopeBindings()`)
    - Does the controller method call `$this->authorize('update', $model)`?
    - In Filament: Do Resources use `getEloquentQuery()` with tenant scopes?

### 4. Mass Assignment

**Context**: Models.

- **Rule**: avoid `$guarded = []` unless strictly necessary and controlled.
- **Prefer**: `$fillable` with explicit fields.

## Execution Steps

1.  **Search**: Run `grep_search` patterns for potential issues.
2.  **Analyze**: Read the surrounding code of matches.
3.  **Report**: List findings classified by Severity (High/Medium/Low).
4.  **Fix**: Propose specific code changes to mitigate.
