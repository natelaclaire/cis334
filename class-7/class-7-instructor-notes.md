---
layout: default
title: Class 7 instructor notes
nav_exclude: true
---

{% raw %}

# Class 7 instructor notes

## Recording and review

Teach the [eight tutorials](7.0-class-7-overview.md) in order using PHP 8.4 or newer. Their suggested lengths total about 83–101 minutes. Use a fresh set of class7 files in the existing student repository; no archived scripts or files from earlier weeks are prerequisites to copy. The notes folder contains teaching material, not an installed student application. Complete replacement listings include the whole file.

Keep Composer's existing App PSR-4 mapping and pre-installed Twig. For a fresh clone lacking vendor/, restore locked dependencies with composer install. Check both CLI and web runtimes. The first four tutorials use public/tutorials/class7/index.php as a CLI trace. Tutorial 7.5 replaces it with the web boundary. Tutorial 7.6 keeps that boundary and replaces Application and DessertController, adding NoticeProvider and the planner template. Tutorial 7.7 temporarily uses the entry for the CLI cleanup demo, then restores the complete boundary from 7.5. The final web application combines that boundary with the 7.6 files. The preview template can remain unused and StreamDemo remains a separate helper.

The five exercises form a separate cumulative Campus Supply Quote application under App\Exercises\Class7. The quiz has ten single-answer questions and a teaching key. Remove the key from the student LMS version; one point per question gives ten points.

## Main teaching thread

Ask three questions at every catch: **What failed? What can this layer do about it? What happens next?** Require a concrete answer to the last question: correction response, safe fallback, propagation, or termination. A catch prevents that particular failure from propagating further unless it throws again; it does not retry the skipped statements or restore state.

Begin with a small object example so the exception mechanics are visible. Introduce the familiar Application/controller/Twig arrangement when discussing where handling belongs. Keep object validation independent of browser response policy. Normalize ordinary request input before invoking typed model methods; domain validation still protects objects created by callers other than the form.

## Recording checkpoints

- In 7.1, show valid construction and invalid construction separately; the invalid CLI run intentionally exits with a failure status. In 7.2, contrast this with a rejected setter on an existing valid object. Validate before assignment so the existing property stays unchanged. Do not imply that exceptions generally roll back changes.
- In 7.2, mark a statement immediately after the failing call inside try and another after catch. Ask students to predict which runs. Display a controlled developer diagnostic only in the local demonstration; the final public-facing application uses approved correction text and generic unexpected-error text.
- In 7.3, show a superclass catch stealing a subclass's response when ordered first. Explain that catching TypeError for demonstration does not make the bad call correct.
- In 7.4, choose responses by class. Message wording is diagnostic detail, not a reliable condition to branch on.
- In 7.5, trace a known exception handled locally and another that reaches index.php. The boundary surrounds construction as well as run. Keep rendering inside the protected operation and emit output only when a complete response is available, preventing partial success HTML followed by an error page. A rendering failure needs an independent fallback that does not call the failing Twig template again.
- In 7.6, distinguish three outcomes visibly: no result plus actionable correction, usable result plus optional-feature fallback, and no result plus generic request failure. Demonstrate array-shaped input as well as malformed numeric text. Preserve entered values through Twig's escaping. Make the absence of diagnostic logging in this week's example explicit.
- In 7.7, acquire a temporary stream, close it in finally, and check release on both paths. Check resource acquisition before entering the cleanup block; a failed open can produce a warning/false rather than automatically throwing. Use a fixed in-memory stream, not uploads or writable server files. Keep exit out of the protected demonstration, and avoid claiming finally runs after process termination or every engine failure.
- In 7.8, ask students to defend the catch location and response policy, not simply recognize syntax. Discuss a bounded retry as a policy choice; do not add external-service code or retry an unchanged bad input.

## Response policy to emphasize

The 7.5 controller deliberately handles only negative calories. An excessive value therefore reaches the boundary and produces 500 in that intermediate stage. In 7.6, it becomes a known input correction with 422. Make this change explicit; the purpose of the intermediate gap is to demonstrate propagation, not endorse treating a known input failure as an unexpected server defect.

