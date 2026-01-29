# LabSIS KIT

<div align="center">
  <img src="public/images/LabSIS.png" alt="LabSIS Logo" width="700" />
  <br>
  <a href="https://www.labsis.dev.br">www.labsis.dev.br</a><br>
  <em>Transformando desafios reais em soluções inteligentes</em>
</div>

<br>
<p align="center">
    <a href="https://filamentphp.com"><img alt="Filament v5" src="https://img.shields.io/badge/Filament-v5-eab308?style=for-the-badge"></a>
    <a href="https://laravel.com"><img alt="Laravel v12+" src="https://img.shields.io/badge/Laravel-v12+-FF2D20?style=for-the-badge&logo=laravel"></a>
    <a href="https://livewire.laravel.com"><img alt="Livewire v4" src="https://img.shields.io/badge/Livewire-v4-FB70A9?style=for-the-badge"></a>
    <a href="https://php.net"><img alt="PHP 8.5+" src="https://img.shields.io/badge/PHP-8.5+-777BB4?style=for-the-badge&logo=php"></a>
</p>

## Sobre o labSIS KIT

Este repositório é um Kit de Iniciação (Starter Kit) para o desenvolvimento de aplicações SaaS (Software as a Service) utilizando a stack TALL (Tailwind, Alpine.js, Laravel, Livewire) e Filament.

O objetivo deste projeto é fornecer uma base sólida e rica em recursos para acelerar o desenvolvimento de novas aplicações, seguindo as melhores práticas e convenções do ecossistema Laravel.

Confira também o nosso [**Roadmap de Desenvolvimento**](ROADMAP.md) para ver o que planejado para o futuro do kit.

## Documentação do Kit

Esta documentação foi criada para facilitar o entendimento de como as funcionalidades do kit foram implementadas, descrevendo os padrões adotados, decisões técnicas e exemplos práticos.

Toda a documentação sobre como utilizar os recursos, padrões e arquitetura deste kit está disponível na pasta [`/docs`](/docs). Recomendamos a leitura para todos os desenvolvedores que pretendem utilizar este projeto.

