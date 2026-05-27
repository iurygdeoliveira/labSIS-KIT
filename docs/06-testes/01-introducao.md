# 🧪 Testes Automatizados

Este projeto utiliza **Pest PHP** como framework de testes, rodando dentro do ambiente **Laravel Sail**. Os testes garantem a integridade das funcionalidades críticas, como autenticação, validação e fluxos de usuário.

## 🛠 Ferramentas Utilizadas

-   **[Pest PHP](https://pestphp.com/)**: Framework de testes elegante e minimalista.
-   **[Laravel Sail](https://laravel.com/docs/sail)**: Ambiente de desenvolvimento Docker.
-   **[Livewire Testing](https://livewire.laravel.com/docs/testing)**: Utilitários para testar componentes Livewire.

## 🚀 Executando os Testes

Como o projeto roda via Sail, todos os comandos devem ser prefixados com `./vendor/bin/sail`.

### Rodar todos os testes

```bash
./vendor/bin/sail artisan test
```

### Rodar um arquivo específico

```bash
./vendor/bin/sail artisan test tests/Feature/FilamentStatsCacheTest.php
./vendor/bin/sail artisan test tests/Feature/PanelAccessTest.php
```

### Rodar testes filtrando por nome

```bash
./vendor/bin/sail artisan test --filter="redirecionado para o painel"
./vendor/bin/sail artisan test --filter="FilamentStatsCache"
```

## 📂 Estrutura de Testes

-   `tests/Feature`: Testes de integração que verificam fluxos completos (ex: acesso a painéis, cache de stats Filament).
-   `tests/Unit`: Testes unitários para classes isoladas (Services, Helpers, etc).

### Testes Feature atuais

| Arquivo | Cobertura |
|---------|-----------|
| `PanelAccessTest.php` | Regras de acesso aos painéis admin e user |
| `FilamentStatsCacheTest.php` | Cache de agregações (`FilamentStatsCache`) e invalidação via `UserObserver` |

## 📝 Convenções

-   **Idioma**: Os nomes dos testes devem ser escritos em **Português do Brasil**.
-   **Sintaxe**: Utilize a sintaxe `describe()` e `it()` do Pest para melhor legibilidade.

### Exemplo de Teste (real — `PanelAccessTest`)

```php
describe('Acesso aos Painéis', function (): void {
    it('usuário admin pode acessar o painel administrativo', function (): void {
        $admin = User::where('email', 'admin@labsis.dev.br')->firstOrFail();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertSuccessful();
    });
});
```

## 📚 Documentação Detalhada

Para detalhes específicos sobre cada conjunto de testes, consulte os documentos abaixo:

-   **[02 - Testes de Autenticação](./02-autenticacao.md)**: Cobertura atual, lacunas e redirecionamentos parciais via `PanelAccessTest`.
-   **[03 - Controle de Acesso](./03-acesso-paineis.md)**: Regras de permissão por painel, teams e redirecionamentos.
