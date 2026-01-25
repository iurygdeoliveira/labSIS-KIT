# 🛡️ Testes de Controle de Acesso e Redirecionamento

Este documento detalha os testes de integração responsáveis por verificar as regras de acesso aos diferentes painéis do sistema (Admin, User) e a lógica de redirecionamento inteligente.

O arquivo de teste está localizado em: `tests/Feature/PanelAccessTest.php`.

## Estrutura e Dados

Diferente dos testes de autenticação que utilizam factories isoladas, estes testes utilizam os **Seeders** do projeto (`DatabaseSeeder`) para garantir um ambiente consistente com roles e permissions reais configuradas pelo `UserSeeder`.

Isso garante que estamos testando contra a mesma estrutura de dados utilizada em produção (Roles do Spatie Permission, Tenants, etc).

## Cenários Cobertos

### 1. Visitantes (Guests)

-   **Redirecionamento**: Garante que usuários não autenticados que tentam acessar `/admin` ou `/user` sejam redirecionados para a tela de login (`/login` via rota de compatibilidade).

### 2. Administradores (Admin)

-   **Acesso ao Painel**: Verifica se usuários com role `Admin` conseguem acessar a rota `/admin`.
-   **Redirecionamento Inteligente**: Se um administrador autenticado tentar acessar a página de login (`/login`), ele deve ser redirecionado automaticamente para o painel administrativo (`/admin`), melhorando a experiência do usuário.

### 3. Usuários Comuns (User)

-   **Bloqueio ao Painel Admin**: Garante que usuários comuns **não** consigam acessar o painel administrativo, recebendo um erro de autorização (403 Forbidden).
-   **Acesso ao Painel do Tenant**: Verifica se o usuário consegue acessar o painel de seu tenant específico (`/user/{uuid}`).
-   **Redirecionamento Inteligente**: Se um usuário comum autenticado tentar acessar a página de login, ele deve ser redirecionado para o painel do seu primeiro tenant disponível (`/user/{uuid}`).

## Middleware Testado

Estes testes validam indiretamente o funcionamento do middleware `App\Http\Middleware\RedirectToProperPanelMiddleware`, que centraliza toda a lógica de proteção de rotas e redirecionamento de usuários logados.

## Executando estes testes

Para rodar apenas os testes de controle de acesso:

```bash
./vendor/bin/sail artisan test tests/Feature/PanelAccessTest.php
```

## Referências

- [Test: PanelAccessTest](/tests/Feature/PanelAccessTest.php)
- [Middleware: RedirectToProperPanelMiddleware](/app/Http/Middleware/RedirectToProperPanelMiddleware.php)
- [Seeder: DatabaseSeeder](/database/seeders/DatabaseSeeder.php)
- [Seeder: UserSeeder](/database/seeders/UserSeeder.php)
