# Manual HTML — labSIS-KIT

Manual técnico estático gerado a partir dos arquivos Markdown em `docs/`.

## Navegação (UX inspirada em [Filament Docs](https://filamentphp.com/docs/5.x/getting-started))

- **Sidebar esquerda:** seções sempre visíveis + link direto para cada página.
- **Conteúdo:** breadcrumb, título H1, largura de leitura confortável (~42rem).
- **Coluna direita (desktop ≥1100px):** “Nesta página” com âncoras dos `h2` e destaque ao rolar.
- **Rodapé:** cards Anterior/Próximo entre documentos do mesmo capítulo.
- **Mobile:** botão Menu + sidebar em overlay.
- **Hub `index.html`:** grade de capítulos (sem layout de artigo).

## Abrir no navegador

- **Hub:** abra [`index.html`](index.html) nesta pasta (duplo clique ou servidor local).
- Com Laravel Sail/Artisan: sirva a pasta `public` e acesse os arquivos via caminho relativo ao projeto, ou use um servidor estático apontando para `docs/manual/`.

```bash
# Exemplo com Python
cd docs/manual && python3 -m http.server 8765
# http://localhost:8765/
```

## Regenerar o manual

Após alterar qualquer `.md` em `docs/` (exceto esta pasta `docs/manual/`):

```bash
python3 scripts/build-docs-manual.py
```

O script gera:

- `docs/manual/**/*.html` — uma página por documento
- `docs/manual/index.html` — hub com os 9 capítulos
- `docs/manual/<secao>/index.html` — índice de cada capítulo
- `docs/manual/manual-manifest.json` — metadados de navegação

## Fonte da verdade

Edite sempre os arquivos **Markdown** em `docs/`. Não altere os HTML gerados manualmente — eles serão sobrescritos no próximo build.

## Visual

Baseado no [design system LabSIS](https://labsis.dev.br) (tema escuro). Assets compartilhados em `assets/labsis-manual.css` e `assets/labsis-manual.js`.
