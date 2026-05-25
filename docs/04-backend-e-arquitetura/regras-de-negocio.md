# Regras de Negócio: labSIS-KIT

Esta documentação detalha as regras de negócio fundamentais do **labSIS-KIT**, servindo como um referencial para garantir a integridade da arquitetura SaaS e a qualidade educacional do projeto.

---

## 🚨 P0: Críticas (Inegociáveis)

*Se estas regras forem violadas, o sistema falha em sua missão de segurança e arquitetura.*

### 1. Isolamento estrito entre equipes (teams)

- **Objetivo**: Garantir a segurança dos dados em uma arquitetura multi-organização (single database).
- **Regra**: Uma equipe (**`Team`**) **não** pode acessar dados de outra, seja por manipulação de URL (`slug`), troca de contexto no Filament ou acesso direto a IDs. O isolamento deve ser garantido em nível lógico (policies, queries escopadas ao `Team` atual do painel, FKs `team_id` onde existirem).
- **Validação**: Tentativas de acesso **cross-team** devem resultar em **403** (proibido) ou **404** (não encontrado), conforme o caso.

### 2. Polimorfismo de papéis (RBAC por contexto)

- **Objetivo**: Refletir cenários em que um usuário assume funções diferentes conforme a organização.
- **Regra**: Um único `User` pode ter papéis distintos em **teams** diferentes (ex.: `Owner` na Empresa A e apenas `User` na Empresa B). As roles Spatie são vinculadas ao **`team_id`** (igual a `teams.id`). O pivot **`team_members`** do FilaTeams reflete o papel na UI e é **sincronizado** com o Spatie pelo `MembershipObserver`.
- **Validação**: Verificações `can`, `hasRole`, `hasPermissionTo` devem respeitar o **team ativo** (`SpatieTeamResolver` + `TeamSyncMiddleware`).

### 3. Proteção contra destruição (exclusão de organização)

- **Objetivo**: Reduzir perda acidental ou maliciosa de dados ligados a uma equipe.
- **Regra**: Ações destrutivas (ex.: exclusão de **Team** no painel admin, exclusão/leave no fluxo FilaTeams) devem seguir **policies**, gates do pacote e regras de negócio explícitas (ex.: não excluir team com membros no recurso admin, quando aplicável). Apenas perfis autorizados executam exclusão; owners/colaboradores não “furam” o isolamento de outras equipes.
- **Validação**: UI e endpoints negam operação quando a policy falhar; testes cobrem fluxos críticos de acesso.

---

## ⚠️ P1: Importantes (Experiência do Usuário)

*Regras que garantem a fluidez e a percepção de qualidade do kit.*

### 4. Onboarding "One-Click"

- **Objetivo**: Facilitar a instalação e o primeiro contato de novos desenvolvedores.
- **Regra**: O script de instalação (`install.php`) deve realizar o setup completo, incluindo banco de dados, migrações e seeds essenciais, sem necessidade de intervenções manuais complexas.
- **Validação**: O sistema deve estar funcional imediatamente após a execução do script em um ambiente limpo.

---

## ℹ️ P2: Desejáveis (Evolução de Escopo)

*Funcionalidades planejadas para versões futuras.*

### 5. Limites de planos (SaaS metrics)

- **Status**: ⏳ Planejado para a V2.
- **Objetivo**: Introduzir conceitos de monetização e quotas de uso.
- **Regra**: Implementação de restrições baseadas no plano ativo (ex.: limite de usuários cadastrados por team).

---

> **Nota**: Estas regras são monitoradas e validadas automaticamente pelos agentes de IA através da skill `validate-project-rules`.

### Glossário rápido

| Termo na doc / Filament | Significado no labSIS-KIT |
|-------------------------|---------------------------|
| Tenant (API Filament)   | Instância de **`Team`** na rota `/user/{slug}` |
| `getTenant()`           | Retorna o **`Team`** atual do painel `user` |
| `team_id` (Spatie)      | FK para **`teams.id`** |
| `team_id` (domínio)     | Mesma organização (`teams`) quando presente em tabelas de negócio |
