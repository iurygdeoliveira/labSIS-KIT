# Instalação manual (clonando o repositório)

Siga os passos abaixo em sequência:

## 1. Clonar o repositório

Escolha UMA das opções:

**HTTPS:**

```bash
git clone https://github.com/iurygdeoliveira/labSIS-KIT.git minha-app
cd minha-app
```

**SSH:**

```bash
git clone git@github.com:iurygdeoliveira/labSIS-KIT.git minha-app
cd minha-app
```

## 2. Executar o script de instalação

> **Pré-requisito:** Certifique-se de que o **Docker** está instalado e rodando antes de prosseguir.

```bash
php install.php
```

O script irá:

-   ✅ Verificar e instalar automaticamente: PHP 8.5, extensões PHP e Composer
-   ✅ Remover Apache2 se instalado (conflito com Nginx do Sail)
-   ✅ Orientar instalação manual de: Node.js e Docker
-   ✅ Configurar permissões Docker automaticamente
-   ✅ Criar arquivo `.env` a partir de `.env.example`
-   ✅ Instalar dependências Composer
-   ✅ Iniciar containers Sail
-   ✅ Executar migrations e seeders
-   ✅ Instalar dependências NPM e build dos assets

### Credenciais padrão:

-   **Admin**: admin@labsis.dev.br / mudar123
-   **Sicrano**: sicrano@labsis.dev.br / mudar123
-   **Beltrano**: beltrano@labsis.dev.br / mudar123
