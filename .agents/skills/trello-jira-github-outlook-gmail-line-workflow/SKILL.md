---
name: trello-jira-github-outlook-gmail-line-workflow
description: Use this skill when the task involves reading or writing Trello, Jira, GitHub, Outlook, Gmail, or LINE data from this Laravel project. Use it for cards, issues, repositories, inboxes, dashboards, comments, drafts, images, attachments, LINE rich menus, and other common provider workflows that should be implemented through Laravel service classes and artisan commands grouped under library/hahaha_laravel_lib.
---

# Multi Provider Workflow

Read [references/provider-map.md](references/provider-map.md) before changing provider services.
Read [references/command-map.md](references/command-map.md) before adding or updating artisan entrypoints.
Read [references/workflow-notes.md](references/workflow-notes.md) when implementing image, card, dashboard, or inbox workflows.

## 1. Structure

- Keep provider services under `library/hahaha_laravel_lib/Services/agents/skills/{provider}`.
- Keep provider commands under `library/hahaha_laravel_lib/Console/Commands/agents/skills/{provider}`.
- Keep provider registration helpers under `library/hahaha_laravel_lib/Providers/agents/skills`.
- Keep shared helpers under `common`.
- Keep skill references under `references`.

## 2. Laravel HTTP Rules

- Use Laravel `Http` client, not `curl`.
- Put credentials in `config/services.php` and `.env`.
- Use explicit `timeout`, `connectTimeout`, `retry`, and `throw()`.
- Use `Http::fake()` and `Http::preventStrayRequests()` in tests.

## 3. Common Provider Scope

- Trello: board, list, card, dashboard, comment, move, archive, image attachment.
- Jira: project, issue, transition, board, dashboard, comment, image attachment.
- GitHub: repo, issue, pull request list, dashboard, comment, contents, image file commit.
- Outlook: folders, messages, drafts, send mail, inbox dashboard, events, image attachment.
- Gmail: labels, messages, threads, drafts, send mail, inbox dashboard, image mail.
- LINE: bot info, group summary, rich menus, dashboard, text, image, flex card.

## 4. Command Pattern

- Each provider gets one artisan command.
- Commands accept `--action` plus provider-specific ids and JSON `--query` / `--payload`.
- Commands should output JSON so the result is easy to pipe or inspect.
