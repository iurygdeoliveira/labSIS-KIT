# Protocolo Operacional do Agente (Skill-First & MCP)

Este documento descreve o conjunto de regras mandatórias utilizadas pelo Agente de IA (Antigravity/Gemini) neste projeto. Estas instruções são inseridas diretamente na "memória" do agente (arquivo `GEMINI.md`) para garantir alta eficiência, economia de tokens e adesão estrita aos padrões do projeto.

## Por que este protocolo existe?

Utilizamos um sistema **Skill-First** (Habilidades Primeiro) combinado com **MCPs** (Model Context Protocol). O objetivo é:

1.  **Economia Financeira (Zero Token Waste)**: Evitar que a IA gaste tokens "raciocinando" sobre arquiteturas ou soluções que já estão padronizadas. Se existe uma Skill documentada (`.agent/skills`), a IA deve usá-la cegamente em vez de tentar recriar a roda.
2.  **Qualidade e Padronização**: Ao forçar o uso de Skills, garantimos que todo código gerado (Models, Factories, Testes, etc.) siga rigorosamente as convenções de versão do projeto (Laravel 12, Filament 4, Pest 4).
3.  **Prevenção de Alucinação**: Se não houver uma Skill, a IA é proibida de "adivinhar". Ela é instruída a usar ferramentas MCP (`Laravel Boost` para docs, `Serena` para análise de código, `AI-Context` para arquitetura) para obter a "verdade" (Ground Truth) antes de escrever qualquer linha de código.

---

## 📋 Instruções Atuais (Versão em Português)

Abaixo está o conteúdo original em Português que definiu este comportamento. A versão ativa no agente foi traduzida para Inglês para melhor "compreensão" e seguimento de instruções pela LLM.

### Protocolo de Economia Extrema & Qualidade (SKILL-FIRST)

Você opera em modo de alta eficiência. Seu objetivo é **zero desperdício de tokens** e **100% de adesão aos padrões**. Para isso, você NÃO DEVE "pensar" em soluções que já foram resolvidas. Você deve SEGUIR INSTRUÇÕES.

#### 🥇 Regra de Ouro: CHECK-SKILL OBRIGATÓRIO

Antes de planejar ou escrever qualquer código, verifique se a tarefa se encaixa em uma **Skill Otimizada** (`.agent/skills/`).

**Mapa de Ativação (Se o usuário pedir...) -> (...Use esta Skill):**

1.  **"Crie um Model/Tabela/Migration"** -> `laravel-entity-scaffold`
2.  **"Crie/Ajuste um Painel Admin/Resource"** -> `filament-resource-v4`
3.  **"Crie um Teste" ou "Valide isso"** -> `pest-test-generator`
4.  **"Crie um Serviço/Lógica de Negócio"** -> `service-pattern`
5.  **"Otimize esse componente/tela"** -> `livewire-component-optimize`
6.  **"Ajuste o CSS/Design"** -> `tailwind-v4-styling`

**Por que?** Ler um `SKILL.md` custa ~200 tokens. "Deduzir" a arquitetura certa custa ~2000 tokens e tem risco de erro. **Use a Skill.**

---

#### 🥈 A Tríade de Execução MCP (Quando não houver Skill)

Se não houver Skill, use a **Tríade MCP** para economizar tokens de "tentativa e erro":

**1. 🐘 Laravel Boost (A Verdade / Ground Truth)**
_Evita alucinações de versões e sintaxe._

-   **Dúvida de Framework?** -> `search-docs` (Ex: `['filament v4 upload field']`). _Nunca adivinhe sintaxe._
-   **Dúvida de Banco?** -> `database-schema`. _Nunca adivinhe nomes de colunas._

**2. 🔮 Serena (O Cirurgião / Precision)**
_Evita ler arquivos gigantes (economia de contexto)._

-   **Precisa editar um método?** -> `find_symbol` -> `replace_symbol_body`. _Não leia o arquivo todo._
-   **Precisa inserir uma rota/config?** -> `insert_after_symbol`.

**3. 🧠 AI-Context (O Arquiteto / Big Picture)**
_Evita erros de design._

-   **Dúvida de onde colocar um arquivo?** -> Verifique a estrutura com `list_dir` ou leia `architecture.md`.

---

#### 🚫 Pecados Capitais (Desperdício de Dinheiro)

1.  **Ignorar Skills**: Tentar criar um Resource do Filament "de cabeça" e errar o import da Action (v3 vs v4).
2.  **Ler arquivos inteiros**: Usar `read_file` em um Controller de 2000 linhas para mudar 1 linha. Use Serena.
3.  **Adivinhar Bibliotecas**: Usar sintaxe do Tailwind v3 (`bg-opacity`) no projeto v4 (`bg-black/50`). Use a Skill `tailwind-v4-styling`.

**Resumo**: Se existe uma Skill, siga-a cegamente. Se não existe, use Boost para saber COMO fazer e Serena para FAZER cirurgicamente.

## Instruções Personalizadas do Projeto

### Workflow e Comunicação

-   **Testes:** Sempre proponha construir testes automatizados, mas **não crie automaticamente**. Pergunte ao usuário primeiro.
-   **Idioma:** Use **Português Brasileiro** exclusivamente. Nunca responda em inglês.
-   **Planos:** Escreva planos de implementação em português do Brasil.
-   **Confirmação:** Antes de alterar código, explique o que vai fazer e **solicite confirmação**.
-   **Escopo:** Não altere arquivos não solicitados explicitamente.

### Git Commit Workflow

Quando solicitado um commit, execute:

1. `./vendor/bin/sail bin pint --dirty`
2. `git add .`
3. `git commit -m "mensagem"`
4. `git push`

Formato Conventional Commits (PT-BR), máximo 3 tópicos:

```
<tipo>(<escopo>): <descrição>
- Item 1
- Item 2
- Item 3
```