The final controller uses conditionals for ordinary field shape and syntax, then catches the two domain exceptions around model construction only. The model's allowed range is 0–5000 inclusive. filter_var must be compared to false strictly so zero is not misclassified. The integer format is intentionally narrow: no leading plus, decimal fraction, exponent notation, or leading zeros except zero. Trim whitespace for validation while retaining original scalar values in the form. The 80-byte name rule is a request rule; it is not a Unicode character count or an added invariant in Food.

The notice helper catches only JsonException from decoding, then checks the decoded structure with conditionals. Its labeled default is a safe substitute because the notice is optional. Never generalize this to manufacturing essential model data. A successful fallback means the main task can still complete; a correction means it did not complete and the user must try changed input in a new request.

The Response holder carries a complete body and status so headers are sent before output. Controllers render into strings instead of echoing. Keep the final error fallback independent of Twig and finish its branch with return. It controls an unexpected thrown failure; it does not fix the defect or record diagnostic evidence. Public display_errors should be disabled in a deployed environment; catching Throwable is not a mechanism for suppressing ordinary warnings.

## Exercise assessment guidance

Assess the cumulative application and the written explanations, not only the presence of try/catch syntax. No full exercise solution is supplied to students; the tutorial patterns are sufficient to implement each task.

| Exercise | Evidence to look for | Common incomplete answer |
|---|---|---|
| 7-1 | Constructor uses setters; names trimmed; negative/non-finite/over-limit prices rejected before assignment; zero and maximum accepted; old values survive rejected updates | Catching the failure but assigning first, or claiming every previous change rolled back |
| 7-2 | Custom types inherit InvalidArgumentException; specific catches precede parent; correction selected by type; completion marker only on success | Parsing message text or placing the parent catch first |
| 7-3 | Scalar shape/format checks; retained escaped input; narrow model catch; complete 200/422 responses; independent final 500 boundary | A broad controller catch labels every rendering or programming failure as bad input |
| 7-4 | Malformed and wrongly shaped JSON use a labeled default; a valid quote remains usable; rejected Product still has no success result | Replacing an essential failed price with zero or retrying malformed content unchanged |
| 7-5 | Temporary stream closes before return delivery and caller catch; no return from finally; web entry restored; reasoned policy table | Success in finally, missing cleanup on failure, or unbounded retry |

The expected policy table handles negative price with correction, optional notice failure with a default, and an unhelpful routing layer with propagation. A wrong typed call needs fixing; the boundary can control its failure response while the defect is investigated. Uncertain side effects make an automatic repeat unsafe. A defensible retry requires a temporary cause, a safe repeated operation, and a strict limit.

For exercise prices, float holds the supplied demonstration value; no monetary calculations or purchase persistence are introduced. The required is_finite check handles direct NAN/INF inputs and huge strings converted after syntax validation. EmptyNameException and ExcessivePriceException are caught before InvalidArgumentException, which still covers negative and non-finite values.

Ask students to show actual status codes rather than infer them from text. Inspect the accepted boundaries and rejected updates as well as the visible form. The policy explanations should recognize that a generic 500 response is not a private diagnostic record.

## Common troubleshooting

- Class not found: check namespace, filename case, existing App mapping, and the three-level entry autoloader path.
- Template not found: check the three-level template path and current filename. Restore planner.html.twig after the fault demonstration.
- Page shows only generic 500: reproduce locally and inspect the defect using developer tools or a temporary controlled diagnostic. Do not expose getMessage or a trace in the final page. Detailed logging arrives next week.
- Zero rejected: use `=== false` for the filter result rather than a truthiness check.
- Unexpected TypeError from form data: validate string shape before trimming and converting; arrays are valid shapes for malicious or constructed requests.
- Strict-variable Twig error: supply all expected keys, including null dessert and empty errors, on every path.
- Headers already sent or two documents: remove echo from controllers and prepare the whole Response before sending it. Keep the fallback independent.
- Excessive demo shows 500 at the end: finish the 7.6 controller replacement; final known range failures should return 422.
- Cleanup trace appears in browser: restore the full 7.5 boundary after the temporary 7.7 entry.
- Invalid price exercise silently succeeds: keep non-finite and range checks in Product as well as request-format checks in the controller.

## Textbook corrections and adaptations

Listing 23-12 has `catch (NegativeCaloriesException)` but subsequently reads `$e->getMessage()`. Bind the exception as `catch (NegativeCaloriesException $e)` if accessing its object. Omitting the variable is legal in current PHP only when the body does not need it.

