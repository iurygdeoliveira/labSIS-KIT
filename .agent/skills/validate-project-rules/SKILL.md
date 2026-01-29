---
name: validate-project-rules
description: Lista Técnica de Regras de Negócio do labSIS-KIT. Define comportamento esperado para isolamento, permissões e instalação.
tools: read_file, grep_search, view_file, run_command
---

# Regras de Negócio: labSIS-KIT (Referencial Educacional)

Lista priorizada de comportamentos esperados do sistema.
Estas regras garantem que o kit funcione como um referencial teórico-prático de qualidade.

---

## 🚨 P0: Críticas (O sistema NÃO pode falhar aqui)
*Se estas regras quebrarem, o kit falha em sua missão educacional de segurança e arquitetura.*

### 1. Isolamento Estrito de Tenant
- **Motivo**: Ensinar arquitetura SaaS segura evitando vazamento de dados (erro #1).
- **Regra**: Um Tenant **NUNCA** deve conseguir acessar dados de outro, nem por falha de acesso direto (URL manipulation). O isolamento deve ser garantido por Global Scopes/Policies.
- **Validação**: Testes automatizados tentando acessar recursos Cross-Tenant devem falhar (403/404).

### 2. Polimorfismo de Papéis (RBAC Dinâmico)
- **Motivo**: Refletir cenários reais onde uma pessoa possui múltiplos contextos.
- **Regra**: O sistema deve permitir que o mesmo `User` tenha papéis diferentes em Tenants diferentes (Ex: `Owner` na Empresa A, `User` na Empresa B). A role é vinculada ao relacionamento (pivot), não ao usuário absoluto.
- **Validação**: Verificar se usuário com múltiplas associações tem permissões corretas em cada contexto ativo.

### 3. Proteção Contra Destruição (Tenant Deletion)
- **Motivo**: Prevenir perda de dados acidental ou maliciosa por parte de um Owner.
- **Regra**: Apenas o **Super Admin** (Global) tem permissão para deletar um Tenant. O Owner do tenant **NÃO** deve ter acesso a essa ação destrutiva.
- **Validação**: Tentar deletar o próprio tenant logado como Owner -> Ação Bloqueada/Inexistente.

---

## ⚠️ P1: Importantes (O sistema funciona, mas a experiência degrada)
*Problemas aqui afetam a percepção de qualidade do kit.*

### 4. Onboarding "One-Click"
- **Motivo**: Facilitar a entrada de iniciantes no mundo SaaS.
- **Regra**: O script `install.php` deve realizar o setup completo (Banco, Migrations, Seeds Essenciais) sem exigir configuração manual complexa.
- **Validação**: Rodar `php install.php` em ambiente limpo e ter sistema funcional imediatamente.

---

## ℹ️ P2: Desejáveis (Escopo Futuro)

### 5. Limites de Planos (SaaS Metrics)
- **Motivo**: Ensinar monetização e quotas.
- **Regra**: Implementar lógica de limites (ex: máx usuários por plano).
- **Status**: ⏳ Adiado para V2.

---

**Gerado por**: Product Manager Agent
**Data**: 2026-01-27
