---
name: trello-jira-workflow
description: Use this skill when the task involves reading or writing Trello boards, lists, cards, comments, labels, due dates, members, Jira projects, issues, comments, transitions, statuses, assignees, or related work-tracking data from a Laravel application. Use it for requests to integrate Trello or Jira with Laravel using the HTTP client, sync task data, inspect ticket state, update cards or issues, prepare service classes, commands, configuration, and safely operate Trello and Jira workflows from Codex.
---

# Trello Jira Workflow

Follow this workflow whenever a task touches Trello or Jira.

## 1. Confirm The Execution Path

- Default to integrating Trello or Jira through Laravel's HTTP client, not `curl`.
- Store credentials and base URLs in Laravel configuration, typically `config/services.php` plus environment variables.
- Put remote API logic in dedicated service classes or actions, not in controllers or Blade files.
- If the user explicitly asks to install a Trello or Jira connector or plugin instead, use tool discovery and installation flow before continuing.

## 2. Gather Minimal Context First

- Identify the target system: Trello, Jira, or both.
- Identify the target container:
  - Trello: workspace, board, list, or card
  - Jira: site, project, issue, sprint, or board
- Identify the intended action:
  - read
  - create
  - update
  - move / transition
  - comment
  - assign
  - close / archive
- Identify the exact record keys when available:
  - Trello board id, list id, card id
  - Jira project key, issue key

## 3. Read Workflow

- Read only the narrow scope needed for the task.
- Prefer fetching one board, list, card, project, or issue set instead of broad workspace-wide reads.
- Summarize the current remote state before proposing any mutation.
- When fields are ambiguous, present the found identifiers and ask the user to choose only if the ambiguity materially changes the result.
- When implementing Laravel code, use `Http::baseUrl(...)`, explicit `timeout(...)`, `connectTimeout(...)`, and `throw()`.
- Use `retry()` with backoff for transient remote failures.

## 4. Write Workflow

- Before any write, state:
  - target system
  - target record
  - fields to change
  - current value when known
  - new value
- For destructive or high-impact actions such as bulk move, bulk close, delete, archive, or status transitions, pause and confirm if the scope is not fully explicit.
- Prefer idempotent writes where possible. If rerunning the action could duplicate cards, comments, or issues, call out the risk first.
- After a write, re-read the changed record when tooling allows it and report the actual resulting state.
- For Laravel implementation, keep request construction in one place so headers, auth, timeouts, and retry policy stay consistent.
- Prefer service methods such as `Card_Get`, `Card_Update`, `Issue_Get`, `Issue_Transition` over scattered inline `Http::` calls.

## 5. Safe Defaults By System

### Trello

- Prefer card updates over recreating cards that already exist.
- Be careful when moving cards between lists because automation may trigger downstream actions.
- When editing labels, members, due dates, descriptions, or checklists, update only the requested fields.

### Jira

- Prefer issue-key-targeted operations over summary-text matching.
- Treat transitions separately from field edits because workflow rules may block or alter updates.
- When editing assignee, priority, status, labels, fix versions, or comments, verify the target issue exists and belongs to the expected project.

## 6. Laravel Integration Defaults

- Keep Trello or Jira credentials in environment variables and expose them through `config/services.php`.
- Use Laravel service classes for API access and, when needed, commands or jobs for sync workflows.
- Do not inject request-specific state into Octane-unsafe singletons. Prefer normal bindings, scoped services, or resolver closures.
- Test remote integrations with `Http::fake()` and `Http::preventStrayRequests()`; never hit real Trello or Jira in automated tests.
- If multiple API calls are independent, consider `Http::pool()`.

## 7. When Credentials Or API Details Are Missing

- Do not fabricate remote reads or writes.
- Provide one of these instead:
  - a precise Laravel implementation plan
  - a service class method outline
  - a payload structure
  - a checklist of environment variables, endpoints, and identifiers the user needs to provide
- Clearly label the result as not yet executed.

## 8. Response Pattern

Use this structure in Trello or Jira tasks:

1. Available integration path
2. Target system and records
3. Planned read or write operation
4. Required confirmation or missing identifiers
5. Result or prepared Laravel implementation
