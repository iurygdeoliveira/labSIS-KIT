# Widgets no Filament — Métricas e Componentização no Painel

## 📋 Índice

- [Introdução](#introdução)
- [Por que usar Widgets](#por-que-usar-widgets)
- [Exemplo no Projeto: `UsersStats`](#exemplo-no-projeto-usersstats)
- [Onde os Widgets são exibidos](#onde-os-widgets-são-exibidos)
- [Passo a passo para criar um Widget](#passo-a-passo-para-criar-um-widget)
- [Referências](#referências)

## Introdução

Widgets no Filament são componentes reutilizáveis que exibem blocos de informação, como métricas, gráficos e listas. Eles ajudam a destacar Indicadores e consolidar lógicas de consulta/transformação de dados em um único lugar, mantendo o código organizado e fácil de manter.

## Por que usar Widgets

- **Métricas visíveis**: Exibir números-chave (ex.: total de usuários, itens suspensos, etc.) diretamente no cabeçalho de páginas ou no Dashboard.
- **Reuso**: Um mesmo widget pode aparecer na listagem do recurso e no painel principal sem duplicar lógica.
- **Performance**: Combinado com `#[Computed]` do Livewire, evita recomputações no mesmo render.

## Exemplo no Projeto: `UsersStats`

Arquivo: `app/Filament/Resources/Users/Widgets/UsersStats.php`

```php
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\Computed;

class UsersStats extends BaseWidget
{
    #[Computed]
    protected function summary(): array
    {
        $totalUsers = User::query()->count();
        $suspendedUsers = User::query()->where('is_suspended', true)->count();
        $verifiedUsers = User::query()->whereNotNull('email_verified_at')->count();

        return [
            'total' => $totalUsers,
            'suspended' => $suspendedUsers,
            'verified' => $verifiedUsers,
        ];
    }

    protected function getStats(): array
    {
        $summary = $this->summary; // memoizado no mesmo render

        return [
            Stat::make('Usuários', (string) $summary['total'])
                ->icon('heroicon-c-user-group'),
            Stat::make('Suspensos', (string) $summary['suspended'])
                ->color('danger')
                ->icon('heroicon-c-no-symbol'),
            Stat::make('Verificados', (string) $summary['verified'])
                ->color('success')
                ->icon('heroicon-c-check-badge'),
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

2. **No painel Admin (Dashboard)** — registro global do painel:

Arquivo: `app/Providers/Filament/AdminPanelProvider.php`

```php
use App\Filament\Resources\Users\Widgets\UsersStats;

$panel = $panel
    // ...
    ->widgets([
        UsersStats::class,
    ]);
```

## Passo a passo para criar um Widget

1. **Criar a classe do widget**
   - Extenda `StatsOverviewWidget` (para cards) ou outra base de widget necessária.
   - Encapsule consultas em um método e, se fizer sentido, marque com `#[Computed]`.

2. **Registrar na página do recurso** (opcional)
   - Sobrescreva `getHeaderWidgets()` na página `ListRecords` do recurso.

3. **Registrar no painel** (opcional)
   - Adicione a classe do widget em `AdminPanelProvider->widgets([...])` para aparecer no Dashboard.

4. **Ajustar colunas (responsivo)**
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

- Documentação Filament — Widgets: `https://filamentphp.com/docs/4.x/widgets/overview`
