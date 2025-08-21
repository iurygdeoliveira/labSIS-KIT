# LabSIS SaaS KIT V4

<div align="center">
  <img src="public/images/LabSIS.png" alt="LabSIS Logo" width="700" />
  <br>
  <em>Transformando desafios reais em soluções inteligentes</em>
</div>

<br>
<p align="center">
    <a href="https://filamentphp.com"><img alt="Filament v3" src="https://img.shields.io/badge/Filament-v4-eab308?style=for-the-badge"></a>
    <a href="https://laravel.com"><img alt="Laravel v12+" src="https://img.shields.io/badge/Laravel-v12+-FF2D20?style=for-the-badge&logo=laravel"></a>
    <a href="https://livewire.laravel.com"><img alt="Livewire v3" src="https://img.shields.io/badge/Livewire-v3-FB70A9?style=for-the-badge"></a>
    <a href="https://php.net"><img alt="PHP 8.3+" src="https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php"></a>
</p>

## Sobre o labSIS SaaS KIT

Este repositório é um Kit de Iniciação (Starter Kit) para o desenvolvimento de aplicações SaaS (Software as a Service) utilizando a stack TALL (Tailwind, Alpine.js, Laravel, Livewire) e Filament.

O objetivo deste projeto é fornecer uma base sólida e rica em recursos para acelerar o desenvolvimento de novas aplicações, seguindo as melhores práticas e convenções do ecossistema Laravel.

## Documentação do Kit

Toda a documentação sobre como utilizar os recursos, padrões e arquitetura deste kit está disponível na pasta [`/docs`](/docs). Recomendamos a leitura para todos os desenvolvedores que irão atuar neste projeto.

- [**Utilizando Enumerações (Enums) com Filament**](/docs/enums.md)
- [**Customização da Aparência do Painel**](/docs/customizando-layout.md)
- [**Autenticação de Dois Fatores (2FA) no Filament**](/docs/autenticacao-2fa.md)
- [**Entendendo o AppServiceProvider**](/docs/app-service-provider.md)
- [**Edição de Perfil no Filament**](/docs/edicao-perfil.md)
- [**Sistema de Suspensão de Usuários no Filament**](/docs/suspensao-usuarios.md)

## Como realizar a instalação

Siga os passos abaixo para configurar o ambiente de desenvolvimento localmente.

**1. Clonar o Repositório**

Primeiro, clone este repositório para a sua máquina local utilizando Git:

```bash
git clone git@github.com:iurygdeoliveira/labSIS-SaaS-KIT-V4.git
cd labSIS-SaaS-KIT-V4
```

**2. Instalar Dependências (PHP e JS)**

Execute os comandos abaixo para instalar as dependências do Composer (backend) e do NPM (frontend).

```bash
composer install
npm install
```

**3. Configurar o Ambiente**

Copie o arquivo de exemplo `.env.example` para criar seu próprio arquivo de configuração `.env`. Em seguida, gere a chave da aplicação, que é essencial para a segurança da sua instância Laravel.

```bash
cp .env.example .env
php artisan key:generate
```

**4. Configurar o Banco de Dados**

Este projeto está configurado para utilizar sqlite. Execute as migrations para criar as tabelas no banco de dados. Para popular o banco com dados de exemplo, execute as seeders.

```bash
php artisan migrate --seed
```

**5. Compilar os Assets**

Compile os arquivos de frontend (CSS e JavaScript) utilizando o Vite.

```bash
npm run build
```

**6. Iniciar o Servidor de Desenvolvimento**

Finalmente, inicie o servidor de desenvolvimento local do Laravel.

```bash
php artisan serve
```

Sua aplicação estará disponível em `http://127.0.0.1:8000`. Para o painel administrativo, acesse `http://127.0.0.1:8000/admin`.

## Agradecimentos

Gostaríamos de expressar nossa sincera gratidão a todas as pessoas e equipes cujo trabalho tornou este projeto possível. Suas contribuições para a comunidade de código aberto são uma fonte constante de inspiração e um pilar fundamental para o nosso desenvolvimento.

