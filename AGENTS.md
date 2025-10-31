# Repository Guidelines

## Project Structure & Module Organization
- Course content lives in numbered folders such as `class-3/` or `class-9/`, with each lesson stored as `N.M-topic.md` and a matching `class-N-reinforcement-exercises.md` when practice material is available.
- Root-level pages (`index.md`, `syllabus.md`, `topics-by-week.md`, `final-project.md`) define site navigation; keep their YAML front matter (`layout`, `title`, `nav_order`) aligned with the sidebar flow.
- Shared assets reside in `images/`; reuse existing filenames when possible and prefer `kebab-case` for any new media.

## Build, Test, and Development Commands
- `bundle exec jekyll serve --livereload` — start the local site preview at http://127.0.0.1:4000 while auto-reloading edits.
- `bundle exec jekyll build` — generate the static site output in `_site/` to spot broken links or front-matter issues.
- `find class-4/app -name '*.php' -print0 | xargs -0 php -l` — lint the PHP examples before publishing them in lesson notes.
- `bundle exec jekyll doctor` — quick health check for configuration or Liquid problems before pushing.

## Coding Style & Naming Conventions
- Markdown pages begin with YAML front matter; use sentence-case headings (`#`, `##`, `###`) and ordered callouts for step-by-step instructions.
- Number new lesson files with the `class-N/N.M-topic.md` pattern to keep modules sortable.
- PHP snippets follow strict types, PSR-12 indentation (4 spaces), and camelCase method/property names; import classes with `use` statements rather than fully-qualified strings in code.
- Sample SQL or CLI excerpts should be fenced with ```sql``` / ```bash``` so syntax highlighting works in the rendered theme.

## Testing Guidelines
- Before submitting content, run `bundle exec jekyll build` or `serve` and skim the affected pages for navigation, formatting, and internal link correctness.
- Validate PHP examples with `php -l` and, when code interacts with provided SQL dumps, describe the expected schema in the lesson text.
- Note any manual verification (screenshots, DB outputs) in the pull request if automated coverage is not practical.

## Commit & Pull Request Guidelines
- Match the existing history: short, imperative summaries (e.g., “Update 9.1 walkthrough”) and group related lesson adjustments in a single commit.
- Reference the impacted lesson in the body (e.g., `class-7` exercises, `final-project.md`) and mention tooling commands run.
- Pull requests should include a concise overview, affected files list, preview screenshots for visual changes, and any blockers or follow-up tasks for the next instructor.
