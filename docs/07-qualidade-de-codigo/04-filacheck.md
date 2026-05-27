# Filacheck Pro: Validação de Convenções Filament 🧩

O **Filacheck Pro** (`laraveldaily/filacheck-pro`) valida automaticamente se Resources, Pages, Tables, Forms e Widgets do Filament seguem as convenções e boas práticas da versão instalada (Filament v5 neste projeto). Complementa o Larastan: enquanto o PHPStan analisa tipos e estrutura PHP, o Filacheck foca em padrões específicos do ecossistema Filament.

## Por que utilizar?

1. **Conformidade com Filament v5**: Detecta uso de APIs depreciadas, namespaces incorretos e padrões incompatíveis com a versão atual.
2. **Gate antes do merge**: Integra-se ao fluxo de qualidade junto com Larastan, Rector e Pest.
3. **Feedback estruturado**: Retorna JSON com lista de issues ou `{"result":"pass","issues":0}`.

---

## Como executar

### Via Sail (recomendado)

```bash
./vendor/bin/sail php ./vendor/bin/filacheck
```

### Local (sem Docker)

```bash
php ./vendor/bin/filacheck
```

### Saída esperada (sucesso)

```json
{"result":"pass","issues":0}
```

Quando há violações, o JSON inclui detalhes de cada issue (arquivo, regra, mensagem).

---

## Ordem sugerida no quality gate

Execute na sequência abaixo antes de abrir PR ou fazer merge:

```bash
./vendor/bin/sail php ./vendor/bin/phpstan analyse
./vendor/bin/sail php ./vendor/bin/rector process --dry-run
./vendor/bin/sail php ./vendor/bin/filacheck
./vendor/bin/sail php ./vendor/bin/pint --test
./vendor/bin/sail artisan test
```

Consulte também [Larastan](./01-larastan.md) e [Rector](./02-rector.md).

---

## Configuração

O pacote é instalado via Composer a partir do repositório Satis da Laravel Daily (`composer.json` → `repositories.filacheck`). Não requer arquivo de configuração adicional na raiz do projeto — analisa automaticamente os diretórios Filament em `app/Filament/`.

---

## Referências

- [Stack Tecnológica — Filacheck Pro](../04-backend-e-arquitetura/stack-tecnologica.md)
- [Diretório Filament](../../app/Filament/)
