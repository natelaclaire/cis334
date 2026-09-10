---
layout: default
title: Class 2 Content Migration Notes
nav_exclude: true
---

# Class 2 Content Migration Notes

This instructor-facing inventory records the decisions made while aligning Class 2 with Chapters 17–18 of *PHP Crash Course*.

## Tutorial sequence

| Order | Script | Primary coverage |
|---|---|---|
| 1 | `2.1-oop.md` | Chapter 17 OOP concepts, relationships, encapsulation, inheritance preview, flow of control |
| 2 | `2.2-defining-custom-classes.md` | Chapter 18 class declarations, visibility, typed members, loading, instantiation, `$this` |
| 3 | `2.3-accessors-and-property-hooks.md` | Chapter 18 accessors and validation; PHP 8.4 property hooks and asymmetric visibility |
| 4 | `2.4-constructors-and-object-behavior.md` | Chapter 18 constructors, promotion, `__toString()`, custom methods, virtual attributes |
| 5 | `2.5-object-references-and-missing-objects.md` | Chapter 18 object identity, nullable objects; modern nullsafe syntax |
| 6 | `2.6-cloning-objects.md` | Supplemental cloning, shallow copies, `__clone()`, readonly cloning |
| 7 | `2.7-additional-magic-methods.md` | Supplemental serialization, debugging, overloading, and callable objects |
| 8 | `2.8-dynamic-constant-fetch.md` | Supplemental PHP 8.3 dynamic class constant fetch |

## Files kept and rewritten

* `2.0-class-2-overview.md` — rewritten as outcomes, scope, and sequence.
* `2.1-oop.md` — rewritten around all Chapter 17 sections.
* `2.2-defining-custom-classes.md` — rewritten and narrowed to the first part of Chapter 18.
* `2.4-class-2-reinforcement-exercises.md` — ultimately renamed to `2.9-class-2-reinforcement-exercises.md` and replaced with five exercises aligned to all eight tutorials.

## Files moved out of Class 2

* `2.3-inheritance-and-polymorphism.md` moved to `class-4/4.1-inheritance-and-polymorphism.md`. Chapter 17 introduces inheritance conceptually, but the detailed PHP syntax and polymorphism belong with Chapter 19.

## Files moved into Class 2

* `class-3/3.1-cloning-objects.md` moved to `2.6-cloning-objects.md` and was rewritten with typed examples, identity checks, shallow/deep-copy guidance, and PHP 8.3 readonly cloning.
* `class-3/3.2-magic-methods.md` moved to `2.7-additional-magic-methods.md` and was rewritten with typed signatures and stronger guidance about security and appropriate use.
* `class-3/3.3-modern-php-oop-additions.md` previously moved into the Class 2 revision. Its constructor-promotion and nullsafe material appears in 2.4 and 2.5; its dynamic constant fetch content was restored and expanded as `2.8-dynamic-constant-fetch.md`.

## Files left in place elsewhere

* Namespace and autoloading scripts remain outside Class 2 because those topics are developed with Chapter 20.

## Supplemental coverage decision

Cloning, magic methods beyond `__construct()` and `__toString()`, and dynamic class constant fetch are not attributed to Chapters 17–18. They are deliberately included as instructor-selected extensions because they reinforce object identity, controlled object behavior, and modern PHP syntax.

## Archive result

The `archive/` folder was created for superseded material. No original Class 2 file needed to be archived: three were rewritten in place, one moved to its Chapter 19 week, and the exercise file was renamed and replaced. The archive README records this no-loss decision.
