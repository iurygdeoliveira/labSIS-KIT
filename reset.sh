#!/bin/bash

# Script de reset otimizado para desenvolvimento
# Este script limpa caches e reconstrói assets para ambiente de desenvolvimento

echo "🚀 Iniciando reset de desenvolvimento..."

# Limpar cache do Laravel
echo "🧹 Limpando cache e assets do sistema..."
php artisan optimize:clear
php artisan filament:optimize-clear
rm -rf public/build

#Instalando dependências do sistema
composer update --optimize-autoloader
npm update

# Build para desenvolvimento
echo "🔨 Executando build para desenvolvimento..."
npm run build