---
name: tailwind-v4-styling
description: Enforces Tailwind CSS v4 standards, including CSS-first configuration and new utility syntax.
---

# Tailwind v4 Styling Skill

Use this skill whenever writing CSS or applying classes. Tailwind v4 behaves differently from v3 (No config js, dynamic values).

## When to use this skill

-   When applying styles to Blade/Flux components.
-   When creating custom CSS files.
-   When configuring theme colors/fonts.

## Workflow

### 1. Configuration (CSS-First)

Do **NOT** look for `tailwind.config.js`. Theme extensions happen in CSS:

```css
@theme {
    --color-brand: oklch(0.72 0.11 178);
    --font-sans: "Inter", sans-serif;
}
```

### 2. Deprecated Utilities (DO NOT USE)

| Deprecated        | Replacement     |
| :---------------- | :-------------- |
| `bg-opacity-50`   | `bg-black/50`   |
| `text-opacity-25` | `text-black/25` |
| `flex-grow`       | `grow`          |
| `flex-shrink`     | `shrink`        |

### 3. Dynamic Values

Tailwind v4 allows dynamic values without configuration:

-   Use `w-[153px]` freely.
-   Use `bg-[#123456]` freely.

### 4. Dark Mode

-   Unless specified otherwise, always include Dark Mode support.
-   Use `dark:` modifier: `<div class="bg-white dark:bg-zinc-800">`.
-   Colors: Prefer semantic names (`bg-zinc-100`) over absolute ones (`bg-gray-100`) for better dark mode adaptation.
