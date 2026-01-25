# Laravel Boost - MCP para Laravel

## 🎯 O que é?

Laravel Boost é um **servidor MCP (Model Context Protocol)** oficial do Laravel que fornece contexto inteligente sobre a aplicação Laravel para agentes de IA.

## 🔧 Como Funciona

O Boost roda **dentro do container Docker** do projeto (via Laravel Sail) e expõe informações estruturadas sobre:

-   **Versões** de PHP, Laravel e todos os pacotes instalados (Filament, Livewire, Pest, etc.)
-   **Estrutura do banco de dados** (tabelas, colunas, relacionamentos)
-   **Rotas** registradas na aplicação
-   **Models** Eloquent disponíveis
-   **Comandos Artisan** personalizados
-   **Logs** de aplicação e erros

## 📚 Ferramentas Disponíveis

### `application-info`

Retorna informações globais da aplicação:

```json
{
    "php_version": "8.5.1",
    "laravel_version": "12.46.0",
    "database_engine": "pgsql",
    "packages": [...],
    "models": ["App\\Models\\Video"]
}
```

### `database-schema`

Exporta schema completo do banco (tabelas, colunas, indexes, foreign keys).

### `list-routes`

Lista todas as rotas com filtros opcionais (método HTTP, nome, controller).

### `tinker`

Executa código PHP no contexto da aplicação (equivalente ao `php artisan tinker`).

### `search-docs`

Busca documentação oficial **específica para a versão** dos pacotes instalados (Laravel 12, Filament 4, etc.).

## 🚀 Casos de Uso

### 1. Verificar versões antes de sugerir código

```
Agente: "Preciso criar um Resource no Filament"
Boost: "Filament 4.5.2 instalado - use sintaxe v4"
```

### 2. Debugar erros usando Tinker

```php
// Via Boost, não precisa abrir terminal
User::whereEmail('admin@labsis.dev.br')->first()
```

### 3. Consultar schema sem acessar DB

```
Agente: "Quais colunas tem a tabela users?"
Boost: [uuid, email, password, team_id, is_suspended, ...]
```

## ⚙️ Configuração

O Boost é configurado em `.gemini/antigravity/mcp_config.json`:

```json
{
    "laravel-boost": {
        "command": "docker",
        "args": [
            "exec",
            "-i",
            "labsis-kit-laravel.test-1",
            "php",
            "/var/www/html/artisan",
            "boost:mcp"
        ]
    }
}
```

## 🔗 Integração com .context

O Boost é usado para enriquecer os arquivos em `.context/docs/`:

-   `project-overview.md` → Stack real com versões exatas
-   `architecture.md` → Models e relacionamentos detectados
-   `tooling.md` → Lista de comandos Artisan customizados

## 📖 Referência

-   [Laravel Boost (Packagist)](https://packagist.org/packages/laravel/boost)
-   [Documentação oficial](https://laravel.com/docs/12.x/boost)

## Referências

- [Config: MCP Config](/.gemini/antigravity/mcp_config.json)
- [Model: User](/app/Models/User.php)
- [Model: Video](/app/Models/Video.php)
