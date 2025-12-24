# Livewire Computed — Otimizando Renderizações com `#[Computed]`

## 📋 Índice

- [Introdução](#introdução)
- [O que é `#[Computed]` no Livewire v3](#o-que-é-computed-no-livewire-v3)
- [Quando usar](#quando-usar)
- [Como funciona a invalidação](#como-funciona-a-invalidação)
- [Implementação no Projeto](#implementação-no-projeto)
  - [Widget de Estatísticas de Usuários (`UsersStats`)](#widget-de-estatísticas-de-usuários-usersstats)
  - [`canDelete` computado em `EditUser`](#candelete-computado-em-edituser)
- [Boas práticas](#boas-práticas)
- [Problemas comuns](#problemas-comuns)
- [Referências](#referências)

## Introdução

O atributo `#[Computed]` do Livewire v3 permite declarar propriedades computadas que são memoizadas durante um ciclo de renderização do componente. Isso evita recomputações desnecessárias quando o mesmo valor é utilizado repetidamente, reduzindo consultas ao banco e operações de transformação.

## O que é `#[Computed]` no Livewire v3

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

- Resumos/contagens: no `UsersStats`, três cards leem o mesmo `summary()` (total, suspensos, verificados). Com `#[Computed]`, o resumo é calculado uma única vez por render e reutilizado.
- Regras derivadas simples: em `EditUser`, `canDelete` decide se o botão de excluir aparece. Se esse mesmo valor for lido em mais de um ponto da página, ele não é recalculado no mesmo render.

## Como funciona a invalidação

- Recalcula no próximo render se alguma propriedade pública lida dentro do método mudar.
- Em páginas/widgets do Filament, um novo render acontece após ações, validações, mudanças de estado, etc.
- Não persiste entre requisições; é memoização apenas no ciclo de render atual.

## Implementação no Projeto

### Widget de Estatísticas de Usuários (`UsersStats`)

Arquivo: `app/Filament/Resources/Users/Widgets/UsersStats.php`

- Objetivo: exibir métricas no cabeçalho da listagem de usuários.
- Otimização: o método `summary()` é `#[Computed]`, agregando contagens apenas uma vez por render.

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

## Referências

- Laravel News — Livewire Computed: `https://laravel-news.com/livewire-computed`
- Documentação Livewire (v3) — Computed: `https://livewire.laravel.com/docs/data/computed`
