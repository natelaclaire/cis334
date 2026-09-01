---
layout: default
title: Discovery Projects
nav_order: 4
---

# Discovery Projects: Building Campus Connect

Throughout the term, students will build **Campus Connect**, a small social network for a college community. Each discovery project adds one focused capability using the concepts introduced that week. The projects are cumulative: students should begin each week with their previous submission, preserve features that already work, and refactor earlier code when the new chapter offers a better design.

The weekly scope is intentionally narrow. The goal is to discover how a new PHP concept solves a real application problem, explain the design choices made, and leave the application ready for the next increment. Unless a project says otherwise, sample or temporary data is acceptable until database persistence is introduced in Week 10.

## Week 1: Establish the development workflow

**Concept:** A repeatable local PHP workflow: repository organization, the PHP development server, browser testing, source control, and basic debugging.

**Chapter connection:** None. The textbook is not used this week.

**Discovery project — Launch the Campus Connect placeholder:** Create the initial repository and a small PHP site that introduces Campus Connect. Establish a `public/` web root, serve the project locally, and add a landing page that displays the application name, a short description, and a dynamically generated environment check such as the current PHP version. Include a README with setup and launch instructions, then commit the working baseline.

**Evidence of completion:** The application runs through the documented command, the landing page is reachable in a browser, and the repository has a sensible starting structure and commit history.

## Week 2: Model the social network as collaborating objects

**Concept:** Classes and objects, object relationships, encapsulation, inheritance, overriding, and object-oriented flow of control.

**Chapter connection:** Chapter 17, “Introduction to Object-Oriented Programming,” frames an application as collaborating objects whose responsibilities and internal state are deliberately separated.

**Discovery project — Prototype people and posts:** Design a small object model for Campus Connect. Create objects representing a member and a post, give each object responsibility for its own data, and establish the relationship between a post and its author. Instantiate several members and posts in PHP and render a simple activity feed from those objects. Use methods rather than reaching directly into an object’s internal state.

**Evidence of completion:** The page displays posts with their authors, the model contains at least two related classes, and a short design note explains what each class knows and does.

## Week 3: Turn the prototype into well-formed classes

**Concept:** Class declarations, visibility, constructors, constructor property promotion, accessors, validation, magic methods, references, nullable objects, custom methods, and virtual attributes.

**Chapter connection:** Chapter 18, “Declaring Classes and Creating Objects,” supplies the PHP syntax and object-lifecycle tools needed to make the Week 2 model robust.

**Discovery project — Build trustworthy member profiles:** Replace the rough member prototype with a fully declared `Member` class. Initialize required profile data through a constructor, keep properties private, validate changes through methods, and reject invalid values such as an empty display name or malformed email address. Add a useful computed value, such as a profile label or initials, and a string representation suitable for display. Update the feed to use the improved objects and gracefully handle a missing optional profile detail.

**Evidence of completion:** Valid profiles render correctly, invalid data is screened by the class, and application code interacts with private state only through the class’s public API.

## Week 4: Reuse behavior through inheritance

**Concept:** Generalization, subclasses, multilevel inheritance, protected visibility, abstract classes, method overriding and augmentation, substitutability, and `final` classes or methods.

**Chapter connection:** Chapter 19, “Inheritance,” explores how related object types can share a contract and implementation while retaining specialized behavior.

**Discovery project — Introduce specialized community members:** Generalize the profile model into an abstract community-member type and create at least two concrete roles, such as `Student` and `FacultyMember`. Give each role a distinct profile detail or feed badge while ensuring either can be used anywhere a general community member is expected. Override or augment a shared display method without breaking its original promise. Use `final` only where the application has a clear rule that must not be changed.

**Evidence of completion:** A mixed collection of member roles renders through the common parent type, each subtype contributes distinct behavior, and the design note identifies one place where substitutability influenced the implementation.

## Week 5: Organize and autoload the application

**Concept:** Namespaces, fully qualified names, imports, Composer configuration, PSR-4 autoloading, and third-party packages.

**Chapter connection:** Chapter 20, “Managing Classes and Namespaces with Composer,” replaces manual class loading with a scalable project structure and introduces dependency management.

**Discovery project — Give Campus Connect a real package structure:** Move application classes into purposeful namespaces such as `App\Model` and `App\Service`, configure Composer’s PSR-4 autoloader, and remove manual `require` statements for individual classes. Add one small, appropriate Composer package and use it in the site—for example, a UUID package to assign stable identifiers to members and posts.

**Evidence of completion:** `composer install` prepares the project, the app runs through `vendor/autoload.php`, namespaced classes load automatically, and the README identifies the external dependency and its purpose.

## Week 6: Present the network with Twig

