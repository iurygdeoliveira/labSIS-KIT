#!/usr/bin/env python3
"""Gera o manual HTML LabSIS a partir de docs/**/*.md."""
from __future__ import annotations

import html
import json
import re
from datetime import date
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
DOCS = ROOT / "docs"
MANUAL = DOCS / "manual"
ASSETS = MANUAL / "assets"
TEMPLATE = MANUAL / "templates" / "labsis-manual-page.html"

SECTIONS = [
    ("01-instalacao-e-setup", "01 — Instalação e Setup"),
    ("02-autenticacao-e-seguranca", "02 — Autenticação e Segurança"),
    ("03-ui-e-customizacao", "03 — UI e Customização"),
    ("04-backend-e-arquitetura", "04 — Backend e Arquitetura"),
    ("05-otimizacoes", "05 — Otimizações"),
    ("06-testes", "06 — Testes"),
    ("07-qualidade-de-codigo", "07 — Qualidade de Código"),
    ("08-ai-agents", "08 — AI Agents"),
    ("09-roadmap-futuro", "09 — Roadmap futuro"),
]

SECTION_BY_DIR = {d: label for d, label in SECTIONS}

BACK_SVG = (
    '<svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" '
    'stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>'
)
CHEV_SVG = (
    '<svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" '
    'stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>'
)


def slugify(text: str) -> str:
    """Slug de heading compatível com âncoras do Markdown fonte (preserva acentos)."""
    text = text.strip().lower()
    text = re.sub(r"[^\w\s-]", "", text, flags=re.UNICODE)
    return re.sub(r"[-\s]+", "-", text).strip("-")[:80] or "secao"


def parse_frontmatter(text: str) -> tuple[dict, str]:
    meta: dict = {}
    body = text
    if text.startswith("---"):
        parts = text.split("---", 2)
        if len(parts) >= 3:
            for line in parts[1].strip().splitlines():
                if ":" in line:
                    k, v = line.split(":", 1)
                    meta[k.strip()] = v.strip()
            body = parts[2].lstrip("\n")
    return meta, body


def md_path_to_html_href(from_md: Path, target: str) -> str:
    """Resolve link relativo .md para .html no manual."""
    target = target.split("#", 1)
    path_part = target[0]
    anchor = ("#" + target[1]) if len(target) > 1 else ""

    if not path_part or path_part.startswith("http"):
        return path_part + anchor

    if path_part.endswith(".md"):
        path_part = path_part[:-3] + ".html"

    base = from_md.parent
    resolved = (base / path_part).resolve()

    try:
        rel = resolved.relative_to(DOCS.resolve())
    except ValueError:
        return path_part + anchor

    parts = list(rel.parts)
    if parts[0] == "plans":
        parts = ["09-roadmap-futuro"] + parts[1:]
    elif parts[0] == "manual":
        return path_part + anchor

    manual_rel = Path("manual", *parts)
    from_manual = manual_rel_for_md(from_md)
    try:
        href = Path(
            os_path_relpath(manual_rel, from_manual.parent)
        ).as_posix()
    except Exception:
        href = manual_rel.as_posix()
    return href + anchor


def os_path_relpath(target: Path, start: Path) -> Path:
    import os

    return Path(os.path.relpath(target, start))


def manual_rel_for_md(md_path: Path) -> Path:
    rel = md_path.relative_to(DOCS)
    if rel.parts[0] == "plans":
        return MANUAL / "09-roadmap-futuro" / (rel.stem + ".html")
    return MANUAL / rel.with_suffix(".html")


def assets_prefix(manual_html: Path) -> str:
    depth = len(manual_html.relative_to(MANUAL).parts) - 1
    return "../" * depth + "assets/" if depth else "assets/"


def manual_href(from_html: Path, to_parts: list[str]) -> str:
    """Caminho relativo entre páginas do manual (sem passar por assets/)."""
    target = MANUAL.joinpath(*to_parts)
    return os_path_relpath(target, from_html.parent).as_posix()