Além disso, este repositório foi indexado nas plataformas de IA [DeepWiki](https://deepwiki.com/iurygdeoliveira/labSIS-SaaS-KIT-V4) e [Context7](https://context7.com/iurygdeoliveira/labsis-saas-kit-v4), que auxiliam o leitor a explorar o código e compreender as implementações por meio de buscas contextuais e respostas explicativas.

### Autenticação e Segurança

- [**Autenticação de Dois Fatores (2FA) no Filament**](/docs/02-autenticacao-e-seguranca/autenticacao-2fa.md)
- [**Checklist de Segurança**](/docs/02-autenticacao-e-seguranca/checklist-de-seguranca.md)
- [**Content Security Policy (CSP)**](/docs/02-autenticacao-e-seguranca/content-security-policy.md)
- [**Fluxo de Registro de Novos Usuários**](/docs/02-autenticacao-e-seguranca/fluxo-de-registro-de-novos-usuarios.md)
- [**Fortalecimento de Senha**](/docs/02-autenticacao-e-seguranca/fortalecimento-de-senha.md)
- [**Login Unificado**](/docs/02-autenticacao-e-seguranca/login-unificado.md)
- [**Prevenção Contra IDOR**](/docs/02-autenticacao-e-seguranca/prevencao-idor.md)
- [**Roles/Permissions**](/docs/02-autenticacao-e-seguranca/roles-e-permissions.md)
- [**Suspensão de Usuários**](/docs/02-autenticacao-e-seguranca/suspensao-usuarios.md)
- [**Gestão de Tenants**](/docs/02-autenticacao-e-seguranca/tenancy-e-teams.md)
- [**Customização de E-mails e Reset de Senha**](/docs/02-autenticacao-e-seguranca/customizacao-emails.md)

### UI e Customização

- [**Customização de Cores e CSS Modular**](/docs/03-ui-e-customizacao/customizacao-de-cores.md)
- [**Customização da Aparência do Painel**](/docs/03-ui-e-customizacao/customizando-layout.md)
- [**Customização de Logotipo**](/docs/03-ui-e-customizacao/customizando-logo.md)
- [**Widgets no Filament**](/docs/03-ui-e-customizacao/widgets-filament.md)

### Backend e Arquitetura

- [**Entendendo o AppServiceProvider**](/docs/04-backend-e-arquitetura/app-service-provider.md)
- [**Arquitetura do Model User**](/docs/04-backend-e-arquitetura/user-model.md)
- [**Integração MongoDB - Auditoria e Logs**](/docs/04-backend-e-arquitetura/mongodb-integration.md)
- [**Estratégia de Backup**](/docs/04-backend-e-arquitetura/estrategia-backup.md)
- [**Scripts do Composer e Inicialização**](/docs/04-backend-e-arquitetura/scripts-composer.md)
- [**Regras de Negócio**](/docs/04-backend-e-arquitetura/regras-de-negocio.md)
- [**Gestão de mídias**](/docs/04-backend-e-arquitetura/gestao-de-midia.md)
- [**Notificações**](/docs/04-backend-e-arquitetura/notifications-trait.md)
- [**Utilizando Enumerações (Enums) com Filament**](/docs/04-backend-e-arquitetura/enums.md)
- [**Padronização de Data e Hora**](/docs/04-backend-e-arquitetura/padrao-datetime.md)

### Stack Tecnológica

- [**Stack Tecnológica (Versões)**](/docs/04-backend-e-arquitetura/stack-tecnologica.md)

### Otimizações

- [**Otimização com #[Computed]**](/docs/05-otimizacoes/livewire-computed.md)
- [**Cache e Redis**](/docs/05-otimizacoes/cache-e-redis.md)
- [**Otimização de Cache de Página com Cloudflare**](/docs/05-otimizacoes/cloudflare-page-cache.md)
- [**Laravel Pulse**](/docs/05-otimizacoes/laravel-pulse.md)

### Qualidade de Código

- [**Larastan**](/docs/07-qualidade-de-codigo/01-larastan.md)
- [**Rector**](/docs/07-qualidade-de-codigo/02-rector.md)
- [**IDE Helper**](/docs/07-qualidade-de-codigo/03-ide-helper.md)

### Testes Automatizados

- [**Introdução aos Testes Automatizados**](/docs/06-testes/01-introducao.md)
- [**Testes de Autenticação**](/docs/06-testes/02-autenticacao.md)
- [**Testes de Acesso aos Painéis**](/docs/06-testes/03-acesso-paineis.md)

### Inteligência Artificial

- [**Laravel Boost - MCP para Laravel**](/docs/08-ai-agents/laravel-boost.md)
- [**Protocolo de Execução de Skills**](/docs/08-ai-agents/protocolo-operacional.md)
- [**Padrão de Nomenclatura de Skills**](/docs/08-ai-agents/padrao-de-skills.md)
- [**Guia de Workflows do Agente**](/docs/08-ai-agents/guia-workflows.md)

## Pré-requisitos

Antes de começar, certifique-se de ter instalado em sua máquina:

- **Docker** - [Download](https://docs.docker.com/engine/install/)
    - O Docker é essencial para este projeto pois possibilita criar um ambiente de desenvolvimento mais próximo do ambiente de produção, garantindo consistência entre diferentes máquinas e facilitando a implantação.
- **Git** - [Download](https://git-scm.com/)
- **Composer** - [Download](https://getcomposer.org/)
- **Node.js** (versão 18 ou superior) - [Download](https://nodejs.org/)

## Como realizar a instalação

- [Instalação via Laravel Installer](/docs/01-instalacao-e-setup/instalacao-via-laravel-installer.md)
- [Instalação manual (clonando o repositório)](/docs/01-instalacao-e-setup/instalacao-manual.md)

## Primeiro acesso

Após rodar as migrations e seeders, os seguintes usuários são criados pelo `UserSeeder`:

- Admin (escopo global):
    - Email: `admin@labsis.dev.br`
    - Senha: `mudar123`
    - Acesso ao painel: `/admin`
    - Observação: Possui a role Admin em escopo global.

- Usuários de exemplo (escopo por tenant):
    - Sicrano
        - Email: `sicrano@labsis.dev.br`
        - Senha: `mudar123`
        - Tenants: Tenant A (Owner), Tenant B (User)
        - Acesso ao painel: `/user`
    - Beltrano
        - Email: `beltrano@labsis.dev.br`
        - Senha: `mudar123`
        - Tenants: Tenant A (User), Tenant B (Owner)
        - Acesso ao painel: `/user`

## Agradecimentos

Gostaríamos de expressar nossa sincera gratidão a todas as pessoas e equipes cujo trabalho tornou este projeto possível. Suas contribuições para a comunidade de código aberto são uma fonte constante de inspiração e um pilar fundamental para o nosso desenvolvimento.

Em especial, agradecemos a:

- **Equipe Laravel**: Pela criação e manutenção de um framework robusto, elegante e inovador, disponível em [laravel/laravel](https://github.com/laravel/laravel).
- **Equipe Filament**: Pelo incrível trabalho no [Filament](https://github.com/filamentphp/filament), que nos permite construir painéis administrativos complexos com uma velocidade e simplicidade impressionantes.
- **Equipe Spatie** ([spatie.be](https://spatie.be/)): Pelo desenvolvimento dos pacotes [laravel-permission](https://github.com/spatie/laravel-permission) e [laravel-medialibrary](https://github.com/spatie/laravel-medialibrary), amplamente utilizados no ecossistema Laravel.
- **Comunidade Beer and Code** ([beerandcode.com.br](https://beerandcode.com.br/)): Pela excelente metodologia de ensino em Laravel, que tem colaborador com a formação de desenvolvedores PHP, fornecendo conhecimento prático e focado em soluções reais.
- **Leandro Costa** ([@leandrocfe](https://github.com/leandrocfe)): Por suas valiosas contribuições e por compartilhar conhecimento de alta qualidade sobre Filament em seu canal [Filament Brasil no YouTube](https://www.youtube.com/@filamentbr), que foi fundamental para a implementação de diversas features neste projeto.
- **Nanderson Castro** ([@NandoKstroNet](https://github.com/NandoKstroNet)): Pelo excelente trabalho no canal [Code Experts](https://www.youtube.com/@codeexperts), que tem sido uma fonte valiosa de conhecimento técnico e boas práticas de desenvolvimento.
- **João Paulo Leite Nascimento** ([@joaopaulolndev](https://github.com/joaopaulolndev)): Pelo desenvolvimento do pacote [filament-edit-profile](https://github.com/joaopaulolndev/filament-edit-profile), que revolucionou a experiência de edição de perfil de usuários no Filament. Este pacote oferece uma solução completa e elegante para gerenciamento de perfis de usuário.
- **Jeferson Gonçalves** ([@jeffersongoncalves](https://github.com/jeffersongoncalves)): Pelo desenvolvimento de diversos pacotes, que agregam grande valor a comunidade filament + laravel.

O trabalho de vocês contribui significativamente para o avanço e a qualidade deste projeto.

## 🚀 Recursos Atuais

O Kit oferece uma base sólida com os seguintes recursos já implementados:

- **Gestão de Tenants:** Sistema multi-tenant completo com isolamento de dados por organização. Inclui criação e gerenciamento de tenants, controle de acesso baseado em roles (Admin, Owner, User), e interface administrativa para configuração de permissões por tenant.

- **Gestão de Roles e Permissões:** Sistema hierárquico de autorização com três níveis (Admin global, Owner por tenant, User por tenant). CRUD completo para roles e permissões com isolamento por tenant, policies centralizadas e interface de gerenciamento intuitiva.

- **Gestão de Mídias:** CRUD completo para mídias, com Preview de Conteúdo, Organização por Tipo e Tamanho Humanizado.

- **Gestão de Usuários:** CRUD completo para usuários (Criação, Leitura, Atualização e Exclusão). Sistema de Suspensão de Usuários, Organização em Abas com informações detalhadas (Informações Pessoais, Datas, Suspensão).

- **Customização de Logotipo:** Logotipo customizado para o painel de autenticação e para o rodapé do painel.

- **Login Unificado para diferentes painéis:** Login com Email e Senha, recuperação de senha e autenticação de dois fatores (2FA).

- **Exibição de Widgets:** Widgets personalizados para exibição de métricas e informações relevantes.

- **Website / Landing Page**: Página Inicial, Seções Pré-definidas (Hero e Sobre).

- **Registro Histórico de Autenticações:** Monitoramento completo de acessos de usuários com armazenamento em **MongoDB**, incluindo registros de login, logout, endereços IP e dispositivos.

- **Gestão de Templates de E-mail:** Funcionalidade para visualizar e testar templates de e-mail diretamente pelo painel administrativo, com suporte a templates customizados e dados reais de preview.

## 🧩 Plugins Utilizados

Este projeto integra plugins e pacotes robustos para expandir suas funcionalidades. Abaixo, destacamos os componentes utilizados:

- **[Filament Easy Footer](https://github.com/devonab/filament-easy-footer):** Adiciona um rodapé customizável ao painel administrativo, permitindo fácil inclusão de links e informações de copyright.
- **[Filament Spatie Media Library](https://github.com/filamentphp/spatie-laravel-media-library-plugin):** Plugin oficial para integrar a poderosa biblioteca Spatie Media Library ao Filament, facilitando o upload e gestão de arquivos.
- **[Filament Media Action](https://github.com/hugomyb/filament-media-action):** Fornece ações adicionais para manipulação de mídias dentro do Filament, melhorando a experiência de gerenciamento de arquivos.
- **[Laravel Authentication Log](https://github.com/TappNetwork/filament-authentication-log):** Pacote backend que rastreia e registra atividades de autenticação dos usuários, como logins, logouts e dispositivos utilizados.
- **[Spatie Laravel Backup](https://github.com/spatie/laravel-backup):** Solução completa para backups de banco de dados e arquivos da aplicação, com suporte a notificações e armazenamento em cloud.
- **[Spatie Laravel Query Builder](https://github.com/spatie/laravel-query-builder):** Facilita a construção de consultas Eloquent complexas a partir de parâmetros de requisição HTTP, ideal para APIs e filtragem avançada.

## 🛠️ Recomendação de Ferramentas de Desenvolvimento

Este projeto utiliza um conjunto de ferramentas para garantir a qualidade, padronização e agilidade no desenvolvimento. Abaixo estão os pacotes incluídos no ambiente de desenvolvimento (`require-dev`):

- **[barryvdh/laravel-debugbar](https://github.com/barryvdh/laravel-debugbar):** Adiciona uma barra de depuração com informações úteis sobre a aplicação.
- **[beyondcode/laravel-query-detector](https://github.com/beyondcode/laravel-query-detector):** Auxilia na identificação de consultas N+1, notificando o desenvolvedor para melhorar a performance da aplicação.
- **[fakerphp/faker](https://github.com/fakerphp/faker):** Gera dados falsos para popular o banco de dados em testes e seeders.
- **[larastan/larastan](https://github.com/larastan/larastan):** Realiza análise estática de código para encontrar bugs sem executar o código.
- **[laravel-shift/blueprint](https://blueprint.laravelshift.com/):** Gera código Laravel rapidamente a partir de um arquivo de definição.
- **[laravel/boost](https://packagist.org/packages/laravel/boost):** Servidor MCP oficial do Laravel que fornece contexto inteligente sobre a aplicação (versões, schema DB, rotas, Artisan) para agentes de IA. [Ver documentação](/docs/08-ai-agents/laravel-boost.md).
- **[laravel/pint](https://github.com/laravel/pint):** Formata o código PHP para seguir um padrão de estilo consistente (PSR-12).
- **[laravel/sail](https://github.com/laravel/sail):** Ambiente de desenvolvimento local completo baseado em Docker.
- **[laravel/tinker](https://github.com/laravel/tinker):** Console interativo (REPL) para executar código no contexto da aplicação.
- **[lucascudo/laravel-pt-br-localization](https://github.com/lucascudo/laravel-pt-br-localization):** Fornece traduções e configurações para a localização em português do Brasil.
- **[pestphp/pest](https://github.com/pestphp/pest):** Framework de testes elegante e focado no desenvolvedor para PHP.

## 📄 Licença

Este projeto está licenciado sob a [MIT License](LICENSE).

## 👥 Autor

- **Iury Oliveira** - [@iurygdeoliveira](https://github.com/iurygdeoliveira)

---

<div align="center">
  <strong>LabSIS - Transformando desafios reais em soluções inteligentes</strong>
</div>
