---
name: ninja-business-rules
description: Specific business logic and behavioral rules for the Ninja Manager project.
---

# Ninja Manager Business Rules Skill

This skill activates when you are working on the core logic of Ninja Manager, handling transactions, enums, or data persistence.

## Core Rules

- **Transactions Mandatory**: Any action affecting more than one database table MUST use a transaction.
- **Enum Powered**:
    - Use Enums for all status and type fields.
    - Leverage internal methods in Enums for business logic (e.g., `$status->canTransitionTo($newStatus)`).
- **Validation Before Persistence**: Never use `Model::create()` or `$model->save()` without first validating the input data.
- **Semantic Data**: Prefer descriptive methods on models/enums over raw data manipulation.

## Database Integrity

- Use `DB::transaction()` with closures whenever possible.
- If using `try-catch`, always ensure `rollBack()` is called on failure.

## Component Selection Logic

- Always ask: "Can this be a simple Blade component?"
- If it needs internal logic or handles its own state updates -> **Livewire**.
- If it's a wrapper or presentation for passed-in data -> **Blade**.

## Boost Tools (laravel-boost-ninja)

- Use `database-schema` to inspect tables before migrations.
- Use `tinker` for debugging and checking Eloquent models.
- Use `search-docs` for version-specific Laravel/Livewire help.
