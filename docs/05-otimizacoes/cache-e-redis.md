# Cache e Redis no Projeto

## 📋 Índice

- [Introdução](#introdução)
- [Por que usar Cache?](#por-que-usar-cache)
- [Driver e Configuração (Redis)](#driver-e-configuração-redis)
- [Arquitetura no Projeto](#arquitetura-no-projeto)
  - [FilamentStatsCache](#filamentstatscache)
  - [Serviço de Metadados de Vídeo (YouTube)](#serviço-de-metadados-de-vídeo-youtube)
  - [Widget de Estatísticas (SystemStats)](#widget-de-estatísticas-systemstats)
  - [Páginas de Mídia (Filament)](#páginas-de-mídia-filament)
  - [Observers para Invalidação](#observers-para-invalidação)
- [Implementação](#implementação)
  - [Chaves e TTLs](#chaves-e-ttls)
  - [Exemplos de Código](#exemplos-de-código)
- [Testes e Validação](#testes-e-validação)
- [Boas Práticas](#boas-práticas)
- [Problemas Comuns](#problemas-comuns)
- [Conclusão](#conclusão)

## Introdução

Este documento descreve como o **cache** foi implementado no projeto utilizando **Redis** como store principal. O objetivo é reduzir latência de chamadas externas, aliviar carga em agregações e oferecer uma experiência mais responsiva no painel administrativo.

## Por que usar Cache?

- Reduzir chamadas externas repetitivas (ex.: YouTube oEmbed/HTML).
- Diminuir tempo de renderização em telas com **count/sum** intensivos.
- Evitar recomputar valores de curta duração (ex.: URLs temporárias).
- Melhorar a sensação de performance no dashboard e listagens.

## Driver e Configuração (Redis)

- `config/cache.php`: `default => redis` (padrão do projeto).
- `config/database.php` possui blocos `redis.default` e `redis.cache`.
- Exemplo de variáveis `.env`:

```env
CACHE_STORE=redis
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1
```

Sail já disponibiliza um serviço `redis` no `docker-compose.yml` do projeto.

## Exemplos de uso de cache no Projeto

### FilamentStatsCache

- Arquivo: `app/Support/FilamentStatsCache.php`
- Centraliza agregações do painel Filament (usuários, teams, mídia, badges de tabs).
- Usa o store definido em `config('cache.default')` (Redis em produção; `array` nos testes).
- Consumido por: `SystemStats`, `UsersStats`, `MediaStats`, `TeamStats` e badges em `ListUsers`.

### Serviço de Metadados de Vídeo (YouTube)

- Arquivo: `app/Services/VideoMetadataService.php`
- O `getYoutubeTitle()` e o `getYoutubeMetadata()` utilizam `Cache::store('redis')->remember()` com TTL de 6 horas para evitar chamadas repetidas ao YouTube (oEmbed e leitura de HTML).
- Chaves por URL para evitar colisões e facilitar a invalidação:
  - `video:title:{sha1(url)}`
  - `video:meta:{sha1(url)}`
- Onde é usado: criação de mídias com URL do YouTube em `app/Filament/Resources/Media/Pages/CreateMedia.php` (após salvar o `Video`, os metadados são buscados e podem vir do cache).
- Invalidação automática: `VideoObserver` remove as chaves acima quando o `Video` é criado/atualizado/excluído.

### Widget de Estatísticas (SystemStats)

- Arquivo: `app/Filament/Widgets/SystemStats.php`
- Delega agregações para `FilamentStatsCache` com TTL de 60 segundos.
- Chaves utilizadas:
  - `stats:teams`
  - `stats:users`
  - `stats:media`
  - `stats:users:tabs` (badges das tabs em `ListUsers`)

### Páginas de Mídia (Filament)

- Arquivos: `app/Filament/Resources/Media/MediaResource.php` e `app/Filament/Resources/Media/Tables/MediaTable.php`
- Estratégia principal: evitar N+1 via `with(['video', 'team', 'media'])` e usar `$record->linkedVideo()?->title` ao invés de `->value()` em runtime.
- **Atenção:** `MediaItem` possui coluna booleana `video` e relação `video()` — em colunas `state()` use sempre `linkedVideo()` para acessar o model `Video`.
- Benefício: reduz drasticamente consultas por linha na tabela e elimina a necessidade de cachear propriedades simples que já vêm carregadas.
- Observação: caso surja necessidade futura, é possível adicionar accessors com `remember()` de curto prazo (ex.: 5 min), porém a solução preferida continua sendo eager loading.

### Observers para Invalidação

| Observer | Invalida |
|----------|----------|
| `UserObserver` | `stats:users`, `stats:users:tabs` |
| `TeamObserver` | `stats:teams` |
| `VideoObserver` | `stats:media`, `video:meta:*`, `video:title:*` |
| `MediaItemObserver` | `stats:media` |
| `MembershipObserver` | `stats:users`, `stats:users:tabs`, `stats:teams` |

Registro: `app/Providers/AppServiceProvider.php` (método `configObservers()`).

### Exemplos de Código

Agregações do dashboard via `FilamentStatsCache`:

```php
use App\Support\FilamentStatsCache;

$users = FilamentStatsCache::users();
$badges = FilamentStatsCache::usersTabBadges();
```

Invalidação via Observer:

```php
FilamentStatsCache::forgetUsers();
FilamentStatsCache::forgetTeams();
FilamentStatsCache::forgetMedia();
```

Cache de metadados (YouTube):

```php
use Illuminate\Support\Facades\Cache;

$cacheKey = 'video:meta:'.sha1($videoUrl);
$meta = Cache::store('redis')->remember($cacheKey, 6 * 3600, function () use ($videoUrl) {
    return [
        'title' => '...',
        'durationSeconds' => 123,
        'durationIso8601' => 'PT2M3S',
    ];
});
```

## Boas Práticas

- Prefira `remember()` a `get()/put()` manuais.
- Evite `Cache::flush()` global; invalide chaves específicas.
- Defina TTLs coerentes com o frescor exigido pelo negócio.
- Use chaves determinísticas (ex.: `sha1(url)` para URLs).
- Centralize invalidações com **Observers** próximos ao domínio.
- Para novos Resources Filament, consulte [Performance no Filament](./filament-performance.md).

## Problemas Comuns

- “Stale data” por TTL longo sem invalidação de escrita → use observers.
- N+1 em listagens confundido com necessidade de cache → primeiro aplique eager loading.
- Conflitos de chave em ambientes compartilhados → use prefixos (já configurados em `config/cache.php`).

## Conclusão

O uso de cache com Redis neste projeto reduz latência, evita recomputações e melhora a experiência do usuário. Com chaves previsíveis, TTLs adequados e invalidação cirúrgica via observers, mantemos dados frescos e performance consistente no ecossistema Filament.

## Referências

- [FilamentStatsCache](../../app/Support/FilamentStatsCache.php)
- [Performance no Filament](./filament-performance.md)
- [Service: VideoMetadataService](../../app/Services/VideoMetadataService.php)
- [Widget: SystemStats](../../app/Filament/Widgets/SystemStats.php)
- [Observer: VideoObserver](../../app/Observers/VideoObserver.php)
- [Resource: MediaResource](../../app/Filament/Resources/Media/MediaResource.php)
