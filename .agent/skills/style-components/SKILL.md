---
name: style-components
description: Enforces Modular CSS Architecture, requiring specific CSS files for Filament components instead of global styles.
---

# CSS Component Builder Skill

Use this skill when styling new Filament components or Refactoring existing styles.

## Philosophy

We do NOT put all CSS in `app.css`. We treat CSS like modular components.

## Rules

### 1. File Location

- Create new CSS files in `resources/css/filament/admin/components/[category]/[name].css`.
- **Example**: `resources/css/filament/admin/components/sidebar/item.css`.

### 2. Registration

- Register the new file in `vite.config.js` input array.
- Import it in the Filament Panel Provider (`app/Providers/Filament/AdminPanelProvider.php` -> `->viteTheme(...)`) or verify auto-injection usage.

### 3. Tailwind v4 Architecture

- **CSS-First Config**: Use `@theme` inside your CSS files, not `tailwind.config.js`.
- **Import**: Use `@import "tailwindcss";` at the top of your main CSS.
- **Variables**: Use native CSS variables defined in `@theme` (e.g., `--color-primary-500`).
- **Colors**: Prefer `oklch` for new colors.

### 4. Dark Mode

- Use the `dark:` variant or CSS nesting:

    ```css
    .my-component {
        background: var(--color-white);

        @media (prefers-color-scheme: dark) {
            background: var(--color-gray-900);
        }
    }
    ```
