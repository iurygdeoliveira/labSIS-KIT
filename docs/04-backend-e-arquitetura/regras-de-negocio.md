# Regras de Negócio: labSIS-KIT

Esta documentação detalha as regras de negócio fundamentais do **labSIS-KIT**, servindo como um referencial para garantir a integridade da arquitetura SaaS e a qualidade educacional do projeto.

---

## 🚨 P0: Críticas (Inegociáveis)

*Se estas regras forem violadas, o sistema falha em sua missão de segurança e arquitetura.*

### 1. Isolamento Estrito de Tenant

- **Objetivo**: Garantir a segurança dos dados em uma arquitetura Multi-tenant.
- **Regra**: Um Tenant **NUNCA** deve conseguir acessar dados de outro, seja por manipulação de URL ou falhas de acesso direto. O isolamento deve ser garantido em nível de infraestrutura lógica (Global Scopes e Policies).
- **Validação**: Tentativas de acesso Cross-Tenant devem resultar em erro 403 (Proibido) ou 404 (Não Encontrado).

### 2. Polimorfismo de Papéis (RBAC Dinâmico)

- **Objetivo**: Refletir cenários do mundo real onde um usuário assume diferentes funções dependendo do contexto.
- **Regra**: O sistema permite que um único `User` possua papéis distintos em diferentes Tenants (ex: `Owner` na Empresa A e apenas `Colaborador` na Empresa B). A role é vinculada à relação (pivot) entre o usuário e o tenant, não ao usuário de forma absoluta.
- **Validação**: O sistema de permissões deve respeitar o contexto do Tenant ativo.

### 3. Proteção Contra Destruição (Tenant Deletion)

- **Objetivo**: Prevenir a perda acidental ou maliciosa de dados críticos.
- **Regra**: Somente o **Super Admin** (nível global) possui permissão para excluir um Tenant. O Owner do próprio tenant não possui acesso a esta ação destrutiva.
- **Validação**: A opção de deletar o tenant deve estar bloqueada ou inexistente para o Owner.

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

### 5. Limites de Planos (SaaS Metrics)

- **Status**: ⏳ Planejado para a V2.
- **Objetivo**: Introduzir conceitos de monetização e quotas de uso.
- **Regra**: Implementação de restrições baseadas no plano ativo (ex: limite de usuários cadastrados).

---

> **Nota**: Estas regras são monitoradas e validadas automaticamente pelos agentes de IA através da skill `validate-project-rules`.
