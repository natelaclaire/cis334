# CIS 334 – Discovery Project 1: ERD + PHP Class Blueprint

**Context:** You’re extending the CIS 333 “Discovery Projects” site (Bootstrap + PHP + Markdown/YAML CMS) with **simple social features** and a **lightweight CRM** for a small web agency. This first Discovery Project focuses on **data modeling** and **class design**—no SQL or PDO yet. You’ll deliver a complete ERD and PHP class stubs that mirror your entities and will be wired to PDO in later weeks.

---

## Learning objectives
- Translate feature requirements into a well-structured **entity–relationship diagram (ERD)**.
- Apply **normalization (≈3NF)**, clear **cardinalities**, and **ON DELETE** strategies to preserve data integrity.
- Define a coherent **domain model** in object-oriented PHP: private properties, typed fields, getters/setters, and validation method stubs.

---

## Scenario and scope (the minimum you must model)
Design for the following capabilities:

### Social layer
- Users can **follow** each other (self-referencing relation) and **post updates** on projects with **comments**.
- Optional: **Reactions** to posts or comments (e.g., like/celebrate/insight).

### CRM layer
- **Companies** with **Contacts**.
- **Activities** (call/email/meeting/task) owned by a **User** and attached to a **Contact**; can be open or completed, with optional due date.

### Projects & team
- Users own **Projects** (and can collaborate via a join table with roles).
- Projects have **Posts**, and posts have **Comments**.

---

## Part A — ERD (diagram + rationale)

### A1. Entities (suggested baseline)
- **User**, **Profile**
- **Company**, **Contact**, **Activity**
- **Project**, **ProjectMember**, **Post**, **Comment**
- **Follow** (self-join), **Reaction** *(optional but recommended)*

> You may add fields/entities, but keep the baseline intact. If you deviate on cardinalities or delete rules, justify in the rationale.

### A2. Suggested key fields
Use snake_case for columns; UTC timestamps; long-form text in Markdown.

- **User**: `id` PK, `email` (unique), `password_hash`, `display_name`, `role` (enum: admin|staff|client), `created_at`, `updated_at`, `deleted_at` (nullable)
- **Profile**: `user_id` PK&FK→User, `bio_md` TEXT, `website_url`, `location`, `avatar_url`
- **Company**: `id` PK, `name`, `website_url`, `industry` (nullable), `notes_md` TEXT, `created_at`, `updated_at`
- **Contact**: `id` PK, `company_id` FK→Company (nullable), `first_name`, `last_name`, `email` (nullable), `phone` (nullable), `title` (nullable), `notes_md` TEXT, `created_at`, `updated_at`
- **Activity**: `id` PK, `contact_id` FK→Contact, `user_id` FK→User (owner), `type` (enum), `subject`, `due_at` (nullable), `completed_at` (nullable), `notes_md` TEXT, `created_at`
- **Project**: `id` PK, `owner_id` FK→User, `slug` (unique), `title`, `summary_md` TEXT, `status` (enum: active|paused|archived), `visibility` (enum: public|private|unlisted), `created_at`, `updated_at`
- **ProjectMember**: `project_id` FK→Project, `user_id` FK→User, `role` (enum: owner|collaborator|viewer), `added_at`; **PK** (`project_id`,`user_id`)
- **Post**: `id` PK, `project_id` FK→Project, `author_id` FK→User, `body_md` TEXT, `created_at`, `updated_at`, `visibility` (enum: public|project|private)
- **Comment**: `id` PK, `post_id` FK→Post, `author_id` FK→User, `body_md` TEXT, `created_at`
- **Follow**: `follower_id` FK→User, `followee_id` FK→User, `created_at`; **PK** (`follower_id`,`followee_id`); prevent self-follows
- **Reaction**: `id` PK, `user_id` FK→User, `post_id` FK→Post (nullable), `comment_id` FK→Comment (nullable), `type` (enum); exactly one of `post_id`/`comment_id` must be NOT NULL

