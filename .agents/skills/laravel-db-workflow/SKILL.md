---
name: laravel-db-workflow
description: Use this skill when the task involves reading database schema, running read-only database queries, planning database writes, creating migrations, writing seeders, backfilling data, or making Laravel-safe database changes in this project. Use it for requests about DB read/write workflows, schema inspection, data fixes, migration design, and batch updates where the agent should prefer Laravel Boost database tools for reads and Laravel migrations, seeders, commands, or models for writes.
---

# Laravel DB Workflow

Follow this workflow whenever working on database-related tasks in this project.

## 1. Start With Safe Discovery

- Read `storage/app/ai-context/node/work-target-analysis.md`, then `storage/app/ai-context/node/page-node-analysis.md`, then `storage/app/ai-context/node/project-analysis.md` before opening unrelated project files.
- Use `search-docs` before code changes when the task touches Laravel database behavior.
- Inspect database structure with `database-schema` before proposing queries, migrations, or data updates.

## 2. Read Operations

- Prefer `database-query` for read-only SQL.
- Before querying, state the target tables, columns, filters, ordering, and limit.
- Keep queries narrow. Avoid unbounded full-table reads unless the user clearly asks for them.
- Use schema inspection first if table or column names are uncertain.

## 3. Write Operations

- Do not perform direct write SQL through ad hoc scripts when a Laravel-native path is appropriate.
- Use migrations for schema changes.
- Use seeders, commands, services, or models for business-data writes and backfills.
- Use `php artisan make:` commands with `--no-interaction` when generating Laravel files.
- For temporary one-off writes, use `php artisan tinker --execute '...'` only when the change is small, explicit, and low risk.

## 4. Before Any Write

- List the affected tables.
- List the affected columns.
- List the selection criteria or `where` conditions.
- Estimate the affected row scope when possible.
- Call out any risk of updating or deleting more rows than intended.

## 5. Guardrails

- Never run an unscoped mass `update` or `delete`.
- Do not mix schema changes and data backfills in the same migration unless the codebase already follows that pattern and the change is trivial.
- Prefer reversible migrations.
- Follow the project's naming rules:
  - Tables use `hahaha_xxx_xxx`.
  - PHP local variables use `xxx_xxx_`.
  - Enum-like values in `code/enum` use PHP class constants, not PHP native enums.
- Respect Octane safety rules: avoid request-specific state in singletons and avoid static accumulation patterns.

## 6. Verification

- Run the smallest relevant test after changes.
- If PHP files changed, run `vendor/bin/pint --dirty --format=agent`.
- For migrations or data-writing commands, verify the affected records with a follow-up read query when practical.

## 7. Response Pattern

Use this structure in database tasks:

1. Schema or file context reviewed
2. Planned read or write path
3. Affected tables / columns / filters
4. Code changes or query results
5. Verification performed
