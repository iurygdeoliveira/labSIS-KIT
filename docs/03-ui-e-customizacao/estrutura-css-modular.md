# Cores e Estrutura CSS Modular do Filament Admin

Este documento é o guia completo sobre a gestão de cores e organização CSS do painel Filament Admin deste projeto, explicando desde a definição das cores até a estrutura modular dos componentes.

## 📋 Índice

1. [Filosofia: CSS-First](#filosofia-css-first)
2. [Paleta de Cores](#paleta-de-cores)
3. [Estrutura de Arquivos](#estrutura-de-arquivos)
4. [Como Usar as Cores](#como-usar-as-cores)
5. [Como Manter e Modificar](#como-manter-e-modificar)
6. [Exemplos Práticos](#exemplos-práticos)
7. [Checklists](#checklists)

---

## Filosofia: CSS-First

### Onde as Cores SÃO Definidas

Neste projeto, adotamos uma abordagem **CSS-First**. As cores **NÃO** são definidas no `AppServiceProvider`. Elas são gerenciadas exclusivamente através de arquivos CSS, garantindo que o Tailwind e o navegador tenham controle total sobre a renderização, incluindo gradientes precisos.

### Arquivo Fonte da Verdade

A definição das cores (os códigos hexadecimais de 50 a 950) está localizada em:

**`resources/css/filament/admin/components/colors.css`**

Este arquivo é importado pelo tema principal (`theme.css`) e injeta as variáveis CSS necessárias para o Filament funcionar corretamente.

### Por Que CSS-First?

1. **Performance**: O navegador gerencia as cores nativamente
2. **Gradientes Precisos**: Controle total sobre transições de cor
3. **Manutenibilidade**: Um único ponto de verdade
4. **Compatibilidade**: Funciona perfeitamente com Tailwind CSS v4
5. **Flexibilidade**: Fácil ajustar temas e dark mode

---

## Paleta de Cores

O sistema possui **5 cores principais**, cada uma com **11 variantes** (50-950):

### Primary (Verde Floresta) 🌲

-   **Cor principal**: `#014029` (`--color-primary-500`)
-   **Diretório**: `components/primary/`
-   **Uso**: Ações principais, navegação ativa, elementos de destaque, marca do sistema

**Variantes**:

```css
--color-primary-50: #e6f4ed; /* Mais claro - fundos sutis */
--color-primary-100: #cce9dc;
--color-primary-200: #99d3b9;
--color-primary-300: #66bd96;
--color-primary-400: #33a773;
--color-primary-500: #014029; /* COR BASE */
--color-primary-600: #013322;
--color-primary-700: #01261a;
--color-primary-800: #011a11;
--color-primary-900: #000d09;
--color-primary-950: #000604; /* Mais escuro - textos em fundos claros */
```

### Danger (Vermelho) 🔴

-   **Cor principal**: `#D93223` (`--color-danger-500`)
-   **Diretório**: `components/danger/`
-   **Uso**: Ações destrutivas, alertas de erro, validações críticas

**Variantes**:

```css
--color-danger-50: #fef2f1;
--color-danger-100: #fde5e3;
--color-danger-200: #fbcbc7;
--color-danger-300: #f8b1ab;
--color-danger-400: #f6978f;
--color-danger-500: #d93223; /* COR BASE */
--color-danger-600: #ae281c;
--color-danger-700: #821e15;
--color-danger-800: #57140e;
--color-danger-900: #2b0a07;
--color-danger-950: #160503;
```

### Warning (Laranja) 🟠

-   **Cor principal**: `#F28907` (`--color-warning-500`)
-   **Diretório**: `components/warning/`
-   **Uso**: Avisos, atenção, alertas moderados

**Variantes**:

```css
--color-warning-50: #fef5ed;
--color-warning-100: #fdebdb;
--color-warning-200: #fbd7b7;
--color-warning-300: #f9c393;
--color-warning-400: #f7af6f;
--color-warning-500: #f28907; /* COR BASE */
--color-warning-600: #c26e06;
--color-warning-700: #915204;
--color-warning-800: #613703;
--color-warning-900: #301b01;
--color-warning-950: #180e01;
```

### Info (Azul) 🔵

-   **Cor principal**: `#3b82f6` (`--color-info-500`)
-   **Diretório**: `components/info/`
-   **Uso**: Informações, ajuda, notificações neutras

**Variantes**:

```css
--color-info-50: #eff6ff;
--color-info-100: #dbeafe;
--color-info-200: #bfdbfe;
--color-info-300: #93c5fd;
--color-info-400: #60a5fa;
--color-info-500: #3b82f6; /* COR BASE */
--color-info-600: #2563eb;
--color-info-700: #1d4ed8;
--color-info-800: #1e40af;
--color-info-900: #1e3a8a;
--color-info-950: #172554;
```

### Secondary (Cinza) ⚫

-   **Cor principal**: `#71717a` (`--color-secondary-500`)
-   **Diretório**: `components/secondary/`
-   **Uso**: Ações secundárias, elementos de background, textos auxiliares

**Variantes**:

```css
--color-secondary-50: #fafafa;
--color-secondary-100: #f4f4f5;
--color-secondary-200: #e4e4e7;
--color-secondary-300: #d4d4d8;
--color-secondary-400: #a1a1aa;
--color-secondary-500: #71717a; /* COR BASE */
--color-secondary-600: #52525b;
--color-secondary-700: #3f3f46;
--color-secondary-800: #27272a;
--color-secondary-900: #18181b;
--color-secondary-950: #09090b;
```

---

## Estrutura de Arquivos

### Organização Modular por Cor

Todos os arquivos CSS estão em: **`resources/css/filament/admin/components/`**

```
components/
├── colors.css              # ⭐ Variáveis de cores base (FONTE DA VERDADE)
│
├── primary/                # 🌲 Verde Floresta (#014029)
│   ├── badges.css
│   ├── buttons.css
│   ├── checkboxes.css
│   ├── icons.css
│   ├── inputs.css
│   ├── links.css
│   ├── stats.css           # ⭐ Widgets de estatísticas
│   ├── tabs.css
│   └── toggles.css
│
├── danger/                 # 🔴 Vermelho (#D93223)
│   ├── badges.css
│   ├── buttons.css
│   ├── checkboxes.css
│   ├── icons.css
│   ├── inputs.css
│   ├── links.css
│   ├── stats.css           # ⭐ Widgets de estatísticas
│   ├── tabs.css
│   └── toggles.css
│
├── warning/                # 🟠 Laranja (#F28907)
│   ├── badges.css
│   ├── buttons.css
│   ├── checkboxes.css
│   ├── icons.css
│   ├── inputs.css
│   ├── links.css
│   ├── stats.css           # ⭐ Widgets de estatísticas
│   ├── tabs.css
│   └── toggles.css
│
├── info/                   # 🔵 Azul (#3b82f6)
│   ├── badges.css
│   ├── buttons.css
│   ├── checkboxes.css
│   ├── icons.css
│   ├── inputs.css
│   ├── links.css
│   ├── stats.css           # ⭐ Widgets de estatísticas
│   ├── tabs.css
│   └── toggles.css
│
├── secondary/              # ⚫ Cinza (#71717a)
│   ├── badges.css
│   ├── buttons.css
│   ├── checkboxes.css
│   ├── icons.css
│   ├── inputs.css
│   ├── links.css
│   ├── stats.css           # ⭐ Widgets de estatísticas
│   ├── tabs.css
│   └── toggles.css
│
├── sidebar.css             # Menu lateral
└── login.css               # Página de login
```

### Ordem de Importação (theme.css)

```css
/* 1. Variáveis base (SEMPRE PRIMEIRO) */
@import "./components/colors.css";

/* 2. Componentes PRIMARY */
@import "./components/primary/buttons.css";
@import "./components/primary/links.css";
@import "./components/primary/toggles.css";
@import "./components/primary/checkboxes.css";
@import "./components/primary/inputs.css";
@import "./components/primary/icons.css";
@import "./components/primary/tabs.css";
@import "./components/primary/badges.css";
@import "./components/primary/stats.css";

/* 3. Componentes DANGER */
@import "./components/danger/buttons.css";
@import "./components/danger/links.css";
@import "./components/danger/toggles.css";
@import "./components/danger/checkboxes.css";
@import "./components/danger/inputs.css";
@import "./components/danger/icons.css";
@import "./components/danger/tabs.css";
@import "./components/danger/badges.css";
@import "./components/danger/stats.css";

/* 4. Componentes WARNING */
@import "./components/warning/buttons.css";
@import "./components/warning/links.css";
@import "./components/warning/toggles.css";
@import "./components/warning/checkboxes.css";
@import "./components/warning/inputs.css";
@import "./components/warning/icons.css";
@import "./components/warning/tabs.css";
@import "./components/warning/badges.css";
@import "./components/warning/stats.css";

/* 5. Componentes INFO */
@import "./components/info/buttons.css";
@import "./components/info/links.css";
@import "./components/info/toggles.css";
@import "./components/info/checkboxes.css";
@import "./components/info/inputs.css";
@import "./components/info/icons.css";
@import "./components/info/tabs.css";
@import "./components/info/badges.css";
@import "./components/info/stats.css";

/* 6. Componentes SECONDARY */
@import "./components/secondary/buttons.css";
@import "./components/secondary/links.css";
@import "./components/secondary/toggles.css";
@import "./components/secondary/checkboxes.css";
@import "./components/secondary/inputs.css";
@import "./components/secondary/icons.css";
@import "./components/secondary/tabs.css";
@import "./components/secondary/badges.css";
@import "./components/secondary/stats.css";

/* 7. Componentes específicos */
@import "./components/sidebar.css";
@import "./components/login.css";
```

---

## Como Usar as Cores

### Em Componentes PHP (Filament)

Use os nomes das cores nos componentes do Filament:

```php
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\BadgeColumn;

// Botões
Action::make('salvar')
    ->color('primary')      // Verde
    ->icon('heroicon-o-check');

Action::make('deletar')
    ->color('danger')       // Vermelho
    ->requiresConfirmation();

Action::make('editar')
    ->color('warning');     // Laranja

// Toggles
Toggle::make('is_active')
    ->onColor('primary')    // Verde quando ativo
    ->offColor('secondary'); // Cinza quando inativo

// Badges
BadgeColumn::make('status')
    ->color(fn (string $state): string => match ($state) {
        'aprovado' => 'primary',   // Verde
        'rejeitado' => 'danger',   // Vermelho
        'pendente' => 'warning',   // Laranja
        'info' => 'info',          // Azul
        default => 'secondary',    // Cinza
    });

// Notificações
Notification::make()
    ->title('Sucesso!')
    ->success()              // Usa primary
    ->send();

Notification::make()
    ->title('Erro!')
    ->danger()               // Usa danger
    ->send();
```

### Em Templates Blade

Use as classes CSS do Tailwind com as variáveis de cor:

```blade
{{-- Texto --}}
<p class="text-primary-700">Texto em verde escuro</p>
<p class="text-danger-500">Texto em vermelho</p>

{{-- Background --}}
<div class="bg-primary-100 text-primary-800">
    Card com fundo verde claro
</div>

{{-- Bordas --}}
<div class="border-2 border-primary-500">
    Borda verde
</div>

{{-- Hover states --}}
<button class="bg-primary-500 hover:bg-primary-600 text-white">
    Botão verde
</button>
```

### Cores Disponíveis

Sempre use os **nomes semânticos** das cores:

✅ **Recomendado**:

-   `primary` (verde)
-   `danger` (vermelho)
-   `warning` (laranja)
-   `info` (azul)
-   `secondary` (cinza)

❌ **Evitar**:

-   Códigos hexadecimais diretos (`#014029`)
-   Cores genéricas (`green`, `red`, `blue`)

> **Por quê?** Os nomes semânticos garantem consistência e permitem mudanças globais de tema facilmente.

---

### Widgets de Estatísticas (Stats)

Os widgets de estatísticas (`StatsOverviewWidget`) são componentes especiais que exibem métricas do sistema.

#### Como Usar em PHP

```php
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MediaStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Imagens', number_format($count))
                ->description('Total cadastrado')
                ->icon('heroicon-c-photo')
                ->color('primary'),      // Verde

            Stat::make('Documentos', number_format($count))
                ->description('Arquivos PDF')
                ->icon('heroicon-c-document')
                ->color('info'),         // Azul

            Stat::make('Vídeos', number_format($count))
                ->description('YouTube/Vimeo')
                ->icon('heroicon-c-video-camera')
                ->color('warning'),      // Laranja

            Stat::make('Áudios', number_format($count))
                ->description('MP3/WAV')
                ->icon('heroicon-c-musical-note')
                ->color('danger'),       // Vermelho
        ];
    }
}
```

#### Estrutura CSS dos Stats

Os widgets de stats têm uma peculiaridade: o **Filament aplica a classe de cor apenas na descrição**, não no card inteiro. Por isso, o CSS usa o seletor `:has()` para estilizar todo o widget:

```css
/* Exemplo do arquivo primary/stats.css */

/* Card colorido quando a descrição tem a classe primary */
.fi-wi-stats-overview-stat:has(
        .fi-wi-stats-overview-stat-description.fi-color-primary
    ) {
    background-color: var(--color-primary-50) !important;
    border-color: var(--color-primary-200) !important;
}

/* Ícone colorido (SVG dentro do label container) */
.fi-wi-stats-overview-stat:has(
        .fi-wi-stats-overview-stat-description.fi-color-primary
    )
    .fi-wi-stats-overview-stat-label-ctn
    svg {
    color: var(--color-primary-600) !important;
}

/* Valor (número) */
.fi-wi-stats-overview-stat:has(
        .fi-wi-stats-overview-stat-description.fi-color-primary
    )
    .fi-wi-stats-overview-stat-value {
    color: var(--color-primary-900) !important;
}

/* Label */
.fi-wi-stats-overview-stat:has(
        .fi-wi-stats-overview-stat-description.fi-color-primary
    )
    .fi-wi-stats-overview-stat-label {
    color: var(--color-primary-700) !important;
}

/* Descrição */
.fi-wi-stats-overview-stat-description.fi-color-primary {
    color: var(--color-primary-600) !important;
    font-weight: 600;
}
```

#### Resultado Visual

Com essa estrutura, cada widget terá:

-   **Background**: Tom claro da cor (ex: verde-50 para primary)
-   **Borda**: Tom médio da cor (ex: verde-200 para primary)
-   **Ícone**: Tom escuro da cor (ex: verde-600 para primary)
-   **Valor**: Tom muito escuro da cor (ex: verde-900 para primary)
-   **Label**: Tom escuro da cor (ex: verde-700 para primary)
-   **Descrição**: Tom escuro da cor com peso bold (ex: verde-600 para primary)

#### Por Que Usar `:has()`?

O seletor `:has()` é usado porque:

1. O Filament não aplica `fi-color-*` no elemento raiz do widget
2. Apenas a descrição recebe a classe de cor
3. `:has()` permite estilizar o pai com base no filho
4. É um recurso moderno do CSS (suportado em todos navegadores atuais)

---

## Como Manter e Modificar

### Cenário 1: Alterar uma Cor Específica

**Exemplo**: Mudar a cor primary de verde para azul

1. **Editar `colors.css`**:

```css
/* Antes */
--color-primary-500: #014029; /* Verde */

/* Depois */
--color-primary-500: #1d4ed8; /* Azul */
```

2. **Ajustar todas as variantes** (50-950) para manter harmonia
3. **Recompilar**: `vendor/bin/sail npm run build`
4. **Testar**: Verificar visualmente em todas as páginas

### Cenário 2: Modificar Estilos de UMA Cor

**Exemplo**: Alterar o estilo dos botões PRIMARY

1. **Localizar**: `components/primary/buttons.css`
2. **Editar**:

```css
/* Modificar apenas este arquivo */
.fi-btn.fi-color-primary {
    background-color: var(--color-primary-600) !important; /* Era 500 */
    /* Adicionar sombra */
    box-shadow: 0 4px 6px rgba(1, 64, 41, 0.3);
}
```

3. **Recompilar**: `vendor/bin/sail npm run build`
4. **Resultado**: Apenas botões PRIMARY são afetados

**Vantagem**: Outras cores não são afetadas! ✨

### Cenário 3: Modificar TODAS as Cores de um Componente

**Exemplo**: Adicionar border-radius em todos os botões

1. **Editar cada arquivo**:

    - `primary/buttons.css`
    - `danger/buttons.css`
    - `warning/buttons.css`
    - `info/buttons.css`
    - `secondary/buttons.css`

2. **Adicionar o mesmo código em todos**:

```css
.fi-btn.fi-color-{cor} {
    /* ... estilos existentes ... */
    border-radius: 12px; /* Novo estilo */
}
```

3. **Recompilar**: `vendor/bin/sail npm run build`

### Cenário 4: Adicionar Nova Cor

**Exemplo**: Adicionar cor "success" (verde claro)

1. **Editar `colors.css`**:

```css
/* Adicionar nova paleta */
--color-success-50: #f0fdf4;
--color-success-100: #dcfce7;
--color-success-200: #bbf7d0;
--color-success-300: #86efac;
--color-success-400: #4ade80;
--color-success-500: #22c55e; /* COR BASE */
--color-success-600: #16a34a;
--color-success-700: #15803d;
--color-success-800: #166534;
--color-success-900: #14532d;
--color-success-950: #052e16;
```

2. **Criar diretório**:

```bash
mkdir resources/css/filament/admin/components/success
```

3. **Criar arquivos** (copiar de primary e adaptar):

```bash
cd resources/css/filament/admin/components
cp -r primary/* success/
```

4. **Substituir em todos os arquivos**:

-   Substituir `primary` por `success`
-   Verificar referências de variáveis

5. **Atualizar `theme.css`**:

```css
/* Componentes - SUCCESS */
@import "./components/success/buttons.css";
@import "./components/success/links.css";
@import "./components/success/toggles.css";
@import "./components/success/checkboxes.css";
@import "./components/success/inputs.css";
@import "./components/success/icons.css";
@import "./components/success/tabs.css";
@import "./components/success/badges.css";
```

6. **Recompilar**: `vendor/bin/sail npm run build`

### Cenário 5: Adicionar Novo Componente

**Exemplo**: Adicionar estilos para dropdowns

1. **Criar arquivo em cada cor**:

    - `primary/dropdowns.css`
    - `danger/dropdowns.css`
    - `warning/dropdowns.css`
    - `info/dropdowns.css`
    - `secondary/dropdowns.css`

2. **Escrever estilos** (exemplo para primary):

```css
/*
 * Dropdowns - PRIMARY (Verde Floresta)
 * Estilos de dropdowns usando a paleta primary (#014029)
 */

.fi-dropdown.fi-color-primary {
    background-color: var(--color-primary-50) !important;
    border-color: var(--color-primary-300) !important;
}

.fi-dropdown.fi-color-primary:hover {
    background-color: var(--color-primary-100) !important;
}

.fi-dropdown-item.fi-color-primary:hover {
    background-color: var(--color-primary-600) !important;
    color: white !important;
}
```

3. **Atualizar `theme.css`** (adicionar em cada seção de cor):

```css
/* PRIMARY */
@import "./components/primary/dropdowns.css";

/* DANGER */
@import "./components/danger/dropdowns.css";
/* ... repetir para todas as cores ... */
```

4. **Recompilar**: `vendor/bin/sail npm run build`

---

## Exemplos Práticos

### Exemplo 1: Modificar Cor de Foco dos Inputs PRIMARY

**Objetivo**: Usar um verde mais claro no foco dos inputs

**Arquivo**: `components/primary/inputs.css`

```css
/* ANTES */
.fi-input.fi-color-primary:focus {
    border-color: var(--color-primary-500) !important;
    --tw-ring-color: var(--color-primary-500) !important;
}

/* DEPOIS */
.fi-input.fi-color-primary:focus {
    border-color: var(--color-primary-400) !important; /* Mais claro */
    --tw-ring-color: var(--color-primary-400) !important;
}
```

**Resultado**: Apenas inputs PRIMARY terão foco verde claro. Outras cores não são afetadas!

### Exemplo 2: Criar Página com Tema Custom

**Cenário**: Página especial com cor roxo

**Solução**:

1. Criar variáveis inline no Blade:

```blade
<div style="
    --color-custom-500: #9333ea;
    --color-custom-600: #7e22ce;
    --color-custom-700: #6b21a8;
">
    <button class="bg-[var(--color-custom-500)] hover:bg-[var(--color-custom-600)]">
        Botão Roxo
    </button>
</div>
```

2. Ou adicionar como nova cor no sistema (ver Cenário 4)

---

## Checklists

### ✅ Ao Modificar UMA Cor

-   [ ] Identificar qual cor precisa ser alterada
-   [ ] Navegar até `components/{cor}/`
-   [ ] Modificar apenas os arquivos necessários
-   [ ] Executar `vendor/bin/sail npm run build`
-   [ ] Testar visualmente no navegador
-   [ ] Verificar todos os estados (hover, focus, active, disabled)
-   [ ] Testar em páginas diferentes do sistema

### ✅ Ao Modificar TODAS as Cores

-   [ ] Modificar o mesmo arquivo em todos os diretórios de cor
-   [ ] Manter consistência entre as cores
-   [ ] Executar `vendor/bin/sail npm run build`
-   [ ] Testar todas as cores visualmente
-   [ ] Documentar padrão aplicado
-   [ ] Verificar compatibilidade com dark mode (se aplicável)

### ✅ Ao Adicionar Nova Cor

-   [ ] Definir 11 variantes (50-950) em `colors.css`
-   [ ] Criar diretório `components/{nova-cor}/`
-   [ ] Criar todos os 8 arquivos de componentes
-   [ ] Atualizar `theme.css` com imports
-   [ ] Executar `vendor/bin/sail npm run build`
-   [ ] Testar em todos os componentes
-   [ ] Atualizar esta documentação
-   [ ] Adicionar exemplos de uso

### ✅ Ao Adicionar Novo Componente

-   [ ] Criar arquivo em cada diretório de cor
-   [ ] Seguir padrão de nomenclatura existente
-   [ ] Usar variáveis CSS adequadas
-   [ ] Atualizar `theme.css` com imports
-   [ ] Executar `vendor/bin/sail npm run build`
-   [ ] Testar com todas as cores
-   [ ] Documentar uso do componente

---

## 🎯 Benefícios da Estrutura

### 1. **Isolamento Total por Cor** 🎨

Modificar PRIMARY não afeta DANGER, WARNING, INFO ou SECONDARY. Cada cor é completamente independente.

### 2. **Navegação Intuitiva** 🗺️

-   Quer ajustar verde? → `primary/`
-   Quer ajustar vermelho? → `danger/`
-   Quer ajustar laranja? → `warning/`

### 3. **Manutenção Simplificada** 🔧

Um diretório = uma cor completa. Fácil localizar e modificar sem afetar o resto do sistema.

### 4. **Escalabilidade** 📈

Adicionar nova cor = criar novo diretório. Copiar estrutura existente e adaptar.

### 5. **Trabalho Paralelo** 👥

Equipe pode trabalhar em cores diferentes sem conflitos de merge.

### 6. **CSS-First = Performance** ⚡

Renderização nativa pelo navegador, sem overhead de JavaScript.

### 7. **Consistência Visual** ✨

Nomes semânticos garantem uso correto das cores em todo o sistema.

---

## 🔗 Referências

-   [Documentação oficial do Filament - Colors](https://filamentphp.com/docs/4.x/styling/colors)
-   [Tailwind CSS v4 - Theme Configuration](https://tailwindcss.com/docs/theme)
-   Arquivo fonte: `resources/css/filament/admin/components/colors.css`
-   Tema principal: `resources/css/filament/admin/theme.css`

---

**Última atualização**: 2026-01-11  
**Versão do Filament**: 4.x  
**Tailwind CSS**: 4.x  
**Estrutura**: Modular por Cor (Versão 2.0)  
**Filosofia**: CSS-First