def md_inline(s: str, from_md: Path | None = None) -> str:
    s = html.escape(s)

    def link_repl(m: re.Match) -> str:
        label = m.group(1)
        url = m.group(2)
        if from_md and not url.startswith(("http", "#", "mailto:")):
            url = md_path_to_html_href(from_md, url)
        return f'<a href="{html.escape(url, quote=True)}">{label}</a>'

    s = re.sub(r"\[([^\]]+)\]\(([^)]+)\)", link_repl, s)
    s = re.sub(r"\*\*(.+?)\*\*", r"<strong>\1</strong>", s)
    s = re.sub(r"(?<!\*)\*([^*]+?)\*(?!\*)", r"<em>\1</em>", s)
    s = re.sub(r"`(.+?)`", r"<code>\1</code>", s)
    return s


def parse_markdown_body(body: str, from_md: Path) -> tuple[str, list[tuple[str, str]]]:
    """Retorna HTML do conteúdo e lista (id, título) dos h2 para TOC."""
    lines = body.splitlines()
    if lines and lines[0].startswith("# "):
        lines = lines[1:]
    while lines and not lines[0].strip():
        lines.pop(0)

    out: list[str] = []
    toc: list[tuple[str, str]] = []
    i = 0
    in_code = False
    code_lang = ""
    code_lines: list[str] = []
    list_buf: list[str] = []
    list_ordered = False

    def flush_list() -> None:
        nonlocal list_buf, list_ordered
        if not list_buf:
            return
        tag = "ol" if list_ordered else "ul"
        out.append(f"<{tag}>")
        for item in list_buf:
            out.append(f"<li>{md_inline(item, from_md)}</li>")
        out.append(f"</{tag}>")
        list_buf = []

    while i < len(lines):
        line = lines[i]

        if in_code:
            if line.strip().startswith("```"):
                lang_label = code_lang or "code"
                code_text = html.escape("\n".join(code_lines))
                out.append(
                    f'<div class="terminal-block"><div class="term-bar">'
                    f'<span class="term-dot red"></span><span class="term-dot yellow"></span>'
                    f'<span class="term-dot green"></span>'
                    f'<span class="term-label">{html.escape(lang_label)}</span></div>'
                    f"<pre><code>{code_text}</code></pre></div>"
                )
                in_code = False
                code_lines = []
                code_lang = ""
            else:
                code_lines.append(line)
            i += 1
            continue

        if line.strip().startswith("```"):
            flush_list()
            in_code = True
            code_lang = line.strip()[3:].strip() or "bash"
            i += 1
            continue

        if line.startswith("|"):
            flush_list()
            rows = []
            while i < len(lines) and lines[i].startswith("|"):
                rows.append(lines[i])
                i += 1
            if len(rows) >= 2 and re.match(r"^\|[-| :]+\|$", rows[1].strip()):
                headers = [c.strip() for c in rows[0].strip("|").split("|")]
                out.append('<table class="data"><thead><tr>')
                for h in headers:
                    out.append(f"<th>{md_inline(h, from_md)}</th>")
                out.append("</tr></thead><tbody>")
                for row in rows[2:]:
                    cells = [c.strip() for c in row.strip("|").split("|")]
                    out.append("<tr>")
                    for c in cells:
                        out.append(f"<td>{md_inline(c, from_md)}</td>")
                    out.append("</tr>")
                out.append("</tbody></table>")
            continue

        m_h2 = re.match(r"^## (.+)$", line)
        m_h3 = re.match(r"^### (.+)$", line)
        m_h4 = re.match(r"^#### (.+)$", line)

        if m_h2:
            flush_list()
            title = m_h2.group(1).strip()
            sid = slugify(title)
            toc.append((sid, title))
            out.append(f'<h2 id="{sid}">{md_inline(title, from_md)}</h2>')
            i += 1
            continue

        if m_h3:
            flush_list()
            out.append(f"<h3>{md_inline(m_h3.group(1).strip(), from_md)}</h3>")
            i += 1
            continue

        if m_h4:
            flush_list()
            out.append(f"<h4>{md_inline(m_h4.group(1).strip(), from_md)}</h4>")
            i += 1
            continue

        if line.startswith("> "):
            flush_list()
            quote_lines = []
            while i < len(lines) and (lines[i].startswith("> ") or lines[i] == ">"):
                quote_lines.append(lines[i][2:].lstrip() if lines[i].startswith("> ") else "")
                i += 1
            q = " ".join(quote_lines).strip()
            out.append(
                f'<div class="callout callout-info"><p>{md_inline(q, from_md)}</p></div>'
            )
            continue

        m_ol = re.match(r"^(\d+)\.\s+(.*)", line)
        m_ul = re.match(r"^(\s*)[-*]\s+(.*)", line)
        m_check = re.match(r"^(\s*)[-*]\s+\[([ xX])\]\s+(.*)", line)

        if m_check:
            flush_list()
            checked = m_check.group(2).lower() == "x"
            icon = "✓" if checked else "○"
            out.append(
                f'<div class="check-item"><span class="check-icon" aria-hidden="true">{icon}</span>'
                f"<span>{md_inline(m_check.group(3), from_md)}</span></div>"
            )
            i += 1
            continue

        if m_ul:
            if list_ordered and list_buf:
                flush_list()
            list_ordered = False
            list_buf.append(m_ul.group(2))
            i += 1
            while i < len(lines):
                cont = re.match(r"^(\s+)[-*]\s+(.*)", lines[i])
                if cont and len(cont.group(1)) >= 2:
                    list_buf.append(cont.group(2))
                    i += 1
                else:
                    break
            continue

        if m_ol:
            if not list_ordered and list_buf:
                flush_list()
            list_ordered = True
            list_buf.append(m_ol.group(2))
            i += 1
            continue

        if not line.strip():
            flush_list()
            i += 1
            continue

        flush_list()
        para = [line.strip()]
        i += 1
        while i < len(lines) and lines[i].strip() and not lines[i].startswith(("#", "|", ">", "```", "-", "*")) and not re.match(r"^\d+\.\s", lines[i]):
            para.append(lines[i].strip())
            i += 1
        out.append(f"<p>{md_inline(' '.join(para), from_md)}</p>")

    flush_list()
    if in_code and code_lines:
        code_text = html.escape("\n".join(code_lines))
        out.append(f'<pre><code>{code_text}</code></pre>')

    return "\n".join(out), toc


