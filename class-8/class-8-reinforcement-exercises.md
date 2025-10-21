---
layout: default
title: 8.5 Class 8 Reinforcement Exercises
nav_order: 5
---

# 8.5 Class 8 Reinforcement Exercises

## Exercise 8-1: Guestbook

In this exercise, you will create a Web page that allows visitors to your site to sign a guest book that is saved to a database.

1. Create a new document called `8-1-guestbook.php` in the `public/exercises` folder.
2. Add the following to the top of the document to set up the page:

```php
<?php
declare(strict_types=1);
session_start(); // initialize session for CSRF token, we'll discuss how sessions work in the future

/* ─────────────────────────────
   1) Configuration
   ───────────────────────────── */
const DB_HOST = 'db';
const DB_USER = 'mariadb';
const DB_PASS = 'mariadb';
const DB_NAME = 'mydb';
const TABLE   = 'visitors';    // table will be auto-created
```

3. Next, add utility functions for HTML escaping, CSRF protection, and name sanitization:

```php
/* ─────────────────────────────
   2) Utilities
   ───────────────────────────── */
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // throw exceptions

function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}
function csrf_ok(?string $t): bool {
    return $t !== null && isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $t);
}

function sanitize_name(string $s): string {
    $s = trim(preg_replace('/\s+/', ' ', $s));
    // Allow letters from any language, spaces, hyphens, apostrophes. Max 40 chars.
    if (!preg_match("/^[\p{L} '-]{1,40}$/u", $s)) {
        throw new InvalidArgumentException('Names may only contain letters, spaces,'.
            ' hyphens, and apostrophes (max 40 chars).');
    }
    return $s;
}
```

4. Now, add a function to connect to the database and ensure the necessary table exists:

```php
/* ─────────────────────────────
   3) Ensure database & table (first run friendly)
   This is not best practice for production code - use "migrations" (PHP code that is run 
   once to set up the database schema) or a separate SQL script instead!
   ───────────────────────────── */
function db(): mysqli {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset('utf8mb4');

    // Create table if missing
    $conn->query("
        CREATE TABLE IF NOT EXISTS `".TABLE."` (
          `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `first_name` VARCHAR(40) NOT NULL,
          `last_name`  VARCHAR(40) NOT NULL,
          `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB
        DEFAULT CHARSET=utf8mb4
        COLLATE=utf8mb4_unicode_ci
    ");

    return $conn;
}
```

5. Finally, implement the routing logic to handle form display, submission, and listing recent entries:

