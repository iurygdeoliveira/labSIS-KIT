---
name: scaffold-filament-page
description: Guidelines for creating Custom Filament Pages (independent of Resources) with Navigation Grouping.
---

# Scaffold Filament Page

Use this skill when you need a dashboard, settings page, or report view that is **NOT** attached to a specific Model Resource.

## Context

Custom pages are useful for "dashboard" like views, settings, or tools that don't map 1:1 to a database record.

## Tools

- `scaffold-filament-resource`: Use that skill if the page IS attached to a model.

## Rules

### 1. Navigation Grouping

- Always define `protected static ?string $navigationGroup = 'Settings';` (or appropriate group).
- Use `protected static ?int $navigationSort` to order items.

### 2. View Construction

- Use standard Filament widgets or Blade components within the `view()`.
- If custom HTML is needed, use standard Tailwind classes (v4).

### 3. Authorization

- Add `mount()` check using `abort_unless(auth()->user()->can('view_page'), 403);` or similar Policy check.

### 4. Title & Breadcrumbs

- Customize title: `protected static ?string $title = 'Custom Title';`

## Workflow

1.  **Check for Clusters**: Ask if this page belongs to an existing Cluster (e.g., `Settings`).
2.  **Create Page**: `php artisan make:filament-page [PageName] --cluster=[ClusterName]`
3.  **Define Cluster/Group**: If not using clusters, use `$navigationGroup`.
