# Git Conventions for Ninja Manager

## Commit Message Format (Conventional Commits)

All commits must follow the Conventional Commits specification.

Format: `<type>(<scope>): <description>`

### Types:

- **feat**: A new feature for the user, not a new feature for builds.
- **fix**: A bug fix for the user, not a fix to a build script.
- **docs**: Changes to the documentation.
- **style**: Formatting, missing semi colons, etc; no production code change.
- **refactor**: Refactoring production code, eg. renaming a variable.
- **test**: Adding missing tests, refactoring tests; no production code change.
- **chore**: Updating grunt tasks etc; no production code change.
- **perf**: A code change that improves performance.
- **ci**: Changes to CI configuration files and scripts.
- **build**: Changes that affect the build system or external dependencies.

### Guidelines:

- Use the imperative, present tense: "change" not "changed" nor "changes".
- Don't capitalize the first letter.
- No dot (.) at the end.

## Branching Guidelines

- **main**: Source of truth for production. Only the user merges to main.
- **stage**: Mirror of production/staging environment. Only the user merges to stage.
- **develop**: Main integration branch. All features start here.
- **feature/[name]**: Feature branches created from `develop`.
- **hotfix/[name]**: Critical fixes created from `main` or `stage`.

## Merging

- Always merge `develop` into your feature branch before requesting a merge into `develop`.
- Use `git merge --no-ff` to keep track of branch history.

## Safety First

- **Branch Validation**: Before making any modification, the Agent must run `git branch --show-current`.
- **Forbidden Branches**: No direct commits to `main`, `stage`, or `develop`. All merges into `develop` MUST have user approval.
- **Work Branches**: All work must happen in dedicated branches (e.g., `feature/*`, `hotfix/*`).