```php
/* ─────────────────────────────
   4) Handle routes (single file)
   - GET  (default): show form
   - POST: process submission
   - GET  ?list=1: show latest entries (optional)
   ───────────────────────────── */
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$isList = isset($_GET['list']) && $_GET['list'] === '1';

try {
    if ($method === 'POST') {
        // CSRF
        if (!csrf_ok($_POST['csrf'] ?? null)) {
            http_response_code(422);
            throw new RuntimeException('Invalid or missing form token. Please reload the form'
                .' and try again.');
        }

        // Validate names
        $first = sanitize_name((string)($_POST['first_name'] ?? ''));
        $last  = sanitize_name((string)($_POST['last_name'] ?? ''));

        if ($first === '' || $last === '') {
            http_response_code(422);
            throw new InvalidArgumentException('Please provide both first and last name.');
        }

        // Insert with MySQLi OOP prepared statement
        $conn = db();
        $stmt = $conn->prepare("INSERT INTO `".TABLE."` (first_name, last_name) VALUES (?, ?)");
        $stmt->bind_param('ss', $first, $last);
        $stmt->execute();
        $stmt->close();
        $conn->close();

        // Success page (303 pattern prevents form resubmission on refresh)
        header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?') . '?success=1', true, 303);
        exit;
    }

    // List page
    if ($isList) {
        $conn = db();
        $res = $conn->query("SELECT first_name, last_name, created_at FROM `".TABLE."` ORDER BY created_at
             DESC LIMIT 25");
        $rows = $res->fetch_all(MYSQLI_ASSOC);
        $res->free();
        $conn->close();
        ?>
        <!doctype html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <title>Recent Signatures</title>
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <style>
                body{font:16px system-ui,-apple-system,Segoe UI,Roboto,sans-serif;max-width:42rem;
                margin:2rem auto;padding:0 1rem}
                li{margin:.35rem 0}
                a.button{display:inline-block;margin-top:1rem;padding:.6rem .9rem;border-radius:.4rem;
                background:#eee;text-decoration:none}
            </style>
        </head>
        <body>
            <h1>Recent Signatures</h1>
            <ol>
                <?php foreach ($rows as $r): ?>
                    <li><?= e($r['first_name'].' '.$r['last_name']) ?>
                        <small>(<?= e($r['created_at']) ?>)</small></li>
                <?php endforeach; ?>
            </ol>
            <p><a class="button" href="<?= e(strtok($_SERVER['REQUEST_URI'], '?')) ?>">Back to form</a></p>
        </body>
        </html>
        <?php
        exit;
    }

    // Default: show form (and success message if redirected)
    $success = isset($_GET['success']);
    $token = e(csrf_token());
    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Guest Book</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            body{font:16px system-ui,-apple-system,Segoe UI,Roboto,sans-serif;max-width:42rem;
            margin:2rem auto;padding:0 1rem}
            form{display:grid;gap:1rem}
            label{display:grid;gap:.5rem}
            input[type="text"]{padding:.6rem .7rem;border:1px solid #ccc;border-radius:.4rem}
            button{padding:.6rem .9rem;border:0;border-radius:.4rem;cursor:pointer;background:#222;color:#fff}
            .card{background:#f8f8f8;border:1px solid #eee;border-radius:.6rem;padding:1rem;margin:.75rem 0}
            .success{background:#eefbea;border-color:#bfe6b9}
            .error{background:#fff0f0;border-color:#ffd3d3}
            nav a{margin-right:.8rem}
        </style>
    </head>
    <body>
        <h1>Guest Book</h1>
        <nav>
            <a href="<?= e(strtok($_SERVER['REQUEST_URI'], '?')) ?>">Sign</a>
            <a href="<?= e(strtok($_SERVER['REQUEST_URI'], '?')) ?>?list=1">Recent signatures</a>
        </nav>

        <?php if ($success): ?>
            <div class="card success">
                <strong>Thank you!</strong> Your name has been added.
            </div>
        <?php endif; ?>

        <h2>Enter your name to sign our guest book</h2>

        <form method="post" action="<?= e(strtok($_SERVER['REQUEST_URI'], '?')) ?>" novalidate>
            <label>
                First Name
                <input type="text"
                       name="first_name"
                       autocomplete="given-name"
                       minlength="1"
                       maxlength="40"
                       required
                       pattern="[\p{L} '\-]{1,40}">
            </label>

            <label>
                Last Name
                <input type="text"
                       name="last_name"
                       autocomplete="family-name"
                       minlength="1"
                       maxlength="40"
                       required
                       pattern="[\p{L} '\-]{1,40}">
            </label>

            <input type="hidden" name="csrf" value="<?= $token ?>">

            <button type="submit">Submit</button>
        </form>

        <p class="card">We store only your name and the time you signed.</p>
    </body>
    </html>
    <?php
    exit;

} catch (Throwable $e) {
    http_response_code(400);
    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Submission Error</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            body{font:16px system-ui;max-width:42rem;margin:2rem auto;padding:0 1rem}
            .card{background:#fff0f0;border:1px solid #ffd3d3;border-radius:.6rem;padding:1rem}
            a.button{display:inline-block;margin-top:1rem;padding:.6rem .9rem;
            border-radius:.4rem;background:#eee;text-decoration:none}
        </style>
    </head>
    <body>
        <h1>We couldn’t process your request</h1>
        <div class="card"><?= e($e->getMessage()) ?></div>
        <p><a class="button" href="<?= e(strtok($_SERVER['REQUEST_URI'], '?')) ?>">Back to form</a></p>
    </body>
    </html>
    <?php
    exit;
}
```

6. Save the file and test it by accessing it through your web server. You should be able to sign the guest book and view recent entries.
7. Remember to run `./dump-mysql.sh`, and commit and sync your changes.

## Exercise 8-2: Guestbook List

Enhance the guestbook application you created in Exercise 8-1 by adding pagination to the list of recent entries. Modify the listing page to show 10 entries per page and include navigation links to move between pages (e.g., "Previous" and "Next" buttons). Ensure that the pagination works correctly and that users can navigate through all entries in the guestbook.

1. Open the `8-1-guestbook.php` file you created in Exercise 8-1.
2. Save the file as `8-2-guestbook-pagination.php` in the same folder.
3. Below the comment that reads `4) Handle routes (single file)`, add a variable to define the number of entries per page, another that calculates the current page based on a `page` query parameter, and an offset for the SQL query:

