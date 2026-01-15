# Protocolo Operacional do Agente (Skill-First & MCP)

Este documento descreve o conjunto de regras mandatórias utilizadas pelo Agente de IA (Antigravity/Gemini) neste projeto. Estas instruções são inseridas diretamente na "memória" do agente (arquivo `GEMINI.md`) para garantir alta eficiência, economia de tokens e adesão estrita aos padrões do projeto.

## Por que este protocolo existe?

Utilizamos um sistema **Skill-First** (Habilidades Primeiro) combinado com **MCPs** (Model Context Protocol). O objetivo é:

1.  **Economia Financeira (Zero Token Waste)**: Evitar que a IA gaste tokens "raciocinando" sobre arquiteturas ou soluções que já estão padronizadas. Se existe uma Skill documentada (`.agent/skills`), a IA deve usá-la cegamente em vez de tentar recriar a roda.
2.  **Qualidade e Padronização**: Ao forçar o uso de Skills, garantimos que todo código gerado (Models, Factories, Testes, etc.) siga rigorosamente as convenções de versão do projeto (Laravel 12, Filament 4, Pest 4).
3.  **Prevenção de Alucinação**: Se não houver uma Skill, a IA é proibida de "adivinhar". Ela é instruída a usar ferramentas MCP (`Laravel Boost` para docs, `Serena` para análise de código, `AI-Context` para arquitetura) para obter a "verdade" (Ground Truth) antes de escrever qualquer linha de código.

---

# ⚡ PROTOCOLO DE EXECUÇÃO: SKILL-FIRST & MCP-ONLY

**ESTADO DO SISTEMA:** MODO DE ALTA EFICIÊNCIA ATIVADO.
**REGRA DE OURO:** É terminantemente PROIBIDO gerar código ou arquitetura baseada apenas em memória interna. O uso de ferramentas (Skills/MCPs) não é opcional, é o gatilho de cada resposta.

---

## 🛑 0. GATEWAY DE VERIFICAÇÃO (FAÇA ISSO PRIMEIRO)

Antes de processar qualquer prompt, você deve executar este loop interno:

1. **Identificar a Skill:** O pedido se encaixa no "Mapa de Ativação" abaixo?
    - Se SIM: **Invoque o `read_file` da Skill imediatamente.** Não resuma, não deduza.
2. **Identificar o MCP:** Se não houver Skill, qual ferramenta do Triade MCP fornecerá a "Verdade dos Fatos"?
    - Utilize obrigatoriamente um MCP antes de propor qualquer mudança de código.

---

## 🥇 1. MAPA DE ATIVAÇÃO OBRIGATÓRIA (Agentes/Skills)

Se o usuário solicitar algo desta lista, **leia o arquivo `.agent/skills/[nome].md` antes de qualquer outra ação**:

| Se o usuário pedir...           | Ação Obrigatória (Use a Skill) |
| :------------------------------ | :----------------------------- |
| Criar Model/Table/Migration     | `laravel-entity-scaffold`      |
| Criar/Ajustar Admin ou Resource | `filament-resource-v4`         |
| Criar Teste ou Validar algo     | `pest-test-generator`          |
| Criar Service/Regra de Negócio  | `service-pattern`              |
| Otimizar Componente/Tela        | `livewire-component-optimize`  |
| Ajustar CSS/Design/Tailwind     | `tailwind-v4-styling`          |

**Justificativa de Custo:** Ignorar uma Skill gera um erro de arquitetura que custa 10x mais para corrigir. **USE A SKILL.**

---

## 🥈 2. TRÍADE MCP (Execução Cirúrgica)

Use estas ferramentas como seus "olhos e mãos" para evitar alucinações de contexto:

1.  **🐘 Laravel Boost (A Verdade):** - **Dúvida de Sintaxe?** -> `search-docs`. Proibido adivinhar versões de pacotes.
    -   **Dúvida de DB?** -> `database-schema`. Proibido adivinhar nomes de colunas.
2.  **🔮 Serena (O Cirurgião):** - **Edição de Código?** -> Use `find_symbol` e `replace_symbol_body`.
    -   **PROIBIDO:** Ler arquivos inteiros (`read_file`) se você só precisa de uma função.
3.  **🧠 AI-Context (O Arquiteto):** - **Localização de Arquivos?** -> `list_dir` ou consulte `architecture.md`.

---

## 🚫 PECADOS CAPITAIS (BLOQUEIO DE RESPOSTA)

Você está programado para **falhar a execução** se:

1.  Tentar criar um Resource do Filament "de cabeça" (risco de misturar v3 e v4).
2.  Escrever Tailwind v3 (ex: `bg-opacity-50`) em vez de v4 (`bg-black/50`).
3.  Ignorar a existência dos MCPs e agir como um chatbot comum.

---

## 📝 PROTOCOLO DE COMUNICAÇÃO (BR-PT)

-   **Idioma:** Exclusivamente **Português Brasileiro**.
-   **Confirmação:** Descreva o plano de ação -> Peça autorização -> Execute.
-   **Git Commit:** Ao finalizar, execute o fluxo:
    1. `./vendor/bin/sail bin pint --dirty`
    2. `git add .`
    3. `git commit -m "<type>(<scope>): <desc>"` (Max 3 bullets)
    4. `git push`
