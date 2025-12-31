# 🔐 Testes de Autenticação

Este documento descreve os testes automatizados relacionados aos fluxos de autenticação do sistema, localizados em `tests/Feature/AuthenticationTest.php`.

Estes testes garantem que as funcionalidades críticas de entrada, cadastro e recuperação de conta funcionem conforme o esperado.

## Cenários Cobertos

### 1. Login

-   **Renderização**: Verifica se a página de login carrega corretamente.
-   **Sucesso**: Confirma que um usuário aprovado consegue fazer login com as credenciais corretas.
-   **Falha**: Garante que tentativas de login com credenciais inválidas são bloqueadas.

### 2. Registro

-   **Renderização**: Verifica se a página de registro carrega corretamente.
-   **Novo Usuário**: Testa o fluxo completo de criação de uma nova conta, garantindo que o usuário seja criado e associado a um novo Tenant automaticamente.

### 3. Recuperação de Senha

-   **Renderização**: Verifica se a página de "Esqueci minha senha" carrega corretamente.
-   **Solicitação**: Testa o envio do link de redefinição de senha para o e-mail do usuário.
-   **Redefinição**: Verifica o fluxo completo de redefinição, garantindo que o usuário consiga alterar sua senha através do link enviado e que a nova senha seja salva corretamente no banco de dados (hash atualizado).

## Executando estes testes

Para rodar apenas os testes de autenticação:

```bash
./vendor/bin/sail artisan test tests/Feature/AuthenticationTest.php
```
