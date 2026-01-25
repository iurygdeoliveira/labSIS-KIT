# Instalação via Laravel Installer

Use o Laravel Installer com `--using` para criar um novo projeto a partir deste Starter Kit.

### 1) Pré‑requisitos

Instale/atualize o Laravel Installer globalmente e confirme a versão:

```bash
composer global require laravel/installer
laravel --version
```

Se necessário, adicione o bin do Composer ao PATH (Linux/macOS):

```bash
export PATH="$HOME/.config/composer/vendor/bin:$HOME/.composer/vendor/bin:$PATH"
```

### 2) Criar o projeto usando este repositório

```bash
laravel new minha-app --using=https://github.com/iurygdeoliveira/labSIS-KIT
```

### 3) Pós‑instalação

Entre no diretório e execute o script de instalação (idempotente):

```bash
cd minha-app
./vendor/bin/sail up -d
./vendor/bin/sail php install.php

Se tiver o PHP instalado globalmente, prefira executar via PHP:

```bash
php install.php
```


A aplicação ficará disponível em `http://localhost` (ajuste portas no `.env` se necessário).

### 4) (Opcional) Iniciar versionamento do novo projeto

```bash
git init
git add .
git commit -m "chore: inicializa projeto a partir do starter kit"
git branch -M main
git remote add origin https://github.com/SEU_USUARIO/minha-app.git
git push -u origin main
git remote add origin https://github.com/SEU_USUARIO/minha-app.git
git push -u origin main
```

## Referências

- [Script: Install](/install.php)
