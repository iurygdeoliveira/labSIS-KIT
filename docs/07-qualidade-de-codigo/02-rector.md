# Rector: Automação e Evolução do Código 🚀

O Rector é uma ferramenta de refatoração automatizada que complementa o Larastan. Enquanto o Larastan aponta os problemas, o Rector os **resolve automaticamente** baseado em regras e padrões pré-definidos. No **labSIS-KIT**, ele é usado para manter o projeto alinhado com PHP 8.5 e Laravel 13.

## Por que utilizar o Rector?

1. **Upgrades Sem Dor**: Automatiza refatorações entre versões do Laravel (ex: atributos PHP 8+, `#[Override]`, APIs modernizadas).
2. **Código Mais Moderno**: Aplica melhorias do PHP moderno (8.2–8.5), como _readonly properties_, _constructor property promotion_, `declare(strict_types=1)` e novas funções de string/array.
3. **Qualidade Consistente**: Remove código morto (_dead code_), simplifica condicionais complexas e garante que as declarações de tipo sejam aplicadas em todo o projeto.

---

## Como Utilizar no labSIS-KIT

O Rector trabalha em duas etapas principais: visualização e aplicação.

### 1. Dry Run (Simulação)

Sempre execute este comando primeiro. Ele mostrará um "diff" das alterações propostas sem modificar os arquivos:

```bash
./vendor/bin/sail php ./vendor/bin/rector process --dry-run
```

### 2. Process (Aplicação)

Após revisar as mudanças propostas no Dry Run e garantir que estão corretas, aplique-as:

```bash
./vendor/bin/sail php ./vendor/bin/rector process
```

---

## Configuração e Regras

A configuração está centralizada em `rector.php`. O projeto está configurado para:

-   **Detecção Automática**: Usa o `LaravelSetProvider` para aplicar regras baseadas na versão detectada no `composer.json` (Laravel 13).
-   **Qualidade de Código**: Ativa conjuntos de regras para Dead Code, Early Returns e Code Quality.
-   **PHP 8.5**: Aplica atributos nativos, `strict_types` e padrões modernos da linguagem.

### Exemplos de Transformações Comuns:

**Antes (Código Verboso):**

```php
public function __construct($name) {
    $this->name = $name;
}
```

**Depois (Modernizado pelo Rector):**

```php
public function __construct(public string $name) {}
```

---

> [!TIP]
> Integre o Rector ao seu fluxo de desenvolvimento após grandes refatorações ou ao atualizar dependências. Ele é o seu "garimpeiro" de código antigo.

## Referências

- [Configuração: Rector](../../rector.php)
- [Filacheck Pro](./04-filacheck.md)
