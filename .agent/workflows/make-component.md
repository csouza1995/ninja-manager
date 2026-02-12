---
description: How to create a new reusable Component in Ninja Manager
---

# Workflow: Create a new Component

Use this workflow to create reusable UI blocks.

// turbo

1. Decide if it should be **Blade** or **Livewire**:
    - **Blade**: Static presentation, data reception only.
    - **Livewire**: Internal logic, state, backend queries.

2. Generate the component:
    - For Blade: `php artisan make:component Components/[ComponentName] --view --no-interaction`
    - For Livewire: `php artisan make:livewire Components/[ComponentName] --no-interaction`

3. Implement the **Ninja Manager Standards**:
    - For Livewire: Use attributes (`#[Computed]`, `#[Locked]`).
    - For Blade: Use `@props()` for data interface. Use DaisyUI 5 and Tailwind 4.

4. Register events (Livewire):
    - Use `#[On]` for inter-component communication.
    - Use `$dispatch` to notify parents of changes.

5. Verification:
    - Test the component in isolation or within a test page.
