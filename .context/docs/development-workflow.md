---
status: template
generated: 2026-01-13
---

# Development Workflow

## Branching & Releases
- **Main branch**: Production-ready code
- **Develop branch**: Integration branch for features
- Feature branches: `feature/nome-funcionalidade`
- Hotfix branches: `hotfix/descricao`

## Local Development

### Setup
```bash
./vendor/bin/sail up -d
./vendor/bin/sail composer install
./vendor/bin/sail npm install
./vendor/bin/sail artisan migrate --seed
```

### Running
```bash
./vendor/bin/sail npm run dev  # Frontend hot-reload
./vendor/bin/sail artisan serve # Backend (if not using Sail HTTP)
```

### Building
```bash
./vendor/bin/sail npm run build
./vendor/bin/sail bin pint  # Code style
```

## Code Review Expectations
- All PRs require 1 approval
- Must pass:  
  ✅ Pint (code style)  
  ✅ Larastan (static analysis)  
  ✅ Pest tests
- No merge conflicts
- Documented breaking changes

## Onboarding Tasks
1. Read `/docs` folder
2. Run `reset.sh` to initialize local environment
3. Review `.context/agents` for specialized workflows
4. Pick "good first issue" from project board

---
*Customize for your team's process.*
