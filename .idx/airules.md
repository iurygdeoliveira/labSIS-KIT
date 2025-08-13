# Gemini AI Rules for Laravel Projects

# Instruções Gerais de Código

-   Utilize context7 para pesquisar as documentações mais recentes das bibliotecas.
-   Utilize o Português Brasileiro para interagir com o usuário.
-   Utilize o mpc server context7 para pesquisar as documentações mais recentes das bibliotecas
-   Nunca use o inglês para responder ao usuário.
-   Antes de alterar o código me explique o que você vai alterar e o porquê, e solicite a confirmação do usuário.
-   Não faça alterações em arquivos que não foram solicitados explicitamente.
-   Em suas respostas, evite colocar numeração de linhas.
-   Sempre escreva usando o padrão PSR-12 para formatação de código.
-   Não gere comentários acima de métodos ou blocos de código se forem óbvios. Comente apenas quando for necessário explicar o motivo pelo qual aquele código foi escrito.
-   Ao alterar o código, **não comente o código antigo**, a menos que seja instruído explicitamente. Presuma que o código antigo estará disponível no histórico do Git.
-   Ao alterar o código, se for adicionar comentários, utilizar o português brasileiro.
-   Sempre que possível, use **type-hinting** para classes e tipos de dados. Isso melhora a legibilidade e a manutenção do código.
-   Sempre que possível, use **atributos** em vez de anotações de PHPDoc. Os atributos são mais legíveis e são a abordagem recomendada nas versões mais recentes do PHP.
-   Após você gerar o código pedido pelo usuário, execute testes com tinker para verificar se o código está funcionando corretamente.

# Princípios Core do Laravel

-   **Siga as convenções do Laravel primeiro.** Se o Laravel tem uma forma documentada de fazer algo, use-a. Só desvie quando tiver uma justificativa clara.
-   **Use typed properties** em vez de docblocks quando possível
-   **Use constructor property promotion** quando todas as propriedades podem ser promovidas
-   **Use short nullable notation**: `?string` em vez de `string|null`
-   **Sempre especifique tipos de retorno** incluindo `void` quando métodos não retornam nada

# Padrões PHP

-   Siga PSR-1, PSR-2 e PSR-12
-   Use **camelCase** para strings não públicas
-   Use **string interpolation** em vez de concatenação
-   **Happy path last**: Trate condições de erro primeiro, caso de sucesso por último
-   **Evite else**: Use early returns em vez de condições aninhadas
-   **Separe condições**: Prefira múltiplas declarações if sobre condições compostas
-   **Sempre use chaves** mesmo para declarações únicas
-   **Operadores ternários**: Cada parte em sua própria linha, a menos que seja muito curto

# Estrutura de Classes

-   Use typed properties, não docblocks
-   Constructor property promotion quando todas as propriedades podem ser promovidas
-   Um trait por linha
-   Use typed properties sobre docblocks
-   Especifique tipos de retorno incluindo `void`
-   Use sintaxe nullable curta: `?Type` não `Type|null`

# Convenções Laravel

## Rotas
-   URLs: kebab-case (`/open-source`)
-   Nomes de rotas: camelCase (`->name('openSource')`)
-   Parâmetros: camelCase (`{userId}`)
-   Use notação tuple: `[Controller::class, 'method']`

## Controllers
-   Nomes de recursos no plural (`PostsController`)
-   Mantenha-se aos métodos CRUD (`index`, `create`, `store`, `show`, `edit`, `update`, `destroy`)
-   Extraia novos controllers para ações não-CRUD

## Configuração
-   Arquivos: kebab-case (`pdf-generator.php`)
-   Chaves: snake_case (`chrome_path`)
-   Adicione configs de serviço ao `config/services.php`, não crie novos arquivos
-   Use helper `config()`, evite `env()` fora de arquivos de config

## Comandos Artisan
-   Nomes: kebab-case (`delete-old-records`)
-   Sempre forneça feedback (`$this->comment('All ok!')`)
-   Mostre progresso para loops, resumo no final
-   Coloque output ANTES de processar item (mais fácil para debug)

# Estrutura Base do Laravel 11+

-   **Service Providers**: não existem outros service providers além de `AppServiceProvider`. Não crie novos service providers a menos que seja absolutamente necessário. Use os novos recursos do Laravel 11+. Se for realmente necessário criar um novo provider, registre-o em `bootstrap/providers.php` e **não** em `config/app.php` como nas versões anteriores.
-   **Event Listeners**: desde o Laravel 11, os Listeners são registrados automaticamente se forem corretamente type-hintados.
-   **Agendador de Comandos (Console Scheduler)**: comandos agendados devem ser definidos em `routes/console.php`, e não em `app/Console/Kernel.php` (que não existe mais no Laravel 11).
-   **Middleware**: sempre que possível, utilize o middleware pelo nome da classe nas rotas. Se for necessário registrar um alias de middleware, faça isso em `bootstrap/app.php`, e não em `app/Http/Kernel.php` (que não existe mais desde o Laravel 11).
-   **Tailwind**: em novas páginas Blade, use Tailwind em vez de Bootstrap, salvo instrução contrária. Tailwind já está pré-configurado com Vite no Laravel 11.
-   **Faker**: em Factories, use o helper `fake()` em vez de `$this->faker`.
-   **Policies**: o Laravel detecta automaticamente as Policies. Não é necessário registrá-las manualmente nos service providers.

