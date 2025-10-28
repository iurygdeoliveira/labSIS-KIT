#!/bin/bash

# Script de reset otimizado para desenvolvimento
# Este script limpa caches e reconstrói assets para ambiente de desenvolvimento
# Uso: ./reset.sh [--install] [--reset]

set -Eeuo pipefail

# Verificar se é modo instalação
INSTALL_MODE=false
if [[ "${1:-}" == "--install" ]]; then
    INSTALL_MODE=true
    echo "🚀 Iniciando configuração pós-instalação..."
else
    echo "🚀 Iniciando reset de desenvolvimento..."
fi

# Função para executar comando com tratamento de erro
safe_artisan() {
    local cmd="$1"
    local description="$2"
    
    echo "⏳ $description..."
    
    if ./vendor/bin/sail artisan "$cmd" 2>/dev/null; then
        echo "✅ $description concluído"
        return 0
    else
        echo "⚠️  $description falhou (ignorando - pode ser esperado durante instalação)"
        return 0
    fi
}

# Se NÃO for modo instalação, limpar cache normalmente
if [[ "$INSTALL_MODE" != "true" ]]; then
    echo "🧹 Limpando cache e assets do sistema..."
    safe_artisan "optimize:clear" "Limpando cache otimizado"
    safe_artisan "filament:optimize-clear" "Limpando cache do Filament"
fi

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

# Executar migrations PRIMEIRO
if [[ "$INSTALL_MODE" == "true" ]]; then
    echo "🗄️ Executando migrations e seeders (modo instalação)..."
    ./vendor/bin/sail artisan migrate:fresh --seed
else
    echo "🔄 Executando reset de banco de dados..."
    ./vendor/bin/sail artisan migrate:fresh --seed
fi

# AGORA podemos limpar cache com segurança
echo "🧹 Limpando cache após migrations..."
safe_artisan "config:clear" "Limpando cache de configuração"
safe_artisan "clear-compiled" "Limpando arquivos compilados"
safe_artisan "optimize:clear" "Limpando cache otimizado"
safe_artisan "package:discover" "Redescobrindo pacotes"

# Atualizar dependências do Node
echo "📦 Atualizando dependências do NPM..."
npm update || true

# Build para desenvolvimento
echo "🔨 Executando build para desenvolvimento..."
./vendor/bin/sail exec laravel.test npm run build

# Verificar e corrigir permissões de forma segura
echo "🔐 Verificando e corrigindo permissões de arquivos..."

# Função para verificar se um diretório é gravável
check_writable() {
    local dir="$1"
    local description="$2"
    
    if ./vendor/bin/sail exec laravel.test test -w "$dir" 2>/dev/null; then
        echo "✅ $description OK: $dir"
        return 0
    else
        echo "❌ $description não tem permissão de escrita: $dir"
        return 1
    fi
}

# Verificar permissões antes de corrigir
echo "📋 Verificando permissões atuais..."
has_issues=false

if ! check_writable "/var/www/html/storage" "Diretório de armazenamento"; then
    has_issues=true
fi

if ! check_writable "/var/www/html/bootstrap/cache" "Cache do Bootstrap"; then
    has_issues=true
fi

if ! check_writable "/var/www/html/storage/framework/sessions" "Sessões do Laravel"; then
    has_issues=true
fi

if ! check_writable "/var/www/html/storage/logs" "Logs do sistema"; then
    has_issues=true
fi

# Corrigir permissões se necessário
if [ "$has_issues" = true ]; then
    echo "🔧 Corrigindo permissões..."
    ./vendor/bin/sail exec laravel.test chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
    ./vendor/bin/sail exec laravel.test chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache
    ./vendor/bin/sail exec laravel.test chmod -R 775 /var/www/html/storage/framework/sessions /var/www/html/storage/logs
    
    echo "✅ Permissões corrigidas com sucesso!"
else
    echo "✅ Todas as permissões já estão corretas!"
fi

echo "✅ Script de reset concluído com sucesso!"