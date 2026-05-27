# Widgets no Filament — Métricas e Componentização no Painel

## 📋 Índice

- [Introdução](#introdução)
- [Por que usar Widgets](#por-que-usar-widgets)
- [Widgets de stats no projeto](#widgets-de-stats-no-projeto)
- [Exemplo: `UsersStats`](#exemplo-usersstats)
- [Onde os Widgets são exibidos](#onde-os-widgets-são-exibidos)
- [Passo a passo para criar um Widget](#passo-a-passo-para-criar-um-widget)
- [Referências](#referências)

## Introdução

Widgets no Filament são componentes reutilizáveis que exibem blocos de informação, como métricas, gráficos e listas. Eles ajudam a destacar indicadores e consolidar lógicas de consulta/transformação de dados em um único lugar, mantendo o código organizado e fácil de manter.

## Por que usar Widgets

- **Métricas visíveis**: Exibir números-chave (ex.: total de usuários, itens suspensos, etc.) diretamente no cabeçalho de páginas ou no Dashboard.
- **Reuso**: Um mesmo widget pode aparecer na listagem do recurso e no painel principal sem duplicar lógica.
- **Performance**: Combine `FilamentStatsCache` (cache entre requisições) com `#[Computed]` do Livewire (memoização no mesmo render).

## Widgets de stats no projeto

| Widget | Arquivo | Onde aparece |
|--------|---------|--------------|
| `SystemStats` | `app/Filament/Widgets/SystemStats.php` | Dashboard admin (auto-descoberto) |
| `UsersStats` | `app/Filament/Resources/Users/Widgets/UsersStats.php` | Cabeçalho de `ListUsers` |
| `MediaStats` | `app/Filament/Resources/Media/Widgets/MediaStats.php` | Cabeçalho de `ListMedia` |
| `TeamStats` | `app/Filament/Resources/Teams/Widgets/TeamStats.php` | Cabeçalho de `ListTeams` |

Todos consomem [`FilamentStatsCache`](../../app/Support/FilamentStatsCache.php) para evitar queries repetidas. Consulte [Cache e Redis](../05-otimizacoes/cache-e-redis.md).

## Exemplo: `UsersStats`

Arquivo: `app/Filament/Resources/Users/Widgets/UsersStats.php`

```php
use App\Support\FilamentStatsCache;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\Computed;

class UsersStats extends BaseWidget
{
    #[Computed]
    protected function summary(): array
    {
        $stats = FilamentStatsCache::users();

        return [
            'total' => $stats['total'],
            'suspended' => $stats['suspended'],
            'verified' => $stats['verified'],
            'unapproved' => $stats['unapproved'],
        ];
    }

    #[Computed]
    protected function percentages(): array
    {
        $total = $this->summary['total'];

        return [
            'verified' => $total > 0 ? round(($this->summary['verified'] / $total) * 100, 1) : 0,
            // ...
        ];
    }

    protected function getStats(): array
    {
        $summary = $this->summary;
        $percentages = $this->percentages;

        return [
            Stat::make('Total de Usuários', number_format($summary['total']))
                ->description('Cadastrados no sistema'),
            Stat::make('Usuários Verificados', number_format($summary['verified']))
                ->description("{$percentages['verified']}% do total"),
            // ...
        ];
    }
}
```

## Onde os Widgets são exibidos

1. **Na listagem do recurso (`ListUsers`)** — cabeçalho da página:

```php
use App\Filament\Resources\Users\Widgets\UsersStats;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected function getHeaderWidgets(): array
    {
        return [
            UsersStats::class,
        ];
    }
}
```

2. **No painel Admin (Dashboard)** — auto-descoberta de widgets globais:

Arquivo: `app/Providers/Filament/AdminPanelProvider.php`

```php
$panel = $panel
    // ...
    ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets');
```

Widgets em `app/Filament/Widgets/` (como `SystemStats`) aparecem automaticamente no Dashboard.

## Passo a passo para criar um Widget

1. **Criar a classe do widget**
   - Extenda `StatsOverviewWidget` (para cards) ou outra base de widget necessária.
   - Encapsule agregações em `FilamentStatsCache` (ou método dedicado no cache).
   - Marque transformações derivadas com `#[Computed]`.

2. **Registrar na página do recurso** (opcional)
   - Sobrescreva `getHeaderWidgets()` na página `ListRecords` do recurso.

3. **Registrar no painel** (opcional)
   - Coloque widgets globais em `app/Filament/Widgets/` para auto-descoberta, ou registre manualmente em `AdminPanelProvider`.

4. **Invalidar cache**
   - Registre ou atualize Observers que chamam `FilamentStatsCache::forget*()` quando os dados mudarem.

5. **Ajustar colunas (responsivo)**
   - Se precisar controlar a largura/colunas dos cards:

```php
protected function getColumns(): int|array
{
    return [
        'sm' => 2,
        'md' => 3,
        'xl' => 5,
    ];
}
```

## Referências

- Documentação Filament — Widgets: `https://filamentphp.com/docs/5.x/widgets/overview`
- [Widget: UsersStats](../../app/Filament/Resources/Users/Widgets/UsersStats.php)
- [FilamentStatsCache](../../app/Support/FilamentStatsCache.php)
- [Performance no Filament](../05-otimizacoes/filament-performance.md)