def section_for_md(md_path: Path) -> tuple[str, str]:
    rel = md_path.relative_to(DOCS)
    if rel.parts[0] == "plans":
        return "09-roadmap-futuro", SECTION_BY_DIR["09-roadmap-futuro"]
    folder = rel.parts[0]
    return folder, SECTION_BY_DIR.get(folder, folder)


def build_sidebar_html(
    from_html: Path,
    by_section: dict[str, list[dict]],
    active_filename: str = "",
    active_section: str = "",
) -> str:
    """Menu lateral com links diretos para cada página do manual."""
    home = manual_href(from_html, ["index.html"])
    parts = [
        '<div class="sidebar-header">',
        '<button type="button" class="sidebar-close" data-sidebar-close aria-label="Fechar menu">',
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">',
        '<path d="M18 6 6 18M6 6l12 12"/></svg></button>',
        "</div>",
        '<nav class="sidebar-nav" aria-label="Documentação">',
    ]
    is_home = from_html.resolve() == (MANUAL / "index.html").resolve() and not active_filename
    home_active = ' class="is-active"' if is_home else ""
    parts.append('<ul class="sidebar-pages">')
    parts.append(f'<li><a href="{home}"{home_active}>Início do manual</a></li>')
    parts.append("</ul>")

    for sid, label in SECTIONS:
        pages = by_section.get(sid, [])
        if not pages:
            continue
        group_open = sid == active_section
        open_attr = " open" if group_open else ""
        parts.append(
            f'<details class="sidebar-section" data-section="{sid}"{open_attr}>'
            f'<summary class="sidebar-section-label">{html.escape(label)}</summary>'
            f'<ul class="sidebar-pages">'
        )
        for p in pages:
            href = manual_href(from_html, [sid, p["filename"]])
            cls = ' class="is-active"' if p["filename"] == active_filename else ""
            short_title = html.escape(p["title"])
            if len(p["title"]) > 56:
                short_title = html.escape(p["title"][:53] + "…")
            parts.append(f'<li><a href="{href}"{cls}>{short_title}</a></li>')
        parts.append("</ul></details>")

    parts.append("</nav>")
    return "\n".join(parts)


