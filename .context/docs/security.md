---
status: template
generated: 2026-01-13
---

# Security & Compliance

## Authentication & Authorization

### Authentication
- Laravel Sanctum for session-based auth
- Passwords hashed using bcrypt (cost: 12)
- 2FA optional (not implemented in base kit)

### Authorization
- Spatie Laravel Permission (RBAC)
- Policies for all resources (`UserPolicy`, `TenantPolicy`, etc.)
- Gate checks: `Gate::allows('users.view')`

## Secrets & Sensitive Data

### Storage
- `.env` file (excluded from git)
- Environment variables for:
  - `APP_KEY` (encryption)
  - `DB_PASSWORD`
  - `AWS_SECRET_ACCESS_KEY`
  - `MAIL_PASSWORD`

### Best Practices
- Rotate `APP_KEY` if compromised
- Use Laravel Vault for production secrets
- Never commit `.env`

## Compliance

### LGPD/GDPR Considerations
- Soft deletes allow data recovery
- User data export/deletion capabilities (to implement)
- Audit trail via `authentication_logs` table

### Internal Policies
- Access logs retained for 90 days
- Password minimum complexity: 8 chars
- Failed login lockout: 5 attempts / 15 min

## Incident Response

### On-Call
- Monitor: Laravel Pulse + application logs
- Alerts: Configured via hosting provider

### Escalation
1. Identify issue via logs/monitoring
2. Isolate affected tenant if needed
3. Apply hotfix via `hotfix/*` branch
4. Post-mortem documentation

---
*Adapt to your compliance requirements.*
