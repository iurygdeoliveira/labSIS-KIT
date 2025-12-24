# Utilizando as cores neste projeto

Esta documentação explica como trabalhamos com cores neste projeto, por que centralizamos nossa paleta no `AppServiceProvider`, e como utilizar e registrar cores nos componentes do projeto.


## Por que registrar cores no `AppServiceProvider`

Centralizamos o registro das cores no `AppServiceProvider` para:
- Ter uma única fonte da verdade das cores do sistema.
- Facilitar ajustes visuais (ex.: adoção de um "cinza suave").
- Permitir registrar cores extras (ex.: `secondary`).
- Manter a configuração de cores junto com outras configurações globais da aplicação.

As paletas são definidas diretamente no código usando o formato hexadecimal.

## Registrando as cores

Neste projeto, as cores são **registradas no `AppServiceProvider`** através do método `configFilamentColors()`:

```php
// app/Providers/AppServiceProvider.php
private function configFilamentColors(): void
{
    FilamentColor::register([
        'danger' => Color::hex('#D93223'),
        'warning' => Color::hex('#F28907'),
        'success' => Color::hex('#52a0fa'),
        'primary' => Color::hex('#014029'),
        'secondary' => Color::hex('#F2F2F0'),
    ]);
}
```

- O método é chamado no `boot()` do provider, garantindo que as cores estejam disponíveis em todas as requisições.
- As cores padrão (`primary`, `success`, `warning`, `danger`) e a cor extra (`secondary`) ficam disponíveis no Filament.
- Se quiser entender melhor a função do `AppServiceProvider` e como ele centraliza configurações globais, consulte a documentação: [`docs/app-service-provider.md`](/docs/app-service-provider.md).

## Como usar as cores nos componentes

Em PHP (Actions, Forms, Tables, etc.):

```php
Action::make('salvar')
    ->color('primary');

Toggle::make('is_active')
    ->onColor('success');
```

## Conclusão

Prefira nomes de cores (`primary`, `success`, `warning`, `danger`, `info`, `gray`). Isso colabora para manter a consistência de cores no sistema.


Para saber mais, consulte a documentação oficial do Filament: [Colors](https://filamentphp.com/docs/4.x/styling/colors).
