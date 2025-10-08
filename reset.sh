#!/bin/bash

# Script de reset otimizado para desenvolvimento
# Este script limpa caches e reconstrói assets para ambiente de desenvolvimento

set -Eeuo pipefail

echo "🚀 Iniciando reset de desenvolvimento..."

# Limpar cache do Laravel
echo "🧹 Limpando cache e assets do sistema..."
./vendor/bin/sail artisan optimize:clear || true
./vendor/bin/sail artisan filament:optimize-clear || true

# Remover assets de build usando Docker para evitar problemas de permissão
echo "🗑️ Removendo assets de build..."
./vendor/bin/sail exec laravel.test rm -rf /var/www/html/public/build || true

# Remover caches que podem referenciar providers antigos
rm -f bootstrap/cache/packages.php bootstrap/cache/services.php || true

# Atualizar dependências do sistema (Composer) sem scripts para evitar Artisan durante estado inconsistente
echo "📦 Atualizando dependências do Composer (sem scripts)..."
composer update --no-scripts --optimize-autoloader

# Regenerar autoload sem scripts
composer dump-autoload -o --no-scripts

# Redescobrir pacotes e limpar caches com vendor atualizado
echo "🔎 Redescobrindo pacotes e limpando caches..."
./vendor/bin/sail artisan package:discover
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan clear-compiled

# Atualizar dependências do Node
echo "📦 Atualizando dependências do NPM..."
npm update

# Build para desenvolvimento
echo "🔨 Executando build para desenvolvimento..."
./vendor/bin/sail exec laravel.test npm run build

# Executando migrations e seeders
./vendor/bin/sail artisan migrate:fresh --seed