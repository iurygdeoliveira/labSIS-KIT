# SecurityHeadersMiddleware - Headers de Segurança HTTP

## 📋 Índice

- [Introdução](#introdução)
- [Objetivo](#objetivo)
- [Headers Implementados](#headers-implementados)
- [Implementação](#implementação)
- [Configuração](#configuração)
- [Content Security Policy](#content-security-policy)
- [Testes e Validação](#testes-e-validação)
- [Troubleshooting](#troubleshooting)
- [Conclusão](#conclusão)

## Introdução

O `SecurityHeadersMiddleware` é um middleware personalizado desenvolvido para adicionar headers de segurança HTTP essenciais a todas as requisições da aplicação. Este middleware complementa as proteções nativas do Filament v4, fornecendo uma camada adicional de segurança contra vulnerabilidades web comuns.

A implementação segue as melhores práticas de segurança web e é compatível com o ecossistema Laravel e Filament, garantindo que todos os painéis e rotas estejam protegidos adequadamente.

## Objetivo

O objetivo principal deste middleware é implementar uma política de segurança robusta através de headers HTTP, protegendo a aplicação contra:

- **Clickjacking**: Ataques que tentam enganar usuários clicando em elementos invisíveis
- **MIME Type Sniffing**: Tentativas do navegador de adivinhar tipos de conteúdo
- **Cross-Site Scripting (XSS)**: Injeção de scripts maliciosos
- **Man-in-the-Middle**: Ataques de interceptação de dados
- **Data Leakage**: Vazamento de informações através de referrers
- **Resource Injection**: Carregamento de recursos não autorizados

## Headers Implementados

### 1. X-Frame-Options: DENY
**Proteção**: Previne clickjacking
**Valor**: `DENY`
**Descrição**: Impede que a página seja exibida em frames ou iframes, bloqueando completamente tentativas de clickjacking.

### 2. X-Content-Type-Options: nosniff
**Proteção**: Previne MIME type sniffing
**Valor**: `nosniff`
**Descrição**: Impede que o navegador tente adivinhar o tipo de conteúdo baseado no conteúdo do arquivo.

### 3. X-XSS-Protection: 1; mode=block
**Proteção**: Proteção adicional contra XSS
**Valor**: `1; mode=block`
**Descrição**: Ativa a proteção XSS do navegador para navegadores mais antigos, bloqueando requisições suspeitas.

### 4. Referrer-Policy: strict-origin-when-cross-origin
**Proteção**: Controla informações de referência
**Valor**: `strict-origin-when-cross-origin`
**Descrição**: Envia a origem completa para requisições do mesmo site e apenas a origem para requisições cross-origin.

### 5. Content-Security-Policy (CSP)
**Proteção**: Previne ataques XSS e injeção de recursos
**Valor**: Configuração personalizada para Laravel + Filament
**Descrição**: Define quais recursos podem ser carregados e de onde, criando uma política restritiva mas compatível.

### 6. Strict-Transport-Security
**Proteção**: Força uso de HTTPS
**Valor**: `max-age=31536000; includeSubDomains; preload`
**Descrição**: Força o navegador a usar HTTPS por 1 ano, incluindo subdomínios.

### 7. Permissions-Policy
**Proteção**: Controla APIs do navegador
**Valor**: `camera=(), microphone=(), geolocation=(), payment=()`
**Descrição**: Desabilita APIs sensíveis do navegador que não são necessárias para a aplicação.

## Implementação

### Estrutura do Middleware

```php
<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Aplicar headers de segurança
        $this->applySecurityHeaders($request, $response);

        return $response;
    }

    private function applySecurityHeaders(Request $request, Response $response): void
    {
        // Implementação dos headers...
    }

    private function buildContentSecurityPolicy(): string
    {
        // Construção da CSP personalizada...
    }
}
```

### Método Principal

O método `handle()` é responsável por:
1. Processar a requisição através da cadeia de middlewares
2. Aplicar os headers de segurança na resposta
3. Retornar a resposta modificada

### Headers Básicos

```php
// X-Frame-Options: Previne clickjacking
$response->headers->set('X-Frame-Options', 'DENY');

// X-Content-Type-Options: Previne MIME type sniffing
$response->headers->set('X-Content-Type-Options', 'nosniff');

// X-XSS-Protection: Proteção adicional contra XSS
$response->headers->set('X-XSS-Protection', '1; mode=block');

// Referrer-Policy: Controla informações de referência
$response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
```

## Configuração

### 1. Registro no Filament (Recomendado)

O middleware é registrado no `BasePanelProvider` para aplicar aos painéis do Filament:

```php
// app/Providers/Filament/BasePanelProvider.php
use App\Http\Middleware\SecurityHeadersMiddleware;

public function panel(Panel $panel): Panel
{
    return $panel
        // ... outras configurações
        ->middleware([
            SecurityHeadersMiddleware::class,  // ← Primeiro na lista
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            // ... outros middlewares
        ]);
}
```

### 2. Registro para Rotas Web

Para cobrir rotas que não passam pelo Filament:

```php
// bootstrap/app.php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->web(append: [
        \App\Http\Middleware\SecurityHeadersMiddleware::class,
    ]);
})
```

### 3. Ordem dos Middlewares

A ordem é importante! O `SecurityHeadersMiddleware` deve ser executado:
- **Primeiro** nos painéis do Filament
- **Após** middlewares de autenticação nas rotas web

## Content Security Policy

### Configuração Personalizada

A CSP é construída especificamente para Laravel + Filament:

```php
private function buildContentSecurityPolicy(): string
{
    $csp = [
        "default-src 'self'",
        "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://unpkg.com",
        "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net",
        "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net",
        "img-src 'self' data: https: blob:",
        "connect-src 'self' https: wss:",
        "media-src 'self' https: blob:",
        "object-src 'none'",
        "base-uri 'self'",
        "form-action 'self'",
        "frame-ancestors 'none'",
        'upgrade-insecure-requests',
    ];

    return implode('; ', $csp);
}
```

### Diretivas Explicadas

- **default-src 'self'**: Apenas recursos do mesmo domínio por padrão
- **script-src**: Permite scripts inline (necessário para Livewire/Filament)
- **style-src**: Permite estilos inline (necessário para Tailwind)
- **img-src**: Permite imagens de várias fontes (data:, https:, blob:)
- **connect-src**: Permite conexões WebSocket (Livewire)
- **object-src 'none'**: Bloqueia objetos (Flash, Java, etc.)
- **frame-ancestors 'none'**: Complementa X-Frame-Options
- **upgrade-insecure-requests**: Força HTTPS quando disponível


## Compatibilidade com Gestão de Mídias

### ✅ **Totalmente Compatível**

O `SecurityHeadersMiddleware` é **100% compatível** com o sistema de gestão de mídias do projeto:

#### **Por que não há impacto:**

1. **URLs Assinadas**: O sistema usa URLs temporárias assinadas do MinIO/S3 que preservam os MIME types originais
2. **Spatie Media Library**: Gerencia corretamente os MIME types durante upload e armazenamento
3. **MinIO/S3**: Serve arquivos com `Content-Type` correto automaticamente
4. **Validação Prévia**: O enum `MediaAcceptedMime` já valida tipos de arquivo antes do upload

#### **Fluxo de Funcionamento:**

```
Upload → Validação MIME → Spatie Media Library → MinIO/S3 → URL Assinada → Navegador
   ↓              ↓                ↓              ↓           ↓            ↓
MIME correto → MIME preservado → MIME armazenado → Content-Type correto → X-Content-Type-Options respeitado
```

#### **Tipos de Mídia Suportados:**

- **Imagens**: `image/*` (JPEG, PNG, GIF, WebP, etc.)
- **Áudios**: `audio/*` (MP3, WAV, OGG, M4A, AAC)
- **Documentos**: `application/pdf`, `application/msword`, `application/vnd.openxmlformats-officedocument.*`
- **Vídeos**: URLs externas (YouTube, Vimeo, etc.)

#### **Verificação de Compatibilidade:**

```php
// Teste de compatibilidade
use App\Models\MediaItem;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

$media = MediaItem::first();
$spatieMedia = $media->getFirstMedia('media');

// MIME type será preservado corretamente
echo 'MIME Type: ' . $spatieMedia->mime_type;
echo 'URL: ' . $spatieMedia->getUrl(); // Content-Type correto no MinIO
```

## Problemas Comuns

#### 1. Erros de CSP com Fontes
**Sintoma**: `Refused to load the font 'data:application/font-woff2...'`
**Solução**: Adicionar `data:` ao `font-src` se necessário, ou usar fontes externas

#### 2. Scripts Inline Bloqueados
**Sintoma**: JavaScript não executa
**Solução**: Verificar se `'unsafe-inline'` está presente no `script-src`

#### 3. WebSockets Bloqueados
**Sintoma**: Livewire não funciona corretamente
**Solução**: Verificar se `wss:` está presente no `connect-src`

#### 4. Headers Duplicados
**Sintoma**: Headers aparecem múltiplas vezes
**Solução**: Verificar se o middleware não está registrado em múltiplos lugares


## Conclusão

O `SecurityHeadersMiddleware` é uma implementação essencial para a segurança da aplicação, fornecendo proteção abrangente contra vulnerabilidades web comuns. Sua integração com o Filament v4 garante que todos os painéis estejam protegidos, enquanto a configuração para rotas web assegura cobertura completa.

A implementação segue as melhores práticas de segurança e é facilmente configurável para diferentes ambientes. O middleware complementa perfeitamente as proteções nativas do Filament, criando uma camada robusta de segurança HTTP.


