# Guia de Workflows do Agente (Gemini)

Este documento explica como utilizar os "Workflows" adaptados para o Português e integrados com as Skills instaladas.

## 🎯 Visão Geral

Os workflows são atalhos mentais. Quando você digita um comando, o agente entra em um "Modo" específico, focado em resolver aquele tipo de problema usando as ferramentas certas.

| Comando | Modo | Quando usar |
| :--- | :--- | :--- |
| **`/planejar`** | 🧠 Arquiteto | Antes de começar qualquer feature. Cria o plano. |
| **`/criar`** | ⚡ Construtor | Para implementar código (Backend + Frontend). |
| **`/testar`** | 🧪 Engenheiro | Para garantir qualidade (TDD). |
| **`/debugar`** | 🔍 Detetive | Quando algo quebra (Erro 500 ou Bug Visual). |
| **`/auditar`** | 🛡️ Auditor | Para Segurança e SEO antes do deploy. |

---

## 1. `/planejar` (O Arquiteto)

**Nunca comece a codar sem isso.**
Este workflow força o agente a pensar na arquitetura antes de escrever linhas de código.

*   **O que ele faz**:
    1.  Analisa seu pedido.
    2.  Faz perguntas (Portão Socrático) se algo estiver vago.
    3.  Gera um arquivo `docs/PLAN-{slug}.md` com o roteiro.

*   **Exemplo**:
    ```
    /planejar sistema de assinaturas com stripe
    ```

---

## 2. `/criar` (O Construtor)

**A fábrica de código.**
Este workflow pega o plano (ou seu pedido) e orquestra as skills de criação.

*   **O que ele faz**:
    1.  Cria Backend (Migrations, Models, Controllers).
    2.  Cria Frontend (Livewire, Blade, Tailwind).
    3.  Valida a estrutura.

*   **Exemplo**:
    ```
    /criar feature de login social
    ```

---

## 3. `/testar` (O Engenheiro)

**Garantia de Qualidade.**
Este workflow blinda o código que você já escreveu.

*   **O que ele faz**:
    1.  Cria testes para funcionalidades existentes.
    2.  Valida que o código atende aos requisitos (Verificação).
    3.  Cria uma rede de segurança contra regressão.

*   **Exemplo**:
    ```
    /testar checkout flow
    ```

---

## 4. `/debugar` (O Detetive)

**Resolve o mistério.**
Este workflow investiga sistematicamente, sem "chutar".

*   **O que ele faz**:
    1.  **Backend**: Analisa logs, exceptions e banco de dados.
    2.  **Frontend**: Analisa console do navegador, CSS e HTML.

*   **Exemplo**:
    ```
    /debugar erro 500 ao salvar produto
    ```

---

## 5. `/auditar` (O Auditor)

**O pente fino final.**
Este workflow deve ser rodado antes de qualquer entrega importante.

*   **O que ele faz**:
    1.  **Segurança**: Busca senhas expostas e falhas de injeção.
    2.  **SEO**: Verifica tags e estrutura para o Google.
    3.  **Qualidade**: Roda linters (Pint/Stan).

*   **Exemplo**:
    ```
    /auditar projeto completo
    ```

---

> **Dica Pro:** Combine os comandos. Comece com `/planejar`, aprove o plano, e então dê o comando `/criar` para executar o plano. Se algo der errado, chame o `/debugar`.
