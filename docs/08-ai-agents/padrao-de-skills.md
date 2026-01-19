# Padrão de Nomenclatura de Skills: Action-Object

Este documento define a estratégia **"Action-Object"** (Ação-Objeto) adotada neste projeto para maximizar a eficiência e a "taxa de ativação" das Skills pelos Agentes de IA.

## Filosofia: User Intent Mapping

Pretende-se garantir que um Agente utilize uma ferramenta específica sem instrução explícita é alinhar o nome da ferramenta com a **Linguagem Natural de Comando** do usuário.

Quando um usuário solicita algo, ele geralmente usa um verbo imperativo seguido de um objeto.

> "User: **Crie** um **Model** para Produto."

Se a skill se chamar `laravel-model-scaffold`, o agente precisa fazer uma inferência de dois passos:

1. "Crie um model" significa "scaffold".
2. "laravel-model-scaffold" parece relevante.

Se a skill se chamar `scaffold-model`, a conexão é direta e neuralmente mais forte para o LLM.

## O Padrão: `<VERBO>-<OBJETO>`

Todas as skills devem seguir rigorosamente o formato:

```
[action]-[target]
```

### Exemplos de Mapeamento

| Intenção do Usuário (Prompt)           | Skill (Action-Object)  | Nome Antigo (Evitar)     |
| :------------------------------------- | :--------------------- | :----------------------- |
| "Crie um model...", "Gere..."          | `scaffold-model`       | `laravel-model-scaffold` |
| "Verifique a segurança...", "Audit..." | `audit-security`       | `security-audit`         |
| "Estilize o botão...", "CSS..."        | `style-components`     | `css-component-builder`  |
| "Teste o browser...", "Debug..."       | `debug-browser-tests`  | `browser-test-debugger`  |
| "Documente isso...", "Escreva docs..." | `write-documentation`  | `documentation-writer`   |
| "Otimize a performance...", "Lento..." | `optimize-performance` | `performance-optimizer`  |
| "Commita...", "Git push..."            | `manage-git`           | `git-workflow`           |

## Categorias de Ações Comuns

### Scaffold (Criação Estrutural)

Usado para geração de código boilerplate, arquivos e estruturas.

- `scaffold-model`
- `scaffold-controller`
- `scaffold-migration`
- `scaffold-policy`
- `scaffold-filament-resource`

### Manage (Gestão/Operação)

Usado para processos contínuos ou operações de manutenção.

- `manage-git`
- `manage-seeders`

### Audit/Optimize/Debug (Qualidade)

Usado para tarefas de análise e melhoria.

- `audit-security`
- `optimize-performance`
- `debug-browser-tests`

### Write (Documentação)

Usado para geração de conteúdo textual.

- `write-documentation`

## Benefícios Esperados

1.  **Redução de Alucinação**: O agente não precisa "adivinhar" qual ferramenta usar.
2.  **Economia de Tokens**: Prompts menores, pois não é necessário explicar qual skill usar.
3.  **Padronização**: Facilita a criação de novas skills seguindo um modelo mental claro.
