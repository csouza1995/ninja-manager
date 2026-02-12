---
description: Step-by-step for branch management and Conventional Commits in Ninja Manager
---

# Workflow: Git Flow

Use this workflow to manage features and releases. The Agent has full autonomy to create feature branches and commit changes following these standards.

> [!CAUTION]
> **SAFETY CHECK**: Before any modification, the Agent MUST verify it is NOT on `main`, `stage`, or `develop`.

## Starting a new Feature

1. Switch to the `develop` branch:
   `git checkout develop`

2. Pull the latest changes:
   `git pull origin develop`

3. Create a new feature branch:
   `git checkout -b feature/[feature-name]`

## Working on a Feature

1. Make your changes following the **Project Standards**.

2. Stage your changes:
   `git add .`

3. Commit following **Conventional Commits**:
   `git commit -m "feat(scope): descriptive message"`

## Finishing a Feature

1. Ensure your code passes all tests:
   `php artisan test --compact`

2. Run Pint to ensure formatting:
   `vendor/bin/pint --dirty --format agent`

3. Merge `develop` into your branch to resolve conflicts:
   `git merge develop`

4. **ASK THE USER**: Explicitly ask for approval to merge the branch into `develop`.

5. **Merge**: Only after user approval, perform the merge into `develop`.

> [!IMPORTANT]
> Never merge directly into `main` or `stage`. These are reserved for the User. All merges into `develop` must be confirmed by the User first.
