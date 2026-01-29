# Otimização de Cache de Página com Cloudflare

Esta documentação detalha a implementação da estratégia de "Full Page Cache" compatível com Cloudflare (e outros CDNs), permitindo servir páginas estáticas diretamente da borda (Edge) sem atingir o servidor Laravel para processamento.

## Objetivo

O comportamento padrão do middleware `web` do Laravel é "stateful" (com estado), o que significa que ele sempre inicia uma sessão e gera cookies (`csrf_token`, `laravel_session`).

Quando o Cloudflare detecta o cabeçalho `Set-Cookie` na resposta, ele **evita automaticamente fazer o cache da página** por segurança, para não servir a sessão de um usuário para outro.

**O objetivo desta implementação é:**

1.  Criar uma rota isolada que **não** inicie sessão.
2.  Adicionar cabeçalhos `Cache-Control` explícitos.
3.  Permitir que o Cloudflare armazene o HTML da Home Page e o sirva globalmente.

## Implementação

### 1. Middleware `SetCacheHeaders`

Foi criado um middleware específico para controlar os cabeçalhos de cache.

Arquivo: `app/Http/Middleware/SetCacheHeaders.php`

```php
public function handle(Request $request, Closure $next): Response
{
    $response = $next($request);

    // public: pode ser cacheado por qualquer um (CDN, Browser)
    // max-age=3600: cache no navegador do usuário por 1 hora
    // s-maxage=86400: cache no CDN (Cloudflare) por 24 horas
    $response->headers->set('Cache-Control', 'public, max-age=3600, s-maxage=86400');

    return $response;
}
```

### 2. Grupo de Middleware `static`

No `bootstrap/app.php`, definimos um novo grupo de middleware chamado `static`. Diferente do grupo `web` padrão, este grupo é minimalista e **stateless** (sem estado).

```php
$middleware->group('static', [
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
    \App\Http\Middleware\SetCacheHeaders::class,
]);
```

Observe que middlewares como `StartSession`, `EncryptCookies` e `VerifyCsrfToken` foram intencionalmente omitidos.

### 3. Aplicação na Rota

A rota da Home Page (`/`) foi alterada para utilizar este novo grupo `static` em vez do padrão.

## Resultado Esperado de Otimização

-   **Menor Latência (TTFB):** O tempo até o primeiro byte (TTFB) cairá drasticamente para visitantes, pois o HTML será entregue pelo servidor do Cloudflare mais próximo, e não pelo servidor de origem.
-   **Redução de Carga no Servidor:** O servidor Laravel deixará de processar requisições para a Home Page, economizando CPU e RAM para tarefas que realmente exigem processamento dinâmico (com login, dashboard, etc).
-   **Escalabilidade:** O site poderá suportar picos de tráfego massivos na Home Page sem degradar a performance da aplicação.

## Trechos Comentados no `bootstrap/app.php`

Durante a configuração, alguns trechos foram comentados para fins de referência ou limpeza:

```php
// commands: __DIR__.'/../routes/console.php',
// health: '/up',
```

-   `commands`: Carrega as rotas de console (comandos `artisan` personalizados). Comentar isso desabilita o carregamento automático dessas rotas, o que pode ser uma micro-otimização se não forem usadas, mas geralmente são mantidas.
-   `health`: Define a rota `/up` para verificação de saúde da aplicação (Health Check). Comentar desabilita essa rota padrão do Laravel 11.

## Referência

Esta implementação foi baseada no artigo:

> [**Separate Your Cloudflare Page Cache with a Middleware Group**](https://laravel-news.com/separate-your-cloudflare-page-cache-with-a-middleware-group) - Laravel News

## Referências

- [Article: Separate Your Cloudflare Page Cache with a Middleware Group](https://laravel-news.com/separate-your-cloudflare-page-cache-with-a-middleware-group)
- [Middleware: SetCacheHeaders](/labsis-kit/app/Http/Middleware/SetCacheHeaders.php)
- [Bootstrap: app.php](/labsis-kit/bootstrap/app.php)
