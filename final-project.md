# Final Project: Authenticated CRUD Web Application

Each student will build a simple CRUD (create, read, update, delete) web application with user authentication and a relational database. This project emphasizes object-oriented PHP, database design and manipulation (via PHP), state management, and robust error handling. Students may either implement the sample described below or propose an original topic that meets the same requirements. Your topic choice is due in Week 4.

## Core Requirements

- Tech stack
   - PHP 8+ (object-oriented style), HTML/CSS, minimal JavaScript as needed
   - MySQL/MariaDB accessed via PDO with prepared statements
   - Sessions for state; no full-stack PHP framework required (libraries allowed with approval)

- Data model
   - At least two related tables (e.g., 1-to-many) beyond the `users` table
   - Provide an Entity Relationship Diagram (ERD) and a `mydb.sql` file that creates tables and optionally seeds the database with test data

- Authentication and authorization
   - Registration, login, logout; passwords hashed with `password_hash`/`password_verify`
   - Auth-only areas (e.g., create/edit/delete) protected by session checks
   - Authorization rule: users can only modify their own records; optional admin role for managing all records

- CRUD functionality
   - Full create/read/update/delete for a primary resource
   - Read views include list and detail pages, with search and basic sort; paginate lists if >25 items

- Validation, security, and errors
   - Server-side validation for all inputs; meaningful error messages and sticky forms
   - Output escaping to prevent XSS; CSRF tokens on all mutating POST forms
   - Graceful error handling with try/catch for DB operations; log unexpected errors (file-based log is fine)

- Architecture and code quality
   - Use classes to separate concerns (e.g., Database, Repository/Model, Service, Controller-like handlers)
   - Keep configuration (DB creds) in a single place (e.g., `config.php`); avoid hard-coding secrets in code
   - Organize code into folders (`public/`, `src/`, `views/`, `data/` or similar); prefer small, testable functions/methods

## Milestones and Deadlines

- Week 1: Project brief and sample topic released
- Week 4: Submit topic selection + draft ERD (tables, fields, relationships)
- Checkpoint A: Database created; basic Create/Read paths working
- Checkpoint B: Update/Delete complete; authentication wired up
- Final Week: Validation and error handling polished; documentation and demo ready

Exact dates will be announced in class/Brightspace for your section.

## Deliverables

- Source code for the application
- `mydb.sql` to set up the database schema and seed the tables
- README with setup/run instructions, assumptions, and how requirements are met
- Demo credentials (e.g., a sample user and, if implemented, an admin)
- Screenshots or a short screencast demonstrating key flows

## Grading Rubric (100 points)

- Functional requirements (CRUD, search/sort, pagination if applicable): 30
- Authentication and authorization (correct, secure, session-based): 15
- Database design and data access (ERD quality, prepared statements, queries): 15
- OOP structure and code organization (classes, separation of concerns): 15
- Validation, security, and error handling (XSS/CSRF, messages, logging): 15
- UX/UI and documentation (readability, navigation, README, demo assets): 10

## Sample Application Topic: "Campus Study Groups"

Build an app where students can create and join study groups.

- Entities: `users`, `groups`, `meetings` (a group has many meetings), and `memberships` (user-to-group)
- Auth: users register/login, manage their profile
- CRUD: users can create a group, edit/delete their own groups; view group details and upcoming meetings
- Search/sort: search groups by course/keywords; sort by newest/most members
- Authorization: only group owners can edit/delete their group; admins (optional) can manage all
- Validation/security: required fields, date/time validation, CSRF tokens, escaped output

Students choosing their own topic must achieve an equivalent scope and demonstrate the same competencies.

## Optional Enhancements (extra credit)

- Password reset flow (token-based); email verification (mock or real)
- File uploads (e.g., group image) with size/type validation
- RESTful JSON endpoints for listing/searching; fetch/AJAX UI
- Improved accessibility and responsive design; client-side validation in addition to server-side
- Basic unit/integration tests for critical classes (where feasible)

## Getting Started (suggested path)

1. Draft your ERD and identify your primary resource and relationships
2. Create your database and set up `config.php` for DB connection details
3. Implement a `Database` class (PDO)
4. Build Create/Read paths first; add Update/Delete once listing/detail pages work
5. Add authentication (register/login/logout) and protect mutating routes
6. Finish validation, CSRF, and error handling; polish UI and documentation

---

## Final Project Analytic Rubric Table

| Category                                     | Weight | Needs Development                                                                                        | Approaching Expectations                                                                                             | Meets Expectations                                                                                                                                    | Exceeds Expectations                                                                                                                  |
| -------------------------------------------- | ------ | -------------------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------- |
| **1. Functional Requirements**               | 30 pts | **0–17**: CRUD partially missing or broken; list/detail inconsistent; no search/sort/pagination.         | **18–23**: CRUD mostly works but with errors; minimal search/sort; pagination missing or incomplete.                 | **24–27**: Full CRUD works correctly; list/detail pages complete; search/sort functional; pagination works if needed.                                 | **28–30**: Full CRUD polished and intuitive; search/sort robust; pagination smooth; extra usability features (filters, bulk actions). |
| **2. Authentication & Authorization**        | 15 pts | **0–8**: Login/registration broken or insecure; no session checks; users can modify others’ data.        | **9–11**: Basic login/registration works; sessions inconsistent; authorization rules weakly enforced.                | **12–13**: Secure login/registration/logout; session checks reliable; users restricted to own records; optional admin role acceptable if implemented. | **14–15**: Strong, seamless auth flows; roles handled gracefully; security best practices evident (session hardening, timeout, etc.). |
| **3. Database Design & Data Access**         | 15 pts | **0–8**: ERD/schema incomplete or incorrect; tables unnormalized; SQL uses string concatenation.         | **9–11**: Schema mostly valid but relationships unclear; prepared statements sometimes missing; queries inefficient. | **12–13**: Clear ERD; normalized schema with ≥2 related tables; PDO with prepared statements used correctly; queries accurate.                        | **14–15**: ERD/schema excellent; database seeded with useful test data; queries optimized and well-structured.                        |
| **4. OOP Structure & Code Organization**     | 15 pts | **0–8**: Minimal or no OOP; logic in single script; config hard-coded; poor organization.                | **9–11**: Some classes but poor separation of concerns; file structure inconsistent.                                 | **12–13**: Proper OOP structure with classes for DB, models, services, controllers; organized file structure; reusable functions.                     | **14–15**: Elegant OOP design; highly modular and maintainable; code follows clear conventions; easily extensible.                    |
| **5. Validation, Security & Error Handling** | 15 pts | **0–8**: Little/no validation; XSS/SQL injection vulnerabilities; no CSRF protection; crashes on errors. | **9–11**: Basic validation present; CSRF or XSS protection missing; error handling inconsistent.                     | **12–13**: Server-side validation complete; sticky forms; CSRF tokens and output escaping used; errors handled with try/catch and logged.             | **14–15**: Strong, user-friendly validation; comprehensive security measures; robust logging and graceful recovery.                   |
| **6. UX/UI & Documentation**                 | 10 pts | **0–5**: Navigation confusing; little/no documentation; README missing or unclear.                       | **6–7**: Interface somewhat usable but clunky; README incomplete or vague.                                           | **8–9**: Clear and usable interface; README includes setup/run instructions, demo credentials, and screenshots/screencast.                            | **10**: Polished, accessible UI; professional documentation; demo assets highly effective.                                            |

---

### Optional Enhancements

Up to **+5 extra credit** for features like password reset, file uploads, REST API endpoints, responsive design, or unit tests.
