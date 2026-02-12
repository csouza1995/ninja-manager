---
description: Flow for database changes with Enums and Transactions in Ninja Manager
---

# Workflow: Update Database Schema

Use this workflow when modifying the database structure.

// turbo

1. Create a migration:
   `php artisan make:migration [migration_name] --no-interaction`

2. If using new types/statuses:
    - Create or update an Enum in `app/Enums`.
    - Add internal logic/methods to the Enum if needed.

3. Implement the migration:
    - Use `$table->string('status')` for Enum-backed columns.
    - **CRITICAL**: When modifying a column, include all previous attributes to avoid data loss (Laravel 12 behavior).

4. Update Models:
    - Use the `casts()` method to cast columns to Enums.
    - Add relationships and validation rules to FormRequests.

5. Validate:
    - Run `php artisan migrate`.
    - Verify transaction usage in any related Seeders or Logic classes.
