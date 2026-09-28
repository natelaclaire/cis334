---
layout: default
title: Class 5 instructor notes
nav_exclude: true
---

# Class 5 instructor notes

## Recording and review

Teach the seven tutorials in roadmap order. Each intermediate stage is runnable. Pause at the stated checkpoint before replacing code for the next stage. Paths in the lesson text belong to student repositories; this folder contains teaching scripts, not an installed application.

Keep the existing Composer mapping and Twig dependency. The tutorial and exercise Application classes have separate namespaces and template directories. Neither should require another week's class files. The overview maps each tutorial to chapter 21; no archived lesson is reused.

## Common troubleshooting

- Class not found: check the Composer bootstrap, App mapping, namespace, filename, and case.
- Template not found: check the Application constant and the filename relative to the loader directory.
- Variable missing: compare the context key with the Twig name; strict variables are intentionally enabled.
- Duplicate HTML or blank main content: remove the former header include and check the child's block name.
- Wrong destination on navigation: use the documented relative index.php links rather than root-level links from the textbook.
- Missing styling: check the local stylesheet URL or CDN access. PHP/Twig rendering can work even if the CSS request fails.

## Validation

Checked with PHP 8.4.25 and an existing local Twig 3.11.3 installation in an isolated validation harness; no student Composer files or course dependencies were changed. All full PHP examples and method fragments passed syntax checks. Executed every cumulative tutorial stage and checked greeting output, HTML escaping, object getters/string conversion, array access, number formatting, populated and empty loops, both routes, unknown/non-string action fallbacks, includes, inheritance, repeated titles, and active-link classes for both CSS stages.

Checked front matter, balanced code fences, internal lesson links, five exercise headings, and ten quiz questions with four choices each. Reviewed exercise requirements against the techniques taught; the exercises are student implementation tasks, not supplied or executed answer-key applications.

The documented `bundle exec jekyll build` cannot run because this repository has no Gemfile. A direct Jekyll 4.3.3 build of an isolated copy generated all 11 new pages, and the generated HTML preserved Twig expressions and tags through Liquid processing. The lesson bodies use Liquid raw guards because code fences alone do not protect Twig syntax. The local copy has no default theme layout, so final themed appearance and browser/CSS visual checks remain unverified. CDN styling requires internet access.

Scratch scripts, the assembled tutorial application, and generated lesson HTML live outside the course repository in `.class5-validation/`.
