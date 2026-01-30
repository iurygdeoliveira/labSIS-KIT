# Customização da Aparência e Plugins no Filament

## 📋 Índice

- [Introdução](#introdução)
- [1. Utilizando Plugins de Tema](#1-utilizando-plugins-de-tema)
    - [Instalação](#instalação)
    - [Registro no PanelProvider](#registro-no-panelprovider)
    - [Exemplo: EasyFooterPlugin](#exemplo-easyfooterplugin)
- [2. Customização Nativa de Componentes](#2-customização-nativa-de-componentes)
    - [FilamentComponentsConfigurator](#filamentcomponentsconfigurator)
- [3. Customização Avançada com CSS](#3-customização-avançada-com-css)
    - [Arquivo `theme.css`](#arquivo-themecss)
    - [Processo de Build (Vite)](#processo-de-build-vite)
- [Referências](#referências)

## Introdução

O Filament permite uma flexibilidade enorme na customização visual, evoluindo de configurações simples de cores para um ecossistema robusto baseado em **Plugins**.

Neste projeto, adotamos uma abordagem híbrida e modular:
1. **Plugins**: Para funcionalidades visuais complexas e empacotadas (temas, footers, widgets).
2. **Configurators**: Para padronizar o comportamento e estilo padrão dos componentes nativos.
3. **CSS Customizado**: Para ajustes finos de design system que o framework não expõe nativamente.

## 1. Utilizando Plugins de Tema

A forma mais eficiente de customizar o layout é através de plugins comunitários ou próprios. Eles encapsulam Blade views, CSS e JS em pacotes reutilizáveis.

### Instalação

Geralmente, plugins são instalados via Composer.

```bash
composer require nome-do-vendor/nome-do-plugin
```

### Registro no PanelProvider

Após instalar, você deve registrar o plugin no seu `AdminPanelProvider` (ou `BasePanelProvider` se for compartilhado entre painéis).

```php
// app/Providers/Filament/AdminPanelProvider.php

public function panel(Panel $panel): Panel
{
    return $panel
        // ... outras configurações
        ->plugin(
            NomeDoPlugin::make()
                ->opcaoDeConfiguracao()
        );
}
```

### Exemplo: EasyFooterPlugin

Neste kit, utilizamos o `EasyFooterPlugin` para adicionar um rodapé customizado ao painel. Ele está configurado no `BasePanelProvider.php` através do método auxiliar `applySharedPlugins`.

```php
// app/Providers/Filament/BasePanelProvider.php

protected function applySharedPlugins(Panel $panel): Panel
{
    return $panel
        ->plugin(
            EasyFooterPlugin::make()
                ->footerEnabled()
                ->withGithub(showLogo: true, showUrl: true)
                // ...
        );
}
```

Isso demonstra como "injetar" novas seções de UI sem precisar alterar manualmente as views do esqueleto do Filament.

## 2. Customização Nativa de Componentes

Para garantir consistência visual em todo o projeto (ex: todas as tabelas terem paginação de 20 itens, todos os inputs traduzirem labels automaticamente), utilizamos uma classe configuradora central.

### FilamentComponentsConfigurator

Localizado em `app/Filament/Configurators/FilamentComponentsConfigurator.php`, este arquivo define os padrões globais dos componentes usando o método `configureUsing`.

**Exemplo de uso:**

```php
// app/Filament/Configurators/FilamentComponentsConfigurator.php

public static function configure(): void
{
    // Força todos os campos a traduzirem suas labels automaticamente
    Field::configureUsing(function (Field $field): void {
        $field->translateLabel();
    });

    // Centraliza ícones em colunas de tabelas
    IconColumn::configureUsing(function (IconColumn $iconColumn): void {
        $iconColumn
            ->alignment(Alignment::Center)
            ->verticalAlignment(VerticalAlignment::Center);
    });
}
```

Esta classe é inicializada no `bootUsing` do `AdminPanelProvider`, garantindo que as regras sejam aplicadas assim que o painel carrega.

## 3. Customização Avançada com CSS

Quando os métodos PHP não são suficientes, recorremos ao CSS customizado. O Filament utiliza Tailwind CSS, e nós temos um arquivo de entrada específico para o tema do admin.

### Arquivo `theme.css`

O arquivo principal está em:
`resources/css/filament/admin/theme.css`

Ele é registrado no painel via método `viteTheme()`:

```php
// app/Providers/Filament/BasePanelProvider.php
->viteTheme('resources/css/filament/admin/theme.css')
```

Aqui você pode sobrescrever classes do Filament, importar fontes personalizadas ou ajustar variáveis do Tailwind.

**Exemplo:**
```css
/* resources/css/filament/admin/theme.css */

@import '../../../../vendor/filament/filament/resources/css/theme.css';

@config '../../../../tailwind.config.js';

/* Customizações específicas */
.fi-sidebar-item {
    @apply hover:bg-primary-500/10;
}
```

### Processo de Build (Vite)

Sempre que alterar o arquivo `theme.css` ou as configurações do Tailwind, é **obrigatório** recompilar os assets.

**Em desenvolvimento (Hot Reload):**
```bash
npm run dev
```

**Para produção:**
```bash
npm run build
```

---

## Referências

- [BasePanelProvider (Plugins)](/app/Providers/Filament/BasePanelProvider.php)
- [FilamentComponentsConfigurator (Padrões)](/app/Filament/Configurators/FilamentComponentsConfigurator.php)
- [Theme CSS](/resources/css/filament/admin/theme.css)
