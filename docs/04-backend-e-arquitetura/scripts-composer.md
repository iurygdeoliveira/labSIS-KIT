# Scripts do Composer e Inicialização de Storage

## 📋 Índice

-   [Introdução](#introdução)
-   [Scripts Disponíveis](#scripts-disponíveis)
    -   [setup](#setup)
    -   [reset](#reset)
    -   [deploy](#deploy)
-   [Inicialização Otimizada de Storage (S3/MinIO)](#inicialização-otimizada-de-storage-s3minio)
    -   [O Problema Anterior](#o-problema-anterior)
    -   [A Solução: `storage:init`](#a-solução-storageinit)

## Introdução

Para padronizar e automatizar o ciclo de vida da aplicação (instalação, reset de ambiente e deploy), utilizamos scripts personalizados no `composer.json`. Isso elimina a necessidade de scripts shell externos (como o antigo `reset.sh`) e centraliza a execução de comandos críticos.

Além disso, introduzimos uma estratégia otimizada para a inicialização de buckets S3/MinIO, removendo latência das requisições web.

## Scripts Disponíveis

Os scripts podem ser executados via `composer run <nome-do-script>`. Em ambiente de desenvolvimento com Sail, utilize `./vendor/bin/sail composer run <nome-do-script>`.

### `setup`

Responsável pela configuração inicial do projeto. Deve ser executado logo após clonar o repositório.

**O que ele faz:**

1.  Instala dependências PHP (`composer install`).
2.  Cria o arquivo `.env` caso não exista.
3.  Gera a chave da aplicação (`key:generate`).
4.  Executa migrações de banco de dados (`migrate`).
5.  Inicializa diretórios de storage (`storage:init`).
6.  Otimiza componentes e ícones do Filament (`filament:optimize`).
7.  Instala e compila assets de frontend (`npm install` e `npm run build`).

### `reset`

Substituto do antigo `reset.sh`. Utilizado durante o desenvolvimento para "limpar a casa" e recomeçar do zero.

**O que ele faz:**

1.  Limpa caches do Laravel e do Filament.
2.  Redescobre pacotes (`package:discover`).
3.  Reseta o banco de dados e roda seeds (`migrate:fresh --seed`).
4.  Garante a estrutura de storage (`storage:init`).
5.  Otimiza componentes e ícones do Filament (`filament:optimize`).
6.  Recompila assets (`npm run build`).
7.  Executa a suíte de testes (`test`).

### `deploy`

Destinado a ambientes de produção (CI/CD ou servidor final). Executa passos de otimização e deploy seguro.

**O que ele faz:**

1.  Instala dependências de produção (`--no-dev`).
2.  Executa migrações (`migrate --force`).
3.  Garante estrutura de storage (`storage:init`).
4.  Cacheia configurações, eventos, rotas e views.
5.  Otimiza componentes e ícones do Filament (`filament:optimize`).
6.  Compila assets de frontend.

## Inicialização Otimizada de Storage (S3/MinIO)

### O Problema Anterior

Anteriormente, a verificação e criação de diretórios no S3 (`audios`, `images`, `documents`, `avatar`) ocorria no método `boot` do `AppServiceProvider`. Isso significava que **toda requisição web** fazia chamadas de rede para o S3 para verificar a existência dessas pastas, adicionando uma latência significativa (200ms a 1s) no carregamento das páginas.

### A Solução: `storage:init`

Extraímos essa lógica para um comando Artisan dedicado: `storage:init`.

Este comando:

1.  Verifica se as credenciais do S3 estão configuradas.
2.  Cria os diretórios necessários no bucket.
3.  Adiciona arquivos `.keep` para garantir a persistência das pastas.

**Integração:**
O comando `storage:init` foi adicionado aos scripts `setup`, `reset` e `deploy` no `composer.json`. Dessa forma, garantimos que a estrutura de armazenamento esteja correta apenas nos momentos necessários (instalação ou deploy), eliminando completamente o overhead durante a navegação dos usuários.