def _filter_toc_entries(toc: list[tuple[str, str]]) -> list[tuple[str, str]]:
    skip = {"índice", "indice", "index", "sumário", "sumario"}
    filtered = []
    for sid, title in toc:
        plain = re.sub(r"[^\w\s]", "", title.lower()).strip()
        if plain in skip or plain.startswith("indice"):
            continue
        filtered.append((sid, title))
    return filtered


def build_toc_rail(toc: list[tuple[str, str]]) -> str:
    entries = _filter_toc_entries(toc)
    if len(entries) < 2:
        return ""
    links = "\n".join(
        f'<li><a class="doc-toc-link" href="#{sid}" data-toc-target="{sid}">'
        f"{html.escape(title)}</a></li>"
        for sid, title in entries
    )
    return (
        '<aside class="doc-toc-rail" aria-label="Nesta página">'
        '<p class="doc-toc-title">Nesta página</p>'
        f'<ul class="doc-toc-list">{links}</ul>'
        "</aside>"
    )


def build_breadcrumb_html(
    from_html: Path,
    section_label: str,
    page_title: str,
    *,
    is_section_index: bool = False,
) -> str:
    home = manual_href(from_html, ["index.html"])
    parts = [
        f'<a href="{home}">Manual</a>',
        '<span class="doc-breadcrumb-sep" aria-hidden="true">/</span>',
    ]
    if is_section_index:
        parts.append(f'<span class="doc-breadcrumb-current">{html.escape(section_label)}</span>')
    else:
        section_id = None
        for sid, label in SECTIONS:
            if label == section_label:
                section_id = sid
                break
        if section_id:
            sec_href = manual_href(from_html, [section_id, "index.html"])
            parts.append(f'<a href="{sec_href}">{html.escape(section_label.split("—", 1)[-1].strip())}</a>')
            parts.append('<span class="doc-breadcrumb-sep" aria-hidden="true">/</span>')
        parts.append(f'<span class="doc-breadcrumb-current">{html.escape(page_title)}</span>')
    return '<nav class="doc-breadcrumb" aria-label="Trilha">' + "".join(parts) + "</nav>"


def build_prev_next(
    entry: dict,
    section_pages: list[dict],
    assets: str,
) -> str:
    idx = next((i for i, p in enumerate(section_pages) if p["slug"] == entry["slug"]), -1)
    parts = []
    if idx > 0:
        prev_p = section_pages[idx - 1]
        parts.append(
            '<a class="btn btn-secondary" href="' + prev_p["filename"] + '">'
            + CHEV_SVG
            + '<span><span class="label">Anterior</span>'
            + '<span class="title">' + html.escape(prev_p["title"]) + "</span></span></a>"
        )
    else:
        parts.append("<span></span>")
    if idx >= 0 and idx < len(section_pages) - 1:
        next_p = section_pages[idx + 1]
        parts.append(
            f'<a class="btn btn-primary" href="{next_p["filename"]}" style="margin-left:auto">'
            f'<span><span class="label">Próximo</span>'
            f'<span class="title">{html.escape(next_p["title"])}</span></span>'
            '<svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" '
            'stroke-width="2"><path d="m9 18 6-6-6-6"/></svg></a>'
        )
    blocks = []
    if idx > 0:
        prev_p = section_pages[idx - 1]
        blocks.append(
            '<a class="doc-pagination-card doc-pagination-prev" href="' + prev_p["filename"] + '">'
            '<span class="doc-pagination-label">Anterior</span>'
            '<span class="doc-pagination-title">' + html.escape(prev_p["title"]) + "</span></a>"
        )
    if idx >= 0 and idx < len(section_pages) - 1:
        next_p = section_pages[idx + 1]
        blocks.append(
            '<a class="doc-pagination-card doc-pagination-next" href="' + next_p["filename"] + '">'
            '<span class="doc-pagination-label">Próximo</span>'
            '<span class="doc-pagination-title">' + html.escape(next_p["title"]) + "</span></a>"
        )
    if not blocks:
        return ""
    return '<nav class="doc-pagination" aria-label="Navegação entre documentos">' + "".join(blocks) + "</nav>"


