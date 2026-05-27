# Livewire Computed — Otimizando Renderizações com `#[Computed]`

## 📋 Índice

- [Introdução](#introdução)
- [O que é `#[Computed]` no Livewire v4](#o-que-é-computed-no-livewire-v4)
- [Quando usar](#quando-usar)
- [Como funciona a invalidação](#como-funciona-a-invalidação)
- [Implementação no Projeto](#implementação-no-projeto)
  - [Widget de Estatísticas de Usuários (`UsersStats`)](#widget-de-estatísticas-de-usuários-usersstats)
  - [`canDelete` computado em `EditUser`](#candelete-computado-em-edituser)
- [Boas práticas](#boas-práticas)
- [Problemas comuns](#problemas-comuns)
- [Referências](#referências)

## Introdução

O atributo `#[Computed]` do Livewire v4 permite declarar propriedades computadas que são memoizadas durante um ciclo de renderização do componente. Isso evita recomputações desnecessárias quando o mesmo valor é utilizado repetidamente, reduzindo consultas ao banco e operações de transformação.

## O que é `#[Computed]` no Livewire v4

- `#[Computed]` transforma um método em uma propriedade computada.
- O valor é calculado uma única vez por ciclo de render do componente e reutilizado enquanto não houver mudanças de estado que exijam recomputação.

Exemplo genérico:

```php
use Livewire\Attributes\Computed;

#[Computed]
public function expensiveValue(): string
{
    // ... operação custosa que você não quer repetir no mesmo render
    return 'resultado';
}

public function render()
{
    // $this->expensiveValue não executa o método novamente no mesmo render
    return view('...', [
        'value' => $this->expensiveValue,
    ]);
}
```

## Quando usar

Use `#[Computed]` quando o mesmo dado é acessado mais de uma vez no mesmo render ou quando a computação é cara. Exemplos do projeto:

- Resumos/contagens: no `UsersStats`, quatro cards leem o mesmo `summary()` e três leem `percentages()`. Com `#[Computed]`, cada resumo é calculado uma única vez por render.
- Regras derivadas simples: em `EditUser`, `canDelete` decide se o botão de excluir aparece. Se esse mesmo valor for lido em mais de um ponto da página, ele não é recalculado no mesmo render.

> **Nota:** Para agregações de banco (contagens, somas), combine `#[Computed]` com [`FilamentStatsCache`](../../app/Support/FilamentStatsCache.php) — o cache persiste entre requisições; o computed evita recomputação dentro do mesmo render.

## Como funciona a invalidação

- Recalcula no próximo render se alguma propriedade pública lida dentro do método mudar.
- Em páginas/widgets do Filament, um novo render acontece após ações, validações, mudanças de estado, etc.
- Não persiste entre requisições; é memoização apenas no ciclo de render atual.

## Implementação no Projeto

### Widget de Estatísticas de Usuários (`UsersStats`)

Arquivo: `app/Filament/Resources/Users/Widgets/UsersStats.php`

- Objetivo: exibir métricas no cabeçalho da listagem de usuários.
- Otimização dupla:
  1. `FilamentStatsCache::users()` centraliza as queries com TTL de 60s (Redis em produção).
  2. `summary()` e `percentages()` são `#[Computed]`, evitando recomputação no mesmo render.

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
            'suspended' => $total > 0 ? round(($this->summary['suspended'] / $total) * 100, 1) : 0,
            'unapproved' => $total > 0 ? round(($this->summary['unapproved'] / $total) * 100, 1) : 0,
        ];
    }

    protected function getStats(): array
    {
        $summary = $this->summary;       // memoizado no mesmo render
        $percentages = $this->percentages; // memoizado no mesmo render

        return [
            Stat::make('Total de Usuários', number_format($summary['total']))
                ->description('Cadastrados no sistema'),
            // ...
        ];
    }
}
```

Registro no cabeçalho da listagem (`ListUsers`):

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

### `canDelete` computado em `EditUser`

Arquivo: `app/Filament/Resources/Users/Pages/EditUser.php`

- Objetivo: centralizar a regra "pode deletar?" e evitar repetição.
- Otimização: a checagem é computada uma vez por render e reutilizada na definição das ações.

```php
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

class EditUser extends EditRecord
{
    #[Computed]
    public function canDelete(): bool
    {
        return $this->record?->getKey() !== Auth::id();
    }

    protected function getHeaderActions(): array
    {
        $actions = [
            $this->getBackButtonAction(),
            ViewAction::make(),
        ];

        if ($this->canDelete) {
            $actions[] = DeleteAction::make()
                ->successNotification(Notification::make())
                ->after(fn () => $this->notifySuccess('Usuário excluído com sucesso.'));
        }

        return $actions;
    }
}
```

## Boas práticas

- Use `#[Computed]` para transformações derivadas do estado do componente (percentuais, flags booleanas).
- Use `FilamentStatsCache` para agregações de banco compartilhadas entre widgets, badges e tabs.
- Não confunda os dois: computed não substitui cache entre requisições.

## Problemas comuns

- **Queries repetidas entre renders**: `#[Computed]` não ajuda — use cache com observers.
- **Acesso como método vs propriedade**: leia `$this->summary`, não `$this->summary()`.

## Referências

- Laravel News — Livewire Computed: `https://laravel-news.com/livewire-computed`
- Documentação Livewire (v4) — Computed: `https://livewire.laravel.com/docs/computed-properties`
- [Cache e Redis no Projeto](./cache-e-redis.md)
- [Performance no Filament](./filament-performance.md)
