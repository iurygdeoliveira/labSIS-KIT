# Serena - MCP de Navegação Semântica de Código

## 🎯 O que é?

**Serena** é um servidor MCP de terceiros que fornece navegação semântica de código usando **Language Server Protocol (LSP)**. Funciona como uma "IDE inteligente" para agentes de IA.

## 🧠 Principais Capacidades

### 1. Busca Semântica de Símbolos

Em vez de buscar texto bruto (grep), Serena entende a **estrutura do código**:

```php
// Encontra a definição exata de uma classe/método
find_symbol("UserController/store")
```

### 2. Análise de Referências

Descobre onde um símbolo é usado em toda a codebase:

```php
// "Onde a classe Tenant é referenciada?"
find_referencing_symbols("Tenant")
```

### 3. Edição Semântica

Substitui código **sem reescrever o arquivo inteiro**:

```php
// Adiciona método após outro método específico
insert_after_symbol("UserService/create", $newMethod)
```

### 4. Refatoração Segura

Renomeia símbolos em **toda a codebase** automaticamente:

```php
rename_symbol("oldMethodName", "newMethodName")
```

## 🛠️ Ferramentas Disponíveis

| Ferramenta                     | Uso                                               |
| ------------------------------ | ------------------------------------------------- |
| `find_symbol`                  | Localiza classes, métodos, funções por nome       |
| `find_referencing_symbols`     | Mostra onde um símbolo é usado                    |
| `get_symbols_overview`         | Visão geral de um arquivo (classes, métodos)      |
| `replace_symbol_body`          | Substitui corpo de método/classe                  |
| `insert_after_symbol`          | Insere código após um símbolo                     |
| `insert_before_symbol`         | Insere código antes de um símbolo                 |
| `rename_symbol`                | Renomeia em toda a codebase                       |
| `search_for_pattern`           | Busca por regex em arquivos                       |
| `list_dir`                     | Lista arquivos ignorando `.gitignore`             |
| `read_memory` / `write_memory` | Armazena informações sobre o projeto (memória IA) |

## 🚀 Casos de Uso

### 1. Entender estrutura de um arquivo

```
Agente: "O que tem no TenantController?"
Serena: [lista classes, métodos, assinaturas]
```

### 2. Adicionar método em local específico

```php
// Adiciona método `suspend()` após o método `update()`
insert_after_symbol("UserController/update", "
    public function suspend(User $user): RedirectResponse
    {
        $user->update(['is_suspended' => true]);
        return redirect()->back();
    }
")
```

### 3. Encontrar todos os usos de um Trait

```
find_referencing_symbols("UuidTrait")
// Retorna: [User.php:10, Tenant.php:8, Media.php:12]
```

## 📁 Memórias do Projeto

Serena mantém "memórias" sobre o projeto em `.serena/memories/`:

-   `project_overview.md` - Resumo geral do projeto
-   `suggested_commands.md` - Comandos úteis para o projeto
-   `task_completion_guide.md` - Padrões de conclusão de tarefas

Essas memórias são **consultadas automaticamente** por agentes de IA.

## ⚙️ Configuração

O Serena é configurado em `.gemini/antigravity/mcp_config.json`:

```json
{
    "serena": {
        "command": "uvx",
        "args": [
            "--from",
            "git+https://github.com/oraios/serena",
            "serena",
            "start-mcp-server",
            "--context",
            "ide"
        ]
    }
}
```

### Ativação do Projeto

Antes de usar, Serena precisa ser ativado para o projeto:

```typescript
mcp_serena_activate_project({ project: "/home/iury/Projetos/labSIS-KIT" });
```

## 🔗 Integração com .context

Serena é usado para **gerar** conteúdo em `.context/`:

-   Analisa symbols (classes, métodos) para criar `architecture.md`
-   Detecta padrões de código para `development-workflow.md`
-   Mapeia relacionamentos para `data-flow.md`

## 🆚 Serena vs Grep/Find

| Grep/Find           | Serena                  |
| ------------------- | ----------------------- |
| Busca texto literal | Entende estrutura       |
| Linha/coluna        | Símbolos (class/method) |
| Manual              | Automático via LSP      |
| Falha ao renomear   | Refatoração segura      |

## 📖 Referência

-   [Serena no GitHub](https://github.com/oraios/serena)
-   [Documentação MCP](https://modelcontextprotocol.io)
