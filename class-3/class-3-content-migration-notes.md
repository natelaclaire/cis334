---
layout: default
title: Class 3 Content Migration Notes
nav_exclude: true
---

# Class 3 content migration notes

This instructor inventory aligns Class 3 with Chapter 19 of the supplied *PHP Crash Course* EPUB. Existing local moves from class-3 to class-4 were already present when this revision began and were preserved.

## Chapter-to-tutorial map

| Chapter 19 section | Tutorial |
|---|---|
| Inheritance as Generalization; Creating Objects from Subclasses; Using Multiple Levels of Inheritance | 3.1 |
| Protected Visibility; UML visibility and stereotyping sidebar | 3.2 |
| Abstract Classes | 3.2 |
| Overriding Inherited Methods; Liskov Substitution Principle sidebar | 3.3 |
| Augmenting Inherited Behavior; augmented constructors | 3.5 |
| Preventing Subclassing and Overriding; final class and method examples | 3.6 |
| Instructor-requested attributes and Override supplement | 3.4 |
| Instructor-requested parent-call decision guidance | 3.5 |
| Instructor-requested introductory ERD video | 3.7 |

The source was `PHPCrashCourse.epub`, `OEBPS/xhtml/chapter19.xhtml`, sections and Listings 19-1–19-23. Lessons use new examples rather than reproduce the chapter. The chapter's abstract-class discussion does not require abstract methods; those and `instanceof` are explicitly labeled supporting extensions. Modern behavior was checked against the official PHP documentation linked in the tutorials and the local PHP 8.4 runtime.

## Keep and rewrite

| Original file | Result | Reason |
|---|---|---|
| `class-3/3.1-inheritance-and-polymorphism.md` | Same active path rewritten; content divided among 3.1–3.6 | One long mixed-topic script became focused chapter-aligned videos |
| `class-3/temp-override-attribute.md` | Renamed and expanded as `3.4-attributes-and-override.md` | Add metadata introduction, version boundaries, typo demo, limits, and source links |
| `class-3/temp-parent-method-call-order.md` | Renamed and expanded as `3.5-calling-parent-methods.md` | Correct constructor inheritance statement; teach dependency-based placement with runnable output |

## Move in from another week

`class-6/6.5-create-an-erd.md` was an empty file. It moved to `class-3/3.7-introduction-to-erds.md` and now contains a complete introductory video script. No existing ERD prose was available to reuse. No references to its old path were found.

The inheritance script had already been moved locally from `class-4/4.1-inheritance-and-polymorphism.md` into class-3 before this work. The current revision respects that location. No additional complete Chapter 19 tutorial was found elsewhere that needed moving into class-3.

## Move out or preserve for later

* The original mixed-topic 3.1 was preserved at `archive/class-3-inheritance-and-polymorphism-legacy.md` before rewriting. Its interfaces, traits, and combined interface/trait example are outside Chapter 19. They remain available for a future advanced OOP lesson, without assigning them to a week whose scope has not yet been revised.
* `class-4/3.4-namespaces.md` and `class-4/3.5-autoloading.md` stay in class-4 for Chapter 20. Their older filename numbering is outside this revision.
* `class-4/3.5-class-3-reinforcement-exercises.md` is a legacy mixed worksheet: cloning, magic methods, chess inheritance, namespaces, and autoloading. It stays in class-4 for that week's future revision. Its chess activity mixes serialization and other prerequisites, so it was not moved wholesale into this class or reused as the new assessment. The new class-3 overview links only the new exercises.
* `class-4/cis_334_discovery_project_1_erd_php_class_blueprint_assignment_sheet.md`, its code, and mapping material remain there as upcoming assignment context. The ERD tutorial introduces the basic skills without silently rewriting that assignment's requirements.
* Later PDO, design-pattern, authentication, and database lessons remain in their current weeks; their uses of inheritance are application-specific, not missing Chapter 19 foundations.

## Class 2 cleanup

The active 2.0–2.6 lessons, 2.9 exercises, 2.10 project, and 2.11 quiz remain useful for Class 2. Its existing PDF exports are retained. Cloning remains the already-selected supplemental topic; it was not removed merely because it is outside Chapters 17–18.

The old `class-2/class-2-content-migration-notes.md` was superseded: it listed archived 2.7/2.8 as active and pointed inheritance at class-4. It moved to `archive/class-2-content-migration-notes-previous.md`; a concise current inventory replaces it. The extra magic-method and dynamic-constant lessons were already in archive when this work began. Class 2's overview and inheritance preview now point at Class 3. No active Class 2 teaching script required removal.

## New material and format

Added the class-3 roadmap, focused 3.2/3.3/3.6 tutorials, five reinforcement exercises in 3.9, and ten multiple-choice questions with explanations in 3.11. Numbering and YAML layout/title/navigation fields follow Class 2; gaps preserve its assessment-numbering convention. No Class 3 discovery project was requested or added.

The exercise sheet supplies complete setup and an instructor guide; it does not depend on changing the separate assignment-template repository. All deliberate failure demonstrations are labeled and must be removed from submitted working code.

## Validation

Checked with PHP 8.4.25: complete tutorial examples, multilevel file loading, constructor initialization, abstract-class and visibility behavior, Override typo detection, compatible/incompatible signatures, final restrictions, and the completed reinforcement solution with invalid-input cases. Working programs produce the documented output; intentional failures were run separately.

All Class 3 Markdown documents have front matter and balanced code fences; relative links resolve. The exercise sheet contains five exercises and the quiz contains ten questions with four options each. The documented `bundle exec jekyll build` command is blocked by the repository's missing Gemfile. Direct `jekyll build` generates HTML successfully but warns that the `default` layout is unavailable locally, so the final theme appearance was not verified. Mermaid rendering also depends on the viewer; the ERD lesson includes equivalent tables and drawing instructions.
