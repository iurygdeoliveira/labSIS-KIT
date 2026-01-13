# Customização de Cores e CSS Modular do Filament

Este documento é o guia definitivo sobre a gestão de cores e organização CSS do painel Filament Admin deste projeto. Adotamos uma filosofia **CSS-First**, onde as cores são definidas em variáveis CSS e mapeadas nativamente para o Tailwind, removendo a dependência de configurações PHP no `AppServiceProvider`.

## 🎨 Arquitetura de Cores

A definição de cores foi refatorada para ser modular e suportar nativamente temas claro e escuro, garantindo consistência e facilidade de manutenção.

### Estrutura de Definição

O ponto de entrada para as cores é o arquivo **`resources/css/filament/admin/components/colors.css`**, que orquestra a importação de três camadas essenciais localizadas na pasta `colors/`:

1.  **`light.css`** (`components/colors/light.css`):

    -   Define as variáveis CSS globais no escopo `:root`.
    -   Contém os valores hexadecimais para todas as paletas (Primary, Danger, Warning, Info, Secondary) no modo **Claro**.
    -   Exemplo: `--primary-500: #014029;`

2.  **`dark.css`** (`components/colors/dark.css`):

    -   Define as variáveis CSS correspondentes para o escopo `.dark`.
    -   Sobrescreve os valores das variáveis para garantir contraste e legibilidade no modo **Escuro**.
    -   Exemplo: `--primary-500: #33a773;` (um tom mais claro/brilhante para fundo escuro).

3.  **`mapping.css`** (`components/colors/mapping.css`):
    -   Utiliza a diretiva `@theme` do Tailwind CSS v4.
    -   Mapeia as classes utilitárias do Tailwind (ex: `text-primary-500`) para usar as variáveis CSS dinâmicas (ex: `var(--primary-500)`).
    -   Isso permite que a classe `bg-primary-500` mude de cor automaticamente quando o usuário alterna entre modo claro e escuro.

### Paletas de Cores Disponíveis

O sistema utiliza 5 cores semânticas principais, cada uma com 11 variantes (50 a 950):

-   **Primary (Verde Floresta)**: Ações principais, marca, menus ativos.
-   **Danger (Vermelho)**: Erros, ações destrutivas.
-   **Warning (Laranja)**: Alertas, atenção.
-   **Info (Azul)**: Informações, links neutros.
-   **Secondary (Cinza)**: Estrutura, textos secundários, bordas.

---

## 🧩 Estrutura Modular de Componentes

Para garantir que o Filament Admin utilize essas cores customizadas sem conflitos, adotamos uma estrutura de **sobreposição de estilos organizada por cor**.

Todos os componentes customizados estão em: `resources/css/filament/admin/components/`

### Organização por Diretórios

Em vez de grandes arquivos CSS monolíticos, dividimos as estilizações em pastas correspondentes a cada cor semântica. Isso evita que uma alteração no botão "Primary" quebre acidentalmente o botão "Danger".

-   **`primary/`**: Customizações para componentes Verdes.
-   **`danger/`**: Customizações para componentes Vermelhos.
-   **`warning/`**: Customizações para componentes Laranjas.
-   **`info/`**: Customizações para componentes Azuis.
-   **`secondary/`**: Customizações para componentes Cinzas.

### Arquivos de Componentes

Dentro de cada pasta de cor, existem arquivos específicos para cada elemento da UI:

-   `badges.css`
-   `buttons.css`
-   `checkboxes.css`
-   `icons.css`
-   `inputs.css`
-   `links.css`
-   `stats.css` (Widgets de estatísticas)
-   `tabs.css`
-   `toggles.css`

### Exemplo Prático de Isolamento

Se você abrir `primary/buttons.css` e `danger/buttons.css`, notará que eles visam seletores específicos de cor:

**`primary/buttons.css`**:

```css
/* Afeta APENAS botões primary */
.fi-btn.fi-color-primary {
    /* Estilos específicos do verde */
}
```

**`danger/buttons.css`**:

```css
/* Afeta APENAS botões danger */
.fi-btn.fi-color-danger {
    /* Estilos específicos do vermelho */
}
```

---

## 🛠️ Guia de Customização

### Como Alterar uma Cor do Sistema

Se você deseja mudar a cor **Primary** de Verde para Roxo:

1.  Abra **`resources/css/filament/admin/components/colors/light.css`**.
2.  Localize o bloco "Paleta de cor Primária".
3.  Substitua os códigos hexadecimais de `--primary-50` até `--primary-950` pelos novos tons de roxo.
4.  Abra **`resources/css/filament/admin/components/colors/dark.css`**.
5.  Faça o mesmo, escolhendo tons de roxo adequados para fundo escuro.
6.  Execute `vendor/bin/sail npm run build`.

**Resultado**: Todo o painel, incluindo botões, textos e fundos que usam `primary`, mudará para Roxo automaticamente.

### Como Customizar um Componente Específico

Se você deseja arredondar mais as bordas dos **Botões de Perigo (Danger)**:

1.  Vá para **`resources/css/filament/admin/components/danger/buttons.css`**.
2.  Adicione a propriedade `border-radius`:
    ```css
    .fi-btn.fi-color-danger {
        border-radius: 9999px !important; /* Pílula */
    }
    ```
3.  Execute `vendor/bin/sail npm run build`.

**Resultado**: Apenas os botões vermelhos serão arredondados; os verdes e outros permanecerão padrão.

---

## 📂 Visão Geral da Árvore de Arquivos

```text
resources/css/filament/admin/
├── theme.css                   # 🏁 Arquivo raiz (importa tudo)
│
└── components/
    ├── colors.css              # 🎨 Gerenciador de cores
    │
    ├── colors/                 # 🌈 Definições base
    │   ├── light.css           # Variáveis Modo Claro
    │   ├── dark.css            # Variáveis Modo Escuro
    │   └── mapping.css         # Integração Tailwind v4
    │
    ├── primary/                # 🌲 Customizações Verde
    │   ├── buttons.css
    │   ├── inputs.css
    │   └── ...
    │
    ├── danger/                 # 🔴 Customizações Vermelho
    │   └── ...
    │
    ├── sidebar.css             # 🗄️ Estilos Específicos da Sidebar
    └── login.css               # 🔐 Estilos da Página de Login
```

## ✅ Benefícios Desta Estrutura

1.  **Suporte Robusto ao Dark Mode**: As cores mudam automaticamente sem necessidade de classes extras como `dark:bg-green-900`. O CSS cuida disso via variáveis.
2.  **Segurança na Manutenção**: Alterar o estilo de um erro (danger) nunca vai "quebrar" o estilo de um sucesso (primary).
3.  **Organização Mental**: Sabe exatamente onde ir. Quer mexer na cor? Pasta `colors`. Quer mexer no botão? Pasta do componente.
4.  **Performance**: CSS nativo é mais rápido que processamento JS em runtime.
