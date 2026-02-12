---
description: Integration guide for Antigravity Kit 2 principles in Ninja Manager
---

# Workflow: Antigravity Kit 2 Integration

This project implements the principles of Antigravity Kit 2 to ensure a robust multi-agent environment.

## Structure

- **Rules**: Located in `.agent/rules/`. These are core behavioral constraints.
- **Skills**: Located in `.gemini/antigravity/skills/`. These provide specialized knowledge.
- **Workflows**: Located in `.agent/workflows/`. These provide step-by-step procedures (slash commands).

## Usage

1. **Activate Skills**: When starting a task in a specific domain (e.g., Livewire), explicitly call out and read the relevant `SKILL.md`.
2. **Follow Workflows**: Use slash commands (like `/make-page`) to ensure standardized generation of files.
3. **Enforce Rules**: Always check and abide by the rules in `.agent/rules/` before finalizing any PR or change.

## Maintenance

- When discovery of new patterns occurs, update the corresponding Rule or Skill.
- Create new Workflows for repetitive multi-step processes.