def render_page(
    md_path: Path,
    entry: dict,
    section_pages: list[dict],
    by_section: dict[str, list[dict]],
    template: str,
) -> str:
    raw = md_path.read_text(encoding="utf-8")
    meta, body = parse_frontmatter(raw)
    title_m = re.search(r"^# (.+)$", body, re.M)
    page_title = meta.get("title") or (title_m.group(1).strip() if title_m else md_path.stem)
    section_id, section_label = section_for_md(md_path)

    html_path = manual_rel_for_md(md_path)
    assets = assets_prefix(html_path)
    section_index = manual_href(html_path, [section_id, "index.html"])
    home_index = manual_href(html_path, ["index.html"])

    content_html, toc = parse_markdown_body(body, md_path)
    toc_rail = build_toc_rail(toc)
    breadcrumb = build_breadcrumb_html(html_path, section_label, page_title)
    sidebar_html = build_sidebar_html(
        html_path, by_section, entry["filename"], section_id
    )
    prev_next = build_prev_next(entry, section_pages, assets)

    safe_md = raw.replace("</script>", "<\\/script>")

    out = template
    replacements = {
        "LANG": meta.get("lang", "pt-BR"),
        "GENERATED_AT": date.today().isoformat(),
        "PAGE_TITLE": html.escape(page_title),
        "DOCUMENT_TITLE": html.escape(page_title + " — Manual labSIS-KIT"),
        "SECTION_LABEL": html.escape(section_label),
        "ASSETS": assets,
        "SIDEBAR": sidebar_html,
        "BREADCRUMB": breadcrumb,
        "TOC_RAIL": toc_rail,
        "SECTIONS_HTML": content_html,
        "PREV_NEXT": prev_next,
        "SECTION_INDEX": section_index,
        "HOME_INDEX": home_index,
        "SOURCE_MARKDOWN": safe_md,
        "ACTIVE_SECTION": section_id,
    }
    for key, val in replacements.items():
        out = out.replace("{{" + key + "}}", val)
    return out


def render_section_index(
    section_id: str,
    section_label: str,
    pages: list[dict],
    by_section: dict[str, list[dict]],
    template_index: str,
) -> str:
    index_path = MANUAL / section_id / "index.html"
    assets = assets_prefix(index_path)
    sidebar_html = build_sidebar_html(index_path, by_section, "index.html", section_id)
    chips = "\n".join(
        f'<a href="{p["filename"]}">{html.escape(p["title"])}</a>' for p in pages
    )
    doc_items = "\n".join(
        f'<li><a href="{p["filename"]}">{html.escape(p["title"])}</a></li>'
        for p in pages
    )
    breadcrumb = build_breadcrumb_html(
        index_path, section_label, section_label, is_section_index=True
    )
    out = template_index
    for key, val in {
        "SECTION_ID": section_id,
        "SECTION_LABEL": html.escape(section_label),
        "SECTION_SHORT": html.escape(section_label.split("—", 1)[-1].strip()),
        "SIDEBAR": sidebar_html,
        "BREADCRUMB": breadcrumb,
        "CAROUSEL": chips,
        "DOC_LIST": doc_items,
        "DOC_COUNT": str(len(pages)),
        "ASSETS": assets,
        "GENERATED_AT": date.today().isoformat(),
    }.items():
        out = out.replace("{{" + key + "}}", val)
    return out


