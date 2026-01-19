---
name: manage-git
description: Enforces strict Git workflow rules including manual commit triggers, Pint formatting, and Conventional Commits.
---

# Git Workflow Skill

Use this skill to manage version control operations. This skill enforces a strict "User-Triggered Only" policy for commits and pushes.

## Critical Rules

1.  **NEVER AUTO-COMMIT**: You must NEVER automatically commit or push changes without an explicit request or confirmation from the user.
2.  **Lint Before Commit**: Always run `./vendor/bin/sail bin pint --dirty` before staging files.
3.  **Conventional Commits**: All commit messages must follow the [Conventional Commits](https://www.conventionalcommits.org/) specification.

## When to use this skill

- When the user asks to "commit", "push", "save changes to git", or "sync branches".
- When finishing a significant task, you may **Propose** a commit command, but **do not execute it** with `SafeToAutoRun: true`.

## Workflow

### 1. Preparation

Always lint changed files first to ensure code style consistency.

```bash
./vendor/bin/sail bin pint --dirty
```

### 2. Staging

Stage the files relevant to the task. Avoid `git add .` if there are untracked files not related to the current task.

```bash
git add [files]
```

### 3. Committing

Construct a commit message following the pattern: `<type>(<scope>): <description>`

- **Types**: `feat`, `fix`, `docs`, `style`, `refactor`, `perf`, `test`, `chore`, `build`, `ci`.
- **Scope**: The module or component affected (e.g., `auth`, `filament`, `api`).
- **Description**: Short, imperative description (max 50 chars recommended).
- **Body** (Optional): Detailed explanation, bullet points allowed.

**Example:**
`feat(auth): add 2fa support for tenants`

### 4. Pushing

Only push if explicitly requested or if it's a sync operation.

```bash
git push origin [branch]
```

## Protocol for Multi-Branch Sync (Develop -> Main)

If the user asks to "sync branches" or "deploy":

1.  Checkout `develop`.
2.  Pull latest `develop`.
3.  Checkout `main`.
4.  Pull latest `main`.
5.  Merge `develop` into `main` (`git merge develop`).
6.  Push both branches.