Listing 23-6 omits the Food and Dessert imports shown earlier. Complete replacement listings must include all necessary imports. Use App\Tutorials\Class7 or App\Exercises\Class7 consistently and import PHP's built-in exception classes with use statements.

The book's three consecutive calorie assignments test only the final value unless the other lines are commented out. Prefer separate, clearly labeled scenarios and include zero, the permitted maximum, and values just outside the range.

Clarify that PHP uses a **backslash** for namespace qualification. The book calls it a forward slash in one explanation.

The book's Exception boundary catches Exception instances and subclasses. PHP also has a separate Error hierarchy; both implement Throwable. A last-resort Throwable boundary can produce a controlled request failure for thrown objects from either hierarchy, but it cannot guarantee recovery from every PHP failure. Ordinary warnings are not automatically caught, and a parse error in the entry file prevents that file's handler from executing. Do not teach Exception as a universal catcher or Error as proof that all such failures are inherently unrecoverable. See the official [Throwable reference](https://www.php.net/manual/en/class.throwable.php).

Finally runs during ordinary completion and exception unwinding and before a pending return. It provides cleanup, not an assurance that the operation succeeded. Do not put a return in finally or deliberately throw there; either can obscure the intended result. See PHP's [exception control-flow documentation](https://www.php.net/manual/en/language.exceptions.php).

Use a generic Exception briefly to match the chapter, then refine meaningful failures into specific types. Do not imply that every application needs a custom exception for every validation rule. Avoid an empty catch, a catch in every layer, or a generic catch that fabricates a successful result.

## Scope and next week's bridge

Keep Composer and Twig as already configured. No database operations, PDO examples, logging configuration, framework router, or external network dependency is needed. Use the class 14 notes as selective reference material, not scripts to copy wholesale.

Explain why private diagnostic evidence matters once a public error response becomes generic. Mark the place where next week's logging will record an unexpected failure before the response is returned. This is a future integration point, not an implemented logger or a recommendation to silently swallow production failures.

## Validation

All 21 complete PHP listings, including the quiz trace, passed php -l with PHP 8.4.25. Assembled the cumulative tutorial stages from their Markdown listings in an isolated harness using an existing local Twig 3.11.3 installation. No student Composer files or installed dependencies were modified. Scratch generation, assembled applications, and checks are in `.class7-validation/` outside the course repository.

Checked successful and deliberately uncaught construction, skipped statements, rejected updates preserving existing values, zero and exact maximum boundaries, specific-first versus general-first catches, inherited exception ancestry, TypeError's separate hierarchy, and response selection remaining unchanged when diagnostic wording changes. The cleanup demonstration closed its resource before both return delivery and the caller's catch; restoring the web entry preserved final route behavior.

Used PHP's built-in server and actual HTTP requests to check the intermediate and final route policies, content type, one-document responses, GET and POST behavior, and 200/422/500 statuses. Covered missing, unknown, and array-shaped actions; missing and array-shaped fields; whitespace and name byte limits; zero, valid, negative, excessive, decimal, leading-zero, exponent, plus-sign, huge, and malformed integer input; preserved scalar values; and HTML escaping in form and result. Injected unexpected RuntimeException and TypeError at the boundary and a missing Twig template locally; each produced the independent generic 500 response without success content or exception details. Valid, malformed, missing-message, wrong-type, null, and blank-message JSON cases exercised the optional fallback policy, including retention of 422 for invalid desserts when the notice falls back.

Checked front matter, code fences, local links, raw guards, five exercise headings, and ten questions with four choices each. Reviewed exercise requirements against the supplied tutorial patterns. Exercises are student implementation tasks; a complete exercise solution was not supplied or executed.

The required `bundle exec jekyll build` could not run because this repository has no Gemfile or .bundle directory. A direct Jekyll 4.3.3 build of an isolated copy generated all twelve pages. Exact comparisons confirmed all 34 fenced code blocks were preserved, including all 35 Twig expressions and tags. The isolated build omits the remote theme and default layout, so it verifies Markdown/Liquid rendering but not final themed appearance. Browser visual review of the themed course site and final application remains a manual instructor checkpoint; application response behavior was verified through HTTP requests.

{% endraw %}
