# Performance no Filament

Guia de otimizações para Resources, Tables e Widgets do painel administrativo, baseado nas práticas do ecossistema Filament/Laravel Daily e no que já está implementado neste projeto.

## Índice

- [Badges e contagens repetidas](#badges-e-contagens-repetidas)
- [N+1 oculto em colunas com state()](#n1-oculto-em-colunas-com-state)
- [Colunas agregadas sortáveis](#colunas-agregadas-sortáveis)
- [Implementação neste projeto](#implementação-neste-projeto)
- [Checklist para novos Resources](#checklist-para-novos-resources)
- [Referências](#referências)

## Badges e contagens repetidas

### Problema

`getNavigationBadge()` e `Tab::badge()` com `COUNT(*)` executam queries **a cada navegação ou render** da página. Em tabelas grandes, isso degrada a performance mesmo com poucos usuários simultâneos.

### Solução

1. Centralizar contagens em [`app/Support/FilamentStatsCache.php`](../../app/Support/FilamentStatsCache.php).
2. Usar o store definido em `config('cache.default')` com `remember()` e TTL curto (60s neste projeto). Em produção o default é Redis; nos testes, `array`.
3. Invalidar via **Observers** quando os dados mudarem — nunca depender só de TTL longo.

```php
// Exemplo: badge de tab em ListUsers
->badge(fn (): string => (string) FilamentStatsCache::usersTabBadges()['approved'])
```

```php
// Exemplo: invalidação no UserObserver
FilamentStatsCache::forgetUsers();
```

### Quando NÃO cachear

- Contagens que precisam ser **exatas em tempo real** (ex.: fila de jobs crítica).
- Tabelas pequenas (< 100 registros) onde o custo do cache supera o benefício.

## N+1 oculto em colunas com state()

### Problema

Colunas dot-notation (`publisher.name`, `team.name`) recebem eager load automático do Filament. Colunas com `->state(fn ($record) => ...)` que acessam relações **não** recebem esse tratamento.

Sintomas comuns neste projeto:

- `$record->getFirstMedia('media')` na listagem de mídias.
- `$record->video()->value('title')` em vez de `$record->linkedVideo()?->title`.
- `Team::query()->whereKey($id)->value('name')` por linha.

### Solução

Eager load explícito em `getEloquentQuery()` ou `modifyQueryUsing()`:

```php
public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()->with([
        'video',
        'team',
        'media' => fn ($query) => $query->where('collection_name', 'media'),
    ]);
}
```

Use sempre a relação já carregada nos callbacks `state()`. Em `MediaItem`, a coluna booleana `video` colide com a relação `video()` — use o helper `linkedVideo()`:

```php
// Ruim
->state(fn ($record) => $record->video()->value('title'))

// Bom
->state(fn ($record) => $record->linkedVideo()?->title)
```

### Ordenação customizada

Quando a coluna usa `state()` mas precisa ser sortável, prefira JOINs na query (como em `MediaTable::sortAttachmentName()`) em vez de ordenar em PHP.

## Colunas agregadas sortáveis

### Problema

Colunas com `counts('orders')`, `sum('orders.total')` ou similares exibem bem, mas **`->sortable()` dispara subqueries caras** — centenas de ms em tabelas grandes.

### Solução preventiva

Para métricas que precisam ser ordenáveis ou filtráveis:

1. Adicionar coluna física na tabela (`users.orders_count`, `users.lifetime_spent`).
2. Atualizar via Observer/Event quando o dado subjacente mudar.
3. Exibir e ordenar pela coluna denormalizada — nunca pela agregação live.

**Não implemente agregações live sortáveis** em produtos derivados deste starter kit sem avaliar volume de dados.

## Implementação neste projeto

| Artefato | Papel |
|----------|-------|
| [`FilamentStatsCache`](../../app/Support/FilamentStatsCache.php) | Agregações via `config('cache.default')`, TTL e métodos `users()`, `teams()`, `media()`, `usersTabBadges()` |
| [`UserObserver`](../../app/Observers/UserObserver.php) | Invalida `stats:users` e `stats:users:tabs` |
| [`TeamObserver`](../../app/Observers/TeamObserver.php) | Invalida `stats:teams` |
| [`VideoObserver`](../../app/Observers/VideoObserver.php) | Invalida `stats:media` e cache de metadados YouTube |
| [`MediaItemObserver`](../../app/Observers/MediaItemObserver.php) | Invalida `stats:media` |
| [`MembershipObserver`](../../app/Observers/MembershipObserver.php) | Invalida stats de users e teams |
| [`SystemStats`](../../app/Filament/Widgets/SystemStats.php) | Dashboard admin — consome `FilamentStatsCache` |
| [`ListUsers`](../../app/Filament/Resources/Users/Pages/ListUsers.php) | Tabs com badges cacheados |
| [`MediaResource`](../../app/Filament/Resources/Media/MediaResource.php) | Eager load de `video`, `team`, `media` |

Chaves de cache utilizadas (store: `config('cache.default')`):

- `stats:users`
- `stats:users:tabs`
- `stats:teams`
- `stats:media`

TTL padrão: **60 segundos**.

Testes: [`tests/Feature/FilamentStatsCacheTest.php`](../../tests/Feature/FilamentStatsCacheTest.php) valida reutilização e invalidação via `UserObserver`.

## Checklist para novos Resources

Antes de abrir PR com Resource/Table/Widget Filament:

- [ ] Todo `getNavigationBadge()` ou `Tab::badge()` com COUNT usa cache + observer?
- [ ] Todo `->state()` que acessa relação tem `with()` correspondente na query?
- [ ] Nenhuma coluna agregada (`counts`, `sum`, `avg`) está `sortable()` sem coluna denormalizada?
- [ ] Widgets de stats reutilizam `FilamentStatsCache` em vez de queries soltas?
- [ ] Invalidação de cache cobre create/update/delete dos models afetados?
- [ ] Testou com Debugbar ou Query Detector em listagem com 10+ registros?

## Referências

- Vídeo: [3 Filament Performance Tips You May Not Know](https://www.youtube.com/watch?v=eoU6fkxhqmk)
- [Cache e Redis no Projeto](./cache-e-redis.md)
- [Livewire Computed](./livewire-computed.md)
