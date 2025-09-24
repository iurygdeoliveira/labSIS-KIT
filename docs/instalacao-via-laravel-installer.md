# Instalação via Laravel Installer

A opção 1 (Laravel Installer com `--using`) instala o projeto diretamente a partir do GitHub, sem precisar publicar no Packagist. Abaixo estão os passos essenciais, com exemplos práticos e pós-instalação recomendados.

### 1) Pré‑requisitos

Instale/atualize o Laravel Installer globalmente:

```bash
composer global require laravel/installer
laravel --version
```

Se necessário, garanta o bin do Composer no PATH (Linux/macOS):

```bash
export PATH="$HOME/.composer/vendor/bin:$HOME/.config/composer/vendor/bin:$PATH"
```

### 2) Instalar usando o seu repositório do GitHub

Instalação a partir deste repositório:

```bash
laravel new minha-app --using=iurygdeoliveira/labSIS-SaaS-KIT-V4 --database=pgsql
```

Para instalar a partir de um fork (recomendado para cada usuário/time), basta trocar pelo usuário do GitHub:

```bash
laravel new minha-app --using=SEU_USUARIO/labSIS-SaaS-KIT-V4 --database=pgsql
```

Referência com exemplo semelhante: FilaKit usa o mesmo fluxo com `--using` no README do projeto (GitHub: `https://github.com/iurygdeoliveira/filakitv4`).

### 3) Pós‑instalação (no diretório gerado)

O Installer remove o `.git` e prepara a base do projeto. Em seguida:

```bash
cd minha-app

composer install
npm install

cp .env.example .env
```

Edite o `.env` e ajuste as credenciais do banco e a variável `GITHUB_REPOSITORY` para apontar para o fork de quem instalou, por exemplo:

```
GITHUB_REPOSITORY="https://github.com/SEU_USUARIO/labSIS-SaaS-KIT-V4"
```

Suba os containers e rode as migrações/seeders:

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan storage:link
./vendor/bin/sail artisan migrate --seed
```

Construa os assets (ou rode em modo dev):

```bash
./vendor/bin/sail npm run build
# ou
./vendor/bin/sail npm run dev
```

A aplicação ficará disponível em `http://localhost`.

### 4) (Opcional) Versionar o projeto recém‑criado

Como o Installer remove o histórico, se quiser subir o novo projeto para um repositório:

```bash
git init
git add .
git commit -m "chore: inicializa projeto a partir do starter kit"
git branch -M main
git remote add origin https://github.com/SEU_USUARIO/minha-app.git
git push -u origin main
```

### O que publicar no seu README

Inclua um bloco curto para usuários:

```bash
# Instalação rápida com Laravel Installer
laravel new minha-app --using=iurygdeoliveira/labSIS-SaaS-KIT-V4 --database=pgsql

cd minha-app
composer install
npm install
cp .env.example .env
# Ajuste GITHUB_REPOSITORY e DB no .env
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan storage:link
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm run dev
```
