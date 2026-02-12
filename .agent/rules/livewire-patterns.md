# Livewire Patterns for Ninja Manager

## Overview

This rule defines the patterns for Livewire usage in the Ninja Manager project, ensuring consistency and reactivity.

## Component Types

- **Pages**: Located in `app/Livewire/Pages`. These are routeable components used as full pages.
- **Components**: Located in `app/Livewire/Components`. These are reusable UI pieces (forms, tables, etc.).

## Reactivity Guidelines

- **Decorations**: Always prefer PHP 8+ attributes for declaring behavior.
    - Use `#[On('event-name')]` for listeners.
    - Use `#[Computed]` for derived state.
    - Use `#[Url]` for query string parameters.
    - Use `#[Locked]` for data that shouldn't be modified by the frontend.
- **State Management**: Use `Livewire\Attributes\Modelable` when a child component needs to sync state with a parent.
- **Events**: Use `$dispatch` to notify other components of changes, especially when a child action affects a parent's data.

## Blade Integration

- **Direct Class Calls**: NEVER call Eloquent models or static classes directly in Blade (`@php Model::all() @endphp`). All data must be passed from the component.
- **Validation**: Always use `#[Validate]` attributes or the `validate()` method before performing actions.

## Advanced Usage

- **Computed Properties**: Access computed properties via `$this->propertyName` in Blade. Computed properties are cached for the request.
- **Lazy Loading**: Use `#[Lazy]` for components that perform heavy queries.
