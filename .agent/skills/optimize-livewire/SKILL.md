---
name: optimize-livewire-component
description: Best practices for Livewire 3 components, focusing on performance attributes and Flux UI integration.
---

# Livewire Optimization Skill

Use this skill when creating or refactoring Livewire components to ensure they are performant and visually consistent with Flux UI.

## When to use this skill

- When refactoring "slow" components.
- When creating new interactive UI elements.
- When working with search/filtering capabilities.

## Workflow

### 1. Performance Attributes (Livewire 3)

- **Computed Properties**: Use `#[Computed]` instead of methods for expensive logic.
    ```php
    #[Computed]
    public function users() {
        return User::where(...)->get(); // Cached for the request
    }
    ```
- **URL Binding**: Use `#[Url]` for filters to persist state.
    ```php
    #[Url(history: true)]
    public $search = '';
    ```
- **Security**: Use `#[Locked]` for properties that should not be modified by the frontend (like IDs).
    ```php
    #[Locked]
    public $postId;
    ```
- **Real-time**: Use `wire:model.live.debounce.300ms="..."` for search inputs to reduce server load.

### 2. UI Components (Flux UI)

The project uses **Flux UI Free**. Always prefer Flux components over manual HTML.

| HTML Element | Flux Component  | Example                                             |
| :----------- | :-------------- | :-------------------------------------------------- |
| `<button>`   | `<flux:button>` | `<flux:button variant="primary">Save</flux:button>` |
| `<input>`    | `<flux:input>`  | `<flux:input label="Email" icon="envelope" />`      |
| `<a>`        | `<flux:link>`   | `<flux:link href="/home">Home</flux:link>`          |
| `<table>`    | `<flux:table>`  | (Complex, check documentation)                      |

### 3. Javascript Integration

- **Alpine.js**: Do NOT load Alpine manually.
- **Entanglement**: Use `@entangle` only when necessary. Prefer `$wire` content.
- **Hooks**: Use `livewire:init` for global listeners.

### 4. Code Structure

- **Methods**: Keep them focused.
- **Authorization**: Always add `authorize()` checks or Gate calls inside methods (Livewire actions are public endpoints!).
