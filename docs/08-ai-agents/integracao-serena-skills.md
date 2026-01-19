# Integração Serena + Skills: O Protocolo de Execução Cirúrgica

Este documento detalha como o MCP **Serena** (Semantic Code Navigation) deve ser utilizado em conjunto com as **Skills** do Antigravity para maximizar a precisão das alterações de código.

## Conceito Fundamental

Para uma alteração de código perfeita, o Agente precisa de duas coisas:

1.  **O PROCESSO (The "HOW")**: Definido pela **Skill**. (e.g., "Use strict types", "Adicione este método").
2.  **A PRECISÃO (The "WHERE")**: Provida pelo **Serena**. (e.g., "Insira na linha 42", "Substitua o corpo do método `store`").

> **Regra de Ouro**: A Skill dita as regras, o Serena executa a cirurgia.

## Workflow Integrado

Quando o Agente ativa uma Skill (ex: `scaffold-controller`), ele deve seguir este fluxo mental:

### 1. Localização (Serena Reconnaissance)

Antes de editar, o agente deve entender o contexto usando ferramentas do Serena.

- **Ferramenta**: `serena_find_symbol` ou `serena_get_symbols_overview`
- **Uso**: "Encontre o método `store` no `UserController`."
- **Por que?**: Para garantir que não estamos sobrescrevendo código existente cegamente e para pegar o contexto de imports.

### 2. Definição da Alteração (Skill Logic)

O agente lê o `SKILL.md` para saber **o que** escrever.

- **Skill**: `scaffold-controller` diz: "Use FormRequests" e " Injete o Service".
- **Agente**: Prepara o código na memória seguindo essas regras.

### 3. Execução Cirúrgica (Serena Operation)

O agente aplica a alteração usando ferramentas de edição baseadas em símbolos do Serena.

- **Ferramenta**: `serena_insert_after_symbol`, `serena_replace_symbol_body` ou `serena_insert_before_symbol`.
- **Uso**: "Insira o novo método `update` após o método `store`."
- **Benefício**: Não depende de números de linha (que mudam) e evita erros de "search & replace" com strings ambíguas.

## Casos de Uso Específicos

### Caso 1: Injeção de Dependência

- **Cenário**: Adicionar um Service ao Construtor.
- **Serena**: `serena_find_symbol(name_path="UserController/__construct")`.
- **Ação**: `serena_replace_symbol_body` atualizando a assinatura para incluir `protected UserService $service`.

### Caso 2: Registro Global (Provider/Kernel)

- **Cenário**: Registrar um novo Observer no `AppServiceProvider`.
- **Serena**: `serena_find_symbol(name_path="AppServiceProvider/boot")`.
- **Ação**: `serena_insert_after_symbol` (dentro do método) para adicionar `User::observe(UserObserver::class);`.

### Caso 3: Refatoração Segura

- **Cenário**: Renomear um método usado em vários lugares.
- **Serena**: `serena_find_referencing_symbols` (para achar quem usa) -> `serena_rename_symbol` (para renomear atomicamente em todo o projeto).

## Resumo para o Agente

Ao ler uma Skill, procure por instruções que digam **"Onde colocar"**. Traduza essas instruções imediatamente para chamadas de ferramentas do **Serena**.

- "Adicione no final da classe" -> `serena_insert_after_symbol` (último método).
- "Substitua a lógica de validação" -> `serena_replace_symbol_body`.
- "Verifique se já existe" -> `serena_find_symbol`.
