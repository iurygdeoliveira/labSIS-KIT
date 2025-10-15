# Customizando o logotipo no Filament (topbar e rodapé)

Este guia explica, de forma direta, como alterar o logotipo exibido na barra superior (topbar) dos painéis do Filament neste projeto, bem como o logotipo do rodapé adicionado pelo EasyFooter. A solução adotada separa o logotipo do painel de Autenticação do logotipo aplicado aos demais painéis (via `BasePanelProvider`).

## Onde ficam as views de logo

As views usadas pelo Filament para renderizar a logo da topbar ficam em:

- `resources/views/filament/auth/logo_auth.blade.php` — usada no painel de Autenticação (`AuthPanelProvider`), apontando para a imagem `public/images/LabSIS_login.png`.
- `resources/views/filament/auth/logo_base.blade.php` — usada pelos painéis que herdam de `BasePanelProvider`, apontando para a imagem `public/images/LabSIS.png`.

Para trocar a imagem, substitua o arquivo na pasta `public/images/` e mantenha o mesmo nome do arquivo referenciado na view, ou ajuste o caminho na própria view Blade.

## Como o Filament recebe a logo

Nos providers, a logo é registrada com `brandLogo()` recebendo uma view. A altura é controlada por `brandLogoHeight()`.

No painel de Autenticação, em `app/Providers/Filament/AuthPanelProvider.php`:

```php
->brandLogo(fn () => view('filament.auth.logo_auth'))
->brandLogoHeight('8rem')
```

Nos demais painéis (via `BasePanelProvider`), em `app/Providers/Filament/BasePanelProvider.php`:

```php
->brandLogo(fn () => view('filament.auth.logo_base'))
->brandLogoHeight('2rem')
```

Sinta-se à vontade para ajustar os valores de `brandLogoHeight()` conforme a proporção da sua imagem (por exemplo, `1.75rem`, `32px` etc.).

## Views de exemplo

View do painel de Autenticação (`resources/views/filament/auth/logo_auth.blade.php`):

```blade
<img src="{{ asset('images/LabSIS_login.png') }}" alt="LabSIS" class="h-full" />
```

View base para os demais painéis (`resources/views/filament/auth/logo_base.blade.php`):

```blade
<img src="{{ asset('images/LabSIS.png') }}" alt="LabSIS" class="h-full" />
```

Se preferir SVG, você pode colocar o conteúdo SVG diretamente na view para melhor controle de cor (incluindo suporte a `dark:`).

## Logo no rodapé (EasyFooter)

O projeto utiliza o EasyFooter, e o logotipo do rodapé pode ser configurado em `BasePanelProvider` por meio de `withLogo()` do plugin:

```php
EasyFooterPlugin::make()
    ->footerEnabled()
    ->withFooterPosition('footer')
    ->withGithub(showLogo: true, showUrl: true)
    ->withLogo(
        asset('images/LabSIS_painel.png'),
        'https://www.labsis.dev.br'
    )
```

Para alterar, troque a imagem em `public/images/LabSIS_painel.png` ou ajuste o caminho passado para `withLogo()`.

## Dicas úteis

- Se quiser um fallback de texto na área de logo, use `brandName('Seu Nome')` junto do `brandLogo()`.
- Caso deseje mover a navegação para o topo, você pode avaliar `->topNavigation()` no `Panel`.
- Após alterações, execute `./vendor/bin/sail artisan optimize:clear` para garantir que o cache do Filament e do Blade seja atualizado.


