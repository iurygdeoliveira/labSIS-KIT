---
status: filled
generated: 2026-01-15
agents:
    - type: "code-reviewer"
      role: "Revisar mudanças de código seguindo PSR-12 e padrões Laravel 12"
    - type: "bug-fixer"
      role: "Analisar bugs e erros usando Laravel Debugbar e Logs"
    - type: "feature-developer"
      role: "Implementar features seguindo arquitetura Services + Filament Resources"
    - type: "refactoring-specialist"
      role: "Identificar melhorias usando Larastan e Rector"
    - type: "test-writer"
      role: "Escrever testes com Pest 4 (unit, feature, browser)"
    - type: "documentation-writer"
      role: "Documentar em /docs seguindo estrutura existente"
    - type: "performance-optimizer"
      role: "Otimizar usando Laravel Pulse, Redis e #[Computed]"
    - type: "security-auditor"
      role: "Auditar seguindo checklist de segurança do projeto"
    - type: "backend-specialist"
      role: "Desenvolver Services, Migrations e Models com UUIDs"
    - type: "architect-specialist"
      role: "Manter padrões de multi-tenancy e RBAC"
    - type: "devops-specialist"
      role: "Gerenciar Docker Sail e CI/CD"
    - type: "database-specialist"
      role: "Otimizar PostgreSQL e criar migrations"
docs:
    - "project-overview.md"
    - "architecture.md"
    - "development-workflow.md"
    - "testing-strategy.md"
    - "glossary.md"
    - "data-flow.md"
    - "security.md"
    - "tooling.md"
phases:
    - id: "phase-1"
      name: "Discovery & Alignment"
      prevc: "P"
      status: "completed"
    - id: "phase-2"
      name: "Implementation & Iteration"
      prevc: "E"
      status: "in_progress"
    - id: "phase-3"
      name: "Validation & Handoff"
      prevc: "V"
      status: "pending"
---

# Configuração Inicial do Projeto LabSIS-KIT

> Kit SaaS completo com Laravel 12 + Filament 4 para desenvolvimento acelerado de aplicações multi-tenant

## Task Snapshot

-   **Primary goal:** Fornecer base sólida e rica em recursos para acelerar desenvolvimento de aplicações SaaS seguindo melhores práticas Laravel.
-   **Success signal:** Desenvolvedores conseguem criar novos recursos seguindo padrões estabelecidos, testes passam, e sistema escala horizontalmente.
-   **Key references:**
    -   [README principal](../../../README.md)
    -   [Documentação completa](/docs)
    -   [Agent Handbook](../agents/README.md)

## Codebase Context

-   **Propósito**: SaaS Starter Kit multi-tenant com gestão de mídia e segurança empresarial
-   **Stack principal**:
    -   PHP 8.5.1 + Laravel 12.46 + Filament 4.5.2
    -   PostgreSQL + Redis (cache)
    -   Livewire 3.7 + Flux UI 2.10 + Tailwind CSS 4.1
-   **Ferramentas de qualidade**:
    -   Pest 4.3 (testes), Larastan 3.8 (análise estática), Rector 2.3, Pint 1.27
-   **Infraestrutura**: Docker (Laravel Sail 1.52)
-   **Arquitetura**:
    -   UUIDs como primary keys (via `UuidTrait`)
    -   Services layer para lógica de negócios
    -   Multi-tenancy com model `Tenant` + `tenant_user` pivot
    -   RBAC granular via Spatie Permission

### MCPs Integrados (Model Context Protocol)

Este projeto utiliza **3 servidores MCP** para inteligência contextual:

1. **Laravel Boost** ([docs](/docs/08-ai-agents/laravel-boost.md))

    - Fornece contexto Laravel (versões, schema DB, rotas, Artisan)
    - Executa Tinker para debug rápido
    - Busca documentação específica por versão

2. **Serena** ([docs](/docs/08-ai-agents/serena.md))

    - Navegação semântica via LSP
    - Busca/refatora símbolos (classes, métodos)
    - Mantém memórias do projeto

3. **AI-Context** ([docs](/docs/08-ai-agents/integracao-context.md))
    - Scaffolding em `.context/` (docs, agents, plans)
    - Playbooks especializados (backend, test writer, etc)
    - Padrões arquiteturais documentados

## Agent Lineup

