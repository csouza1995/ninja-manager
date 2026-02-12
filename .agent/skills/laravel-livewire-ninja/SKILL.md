---
name: laravel-livewire-ninja
description: Expert guidance for developing reactive Livewire 3 components following Ninja Manager patterns.
---

# Laravel Livewire Ninja Skill

This skill activates when you are creating or modifying Livewire components in the Ninja Manager project. It ensures you follow the specific "Ninja way" of building reactive interfaces.

## Core Directives

- **Component Localization**:
  - Pages: `app/Livewire/Pages`
  - Components: `app/Livewire/Components`
- **Reactivity First**:
  - Use `#[On]`, `#[Computed]`, `#[Url]`, `#[Locked]`.
  - Prefer computed properties for data fetching that stays the same during a request.
- **Blade Purity**:
  - NO classes in Blade.
  - NO heavy logic in Blade. Use Alpine.js for client-side behavior and Livewire for server-side state.
- **Form Handling**:
  - Real-time validation using `wire:model.blur` or `wire:model.live`.
  - Use `#[Validate]` attributes on class properties.

## Component Selection

- **Blade**: For simple data display (badges, avatars, basic buttons).
- **Livewire**: For anything that needs a database query, complex state, or parent-child communication.

## Example Pattern

```php
namespace App\Livewire\Components;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use App\Models\Service;

class ServiceTable extends Component
{
    #[On('service-updated')]
    public function refresh(): void
    {
        // Automatically refreshed by Livewire
    }

    #[Computed]
    public function services()
    {
        return Service::latest()->paginate(10);
    }

    public function render()
    {
        return view('livewire.components.service-table');
    }
}
```
