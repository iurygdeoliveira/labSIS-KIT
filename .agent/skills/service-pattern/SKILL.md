---
name: service-pattern
description: Creates or refactors Business Logic into Service classes using MCP-Aware standards.
---

# Service Pattern Skill

Use this skill to encapsulate complex business logic, keeping Controllers/Livewire components "thin". This is the **Business Logic Layer** of the application.

## When to use this skill

-   When a Controller/Component method exceeds 10 lines of logic.
-   When logic is reused across multiple entry points (API + Web + Console).
-   When implementing complex domain features (e.g., Checkout, Subscription).

## Recommended Tools

-   **serena_find_referencing_symbols**: MANDATORY when refactoring. Check where the code is currently called to avoid breaking changes.
-   **laravel_boost_search_docs**: Check for specific Laravel helpers (e.g., `DB::transaction`, `Pipeline`) if the logic involves flow control.

## Workflow

### 1. Refactoring Analysis (If moving code)

If moving existing logic:

1. Use `serena_find_referencing_symbols` on the method being moved.
2. Plan the dependency injection change (Constructor vs Method).

### 2. Implementation Rules

#### A. Location & Naming

-   **Directory**: `app/Services`
-   **Naming**: `[Domain]Service.php` (e.g., `PaymentService`).
-   **Strict Types**: `declare(strict_types=1);` mandatory.

#### B. Dependency Injection

-   **Constructor**: For dependencies used in multiple methods.
-   **Method Injection**: For dependencies specific to one operation.
-   **No Facades**: Inject contracts when possible (e.g., `Illuminate\Contracts\Mail\Mailer` instead of `Mail` facade) for easier testing.

#### C. Return Types & Exceptions

-   Always specify return types.
-   Throw custom Exceptions (`App\Exceptions\[Domain]Exception`) instead of generic `\Exception` or returning `false`/strings.

### 3. Template

```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Order\PaymentFailedException;

final class CheckoutService
{
    public function __construct(
        private readonly PaymentGateway $gateway
    ) {}

    /**
     * @throws PaymentFailedException
     */
    public function process(Order $order, string $token): void
    {
        DB::transaction(function () use ($order, $token) {
            if (! $this->gateway->charge($order, $token)) {
                throw new PaymentFailedException("Charge failed for Order {$order->id}");
            }

            $order->update(['status' => 'paid']);
        });
    }
}
```