# Uso de Serviços PHP em Controllers

-   Se a classe de Service for usada apenas em **um único método** do Controller, injete-a diretamente nesse método com type-hinting.
-   Se a classe de Service for usada em **vários métodos**, inicialize-a no construtor.
-   Utilize **promoção de propriedades no construtor** do PHP 8. Não crie um construtor vazio se ele não tiver parâmetros.

# Validação

-   Use notação de array para múltiplas regras (mais fácil para classes de regras customizadas):
  ```php
  public function rules() {
      return [
          'email' => ['required', 'email'],
      ];
  }
  ```
-   Regras de validação customizadas usam snake_case

# Blade Templates

-   Indente com 4 espaços
-   Sem espaços após estruturas de controle:
  ```blade
  @if($condition)
      Something
  @endif
  ```

# Autorização

-   Policies usam camelCase: `Gate::define('editPost', ...)`
-   Use palavras CRUD, mas `view` em vez de `show`

# Traduções

-   Use função `__()` sobre `@lang`

# API Routing

-   Use nomes de recursos no plural: `/errors`
-   Use kebab-case: `/error-occurrences`
-   Limite aninhamento profundo para simplicidade

# Testes

-   Mantenha classes de teste no mesmo arquivo quando possível
-   Use nomes de métodos de teste descritivos
-   Siga o padrão arrange-act-assert

# Migrations

-   **NÃO escreva métodos down()** em migrations, apenas métodos up()
-   Para tabelas pivot no banco de dados, use a ordem alfabética correta, como `project_role` em vez de `role_project`.

# Regras para Filament

-   Quando usando Filament 4 ou 3, gere recursos `php artisan make:filament-resource` usando a flag `--view`, para evitar perguntas extras no Terminal.

# Convenções de Nomenclatura

## Nomes de Classes
-   **Classes**: PascalCase (`UserController`, `OrderStatus`)
-   **Métodos/Variáveis**: camelCase (`getUserName`, `$firstName`)
-   **Rotas**: kebab-case (`/open-source`, `/user-profile`)
-   **Arquivos de config**: kebab-case (`pdf-generator.php`)
-   **Chaves de config**: snake_case (`chrome_path`)
-   **Comandos Artisan**: kebab-case (`php artisan delete-old-records`)

## Estrutura de Arquivos
-   Controllers: nome do recurso no plural + `Controller` (`PostsController`)
-   Views: camelCase (`openSource.blade.php`)
-   Jobs: baseado em ação (`CreateUser`, `SendEmailNotification`)
-   Events: baseado em tempo (`UserRegistering`, `UserRegistered`)
-   Listeners: ação + sufixo `Listener` (`SendInvitationMailListener`)
-   Commands: ação + sufixo `Command` (`PublishScheduledPostsCommand`)
-   Mailables: propósito + sufixo `Mail` (`AccountActivatedMail`)
-   Resources/Transformers: plural + `Resource`/`Transformer` (`UsersResource`)
-   Enums: nome descritivo, sem prefixo (`OrderStatus`, `BookingType`)

# Comentários

-   **Evite comentários** - escreva código expressivo em vez disso
-   Quando necessário, use formatação adequada:
  ```php
  // Linha única com espaço após //
  
  /*
   * Blocos multi-linha começam com *
   */
  ```
-   Refatore comentários em nomes de funções descritivos

# Espaçamento

-   Adicione linhas em branco entre declarações para legibilidade
-   Exceção: sequências de operações equivalentes de linha única
-   Sem linhas vazias extras entre chaves `{}`
-   Deixe o código "respirar" - evite formatação apertada

# Instruções para a geração de Commits

Sempre que eu pedir por um commit, execute os seguintes comandos:

- ./vendor/bin/pint --parallel
- git add .
- git commit -m "mensagem do commit"
- git push

Por favor analise o contexto fornecido e sugira uma mensagem de commit seguindo o formato Conventional Commits, em português do Brasil, seja objetivo na geração das mensagens de commit. Realizar os comandos apenas quando eu solicitar explicitamente.

## Formato esperado

```
<tipo>(<escopo>): <descrição>

<corpo do commit com as alterações realizadas>

```

## Exemplo

```
feat(api): implementa autenticação de usuários

- Adiciona middleware de autenticação
- Cria endpoints de login e registro
- Implementa validação de tokens JWT

```
