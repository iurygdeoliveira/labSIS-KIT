---
name: scaffold-controller
description: Enforces "Skinny Controllers" pattern, requiring FormRequests for validation and Services for complex logic.
---

# Laravel Controller Pattern Skill

Use this skill when creating or refactoring Controllers.

## Rules

### 1. Verification First

- **Always** create a Form Request class for validation. Never write validation logic (`$request->validate()`) inside the controller.
- **Always** use Resource Classes for API responses.

### 2. Method Structure

- Controllers should only coordinate flow: Input -> Service/Model -> Output.
- Complex business logic belongs in a Service Class (use `service-pattern` skill).

### 3. Route Model Binding & Typing

- Use **Implicit Binding** by matching the route parameter name `{user}` with the controller argument `$user`.
- Type hint the Model `(User $user)` and the Form Request `(StoreUserRequest $request)`.

```php
// Route: Route::get('/users/{user}', [UserController::class, 'show']);

public function show(User $user): Response
{
    return view('users.show', ['user' => $user]);
}

public function store(StoreUserRequest $request)
{
    $data = $request->validated();
    User::create($data);
    // ...
}
```

### 4. Dependency Injection

- Inject Services into the constructor or method signature.

```php
public function __construct(
    protected UserService $userService
) {}
```
