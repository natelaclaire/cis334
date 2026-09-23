---
layout: default
title: Class 4 Content Migration Notes
nav_exclude: true
---

# Class 4 content migration notes

Destination confirmed as class-4; class-3 provides the formatting model. Existing class-3 edits and the prior move of old class-4 project materials to archive/old-class-4 are preserved.

## Chapter-to-tutorial map

Source: the supplied `PHPCrashCourse.epub`, `OEBPS/xhtml/chapter20.xhtml`. New examples cover the chapter without reproducing its listings.

| Chapter 20 progression | Tutorials |
|---|---|
| Naming collisions; namespaces; fully qualified names; use; names in other namespaces | 4.1–4.2 |
| Composer installation; root composer.json; JSON; command aliases | 4.3 |
| Composer autoloader: declare mapping, generate loader, require bootstrap | 4.4–4.5 |
| Adding third-party libraries; require; constraints; importing installed classes | 4.6–4.7 |
| Where to find libraries: Packagist | 4.6 |
| Requested extensions: App convention, init/project, SPL callbacks, expanded PSR-4 | 4.2–4.5 |
| Supporting practice: lock files, install/update, Dotenv and PHPMailer | 4.6–4.7 and 4.9 |

PSR-4 and a framework App example already appear in the chapter; the new tutorials expand them. PHP callback registration and interactive init are additional material. Strict JSON quoting and caret-range explanations correct misleading details in the textbook's examples.

## Keep and rewrite

| Previous active file | Disposition |
|---|---|
| class-4/3.4-namespaces.md | Retain topic, rewrite and renumber as 4.1; expand application layout in 4.2; preserve original in archive/class-4-legacy |
| class-4/3.5-autoloading.md | Retain topic, replace with 4.4–4.5; fix base paths, prefix removal, and incorrect default-optimization claim; preserve original in archive/class-4-legacy |
| class-4/3.5-class-3-reinforcement-exercises.md | Move original to archive/class-4-legacy; replace mixed cloning/magic-method/inheritance worksheet with five new Chapter 20 exercises |

## Material elsewhere

No complete active tutorial elsewhere needs moving into class-4. The archived `old-class-4/autoloader-example-for-dp.md` contains a global-class loader tied to the old discovery assignment; leave it archived because 4.4 replaces it with a coherent App example. The archived discovery projects, models, and mapping notes concern other assignments and remain archived. The older inheritance/interface/trait script also stays archived; it is not Chapter 20 content.

Later JWT and project pages mention Composer in their own application contexts. Keep those pages in place: they are not general Composer tutorials. Class-3's Chapter 19 scripts remain there. Its historical migration notes describe the state at that earlier revision; this inventory records the current class-4 disposition.

## New material and format

Seven tutorial scripts, a roadmap, five reinforcement exercises, and ten multiple-choice questions follow class-3's YAML front matter, numbered files, demonstrations, expected results, and instructor explanations. Assessment numbers 4.9 and 4.11 preserve the course convention; no new discovery project is implied. Week 4 navigation points to the new materials.

The student project is cumulative and lives in the student's repository root. Course notes do not install dependencies into the teaching-materials repository. All required files are supplied in the prose or assigned explicitly; the exercises rely only on completing these tutorials.

## Validation

Validated all complete PHP snippets with PHP 8.4.25. Ran the explicit-loading, namespace-alias, SPL-loader, and Composer-loader demonstrations and confirmed their stated output. The complete sample composer.json passes Composer validation. An isolated installation resolved Symfony Dotenv 7.4.18 and PHPMailer 6.12.0 within the illustrative constraints; the package demonstration successfully prepared the message with the documented subject and body without sending it. The Composer demo alias also ran successfully.

Checked front matter, balanced fences, JSON examples, relative links, five exercise headings, and ten questions with four choices each. Jekyll generated the affected HTML, including tables and code blocks. `bundle exec jekyll build` is unavailable because the repository has no Gemfile; direct Jekyll reports the missing local default layout. Final themed appearance is therefore unverified. Validation scratch files are outside the course repository in `.class4-validation/`.
