---
name: pest-test-generator
description: Generates high-quality automated tests using Pest v4, including Unit, Feature, and Browser tests.
---

# Pest Test Generator Skill

Use this skill to create reliable automated tests. It enforces the use of Pest v4 syntax and Browser Tests for E2E scenarios.

## When to use this skill

-   When creating any new feature ("Create a test for X").
-   When needing to verify a bug fix.
-   When doing "Visual Regression" (via Browser Testing).

## Recommended Tools

-   **serena_find_file**: To find existing tests for similar features (e.g., "Find tests for UsersTable").
-   **laravel_boost_run_test**: To run the specific test file created immediately after generation.

## Workflow

### 1. Decision: Type of Test

-   **Unit**: Pure logic, no database (e.g., Service class logic). `make:test --unit --pest`
-   **Feature**: HTTP requests, Database side-effects, API, Livewire. `make:test --pest`
-   **Browser**: Full Javascript execution, complex interactions (Drag & drop, Modal flows). `make:test --browser --pest`

### 2. Implementation Rules

#### A. Syntax (Pest v4)

-   **Functions**: Use `it('does something', function () { ... })`.
-   **Expectations**: `expect($value)->toBeTrue()`. Avoid PHPUnit's `$this->assertTrue()`.

#### B. Feature Tests (API / Livewire)

-   **Livewire**: Use `Livewire::test(Component::class)`.
    -   -> `assertSet('prop', 'val')`
    -   -> `call('method')`
    -   -> `assertSee('Text')`
-   **HTTP**: `getJson()`, `postJson()`.
    -   Use specific assertions: `assertUnprocessable()`, `assertForbidden()`.

#### C. Browser Tests (`tests/Browser`)

-   **Setup**: `use RefreshDatabase;` is usually needed.
-   **Interaction**:
    ```php
    $browser->visit('/admin/users')
            ->waitForText('John Doe')
            ->click('@edit-button-1') // Use dusk selectors if available
            ->assertPathIs('/admin/users/1/edit');
    ```

### 3. Naming Conventions

-   **Files**: `[Feature]Test.php` (e.g., `UserRegistrationTest.php`).
-   **Descriptions**: "it [behavior] [context]" -> "it validates email format on registration".

### 4. Datasets

Use datasets for repetition reduction:

```php
it('validates email', function ($email) {
    // ...
})->with(['invalid', 'no-at-sign.com']);
```
