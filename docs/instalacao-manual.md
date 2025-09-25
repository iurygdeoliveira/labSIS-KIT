# Instalação manual (clonando o repositório)

Siga os passos abaixo em sequência (um único passo a passo):

1. Pré‑requisitos: Git, Composer, Node.js 18+ e, opcionalmente, Docker com Laravel Sail.
2. Clonar o repositório (ou o seu fork) — escolha UMA das opções:

- HTTPS

```bash
git clone https://github.com/iurygdeoliveira/labSIS-KIT.git minha-app
cd minha-app
```

- SSH

```bash
git clone git@github.com:iurygdeoliveira/labSIS-KIT.git minha-app
cd minha-app
```

4. Executar o script de instalação (obrigatório):

```bash
./vendor/bin/sail up -d
./vendor/bin/sail php install.php
```

Se tiver o PHP instalado globalmente, prefira executar via PHP:

```bash
php install.php
```
5. Ajustar o arquivo `.env` conforme necessário (DB, portas e variáveis).

A aplicação ficará disponível em `http://localhost`.

