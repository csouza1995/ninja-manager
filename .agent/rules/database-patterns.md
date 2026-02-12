# Database Patterns for Ninja Manager

## Transactions

Always ensure data integrity when performing multi-table or sequence-dependent operations.

- **Closure approach**: Prefer `DB::transaction(fn() => ...)` for simple operations.
- **Manual approach**: Use `DB::beginTransaction()`, `DB::commit()`, and `DB::rollBack()` within `try-catch` blocks for complex logic.
- **Rules**:
    - Wrap any operation that modifies more than one table.
    - Wrap loop-based insertions/updates.
    - Ensure all related data is consistent before committing.

## Enums

Enums are the standard for handled fixed-set values (Status, Types, etc.).

- **Architecture**:
    - Place enums in `app/Enums`.
    - Use for database migrations (e.g., `->string('status')`).
    - Cast model attributes using the `casts()` method.
- **Business Logic**:
    - Enums should NOT just be strings. They should contain methods for formatting, logic, or retrieving CSS classes (for badges).
    - Use `static` methods for factory-like creation or instance methods for specific logic.
- **Naming**: Use `TitleCase` for keys and consistent names for values.

## Validation

- Never persist data without prior validation.
- Use FormRequests for controllers or `#[Validate]` for Livewire.