Em especial, agradecemos a:

-   **Equipe Laravel**: Pela criação e manutenção de um framework robusto, elegante e inovador, disponível em [laravel/laravel](https://github.com/laravel/laravel).
-   **Equipe Filament**: Pelo incrível trabalho no [Filament](https://github.com/filamentphp/filament), que nos permite construir painéis administrativos complexos com uma velocidade e simplicidade impressionantes.
-   **Comunidade Beer and Code** ([beerandcode.com.br](https://beerandcode.com.br/)): Pela excelente metodologia de ensino em Laravel, que tem colaborador com a formação de desenvolvedores PHP, fornecendo conhecimento prático e focado em soluções reais.
-   **Leandro Costa** ([@leandrocfe](https://github.com/leandrocfe)): Por suas valiosas contribuições e por compartilhar conhecimento de alta qualidade sobre Filament em seu canal [Filament Brasil no YouTube](https://www.youtube.com/@filamentbr), que foi fundamental para a implementação de diversas features neste projeto.
-   **Nanderson Castro** ([@NandoKstroNet](https://github.com/NandoKstroNet)): Pelo excelente trabalho no canal [Code Experts](https://www.youtube.com/@codeexperts), que tem sido uma fonte valiosa de conhecimento técnico e boas práticas de desenvolvimento.
-   **João Paulo Leite Nascimento** ([@joaopaulolndev](https://github.com/joaopaulolndev)): Pelo desenvolvimento do pacote [filament-edit-profile](https://github.com/joaopaulolndev/filament-edit-profile), que revolucionou a experiência de edição de perfil de usuários no Filament. Este pacote oferece uma solução completa e elegante para gerenciamento de perfis de usuário.
-   **Wallace Martins** ([@wallacemartinss](https://github.com/wallacemartinss)): Pela disponibilização do [website_template](https://github.com/wallacemartinss/website_template), que forneceu uma base excelente e moderna para a construção do portal público deste projeto.
-   **Jeferson Gonçalves** ([@jeffersongoncalves](https://github.com/jeffersongoncalves)): Pelo desenvolvimento do pacote [filament-cep-field](https://github.com/jeffersongoncalves/filament-cep-field), que agregou grande valor ao projeto ao fornecer um campo de formulário que busca e preenche automaticamente dados de endereço a partir de um CEP, otimizando a experiência do usuário.

O trabalho de vocês contribui significativamente para o avanço e a qualidade deste projeto.

## 🚀 Recursos Atuais

O Kit oferece uma base sólida com os seguintes recursos já implementados:

**Painel Administrativo (Filament)**
- **Segurança:**
  - **Autenticação de Dois Fatores (2FA):** Sistema de 2FA integrado ao perfil do usuário, compatível com aplicativos de autenticação (Google Authenticator, Authy, etc.).
  - **Códigos de Recuperação:** Geração de códigos de recuperação para acesso seguro em caso de perda do dispositivo de autenticação.
- **Gerenciamento de Usuários:**
  - CRUD completo para usuários (Criação, Leitura, Atualização e Exclusão).
  - **Sistema de Suspensão de Usuários:** Controle completo de acesso com toggle de suspensão, registro de motivo e timestamp automático.
  - **Prevenção de Auto-Suspensão:** Usuários não podem suspender suas próprias contas, garantindo acesso contínuo.
  - **Interface Visual Intuitiva:** Badges coloridos (verde para autorizado, vermelho para suspenso) na listagem de usuários.
  - **Organização em Abas:** Visualização detalhada organizada em abas (Informações Pessoais, Datas, Suspensão).
  - **Sincronização Automática:** Campos `is_suspended` e `suspended_at` sincronizados automaticamente.
  - **Controle de Acesso:** Usuários suspensos são automaticamente bloqueados do painel administrativo.
  - **Notificações de Feedback:** Sistema completo de notificações para todas as ações administrativas.
- **Edição de Perfil:**
  - **Sistema de Avatar:** Upload e gerenciamento de foto de perfil com suporte a PNG, JPG e JPEG (máximo 1MB).
  - **Configurações Personalizáveis:** Interface intuitiva para edição de informações pessoais, email e senha.
  - **Integração com 2FA:** Configuração e gerenciamento de autenticação de dois fatores diretamente no perfil.
  - **Códigos de Recuperação:** Geração e visualização de códigos de backup para acesso seguro.
  - **Suporte Multi-idioma:** Seleção de idioma preferido (Português, Inglês, Espanhol) com persistência de preferência.
  - **Menu Organizado:** Funcionalidade agrupada no menu "Configurações" para fácil acesso e organização.

**Website / Landing Page**
- **Página Inicial Completa:** Uma landing page moderna e responsiva construída com componentes Blade e TailwindCSS.
- **Seções Pré-definidas:**
  - **Hero:** Seção principal de boas-vindas.
  - **Benefícios:** Lista de vantagens da plataforma.
  - **Como Funciona:** Guia visual do processo.
  - **Depoimentos:** Seção de prova social com scroll automático.
  - **Tabela de Preços:** Componente interativo com seleção de ciclo de pagamento (mensal/anual).
  - **FAQ:** Acordeão de perguntas e respostas.
- **Navegação Integrada:** Header e footer padronizados com links de navegação e acesso direto à plataforma (`/admin`).

## 🛠️ Ferramentas de Desenvolvimento

Este projeto utiliza um conjunto de ferramentas para garantir a qualidade, padronização e agilidade no desenvolvimento. Abaixo estão os pacotes incluídos no ambiente de desenvolvimento (`require-dev`):

-   **[barryvdh/laravel-debugbar](https://github.com/barryvdh/laravel-debugbar):** Adiciona uma barra de depuração com informações úteis sobre a aplicação.
-   **[egyjs/dbml-to-laravel](https://github.com/egyjs/dbml-to-laravel):** Ferramenta para gerar migrações do Laravel a partir de um esquema DBML.
-   **[fakerphp/faker](https://github.com/fakerphp/faker):** Gera dados falsos para popular o banco de dados em testes e seeders.
-   **[larastan/larastan](https://github.com/larastan/larastan):** Realiza análise estática de código para encontrar bugs sem executar o código.
-   **[laravel/boost](https://packagist.org/packages/laravel/boost):** Otimiza o desempenho de Agentes de IA para o desenvolvimento do sistema em ambiente de desenvolvimento.
-   **[laravel/pail](https://github.com/laravel/pail):** Ferramenta para monitorar e filtrar os logs da aplicação em tempo real no terminal.
-   **[laravel/pint](https://github.com/laravel/pint):** Formata o código PHP para seguir um padrão de estilo consistente (PSR-12).
-   **[laravel/sail](https://github.com/laravel/sail):** Ambiente de desenvolvimento local completo baseado em Docker.
-   **[laravel/tinker](https://github.com/laravel/tinker):** Console interativo (REPL) para executar código no contexto da aplicação.
-   **[leonardolima/laravel-security-check](https://github.com/leonardolima/laravel-security-check):** Verifica dependências do Composer em busca de vulnerabilidades de segurança.
-   **[lucascudo/laravel-pt-br-localization](https://github.com/lucascudo/laravel-pt-br-localization):** Fornece traduções e configurações para a localização em português do Brasil.
-   **[mockery/mockery](https://github.com/mockery/mockery):** Framework para criar objetos de teste "mock" (simulados) para testes unitários.
-   **[nunomaduro/collision](https://github.com/nunomaduro/collision):** Apresenta erros e exceções de forma mais clara e informativa no terminal.
-   **[pestphp/pest](https://github.com/pestphp/pest):** Framework de testes elegante e focado no desenvolvedor para PHP.
 
## 📄 Licença

Este projeto está licenciado sob a [MIT License](LICENSE).

## 👥 Autor

- **Iury Oliveira** - [@iurygdeoliveira](https://github.com/iurygdeoliveira)

---

<div align="center">
  <strong>LabSIS - Transformando desafios reais em soluções inteligentes</strong>
</div