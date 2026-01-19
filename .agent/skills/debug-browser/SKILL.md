---
name: debug-browser-tests
description: Guidelines for creating and debugging Pest v4 Browser tests, including mobile viewports.
---

# Browser Test Debugger Skill

Use this skill to create end-to-end tests or debug UI issues.

## Rules

### 1. Pest v4 Syntax

- Use `visit('/')`.
- Use `->waitForText('Start')`.

### 2. Mobile Debugging

- To test mobile layouts, set the viewport explicitly at the start of the test.

```php
it('works on mobile', function () {
    $browser = visit('/');
    $browser->driver->manage()->window()->setSize(new \Facebook\WebDriver\WebDriverDimension(375, 812)); // iPhone X size

    $browser->clickHamburgerMenu(); // Custom method example
});
```

### 3. Debugging

- **Screenshots**: `$browser->screenshot('debug-step-1');`
- **Pause**: `$browser->pause(1000);` (Only for local debugging!)
- **Console Logs**: `$browser->assertNoConsoleLogs();`

### 4. CSP Handling

- If you hit CSP errors in tests, check `SecurityHeadersMiddleware`.