| Agent                 | Responsabilidade neste plano                                             | Playbook                                                    |
| --------------------- | ------------------------------------------------------------------------ | ----------------------------------------------------------- |
| Backend Specialist    | Criar Services, Models, Migrations seguindo padrões UUID e multi-tenancy | [Backend Specialist](../agents/backend-specialist.md)       |
| Architect Specialist  | Manter decisões arquiteturais (ADRs) e padrões de isolamento             | [Architect Specialist](../agents/architect-specialist.md)   |
| Test Writer           | Garantir cobertura com Pest (unit, feature, browser tests)               | [Test Writer](../agents/test-writer.md)                     |
| Code Reviewer         | Validar PSR-12, Larastan level 9, e convenções do projeto                | [Code Reviewer](../agents/code-reviewer.md)                 |
| Security Auditor      | Validar contra checklist de segurança (2FA, IDOR, Policies)              | [Security Auditor](../agents/security-auditor.md)           |
| Documentation Writer  | Documentar em `/docs` seguindo estrutura de categorias existente         | [Documentation Writer](../agents/documentation-writer.md)   |
| Performance Optimizer | Aplicar otimizações (#[Computed], eager loading, Redis)                  | [Performance Optimizer](../agents/performance-optimizer.md) |
| Database Specialist   | Criar migrations eficientes, indexes e otimizações PostgreSQL            | [Database Specialist](../agents/database-specialist.md)     |

## Documentation Touchpoints

| Guide                       | File                                                       | Conteúdo atual                                   |
| --------------------------- | ---------------------------------------------------------- | ------------------------------------------------ |
| Visão Geral do Projeto      | [project-overview.md](../docs/project-overview.md)         | Stack real + módulos core (Tenancy, Auth, Media) |
| Arquitetura                 | [architecture.md](../docs/architecture.md)                 | UUIDs, Services layer, diagrama ERD, Policies    |
| Workflow de Desenvolvimento | [development-workflow.md](../docs/development-workflow.md) | Uso de Sail, Pint, Larastan, Pest                |
| Estratégia de Testes        | [testing-strategy.md](../docs/testing-strategy.md)         | Pest 4 browser tests + feature/unit              |
| Glossário                   | [glossary.md](../docs/glossary.md)                         | Termos: Tenant, RBAC, UUID, SPA mode             |
| Fluxo de Dados              | [data-flow.md](../docs/data-flow.md)                       | Relação User ↔ Tenant, Upload de mídia           |
| Segurança                   | [security.md](../docs/security.md)                         | 2FA, Policies, IDOR prevention, audit log        |
| Ferramentas                 | [tooling.md](../docs/tooling.md)                           | Sail, Pint, Larastan, Rector, Debugbar           |

## Risk Assessment

### Identified Risks

| Risk                                           | Probability | Impact   | Mitigation Strategy                                    | Owner                 |
| ---------------------------------------------- | ----------- | -------- | ------------------------------------------------------ | --------------------- |
| Breaking changes em Laravel 12 (versão nova)   | Medium      | High     | Usar Laravel Boost para docs específicas de versão     | Backend Specialist    |
| Complexidade de multi-tenancy mal implementada | Low         | Critical | Seguir padrões estabelecidos em `TenantScope` + testes | Architect             |
| Performance degradada por N+1 queries          | Medium      | Medium   | Usar Debugbar + Pulse, aplicar eager loading           | Performance Optimizer |
| Vulnerabilidades de segurança (IDOR, XSS)      | Medium      | High     | Revisar com Security Auditor + policies obrigatórias   | Security Auditor      |

### Dependencies

-   **Internas**:
    -   Spatie Permission (RBAC)
    -   Spatie Media Library (gestão de arquivos)
    -   Filament 4 (admin panels)
-   **Externas**:
    -   PostgreSQL 15+
    -   Redis (cache e sessions)
    -   Docker (ambiente via Sail)
-   **Técnicas**:
    -   PHP 8.5+ (typed properties, enums)
    -   Node.js 18+ (Vite build)

### Assumptions

-   PostgreSQL será usado em produção (não MySQL)
-   Multi-tenancy é via coluna `team_id` (não multi-database)
-   Todos os models core usam UUIDs, não auto-increment
-   Se assumido errado: migração de IDs seria necessária (alto impacto)

## Resource Estimation

### Time Allocation

| Phase                    | Estimated Effort   | Calendar Time   | Team Size  |
| ------------------------ | ------------------ | --------------- | ---------- |
| Phase 1 - Discovery      | 3 person-days      | 1 semana        | 1-2 devs   |
| Phase 2 - Implementation | 10 person-days     | 2-3 semanas     | 2-3 devs   |
| Phase 3 - Validation     | 2 person-days      | 3-5 dias        | 1 dev + QA |
| **Total**                | **15 person-days** | **4-5 semanas** | **-**      |

### Required Skills

-   **Laravel 12** (bleeding edge, docs via Laravel Boost)
-   **Filament 4** (Resources, Actions, Forms)
-   **Multi-tenancy** (isolamento lógico de dados)
-   **PostgreSQL** (migrations, indexes, jsonb)
-   **Pest 4** (Browser testing com Playwright)
-   **Docker/Sail** (ambiente local)
-   **Spatie Packages** (Permission, Media Library)
-   **MCPs**: Laravel Boost, Serena, AI-Context para desenvolvimento assistido

### Resource Availability

-   **Disponível**: 2-3 desenvolvedores full-time
-   **Bloqueado**: Nenhum conflito identificado
-   **Escalação**: Iury Oliveira (@iurygdeoliveira)

## Working Phases

### Phase 1 — Discovery & Alignment ✅ CONCLUÍDA

**Steps**

1. ✅ Setup inicial do projeto via Laravel Installer ou clone manual
2. ✅ Configuração do Docker Sail + PostgreSQL + Redis
3. ✅ Seeders de usuários (admin, tenant owners, users)
4. ✅ Estrutura de documentação em `/docs`
5. ✅ Integração com AI agents (.context)

**Commit Checkpoint**

-   Commits históricos documentam setup inicial (ver histórico Git)

### Phase 2 — Implementation & Iteration 🔄 EM PROGRESSO

**Recursos já implementados**:

-   ✅ Gestão de Tenants (CRUD, isolamento de dados)
-   ✅ Gestão de Roles/Permissions (hierarquia Admin/Owner/User)
-   ✅ Gestão de Mídias (upload, preview, FFmpeg para vídeos)
-   ✅ Gestão de Usuários (CRUD, suspensão, 2FA)
-   ✅ Login unificado para múltiplos painéis
-   ✅ Edição de perfil (avatar, 2FA, configurações)
-   ✅ Widgets customizados
-   ✅ Landing page/website
-   ✅ Histórico de autenticação (audit log)
-   ✅ Templates de e-mail (preview no painel)

**Próximos itens** (conforme README):

-   [ ] Impersonação de usuários
-   [ ] Edição de mails por tenant

**Padrões a seguir**:

-   Use `vendor/bin/sail artisan make:*` para gerar arquivos
-   Models devem usar `UuidTrait`
-   Lógica complexa vai em Services, não Controllers
-   Form Requests para validações (array-based rules)
-   Testes Pest para toda feature nova

**Commit Checkpoint**

-   Criar commits descritivos seguindo Conventional Commits em PT-BR
-   Exemplo: `feat(tenants): adiciona impersonação de usuários`

### Phase 3 — Validation & Handoff (PENDENTE)

**Steps**

1. **Testes automatizados**:
    - `vendor/bin/sail artisan test --compact`
    - Browser tests para UI críticas (Pest 4)
2. **Análise estática**:
    - `vendor/bin/sail composer analyse` (Larastan level 9)
    - `vendor/bin/sail bin pint` (formatação)
3. **Performance**:
    - Revisar N+1 queries via Debugbar
    - Validar cache Redis funcionando
4. **Segurança**:
    - Validar policies em todos os Resources
    - Testar IDOR prevention
    - Confirmar 2FA funcionando
5. **Documentação**:
    - Atualizar `/docs` com novos recursos
    - Atualizar README com features adicionadas

**Evidências obrigatórias**:

-   Screenshot dos testes passando
-   Relatório do Larastan sem erros level 9
-   Evidência de teste manual de feature crítica

**Commit Checkpoint**

-   `chore(plan): validação completa da fase 3`

## Rollback Plan

### Rollback Triggers

-   Bugs críticos afetando core (tenancy, auth)
-   Degradação de performance >30% em queries principais
-   Violações de segurança (IDOR, privilege escalation)
-   Testes falhando em CI/CD
-   Erros de produção >5% de requisições

### Rollback Procedures

#### Phase 1 Rollback

-   **Ação**: Remover containers Docker, restaurar `.env.example`
-   **Impacto de dados**: Nenhum (apenas ambiente local)
-   **Tempo estimado**: <30 minutos

#### Phase 2 Rollback

-   **Ação**:
    1. `git revert HEAD~N` (reverter commits problemáticos)
    2. `vendor/bin/sail artisan migrate:rollback` (reverter migrations)
    3. `vendor/bin/sail composer install` (restaurar dependências)
-   **Impacto de dados**: Possível perda de dados em tabelas novas
-   **Tempo estimado**: 1-2 horas

#### Phase 3 Rollback

-   **Ação**: Deployment rollback via Git tag anterior
-   **Impacto de dados**: Sincronização via backup PostgreSQL
-   **Tempo estimado**: 2-4 horas (dependendo de infra produção)

### Post-Rollback Actions

1. Criar Issue no GitHub documentando falha
2. Notificar equipe via canal de comunicação
3. Post-mortem em 24h (análise de root cause)
4. Atualizar plano antes de retry

## Evidence & Follow-up

### Artifacts obrigatórios

-   **Logs**: `storage/logs/laravel.log` (últimos 100 erros)
-   **PRs**: Links de Pull Requests no GitHub
-   **Tests**: Output de `vendor/bin/sail artisan test --compact`
-   **Static Analysis**: Output de `vendor/bin/sail composer analyse`
-   **Performance**: Screenshots do Laravel Pulse
-   **Docs**: Arquivos em `/docs` atualizados

### Follow-up Actions

-   [ ] Criar templates de e-mail por tenant (próximo recurso)
-   [ ] Implementar impersonação de usuários (próximo recurso)
-   [ ] Configurar CI/CD no GitHub Actions
-   [ ] Configurar ambiente de staging

### Owners

-   **Tech Lead**: Iury Oliveira (@iurygdeoliveira)
-   **Backend**: Backend Specialist Agent
-   **QA**: Test Writer Agent
-   **Docs**: Documentation Writer Agent

---

**Última atualização**: 2026-01-15  
**Status**: Fase 2 em progresso, projeto maduro e estável
