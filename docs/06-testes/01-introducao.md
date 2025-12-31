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
./vendor/bin/sail artisan test tests/Feature/AuthenticationTest.php
```

### Rodar testes filtrando por nome

```bash
./vendor/bin/sail artisan test --filter="pode redefinir a senha"
```

## 📂 Estrutura de Testes

-   `tests/Feature`: Testes de integração que verificam fluxos completos (ex: Login, Registro, Reset de Senha).
-   `tests/Unit`: Testes unitários para classes isoladas (Services, Helpers, etc).

## 📝 Convenções

-   **Idioma**: Os nomes dos testes devem ser escritos em **Português do Brasil**.
-   **Sintaxe**: Utilize a sintaxe `describe()` e `it()` do Pest para melhor legibilidade.

### Exemplo de Teste

```php
describe('Fluxo de Autenticação', function () {
    it('usuário aprovado pode fazer login', function () {
        $user = User::factory()->create(['is_approved' => true]);

        Livewire::test(Login::class)
            ->fillForm([
                'email' => $user->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertRedirect('/');
    });
});
```

## 📚 Documentação Detalhada

Para detalhes específicos sobre cada conjunto de testes, consulte os documentos abaixo:

-   **[02 - Testes de Autenticação](./02-autenticacao.md)**: Login, registro e redefinição de senha.
-   **[03 - Controle de Acesso](./03-acesso-paineis.md)**: Regras de permissão por painel, tenants e redirecionamentos.
