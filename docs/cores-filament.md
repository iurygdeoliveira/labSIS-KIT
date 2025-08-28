# Utilizando as cores neste projeto

Este guia explica como o Filament trabalha com cores, por que centralizamos nossa paleta em `config/filament-colors.php`, e como utilizar e registrar cores nos componentes do projeto.


## Por que `config/filament-colors.php`

Centralizamos nossas paletas em `config/filament-colors.php` para:
- Ter uma única fonte da verdade das cores do sistema.
- Facilitar ajustes visuais (ex.: adoção de um "cinza suave").
- Permitir registrar cores extras (ex.: `secondary`).

Esse arquivo contém paletas no formato OKLCH para todas as cores utilizadas.

## Registrando as cores

Para o Filament reconhecer as paletas definidas em `config/filament-colors.php`, você pode registrar no boot da aplicação. Exemplo sugerido (adicione no seu provider de inicialização global):

```php
use Filament\Support\Facades\FilamentColor;

public function boot(): void
{
    // ...
    FilamentColor::register(config('filament-colors'));
}
```

Neste projeto, entretanto, as cores já estão **registradas no `BasePanelProvider`** através do método `->colors([...])` do painel, apontando diretamente para `config('filament-colors')`:

```php
// app/Providers/Filament/BasePanelProvider.php
$panel->colors([
    'primary' => config('filament-colors.primary'),
    'secondary' => config('filament-colors.secondary'),
    'danger' => config('filament-colors.danger'),
    'warning' => config('filament-colors.warning'),
    'success' => config('filament-colors.success'),
]);
```

- Se quiser entender melhor a função do `BasePanelProvider` e como ele centraliza configurações dos paineis (incluindo cores), consulte a documentação: [`docs/login-unificado.md`](/docs/login-unificado.md).

## Como usar as cores nos componentes

Em PHP (Actions, Forms, Tables, etc.):

```php
Action::make('salvar')
    ->color('primary');

Toggle::make('is_active')
    ->onColor('success');
```

Dicas:
- Prefira nomes de cores (`primary`, `success`, `warning`, `danger`, `info`, `gray`). Isso colabora para manter a consistência de cores no sistema.
- Se você tiver registrado `secondary` em `config/filament-colors.php` (como neste projeto), também pode usá-la da mesma forma (`->color('secondary')`, `color="secondary"`).

---

Para saber mais, consulte a documentação oficial do Filament: [Colors](https://filamentphp.com/docs/4.x/styling/colors).
