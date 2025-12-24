# 🎨 Customizando o Logotipo nos Painéis Filament

Este guia explica, de forma direta e completa, como alterar o logotipo exibido na barra superior (topbar) dos painéis do Filament neste projeto, bem como o logotipo do rodapé adicionado pelo EasyFooter. A solução adotada separa o logotipo do painel de Autenticação do logotipo aplicado aos demais painéis (via `BasePanelProvider`).

## 📋 Índice

- [Onde ficam as views de logo](#onde-ficam-as-views-de-logo)
- [Como o Filament recebe a logo](#como-o-filament-recebe-a-logo)
- [Views de exemplo](#views-de-exemplo)
- [Logo no rodapé (EasyFooter)](#logo-no-rodapé-easyfooter)
- [Passo a passo para alterar a logo](#passo-a-passo-para-alterar-a-logo)
- [Dicas úteis](#dicas-úteis)

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

---

## Passo a passo para alterar a logo

### 1. **Identificar qual logo deseja alterar**

Este projeto possui **3 logos diferentes**:

| Logo | Arquivo de Imagem | View Blade | Provider | Onde aparece |
|------|-------------------|------------|----------|--------------|
| Login/Auth | `public/images/LabSIS_login.png` | `resources/views/filament/auth/logo_auth.blade.php` | `AuthPanelProvider` | Página de login |
| Topbar (Admin/User) | `public/images/LabSIS.png` | `resources/views/filament/auth/logo_base.blade.php` | `BasePanelProvider` | Topo dos painéis |
| Rodapé | `public/images/LabSIS_painel.png` | N/A | `EasyFooterPlugin` | Rodapé dos painéis |

### 2. **Alterar a imagem**

Substitua o arquivo na pasta `public/images/`:

```bash
# Substitua qualquer uma das imagens
# Exemplo: substituindo a logo da topbar
cp sua-logo.png public/images/LabSIS.png

# Ou crie um novo arquivo com outro nome
cp sua-logo.png public/images/MinhaLogo.png
```

### 3. **Atualizar a view (se mudou o nome do arquivo)**

Se você criou um novo arquivo com outro nome, edite a view:

**Para logo de login:**
```bash
nano resources/views/filament/auth/logo_auth.blade.php
```

```blade
<img src="{{ asset('images/SEU_NOVO_ARQUIVO.png') }}" alt="LabSIS" class="h-full" />
```

**Para logo da topbar:**
```bash
nano resources/views/filament/auth/logo_base.blade.php
```

```blade
<img src="{{ asset('images/SEU_NOVO_ARQUIVO.png') }}" alt="LabSIS" class="h-full" />
```

### 4. **Alterar a logo do rodapé (EasyFooter)**

Edite o arquivo `app/Providers/Filament/BasePanelProvider.php`:

```bash
nano app/Providers/Filament/BasePanelProvider.php
```

Procure por `withLogo()` e altere:

```php
->withLogo(
    asset('images/SEU_NOVO_ARQUIVO.png'), // ← Altere aqui
    'https://www.labsis.dev.br'
)
```

### 5. **Ajustar altura (se necessário)**

Se a nova logo tiver proporções diferentes, ajuste a altura nos providers:

**Logo de login** (`AuthPanelProvider.php`):

```php
->brandLogo(fn () => view('filament.auth.logo_auth'))
->brandLogoHeight('10rem') // ← Altere o valor aqui (ex: 8rem, 10rem, 12rem)
```

**Logo da topbar** (`BasePanelProvider.php`):

```php
->brandLogo(fn () => view('filament.auth.logo_base'))
->brandLogoHeight('3rem') // ← Altere o valor aqui (ex: 2rem, 3rem, 4rem)
```

### 6. **Limpar cache**

Após fazer as alterações, limpe o cache:

```bash
./vendor/bin/sail artisan optimize:clear
```

Ou se não estiver usando Sail:

```bash
php artisan optimize:clear
```


