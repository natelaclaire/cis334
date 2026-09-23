# CIS 334 – Discovery Project 1 Starter

This bundle contains PHP class stubs and a mapping document for the ERD you will design in Discovery Project 1.

- PHP version: 8.2+ recommended
- Namespace: `App\Models`
- Location: `app/Models/*.php`

## What’s included
- One class per entity with:
  - private typed properties
  - `fromArray()`, `toArray()`, `validate()`
  - helper stubs (e.g., `isCompleted()`, `isSelfFollow()`, etc. where relevant)
  - PDO placeholders (`findById`, `search`, `save`, `delete`) that throw `LogicException`
- `docs/mapping.md` – class ↔ table mapping scaffold

## How to use
1. Complete your ERD per the assignment sheet.
2. If you add/rename fields, update the generator’s $classes array and re-run.
3. In a later project, wire these to PDO repositories and real tables.
4. Replace placeholder exceptions with working persistence logic.

*Generated 2025-09-15T21:44:56+00:00.*