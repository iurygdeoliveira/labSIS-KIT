# Fortalecimento de Senha (Password Hardening)

Este documento descreve como configurar políticas de senha robustas no projeto, garantindo maior segurança para as contas de usuário.

## Configuração Padrão do Laravel

Por padrão, o Laravel já implementa mecanismos seguros de hash e armazenamento de senhas. No entanto, a complexidade mínima da senha é configurável.

O local correto para definir essas regras é o método `boot` do `AppServicePovider`.

## Implementação Recomendada

Para projetos reais que requerem alta segurança, recomendamos a seguinte implementação no arquivo `app/Providers/AppServiceProvider.php`:

```php
use Illuminate\Validation\Rules\Password;

// ...

public function boot(): void
{
    // ... outras configurações

    Password::defaults(function () {
        $rule = Password::min(8)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols();

        // Em produção, recomenda-se ativar a verificação de vazamento
        // Isso consulta a API do 'Have I Been Pwned'
        if (app()->isProduction()) {
            $rule->uncompromised();
        }

        return $rule;
    });
}
```

## O que cada regra faz?

-   `min(8)`: Exige no mínimo 8 caracteres.
-   `letters()`: Exige pelo menos uma letra.
-   `mixedCase()`: Exige letras maiúsculas e minúsculas.
-   `numbers()`: Exige pelo menos um número.
-   `symbols()`: Exige pelo menos um símbolo (ex: !@#$).
-   `uncompromised()`: Verifica se a senha apareceu em vazamentos de dados conhecidos.

## Observação sobre Desenvolvimento Local

A regra `uncompromised()` realiza uma requisição externa. Em ambientes de desenvolvimento sem internet ou com proxy restrito, isso pode causar lentidão ou falhas. Por isso, recomendamos ativá-la condicionalmente (`app()->isProduction()`).
