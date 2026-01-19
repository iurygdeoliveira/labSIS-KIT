---
name: write-documentation
description: Manages project documentation creation and updates, ensuring synchronization with the codebase.
---

# Documentation Writer Skill

Use this skill to create, update, and audit documentation files in the `docs/` directory.

## When to use this skill

- When adding a new feature that requires explanation (e.g., a new architectural pattern or complex flow).
- When the user asks to "update docs" or "document this".
- When performing an audit of documentation vs. implementation.

## Workflow

### 1. Structure

Documentation should be organized in `docs/` with numbered prefixes for ordering:

- `docs/01-instalacao-e-setup/`
- `docs/02-autenticacao-e-seguranca/`
- `docs/03-ui-e-customizacao/`
- `docs/04-backend-e-arquitetura/`
- `docs/05-otimizacoes/`
- `docs/06-testes/`
- `docs/07-qualidade-de-codigo/`
- `docs/08-ai-agents/`

### 2. File Format

- **Markdown**: Always use `.md`.
- **Header**: Start with a clear `# Title`.
- **Context**: Explain _why_ this feature exists, not just _how_ it works.
- **Code Blocks**: Use language-specific fencing (e.g., ```php).

### 3. Synchronization (Audit Mode)

When verifying documentation:

1.  **Read the Doc**: Parse the existing `.md` file.
2.  **Verify Code**: Check the referenced classes/files in the codebase.
3.  **Identify Drift**: Note differences in method signatures, config values, or file paths.
4.  **Update**: rewriting the documentation to match the _current_ code reality.

### 4. New Document Template

````markdown
# [Feature Name]

## Visão Geral

[Breve descrição do que é e para que serve]

## Como Funciona

[Explicação técnica]

## Exemplos de Código

```php
// Exemplo prático
```
````

## Configuração Relacionada

- `config/feature.php`
- `.env` keys

```

```