def render_home_index(
    manifest: dict,
    by_section: dict[str, list[dict]],
    template_home: str,
) -> str:
    index_path = MANUAL / "index.html"
    assets = assets_prefix(index_path)
    sidebar_html = build_sidebar_html(index_path, by_section, "", "")
    cards = []
    for sec in manifest["sections"]:
        sid = sec["id"]
        cards.append(
            f'<a class="section-card" href="{sid}/index.html">'
            f"<h3>{html.escape(sec['label'])}</h3>"
            f"<p>Documentação técnica do capítulo.</p>"
            f'<span class="count">{sec["count"]} documentos</span></a>'
        )
    out = template_home
    for key, val in {
        "SIDEBAR": sidebar_html,
        "SECTION_CARDS": "\n".join(cards),
        "TOTAL_DOCS": str(manifest["total_pages"]),
        "ASSETS": assets,
        "GENERATED_AT": date.today().isoformat(),
    }.items():
        out = out.replace("{{" + key + "}}", val)
    return out


def collect_md_files() -> list[Path]:
    files = []
    for p in sorted(DOCS.rglob("*.md")):
        rel = p.relative_to(DOCS)
        if rel.parts[0] == "manual":
            continue
        files.append(p)
    return files


def main() -> None:
    template = TEMPLATE.read_text(encoding="utf-8")
    template_section = (MANUAL / "templates" / "labsis-section-index.html").read_text(encoding="utf-8")
    template_home = (MANUAL / "templates" / "labsis-home-index.html").read_text(encoding="utf-8")

    md_files = collect_md_files()
    by_section: dict[str, list[dict]] = {s[0]: [] for s in SECTIONS}

    for md_path in md_files:
        section_id, section_label = section_for_md(md_path)
        meta, body = parse_frontmatter(md_path.read_text(encoding="utf-8"))
        title_m = re.search(r"^# (.+)$", body, re.M)
        title = meta.get("title") or (title_m.group(1).strip() if title_m else md_path.stem.replace("-", " ").title())
        html_path = manual_rel_for_md(md_path)
        html_path.parent.mkdir(parents=True, exist_ok=True)
        entry = {
            "slug": md_path.stem,
            "title": title,
            "filename": html_path.name,
            "md_source": str(md_path.relative_to(ROOT)),
            "html_output": str(html_path.relative_to(ROOT)),
            "section_id": section_id,
            "section_label": section_label,
        }
        by_section.setdefault(section_id, []).append(entry)

    for sid in by_section:
        by_section[sid].sort(key=lambda e: e["filename"])

    manifest = {
        "generated_at": date.today().isoformat(),
        "total_pages": len(md_files),
        "sections": [
            {"id": sid, "label": SECTION_BY_DIR[sid], "count": len(by_section.get(sid, [])), "pages": by_section.get(sid, [])}
            for sid, _ in SECTIONS
        ],
    }

    for md_path in md_files:
        section_id, _ = section_for_md(md_path)
        section_pages = by_section[section_id]
        entry = next(e for e in section_pages if e["slug"] == md_path.stem)
        html_out = render_page(md_path, entry, section_pages, by_section, template)
        out_path = manual_rel_for_md(md_path)
        out_path.write_text(html_out, encoding="utf-8")

    for sid, label in SECTIONS:
        pages = by_section.get(sid, [])
        if not pages and sid != "09-roadmap-futuro":
            continue
        sec_dir = MANUAL / sid
        sec_dir.mkdir(parents=True, exist_ok=True)
        idx_html = render_section_index(sid, label, pages, by_section, template_section)
        (sec_dir / "index.html").write_text(idx_html, encoding="utf-8")

    home_html = render_home_index(manifest, by_section, template_home)
    (MANUAL / "index.html").write_text(home_html, encoding="utf-8")
    (MANUAL / "manual-manifest.json").write_text(
        json.dumps(manifest, ensure_ascii=False, indent=2), encoding="utf-8"
    )

    print(f"Gerado manual: {len(md_files)} páginas em {MANUAL}")


if __name__ == "__main__":
    main()