### A3. Relationship & integrity notes
- Add **FK indexes** for all foreign keys; unique indexes for `user.email` and `project.slug`.
- Recommended ON DELETE rules:
  - `Profile.user_id` → CASCADE
  - `ProjectMember.*` → CASCADE
  - `Post.project_id` → CASCADE; `Post.author_id` → SET NULL or RESTRICT (justify)
  - `Comment.post_id` → CASCADE; `Comment.author_id` → SET NULL or RESTRICT
  - `Contact.company_id` → SET NULL
  - `Activity.contact_id` → CASCADE; `Activity.user_id` → SET NULL
- Consider partial/filtered unique indexes for soft-deleted rows if you use `deleted_at`.

### A4. What to draw and submit
- **Draw** entities with PK/FK, types, nullability, unique/compound keys; draw relationship lines with clear cardinalities; annotate ON DELETE behavior.
- **Submit** a PNG/PDF of the ERD **plus** a 1–2 page **rationale** explaining key choices (normalization, indexes, delete rules, any deviations).

---

## Part B — PHP class blueprint (no I/O yet)
Create one class per entity under `app/Models`. Use **PascalCase** for class names and **camelCase** for property names. Use **private** typed properties and provide **getters/setters**. Implement method **signatures** only; bodies can throw `LogicException` until wired to PDO.

### B1. Required classes
`User`, `Profile`, `Company`, `Contact`, `Activity`, `Project`, `ProjectMember`, `Post`, `Comment`, `Follow`, and `Reaction` *(if you modeled it).*

### B2. Required methods (per class)
- `public static function fromArray(array $row): self`  
- `public function toArray(): array`  
- `public function validate(): array` *(return an array of validation errors; empty if valid)*  
- Standard **getters/setters** for every property

**Relationship helpers (stubs, no DB calls yet):**
- `Project::isMember(int $userId): bool`
- `Project::getMemberIds(): array`
- `Post::getCommentIds(): array`
- `Follow::isSelfFollow(): bool`
- `Activity::isCompleted(): bool`

**Future PDO placeholders (throw `LogicException` for now):**
- `public static function findById(int $id): ?self`
- `public static function search(array $filters): array`
- `public function save(): void`
- `public function delete(): void`

### B3. Mapping document
Create `docs/mapping.md` that lists **Class → Table**, **property → column**, primary key strategy (AUTO_INCREMENT or UUID), and enum strategy.

---

## Deliverables (what to upload)
1. **ERD diagram** (PNG or PDF)
2. **Rationale** (1–2 pages) discussing normalization, indexes, delete rules, and any deviations
3. **PHP class stubs** in `app/Models` with required methods and typed properties
4. **`docs/mapping.md`** documenting class ↔ table mapping
5. *(Optional)* `schema_draft.sql` with `CREATE TABLE` statements matching your ERD

---

## Grading rubric (20 pts)
- **ERD completeness & integrity (8 pts):** Correct entities, PK/FK, cardinalities, sensible ON DELETE rules, and indexes
- **Normalization & constraints (4 pts):** 3NF-ish design, justified enums/nullability
- **Class design (6 pts):** Private typed properties, getters/setters, method signatures, helper stubs, mapping consistency
- **Rationale quality (2 pts):** Clear, concise justification of key choices

---

## Submission checklist
- [ ] Every FK is indexed and has an ON DELETE rule I can defend
- [ ] Join tables use composite PKs (`ProjectMember`, `Follow`)
- [ ] Self-follow prevented; duplicate follows prevented
- [ ] Markdown used for long text; UTC timestamps; emails normalized
- [ ] Visibility/roles modeled for permission checks
- [ ] PHP classes mirror the ERD exactly; strict types enabled
- [ ] Mapping doc explains IDs and enum strategy

---

## Appendix — modeling tips
- **Names:** snake_case columns; singular table names are acceptable if consistent.
- **Soft deletes:** prefer `deleted_at` on user/content tables; pair with filtered unique indexes.
- **Feeds:** index `(project_id, created_at)` on `Post` for recent updates; index `(followee_id)` to show “who follows me.”
- **Diagramming:** include cardinalities on lines (e.g., 1..*, 0..1), label join tables, and annotate unique/compound keys.

