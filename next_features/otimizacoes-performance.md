# 🚀 Plano de Otimização de Performance - LabSIS-KIT

> ⚠️ **Nota:** Este documento foi adaptado especificamente para:
> - Ambiente de desenvolvimento usando Laravel Sail (PHP built-in server)  
> - Aplicação usando Filament Panel Providers  
> - Middlewares devem ser registrados APENAS nos Panel Providers do Filament

## 📊 Resumo dos Problemas Identificados

### Painéis Filament (Admin, User)
- **Foco:** Otimização de performance dos painéis administrativos
- **Áreas de melhoria:** Cache de recursos, compressão de respostas, consultas de banco

---

## 🎯 Soluções Propostas

### 1. 💾 **Implementar Cache de Longo Prazo (Prioridade ALTA)**

#### Problema:
- 1.9MB desperdiçados por falta de cache
- Recursos estáticos sendo baixados a cada visita

#### Soluções:

**2.1. Criar middleware para headers de cache:**

```php
// app/Http/Middleware/CacheControlMiddleware.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheControlMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        $path = $request->getPathInfo();
        
        // Cache de longo prazo para assets estáticos
        if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$/i', $path)) {
            return $response
                ->header('Cache-Control', 'public, max-age=31536000, immutable')
                ->header('Expires', gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        }
        
        // Cache de curto prazo para HTML
        if (preg_match('/\.(html|htm)$/i', $path)) {
            return $response
                ->header('Cache-Control', 'public, max-age=3600');
        }
        
        return $response;
    }
}
```

**2.2. Registrar o middleware nos Panel Providers do Filament:**

Como você está usando Filament, os middlewares devem ser registrados nos Panel Providers. Adicione nos painéis:

```php
// app/Providers/Filament/BasePanelProvider.php (afeta todos os painéis)
public function panel(Panel $panel): Panel
{
    return $panel
        ->id($this->getPanelId())
        ->path($this->getPanelPath())
        ->spa()
        // ... outras configurações ...
        ->middleware([
            CacheControlMiddleware::class, // ✅ Adicionar aqui
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            DisableBladeIconComponents::class,
            DispatchServingFilamentEvent::class,
            RedirectGuestsToCentralLoginMiddleware::class,
            RedirectToProperPanelMiddleware::class,
        ])
        // ... restante ...
}
```

### 2. ⚡ **Otimizar Performance dos Painéis (Prioridade ALTA)**

#### Problema:
- Render delay nos painéis Filament
- JavaScript bloqueando a renderização
- Consultas de banco ineficientes

#### Soluções:

**2.1. Otimizar consultas de banco de dados:**
```php
// Usar eager loading para evitar N+1 queries
$users = User::with(['roles', 'tenant'])->get();

// Implementar cache de consultas frequentes
$users = Cache::remember('users.active', 3600, function () {
    return User::where('status', 'active')->get();
});
```

**2.2. Configurar OPcache no Docker (Sail):**

Como você está usando Laravel Sail, o OPcache já vem habilitado por padrão no container PHP. Para otimizar ainda mais:

```ini
# docker/8.4/php.ini ou criar um arquivo custom php.ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=0
opcache.fast_shutdown=1
```

Para aplicar no Sail:
```bash
./vendor/bin/sail artisan config:clear
./vendor/bin/sail restart
```

**2.3. Otimizar Livewire:**
```php
// config/livewire.php
'asset_url' => env('APP_URL'),
'asset_path' => '/vendor/livewire',
'back_button_cache' => true,
'disable_scripts' => false,
'disable_style' => false,
```

### 3. 🌐 **Melhorar Time to First Byte (TTFB)**

#### Problema:
- TTFB alto nos painéis Filament
- Respostas sem compressão

#### Soluções:

**3.1. Implementar compressão via middleware (Gzip):**

```php
// app/Http/Middleware/CompressResponseMiddleware.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompressResponseMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Verifica se o cliente aceita compressão
        if (str_contains($request->headers->get('Accept-Encoding'), 'gzip')) {
            $content = $response->getContent();
            
            if ($content && strlen($content) > 1024) {
                $compressed = gzencode($content, 6);
                
                if ($compressed !== false) {
                    $response->setContent($compressed);
                    $response->headers->set('Content-Encoding', 'gzip');
                    $response->headers->set('Vary', 'Accept-Encoding');
                    $response->headers->remove('Content-Length');
                }
            }
        }
        
        return $response;
    }
}
```

**3.2. Registrar o middleware de compressão nos Panel Providers:**

Como você está usando Filament, registre também nos Panel Providers:

```php
// app/Providers/Filament/BasePanelProvider.php
public function panel(Panel $panel): Panel
{
    return $panel
        ->id($this->getPanelId())
        ->path($this->getPanelPath())
        ->spa()
        // ... outras configurações ...
        ->middleware([
            CompressResponseMiddleware::class, // ✅ Adicionar aqui (antes do cache)
            CacheControlMiddleware::class,
            EncryptCookies::class,
            // ... demais middlewares ...
        ])
        // ... restante ...
}
```

**Nota sobre ordem de execução dos middlewares:**
1. Primeiro executa `CompressResponseMiddleware` (compressão)
2. Depois executa `CacheControlMiddleware` (headers de cache)

**3.4. Comandos úteis para desenvolvimento com Sail:**

```bash
# Limpar todos os caches
./vendor/bin/sail artisan optimize:clear

# Otimizar para produção (desenvolvimento)
./vendor/bin/sail artisan config:cache
./vendor/bin/sail artisan route:cache
./vendor/bin/sail artisan view:cache

# Rebuild dos assets (quando modificar CSS/JS)
./vendor/bin/sail npm run build

# Ver logs em tempo real
./vendor/bin/sail logs -f
```

### 4. 🔧 **Otimizações Específicas do Laravel**

#### Soluções:

**4.1. Configurar cache de rotas:**
```bash
# Produção
php artisan route:cache
php artisan config:cache
php artisan view:cache
```

**4.2. Otimizar autoloader:**
```bash
composer install --optimize-autoloader --no-dev
```
`

## ⚙️ **Considerações Importantes para Filament**

### **Gerenciamento de Middlewares:**

O Filament gerencia middlewares através dos **Panel Providers** (`app/Providers/Filament/`):

- Cada painel (Admin, User, Auth) pode ter seu próprio conjunto de middlewares
- O `BasePanelProvider` define middlewares compartilhados para todos os painéis
- Middlewares devem ser adicionados no array `->middleware([])` de cada painel
- **Foco:** Otimizações aplicadas APENAS nos painéis Filament

### **Ordem de Execução:**

A ordem dos middlewares importa. Os middlewares de performance devem ser adicionados na seguinte ordem:

```php
->middleware([
    CompressResponseMiddleware::class,    // 1. Primeiro comprime a resposta
    CacheControlMiddleware::class,          // 2. Depois adiciona headers de cache
    // ... demais middlewares do Filament ...
])
```

### **Aplicação Global:**

Para que as otimizações funcionem em todos os painéis Filament:
1. Adicione os middlewares no `BasePanelProvider` (afeta todos os painéis Admin, User, Auth)
2. Os middlewares serão aplicados automaticamente em todas as rotas dos painéis Filament

