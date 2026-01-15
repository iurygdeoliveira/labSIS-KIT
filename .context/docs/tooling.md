# Ferramentas de Desenvolvimento

## 🐳 Laravel Sail (Docker)

### Comandos Essenciais

```bash
# Iniciar ambiente
vendor/bin/sail up -d

# Parar ambiente
vendor/bin/sail stop

# Abrir aplicação no navegador
vendor/bin/sail open

# Acessar shell do container
vendor/bin/sail shell
```

### Artisan via Sail

```bash
# Migrations
vendor/bin/sail artisan migrate
vendor/bin/sail artisan migrate:fresh --seed

# Criar recursos
vendor/bin/sail artisan make:model Post -mfs
vendor/bin/sail artisan make:filament-resource Post --view

# Tinker (REPL)
vendor/bin/sail artisan tinker
```

### Composer via Sail

```bash
vendor/bin/sail composer install
vendor/bin/sail composer require vendor/package
vendor/bin/sail composer update
```

### NPM via Sail

```bash
vendor/bin/sail npm install
vendor/bin/sail npm run dev    # Hot reload
vendor/bin/sail npm run build  # Production
```

## 🎨 Laravel Pint (Formatação)

### Uso

```bash
# Formatar arquivos modificados
vendor/bin/sail bin pint --dirty

# Formatar tudo
vendor/bin/sail bin pint

# Verificar sem modificar
vendor/bin/sail bin pint --test
```

**Padrão**: PSR-12

**Quando usar**: Antes de commit, sempre executar `pint --dirty`.

## 🔍 Larastan (Análise Estática)

### Configuração

Level **5** obrigatório (definido em `phpstan.neon`).

### Uso

```bash
# Rodar análise
vendor/bin/sail composer analyse

# Output detalhado
vendor/bin/sail composer analyse -- --debug
```

**Regras**:

-   Sem erros level 5 antes de commit
-   Type hints obrigatórios
-   Return types declarados

## 🧪 Pest 4 (Testes)

### Comandos

```bash
# Todos os testes
vendor/bin/sail artisan test --compact

# Testes específicos
vendor/bin/sail artisan test --compact tests/Feature/UserTest.php
vendor/bin/sail artisan test --filter=test_user_can_login

# Com coverage
vendor/bin/sail artisan test --coverage
```

### Browser Tests (Pest 4)

```bash
# Rodar browser tests
vendor/bin/sail artisan test tests/Browser/

# Pausar para debug
# Use $page->pause() no código
```

**Exemplo**:

```php
it('allows login', function () {
    $user = User::factory()->create();

    $page = visit('/login');
    $page->fill('email', $user->email)
         ->fill('password', 'password')
         ->click('Login')
         ->assertSee('Dashboard');
});
```

## 🚀 Laravel Boost (MCP)

### Ferramentas Disponíveis

Via Gemini/Claude com Laravel Boost MCP ativo:

-   **application-info**: Versões de pacotes
-   **database-schema**: Estrutura do banco
-   **list-routes**: Rotas registradas
-   **tinker**: Executar PHP
-   **search-docs**: Docs por versão (Laravel 12, Filament 4, etc)

### Uso

```typescript
// Via agente IA
mcp_laravel_boost_search_docs({
    queries: ["filament actions", "pest browser testing"],
});

mcp_laravel_boost_tinker({
    code: "User::count()",
});
```

[Ver documentação completa](/docs/08-ai-agents/laravel-boost.md)

## 🧠 Serena (Navegação Semântica)

### Ferramentas Disponíveis

-   **find_symbol**: Localizar classes/métodos
-   **find_referencing_symbols**: Onde símbolo é usado
-   **replace_symbol_body**: Editar método
-   **rename_symbol**: Renomear em toda codebase

### Uso

```typescript
// Via agente IA
mcp_serena_find_symbol({
    name_path_pattern: "UserController/store",
    relative_path: "app/Http/Controllers",
});
```

[Ver documentação completa](/docs/08-ai-agents/serena.md)

## 🧩 Rector (Refatoração Automatizada)

### Uso

```bash
# Processar refatorações
vendor/bin/sail vendor/bin/rector process

# Dry run
vendor/bin/sail vendor/bin/rector process --dry-run
```

**Casos de uso**:

-   Upgrade PHP version
-   Modernizar código
-   Aplicar type hints

## 📊 Laravel Debugbar

### Ativação

Apenas em ambiente local (já configurado).

### Uso

-   Acessar aplicação no navegador
-   Barra aparece automaticamente no rodapé
-   Monitorar queries, views, routes, logs

## 🔧 Comandos Customizados

### Verificação Completa

```bash
# Formatar + Análise + Testes
vendor/bin/sail bin pint --dirty && \
vendor/bin/sail composer analyse && \
vendor/bin/sail artisan test --compact
```

### Reset Ambiente

```bash
vendor/bin/sail artisan migrate:fresh --seed
vendor/bin/sail artisan config:clear
vendor/bin/sail artisan cache:clear
```

---

**Nota**: Todos os comandos devem ser executados **dentro do container** via `vendor/bin/sail`.
