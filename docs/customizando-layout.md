# Customização da Aparência do Painel Filament

## 📋 Índice

- [Introdução](#introdução)
- [1. Plugin Brisk Theme](#1-plugin-brisk-theme)
- [2. Customização via `AdminPanelProvider.php`](#2-customização-via-adminpanelproviderphp)
- [3. Customização Avançada com CSS (`theme.css`)](#3-customização-avançada-com-css-themecss)
- [Hierarquia de Customização](#hierarquia-de-customização)
- [Conclusão](#conclusão)

## Introdução

O Filament foi projetado para ser altamente extensível e personalizável, permitindo que os desenvolvedores adaptem a aparência do painel administrativo para alinhá-la à identidade visual de um projeto. Este kit inicial já vem com uma estrutura preparada para customizações.

Este documento aborda as **três principais formas** de alterar o layout e o estilo do painel:

1. **Plugin Brisk Theme** - Tema pré-configurado com design moderno e amigável
2. **Configurações via `AdminPanelProvider`** - Para configurações globais de cores, fontes e layout
3. **CSS customizado** - Para ajustes finos e específicos de componentes

## 1. Plugin Brisk Theme

Este projeto utiliza o **Brisk Theme**, um tema gratuito e moderno para Filament PHP que combina simplicidade com uma estética acolhedora e amigável.

### Características do Brisk Theme

- **Gratuito e Open Source** - Sem taxas de licenciamento ou restrições
- **Design Moderno** - Interface limpa e minimalista
- **Modo Claro e Escuro** - Suporte completo para ambos os temas
- **Layout Responsivo** - Funciona perfeitamente em todos os tamanhos de dispositivo
- **Fonte Kumbh Sans** - Tipografia limpa e legível
- **Integração Simples** - Configuração e configuração fáceis

### Configuração Atual

O tema está configurado no `AdminPanelProvider` com a fonte sugerida desabilitada:

```php
// app/Providers/Filament/AdminPanelProvider.php

->plugin(BriskTheme::make()->withoutSuggestedFont())
```

### Documentação Oficial

Para informações detalhadas sobre personalização e recursos do Brisk Theme, consulte a [documentação oficial](https://filafly.com/themes/brisk).

### Personalização do Brisk Theme

Se desejar usar sua própria fonte em vez da Kumbh Sans (que está desabilitada), você pode remover o método `withoutSuggestedFont()`:

```php
// Para usar a fonte padrão do Brisk Theme
->plugin(BriskTheme::make())

// Para manter a configuração atual (sem fonte sugerida)
->plugin(BriskTheme::make()->withoutSuggestedFont())
```

## 2. Customização via `AdminPanelProvider.php`

O arquivo `app/Providers/Filament/AdminPanelProvider.php` é o centro de controle para a configuração do seu painel administrativo. Nele, é possível alterar cores, fontes, favicons, e diversos outros aspectos de forma programática.

### Exemplo 1: Alterando a Paleta de Cores

O método `colors()` permite definir a paleta de cores que será utilizada em todo o painel. A chave `primary` tem um papel de destaque, sendo usada em botões, links, e indicadores de foco.

**Localização:**
```php
// app/Providers/Filament/AdminPanelProvider.php

public function panel(Panel $panel): Panel
{
    return $panel
        // ... outras configurações
        ->colors([
            'primary' => '#014029', // Cor primária atual
            'danger' => '#D93223',
            // ... outras cores
        ])
        // ...
}
```

**Demonstração:**
Vamos supor que desejamos alterar a cor primária para um tom de azul.

```php
// Alteração sugerida
->colors([
    'primary' => '#2563eb', // Novo tom de azul
    'danger' => '#D93223',
    // ...
])
```

**Resultado:**

Após essa alteração, todos os componentes que utilizam a cor primária (botões de ação, links ativos, anéis de foco em campos de formulário) passarão a usar o tom de azul definido, alterando drasticamente a identidade visual do painel.

### Exemplo 2: Ajustando a Largura da Barra Lateral

É possível controlar a largura da barra de navegação lateral através do método `sidebarWidth()`.

**Localização:**
```php
// app/Providers/Filament/AdminPanelProvider.php

->sidebarWidth('15rem') // Largura atual
```

**Demonstração:**
Para tornar a barra lateral mais espaçosa, podemos aumentar seu valor.

```php
// Alteração sugerida
->sidebarWidth('18rem') // Nova largura
```

**Resultado:**

A barra de navegação lateral se tornará visivelmente mais larga, o que pode ser útil caso os nomes dos recursos no menu sejam extensos.

## 3. Customização Avançada com CSS (`theme.css`)

Para um controle mais granular e para aplicar estilos que não são cobertos pelos métodos do `PanelProvider` ou pelo Brisk Theme, podemos escrever CSS customizado. O arquivo preparado para isso neste kit é o `resources/css/filament/admin/theme.css`.

Este arquivo é carregado no painel através do método `viteTheme()`, como pode ser visto no `AdminPanelProvider`:

```php
// app/Providers/Filament/AdminPanelProvider.php

->viteTheme('resources/css/filament/admin/theme.css')
```

**Importante:** Após qualquer alteração neste arquivo CSS, é necessário recompilar os assets do frontend com o Vite:

```bash
npm run dev
# ou para produção
npm run build
```

### Exemplo 1: Alterar a Fonte do Painel

Podemos definir uma nova fonte para todo o painel adicionando uma regra ao `theme.css`.

**Demonstração:**
```css
/* Adicione ao final de resources/css/filament/admin/theme.css */

@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

body {
    font-family: 'Roboto', sans-serif;
}
```

**Resultado:**

Todo o texto dentro do painel administrativo passará a ser renderizado com a fonte "Roboto", conferindo uma nova tipografia à interface.

### Exemplo 2: Arredondar Bordas dos Inputs

Suponha que o design do projeto exija que os campos de formulário tenham bordas mais arredondadas.

**Demonstração:**
```css
/* Adicione ao final de resources/css/filament/admin/theme.css */

.fi-input-wrapper {
    border-radius: 0.75rem !important; /* 12px */
}
```

**Resultado:**

Todos os campos de entrada (`TextInput`, `Select`, etc.) no painel terão suas bordas arredondadas, suavizando a aparência dos formulários. O uso de `!important` pode ser necessário para sobrescrever estilos muito específicos do Filament.

## Hierarquia de Customização

Para entender como as diferentes camadas de customização interagem, é importante saber a ordem de prioridade:

1. **CSS Customizado** (`theme.css`) - Maior prioridade, sobrescreve tudo
2. **Configurações do AdminPanelProvider** - Configurações programáticas
3. **Brisk Theme** - Tema base com estilos padrão

## Conclusão

A customização da aparência no Filament é um processo flexível e em camadas. O **Brisk Theme** fornece uma base sólida e moderna, o **AdminPanelProvider** permite configurações globais de tema (cores, fontes, espaçamentos gerais), e o arquivo **`theme.css`** oferece controle total sobre estilos específicos de componentes.

Para a maioria dos casos de uso, a combinação do Brisk Theme com as configurações do `AdminPanelProvider` será suficiente. Use o CSS customizado apenas quando precisar de ajustes muito específicos ou para implementar um design system complexo.
