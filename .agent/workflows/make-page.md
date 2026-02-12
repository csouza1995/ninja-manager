---
description: How to create a new route-level Page in Ninja Manager
---

# Workflow: Create a new Page

Use this workflow when you need to add a new top-level page to the application.

// turbo

1. Generate the Livewire component in the `Pages` namespace:
   `php artisan make:livewire Pages/[PageName] --no-interaction`

2. If the page requires a route, add it to `routes/web.php` wraped in appropriate middleware.

3. Ensure the component follows the **Ninja Manager Standards**:
    - Use `#[Layout('layouts.app')]` if not globally configured.
    - Use `#[Title('Page Title')]`.
    - Implement a `render()` method that returns the view.

4. If the page handles specialized data:
    - Define `#[Computed]` properties for data fetching.
    - Use Enums for any status-based logic.

5. Validate the design:
    - Use Tailwind 4 and DaisyUI 5 utilities.
    - Ensure responsive layout.