**Concept:** Template engines, passing objects and arrays to views, Twig expressions and control structures, includes, template inheritance, blocks, CSS, and navigation state.

**Chapter connection:** Chapter 21, “Efficient Template Design with Twig,” separates PHP application logic from reusable page presentation.

**Discovery project — Design the feed and profile views:** Install and configure Twig, then replace PHP-generated page markup with Twig templates. Create a base layout with shared navigation and styles, a feed page that loops over posts, and a member profile page. Use template inheritance and at least one include or reusable partial, and show a friendly empty-feed message with Twig conditional logic.

**Evidence of completion:** Both pages share one base layout, PHP supplies data rather than assembling HTML, repeated interface elements are templated once, and the current navigation item is visually identifiable.

## Week 7: Structure requests with controllers

**Concept:** Separating display from front-controller logic, multiple controller classes, and sharing controller features through inheritance.

**Chapter connection:** Chapter 22, “Structuring an Object-Oriented Web Application,” develops a front-controller and controller architecture for a multipage site.

**Discovery project — Route requests to focused controllers:** Keep `public/index.php` as a small front controller and move feed and profile request handling into separate controller classes. Give controllers a shared base class for common Twig rendering behavior. Route at least `/`, `/feed`, and `/profile?id=...`, including a useful response when a route or member cannot be found.

**Evidence of completion:** The entry script delegates rather than containing page logic, each controller has a focused responsibility, and all required routes render through Twig.

## Week 8: Make failures visible and diagnosable

**Concept:** Throwing, catching, and bubbling exceptions; `finally`; built-in and custom exception types; PHP logging; Monolog channels and severity levels; and logging exceptions.

**Chapter connection:** Chapter 23, “Error Handling with Exceptions,” provides structured failure handling. Chapter 24, “Logging Events, Messages, and Transactions,” records operational detail without exposing it to visitors.

**Discovery project — Add resilient error handling and an audit trail:** Define meaningful exceptions for failures such as an unknown member or invalid post, allow lower-level failures to bubble to an appropriate boundary, and render a safe error page instead of a raw PHP failure. Configure Monolog with separate application and activity channels. Log routine events, warnings, and caught exceptions at suitable severity levels while keeping sensitive details out of the browser response.

**Evidence of completion:** Deliberately triggering at least two failure cases produces friendly browser responses and useful log entries, and a `finally` block is used where cleanup is genuinely required or its omission is justified in the design note.

## Week 9: Define shared contracts and reusable capabilities

**Concept:** Static properties and methods, constants, shared resources, the singleton pattern, enumerations, abstract methods, interfaces, multiple interfaces, traits, and interchangeable implementations.

**Chapter connection:** Chapter 25, “Static Methods, Properties, and Enumerations,” covers class-wide state, utilities, shared resources, and enums. Chapter 26, “Abstract Methods, Interfaces, and Traits,” emphasizes contracts and composition across otherwise unrelated classes.

**Discovery project — Add reactions and interchangeable feed storage:** Represent reaction types such as `LIKE`, `CELEBRATE`, and `SUPPORT` with a backed enum. Define a feed-storage or cache interface, then implement both an in-memory version and a JSON-file version that controllers can use interchangeably. Extract one genuinely shared behavior, such as timestamp formatting or author-label generation, into a trait. If a static utility or singleton is included, constrain it to a defensible class-wide or shared-resource need rather than using it as general global state.

**Evidence of completion:** A configuration change swaps storage implementations without changing controller behavior, enum values appear in the interface, and the design note explains why an interface, trait, enum, and any static member fit their respective jobs.

## Week 10: Design the relational data model

**Concept:** Relational databases, tables, rows, columns, primary and foreign keys, SQL, database management systems, and the database’s role in MVC architecture.

**Chapter connection:** Chapter 27, “Introduction to Databases,” connects relational design and SQL to the model layer of an object-oriented web application.

**Discovery project — Create the Campus Connect schema:** Translate the in-memory model into a relational schema for members, posts, and reactions. Identify primary keys, foreign keys, required fields, and useful constraints. Write SQL that creates the schema and inserts a small, coherent sample dataset. Include several read queries that answer social-network questions, such as listing the newest posts with their authors and counting reactions per post.

**Evidence of completion:** The setup script creates the database from scratch, relationships prevent orphaned data, sample queries return expected results, and a short data dictionary explains each table and relationship.

## Week 11: Connect the application with PDO

**Concept:** PDO connections, database configuration, schemas, SQL execution from PHP, database classes, database portability, and database-backed multipage applications.

**Chapter connection:** Chapter 28, “Database Programming with the PDO Library,” shows how an object-oriented PHP application connects to a database and retrieves model data for controllers and templates.

