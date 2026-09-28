---
layout: default
title: Class 6 instructor notes
nav_exclude: true
---

{% raw %}

# Class 6 instructor notes

## Recording and review

Teach the seven tutorials in roadmap order using PHP 8.4 or newer. Use 6.1 to establish a fresh baseline, then demonstrate the first separation into multiple controller classes in 6.2. No archived tutorials or earlier student files are prerequisites to copy. Paths in scripts belong to student repositories; this folder contains teaching notes, not an installed application.

Keep Composer and its App PSR-4 mapping unchanged. Tutorial and exercise classes use separate namespaces and template directories. The nested entry script stays unchanged after its creation. Tutorial 6.3 is a preparation checkpoint: the existing two-page site remains runnable, and the new classes can be linted, but the product page becomes callable only after the template and route are added in 6.4. Tutorial 6.6 adds the detail route and a child constructor; 6.7 is the tracing and verification lesson, with no new code.

## Key teaching checkpoints

- After 6.1, ask students to find the routing and rendering responsibilities in one class.
- After 6.2, repeat the same requests and observe unchanged output. Explain why moving methods across a class boundary requires public visibility.
- After 6.3–6.4, trace the products action through ProductController and its context array. Temporarily test an empty array, then restore it.
- After 6.5, compare the two compact subclasses with their earlier duplicated setup. Inherited code does not imply one shared Twig instance.
- In 6.3, demonstrate valid public writes and caught exceptions for invalid names/prices. Constructor assignments trigger the same hooks as later writes; invalid writes preserve the previous value.
- In 6.6, explain the explicit parent-constructor call, positive-integer request validation, collection lookup, and 404 response. Trace links from the keyed array through the detail route.
- In 6.7, distinguish PHP class inheritance from Twig template inheritance and connect the entry script to both list and detail responses.

The five exercises form a separate cumulative site. They assess a runnable baseline, separation of responsibilities, a new basic page, a specialized data-backed page, and an inheritance refactoring. The quiz has ten single-answer questions and a teaching key. Remove the key for the student LMS version.

## Textbook corrections and course adaptations

The supplied EPUB's listing 22-9 shows private page methods even though Application calls them. Keep home and contact public in our final DefaultController, as required by the separation introduced in listing 22-1. Listing 22-10 also misspells the book's namespace; consistently use App\Tutorials\Class6 (or App\Exercises\Class6 for exercises).

The book begins by copying its earlier project. Our 6.1 supplies all baseline files anew. The book's root-level paths and namespace are adapted to the student's existing PSR-4 setup and nested class6 URLs. We retain an explicit switch and a Home fallback, using a string check for array-shaped GET values. Explicit autoescaping, strict variables, strict PHP types, typed returns, and local styling continue the course conventions.

The private template-path constant differs from the book's public constant: our constructor alone needs it, while the Twig property must be protected for use by child methods. We retain the chapter's simple approach of constructing the concrete controllers in run. Controller inheritance removes duplicated setup code; optimizing object construction is outside the lesson.

Property hooks in 6.3 and the detail feature in 6.6 are course extensions. The hooks retain public read/write access while validating backed properties. The detail feature uses explicit integer array keys as sample IDs and recreates the collection on every request. In 6.5 both children inherit the constructor; in 6.6 only DefaultController does so automatically, while ProductController calls the parent explicitly. Application checks the ID's format, and ProductController checks whether it exists. Neither changes Composer configuration.

## Common troubleshooting

- Class not found: check the App mapping, namespace, filename case, and three-level Composer path.
- Parse error at a property hook: verify that both CLI and the web server use PHP 8.4 or newer.
- Template not found: check the three-level filesystem path from the controller directory and the filename passed to render.
- Call to private method: keep actions invoked by Application public.
- Uninitialized twig property: remove an unnecessary child constructor or call parent::__construct() in a necessary one.
- Cannot instantiate abstract class: instantiate DefaultController or ProductController, not Controller.
- Missing products or staff variable: match the context key to the loop's variable name; an empty array differs from a missing key.
- Products shows Home: finish the explicit products case in 6.4; adding a link alone does not add a route.
- Detail page shows Home: add the singular product case from 6.6; the plural products case still selects the list.
- Product not found: check the positive integer id and the explicit keys in the products property. Test 404 status in the Network panel.
- Wrong navigation destination: keep links relative to index.php in the current class6 folder.

## Validation

All complete PHP listings passed php -l. Assembled each cumulative tutorial stage in an isolated harness using PHP 8.4.25 and an existing local Twig 3.11.3 installation. No student Composer files or dependencies were changed. Checked all documented action routes, missing/unknown/array-shaped action fallbacks, titles, document structure, active navigation, product names and formatted prices, empty collections, and HTML escaping. Compared output before and after the separation in 6.2 and inheritance refactoring in 6.5; it was identical. Reflection checks at 6.5 confirmed the abstract parent, protected Twig property, inherited constructors, and public basic-page actions.

Validated the property hooks through constructor and later assignments: normal public reads/writes, whitespace trimming, rejection of blank names and zero/negative/non-finite prices, and preservation of previous values after failed writes. For 6.6, checked links to all three detail pages, matching names/prices, back links, Products navigation state, malformed/missing/array-shaped/nonexistent IDs, 404 status, and single-document error responses. Checked empty-collection behavior for both actions and escaping on the detail page. Successful renders also exercise the child's explicit parent-constructor call.

Checked Markdown front matter, code fences, internal lesson links, Liquid raw guards, five exercise headings, and ten quiz questions with four choices apiece. Reviewed exercise prerequisites against the tutorials. Exercises are student implementation tasks; no exercise solution application was supplied or executed.

The repository's documented bundle exec jekyll build command could not run because there is no Gemfile. A direct Jekyll 4.3.3 build of an isolated copy generated all eleven pages and preserved all 66 Twig expressions and tags checked against the source. The copy has no default theme layout, so this verifies Markdown/Liquid rendering but not the final themed site appearance. Browser and stylesheet visual checks remain manual instructor checkpoints; application checks executed PHP with simulated GET requests and inspected the resulting HTTP response-code value.

Scratch scripts, the assembled application, and generated HTML are outside the course repository in `.class6-validation/`. The existing archived class-6 material was left untouched.

{% endraw %}