```php
$entriesPerPage = 10;
$currentPage = max(1, (int)($_GET['page'] ?? 1));
$offset = ($currentPage - 1) * $entriesPerPage;
```

4. Near line 109, replace the `$res = $conn->query("SELECT first_name, last_name, created_at FROM ".TABLE." ORDER BY created_at DESC LIMIT 25");`  line with a prepared statement that uses `LIMIT` and `OFFSET`:

```php
$stmt = $conn->prepare("SELECT first_name, last_name, created_at FROM ".TABLE." ORDER BY 
    created_at DESC LIMIT ? OFFSET ?");
$stmt->bind_param('ii', $entriesPerPage, $offset);
$stmt->execute();
$res = $stmt->get_result();
```

5. After fetching the rows and calling `$res->free();` (near line 116), but before the line that reads `$conn->close();` (near line 117), add a query to count the total number of entries to calculate the total number of pages:

```php
$totalRes = $conn->query("SELECT COUNT(*) AS total FROM ".TABLE);
$totalRow = $totalRes->fetch_assoc();
$totalPages = ceil($totalRow['total'] / $entriesPerPage);
$totalRes->free();
```

6. Below the list of entries in the HTML, add navigation links for pagination:

```php
<nav>
    <ul class="pagination">
        <?php if ($currentPage > 1): ?>
            <li><a href="?list=1&page=<?= $currentPage - 1 ?>">Previous</a></li>
        <?php endif; ?>
        <li>Page <?= $currentPage ?> of <?= $totalPages ?></li>
        <?php if ($currentPage < $totalPages): ?>
            <li><a href="?list=1&page=<?= $currentPage + 1 ?>">Next</a></li>
        <?php endif; ?>
    </ul>
</nav>
```

7. Add some basic CSS styles for the pagination links in the `<style>` section of the Recent Signatures page:

```css
.pagination{list-style:none;padding:0;display:flex;gap:1rem}
.pagination li{display:inline}
.pagination a{text-decoration:none;color:#007BFF}
.pagination a:hover{text-decoration:underline}
```

8. Save your changes and test the pagination by accessing the new `8-2-guestbook-pagination.php` file through your web server. You should be able to navigate through the pages of guestbook entries, but in order to confirm that you will need to add more than 10 entries to the guestbook.
9. Remember to run `./dump-mysql.sh`, and commit and sync your changes.

## Exercise 8-3: Software Bug Tracker

Create a Web app to be used for storing software development bug reports in a MySQL database, using MySQLi OOP. Include fields such as product name and version, type of hardware, operating system, frequency of occurrence, and proposed solutions. Include links on the main page that allow you to create a new bug report and update an existing bug report. Use prepared statements for all database interactions to ensure security. Place the app in a new folder called `8-3-bug-tracker` in the `public/exercises` folder. It should be composed of at least an `index.php` but can also include other files as needed. Ensure that you run `./dump-mysql.sh` after creating your tables and then commit and sync your changes.

## Exercise 8-4: Software Bug Progress Notes

Add a progress notes feature to the bug tracker app from Exercise 8-3, allowing users to add notes to each bug report, such as status updates, research findings, and additional comments. The progress notes should include fields for the date, author, and content of the note. Display the progress notes on a bug report details page - only the notes for a single bug report should be shown at one time - and allow users to add new notes using a form on the same page. Use prepared statements for all database interactions to ensure security. The changes can be added to the existing `public/exercises/8-3-bug-tracker` folder. Ensure that you run `./dump-mysql.sh` after creating your tables and then commit and sync your changes.

## Exercise 8-5: Product Inventory

Create a Web app for managing a product inventory for a small business. The main page should include a form with fields for product name, description, quantity in stock, price, and supplier information. When the Submit button is clicked, the data should be saved in a MySQL database, using MySQLi OOP. Include a link to a document that displays the current inventory list, showing all products and their details. There is no need to provide the ability to edit records, but include an "Increase Stock" link for each product in the inventory list that opens a form that asks only for the quantity to add and then uses a prepared statement to increase the quantity in stock by the amount entered for that one product (using a structure similar to what we saw in the bank account example, `balance = balance + ?` or, in this case, something like `quantity_in_stock = quantity_in_stock + ?`). Use prepared statements for all database interactions to ensure security. Place the app in a new folder called `8-5-product-inventory` in the `public/exercises` folder. It should be composed of at least an `index.php` but can also include other files as needed. Ensure that you run `./dump-mysql.sh` after creating your tables and then commit and sync your changes.