**Discovery project — Replace the sample feed with live data:** Create a reusable PDO connection and a repository that retrieves posts and authors from the Week 10 database. Replace temporary arrays or JSON feed data with database results, and display a database-backed feed and individual profile pages. Use prepared statements for values supplied by a request, and configure PDO to report errors through exceptions.

**Evidence of completion:** Changing database rows changes the rendered site, profile lookup uses a bound parameter, database details are configured in one place, and connection or query failures follow the Week 8 error-handling path.

## Week 12: Let members manage posts with CRUD

**Concept:** Creating, reading, updating, and deleting records; form processing; item lookup by ID; and the Post/Redirect/Get pattern.

**Chapter connection:** Chapter 29, “Programming CRUD Operations,” adds complete data-management workflows to the database-backed application.

**Discovery project — Build the post-management workflow:** Add pages and repository methods to create, view, edit, and delete posts. Validate submitted content, require confirmation before deletion, and redirect after successful create or update operations to prevent duplicate form submission. Provide clear success or validation feedback and preserve safe user input when a form must be corrected.

**Evidence of completion:** A visitor can complete every CRUD operation through the browser, refreshing after a successful submission does not repeat the change, invalid input does not alter the database, and all SQL that includes user data is parameterized.

## Week 13: Add accounts and secure persistence

**Concept:** ORM libraries, entities and relationships, environment-based configuration, hashed passwords, password verification, login security, Doctrine, and protecting database credentials.

**Chapter connection:** Chapter 30, “ORM Libraries and Database Security,” replaces repetitive persistence code with object-relational mapping while introducing secure credential and password handling.

**Discovery project — Introduce secure member accounts:** Configure an ORM and map the member-to-post relationship. Move database credentials out of tracked source code and into environment configuration. Add registration and login workflows that hash new passwords with PHP’s password API and verify hashes at login; never store or log plaintext passwords. Restrict post creation, editing, and deletion to an authenticated member, and enforce ownership for changes to an existing post.

**Evidence of completion:** The database stores password hashes, secrets are excluded from version control, ORM relationships can retrieve a member’s posts, anonymous users cannot reach protected actions, and one member cannot alter another member’s post.

## Week 14: Make activity time-aware

**Concept:** ISO 8601, `DateTimeImmutable`, formatting, intervals, recurring periods, time zones, daylight saving time, Unix timestamps, and database date/time values.

**Chapter connection:** Chapter 31, “Working with Dates and Times,” provides reliable date arithmetic and storage/display practices for web applications.

**Discovery project — Add timestamps and feed filters:** Record post creation and update times using an unambiguous database representation. Use `DateTimeImmutable` in the application, display dates in the signed-in member’s selected time zone, and add human-friendly information such as “posted 2 hours ago.” Add a date-based feed filter—for example, posts from the last day, seven days, or thirty days—and ensure an edit updates the modification time without changing the original creation time.

**Evidence of completion:** Stored values remain unambiguous across time zones, two selected time zones display the same event correctly, date filters return expected posts, and at least one boundary case involving daylight saving time or a date interval is documented and tested.

## Week 15: Integrate and demonstrate the complete network

**Concept:** Integration, refactoring, regression testing, security review, and communicating a finished object-oriented database application.

**Chapter connection:** Review of Chapters 17–31. This project asks students to connect the object model, Composer organization, Twig views, controller structure, exceptions and logs, advanced OOP contracts, persistence, security, and time handling developed throughout the term.

**Discovery project — Release Campus Connect 1.0:** Finish the cumulative application as a coherent social network rather than a collection of weekly experiments. Remove obsolete temporary storage, reconcile naming and folder structure, seed demonstration data, improve navigation and empty/error states, and verify the primary user journey: register, log in, view the feed, publish a post, edit it, react, filter by date, and delete it. Update the README so another developer can install dependencies, configure the environment, create the database, run the site, and exercise the main features.

**Evidence of completion:** The documented setup works from a fresh checkout, the full user journey succeeds without uncaught errors, authorization and input-handling checks remain in place, logs contain useful diagnostic information without secrets, and the student submits a concise reflection identifying the most important refactoring decision made across Chapters 17–31.

## Cumulative project arc

By the end of the sequence, Campus Connect should demonstrate the following progression:

1. A reproducible PHP development environment and version-controlled starting point.
2. An encapsulated, reusable object model for members, posts, and reactions.
3. Composer-managed namespaces and dependencies.
4. Twig views served through a front controller and focused controller classes.
5. Exceptions and logs that make failures safe for visitors and useful for developers.
6. Interfaces, traits, enums, and other advanced features used where they clarify the design.
7. A relational schema accessed first through PDO and then through an ORM.
8. Secure accounts and authorized CRUD workflows.
9. Accurate, time-zone-aware activity data.
10. A documented, integrated social network ready to serve as preparation for the final project.
