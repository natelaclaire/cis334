---
layout: default
title: 13.1 Chapter 9 Discovery Projects — Sessions, Authentication, and CSRF
nav_order: 1
---

# 13.1 Chapter 9 Discovery Projects — Sessions, Authentication, and CSRF

In chapters 7 and 8 you designed your CRM/social schema and built PDO model classes for the main tables (users, profiles, companies, contacts, projects, activities, posts, comments, follows, reactions). Now we’ll give that code a usable interface: logging users in and out, remembering who’s logged in with PHP sessions, and protecting your forms with CSRF tokens.

These five discovery projects walk you through:

- Bootstrapping sessions and common helpers
- Implementing a secure login/logout flow
- Guarding pages so only logged-in users can access them
- Building an authenticated dashboard that uses your existing models
- Adding CSRF protection to forms that change data

---

## Discovery Project 9-1 — Session bootstrap and shared helpers

### Goal

Create a single bootstrap file that:

- Starts a PHP session
- Loads Composer autoloading
- Provides helpers for current user, redirects, and flash messages
- Sets up basic CSRF helpers (token generation and verification)

You’ll include this bootstrap file at the top of your public scripts.

### Before you start

Create a new branch for this chapter (for example, `chapter-9`) and make sure your `chapter-8` work is committed and pushed.

### Steps

1. **Create a bootstrap file**

In your project root, create `includes/bootstrap.php`:

```php
<?php
// includes/bootstrap.php

declare(strict_types=1);

session_start();

require(APP_PATH.'vendor/autoload.php');

use App\Models\User;

/**
 * Get the currently logged-in user, or null.
 */
function currentUser(): ?User
{
    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    static $cachedUser = null;

    if ($cachedUser === null || $cachedUser->id !== $_SESSION['user_id']) {
        $cachedUser = User::find((int)$_SESSION['user_id']);
    }

    return $cachedUser;
}

/**
 * Is someone logged in?
 */
function isLoggedIn(): bool
{
    return currentUser() !== null;
}

/**
 * Simple redirect helper.
 */
function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}
```

2. **Add flash message helpers**

Flash messages are one-time messages stored in the session, shown once, then cleared.

Append to `includes/bootstrap.php`:

```php
/**
 * Set a flash message.
 */
function flash(string $key, string $message): void
{
    $_SESSION['_flash'][$key] = $message;
}

/**
 * Get and clear a flash message.
 */
function getFlash(string $key): ?string
{
    if (!isset($_SESSION['_flash'][$key])) {
        return null;
    }
    $msg = $_SESSION['_flash'][$key];
    unset($_SESSION['_flash'][$key]);
    return $msg;
}
```

3. **Add CSRF helpers**

Still in `includes/bootstrap.php`, add:

```php
/**
 * Get (or create) the CSRF token for this session.
 */
function csrfToken(): string
{
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

/**
 * Echo a hidden input for the CSRF token.
 */
function csrfField(): void
{
    $token = htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8');
    echo '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Validate a CSRF token from a POST request.
 */
function requireValidCsrfToken(?string $token): void
{
    $sessionToken = $_SESSION['_csrf_token'] ?? null;

    if (!$token || !$sessionToken || !hash_equals($sessionToken, $token)) {
        http_response_code(419); // "Page expired" style error
        echo 'Invalid CSRF token. Please go back and try again.';
        exit;
    }
}
```

4. **Modifications to our existing layout**

Open `includes/main-navigation.php` and add the following between the line that assigns a value to `$currentUrl` and the `foreach` loop (near line 5):

```php
if (isLoggedIn()) {
    $config['nav'][] = [
        'title' => 'Dashboard',
        'url' => 'dashboard',
    ];
    $config['nav'][] = [
        'title' => 'Logout '.(currentUser()->displayName),
        'url' => 'logout',
    ];
} else {
    $config['nav'][] = [
        'title' => 'Login',
        'url' => 'login',
    ];
}
```

Open `public/css/styles.css` and add:

```css
.flash { padding: .75rem 1rem; margin: 0 1.5rem; border-radius: 4px; }
.flash-error { background: #fee; border: 1px solid #f99; color: #900; }
.flash-success { background: #efe; border: 1px solid #9c9; color: #060; }
```

Open `public/index.php` and add the following above the PHP code block that reads `echo $pageContent;` (near line 135):

```php
<?php if ($msg = getFlash('error')): ?>
    <div class="flash flash-error"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>
<?php if ($msg = getFlash('success')): ?>
    <div class="flash flash-success"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>
```

Still in `public/index.php`, change the line that includes the `autoload.php` file (near line 4) to instead include the new bootstrap:

```php
require APP_PATH.'includes/bootstrap.php';
```

5. **Quick smoke test**

* Start your dev environment.
* Visit `/index.php` in the browser.
* Confirm there are no errors and that the new navigation renders (you won't yet be able to see the flash area render).

1. **Commit and push**

Commit with a message like `Discovery Project 9-1` and push your branch.

---

## Discovery Project 9-2 — Login form and authentication with password hashing

### Goal

Create a secure login form that:

* Looks up a user by email
* Verifies their password using `password_verify()`
* Stores their user ID in the session
* Uses a CSRF token on the POST request
* Shows appropriate flash messages for success/failure

### Steps

1. **Add a `findByEmail()` method to the `User` model**

Open `classes/Models/User.php` and add:

```php
public static function findByEmail(string $email): ?self
{
    $sql = 'SELECT id,
                   email,
                   password_hash AS passwordHash,
                   display_name AS displayName,
                   role,
                   active,
                   created_at AS createdAt,
                   updated_at AS updatedAt,
                   deleted_at AS deletedAt
            FROM users
            WHERE email = :email
            LIMIT 1';
    $st = Database::get()->prepare($sql);
    $st->execute([':email' => $email]);
    return $st->fetchObject(self::class) ?: null;
}
```

2. **Create `login.php`**

Create `includes/login.php`:

```php
<?php

use App\Models\User;

if (isLoggedIn()) {
    flash('success', 'You are already logged in.');
    redirect(constructUrl('dashboard'));
}

// Handle POST (form submission)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireValidCsrfToken($_POST['csrf_token'] ?? null);

    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        flash('error', 'Please enter both email and password.');
        redirect('/login.php');
    }

    $user = User::findByEmail($email);

    if (!$user || !$user->isActive()) {
        // Avoid revealing which part failed
        flash('error', 'Invalid email or password.');
        redirect(constructUrl('login'));
    }

    if (!password_verify($password, $user->passwordHash)) {
        flash('error', 'Invalid email or password.');
        redirect(constructUrl('login'));
    }

    // Authentication successful
    $_SESSION['user_id'] = $user->id;
    flash('success', 'Welcome back, ' . $user->displayName . '!');
    redirect(constructUrl('dashboard'));
}

// GET request: show the login form
?>
    <h2>Login</h2>
    <form method="post" action="<?php echo constructUrl('login'); ?>" autocomplete="off">
        <?php csrfField(); ?>
        <div>
            <label for="email">Email</label><br>
            <input
                type="email"
                id="email"
                name="email"
                required
                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
            >
        </div>
        <div style="margin-top:.5rem;">
            <label for="password">Password</label><br>
            <input
                type="password"
                id="password"
                name="password"
                required
            >
        </div>
        <div style="margin-top:1rem;">
            <button type="submit">Log In</button>
        </div>
    </form>
```

3. **Add a route for login**

Open `public/index.php`, find the `switch` block that handles routing (near line 20), and add a case for `login` that includes `includes/login.php`, using the prior examples as a guide.

4. **Seed a test user**

If you don’t already have a user with a known password, create a quick CLI script `demo-create-user.php` at the project root:

```php
<?php
require __DIR__ . '/vendor/autoload.php';

use App\Models\User;

$email = 'student@example.com';
$password = 'secret123';

$user = new User();
$user->email = strtolower($email);
$user->passwordHash = password_hash($password, PASSWORD_DEFAULT);
$user->displayName = 'Student Example';
$user->role = 'staff';
$user->active = 1;

$user->insert();

echo "Created user #{$user->id} with email {$user->email} and password {$password}\n";
```

Run:

```sh
php demo-create-user.php
```

5. **Test the login flow**

* Visit `/login`.
* Try logging in with incorrect credentials (should see an error flash).
* Log in with the seeded user (should see a success flash and redirect to `/dashboard`—we’ll build that next).

6. **Commit and push**

Commit with a message like `Discovery Project 9-2` and push.

---

## Discovery Project 9-3 — Logout, route protection, and navigation

### Goal

* Implement logout (destroy the session for the current user)
* Create a reusable “require login” guard
* Protect selected pages so only authenticated users can access them
* Make navigation reflect authentication state (already partly done in 9-1)

### Steps

1. **Create a `require-login.php` helper**

Create `includes/require-login.php`:

```php
<?php
// includes/require-login.php

require __DIR__ . '/bootstrap.php';

if (!isLoggedIn()) {
    flash('error', 'Please log in to access that page.');
    redirect('/login.php');
}
```

Any script that needs protection will include this file instead of including `bootstrap.php` directly.

2. **Implement logout**

Create `public/logout.php`:

```php
<?php
// public/logout.php
require __DIR__ . '/../includes/bootstrap.php';

if (isLoggedIn()) {
    // Clear session data
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    session_destroy();
    flash('success', 'You have been logged out.');
} else {
    flash('success', 'You are already logged out.');
}

redirect('/index.php');
```

3. **Protect the dashboard (and any other sensitive pages)**

Create `public/dashboard.php`:

```php
<?php
// public/dashboard.php
require __DIR__ . '/../includes/require-login.php';
require __DIR__ . '/../includes/header.php';
?>
    <h2>Dashboard</h2>
    <p>Welcome, <?= htmlspecialchars(currentUser()->displayName) ?>.</p>

    <ul>
        <li><a href="/companies.php">View companies</a> (example)</li>
        <li><a href="/projects.php">View projects</a> (example)</li>
        <li><a href="/activities.php">View activities</a> (example)</li>
    </ul>

    <p>We’ll build out this dashboard further in the next discovery project.</p>
<?php
require __DIR__ . '/../includes/footer.php';
```

If you have existing pages that should only be accessible to logged-in users (for example, `companies.php`, `contacts.php`), change their first line from:

```php
require __DIR__ . '/../includes/bootstrap.php';
```

to:

```php
require __DIR__ . '/../includes/require-login.php';
```

4. **Verify navigation behavior**

* When logged out, the nav should only show `Home` and `Login`.
* When logged in, it should show `Dashboard` and `Logout`, plus your greeting.
* Try accessing `/dashboard.php` while logged out; confirm you’re redirected to `/login.php` with a flash message.

5. **Commit and push**

Commit with a message like `Discovery Project 9-3` and push.

---

## Discovery Project 9-4 — Auth-aware dashboard using your models

### Goal

Use your existing PDO models to build a simple dashboard that shows data specific to the currently logged-in user, such as:

* Their profile info
* Their project memberships
* Their assigned activities

This ties together authentication, sessions, and database access.

### Steps

1. **Add an `Activity::forUser()` helper**

Open `classes/Models/Activity.php` and add:

```php
/** @return Activity[] */
public static function forUser(int $userId): array
{
    $sql = 'SELECT id,
                   contact_id AS contactId,
                   user_id AS userId,
                   type,
                   subject,
                   due_at AS dueAt,
                   completed_at AS completedAt,
                   notes_md AS notesMd,
                   created_at AS createdAt
            FROM activities
            WHERE user_id = :uid
            ORDER BY COALESCE(due_at, created_at) ASC';
    $st = Database::get()->prepare($sql);
    $st->execute([':uid' => $userId]);
    return $st->fetchAll(PDO::FETCH_CLASS, self::class);
}
```

2. **Add a `ProjectMember::membershipsForUser()` helper (if not already present)**

Open `classes/Models/ProjectMember.php` and add:

```php
/** @return ProjectMember[] */
public static function membershipsForUser(int $userId): array
{
    $sql = 'SELECT project_id AS projectId,
                   user_id AS userId,
                   role,
                   added_at AS addedAt
            FROM project_members
            WHERE user_id = :uid
            ORDER BY added_at DESC';
    $st = Database::get()->prepare($sql);
    $st->execute([':uid' => $userId]);
    return $st->fetchAll(PDO::FETCH_CLASS, self::class);
}
```

3. **Enhance `dashboard.php` to use the models**

Update `public/dashboard.php`:

```php
<?php
// public/dashboard.php
require __DIR__ . '/../includes/require-login.php';

use App\Models\Profile;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Activity;

$user = currentUser();
$profile = $user?->getProfile();
$memberships = ProjectMember::membershipsForUser($user->id);
$activities  = Activity::forUser($user->id);

require __DIR__ . '/../includes/header.php';
?>
    <h2>Dashboard</h2>
    <p>Welcome, <?= htmlspecialchars($user->displayName) ?>.</p>

    <section>
        <h3>Your Profile</h3>
        <p><strong>Email:</strong> <?= htmlspecialchars($user->email) ?></p>
        <?php if ($profile): ?>
            <p><strong>Website:</strong>
                <?= $profile->websiteUrl
                    ? '<a href="' . htmlspecialchars($profile->websiteUrl) . '">' . htmlspecialchars($profile->websiteUrl) . '</a>'
                    : '—' ?>
            </p>
            <p><strong>Location:</strong> <?= htmlspecialchars($profile->location ?? '—') ?></p>
        <?php else: ?>
            <p>No profile yet.</p>
        <?php endif; ?>
    </section>

    <section>
        <h3>Your Projects</h3>
        <?php if (!$memberships): ?>
            <p>You are not a member of any projects yet.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($memberships as $m): ?>
                    <?php $project = Project::findBySlug('dummy'); // placeholder to show pattern ?>
                    <!-- In your real code, you might add a Project::find(int $id) method and use $m->projectId -->
                    <li>
                        Project #<?= (int)$m->projectId ?> —
                        Role: <?= htmlspecialchars($m->role) ?>
                        (added <?= htmlspecialchars($m->addedAt) ?>)
                    </li>
                <?php endforeach; ?>
            </ul>
            <p><em>On your own, add a proper Project::find(int $id) method to show project titles here.</em></p>
        <?php endif; ?>
    </section>

    <section>
        <h3>Your Activities</h3>
        <?php if (!$activities): ?>
            <p>You have no assigned activities.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($activities as $a): ?>
                    <li>
                        [<?= htmlspecialchars($a->type) ?>]
                        <?= htmlspecialchars($a->subject) ?>
                        <?php if ($a->dueAt): ?>
                            — due <?= htmlspecialchars($a->dueAt) ?>
                        <?php endif; ?>
                        <?php if ($a->completedAt): ?>
                            — completed <?= htmlspecialchars($a->completedAt) ?>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
<?php
require __DIR__ . '/../includes/footer.php';
```

> Note: The snippet above uses a placeholder for project titles. On your own, you can:
>
> * Add `public static function find(int $id): ?self` to `Project`, and
> * Use it inside the loop to show the project’s `title` alongside the role.

4. **Test**

* Log in as a user who has at least one `project_members` row and a couple of `activities`.
* Visit `/dashboard.php` and confirm that the data shown matches the logged-in user.
* Log out and confirm that `/dashboard.php` is no longer accessible.

5. **Commit and push**

Commit with a message like `Discovery Project 9-4` and push.

---

## Discovery Project 9-5 — CSRF protection for create/update/delete forms

### Goal

Apply CSRF protection to forms that change data in your app, using the helpers you added in 9-1. You’ll:

* Add hidden CSRF fields to key forms (login is already using it)
* Verify tokens before processing POST requests
* See what happens when the token is missing or invalid

### Steps

1. **Identify key POST forms**

Pick at least **three** forms in your project that perform state-changing actions, such as:

* Create or edit a company (`companies-create.php`, `companies-edit.php`)
* Create or edit a contact
* Create a post or comment
* Mark an activity complete
* Follow/unfollow another user

For each of these forms, make sure:

* The script includes either `bootstrap.php` or `require-login.php`
* It uses `method="post"`

2. **Add the CSRF hidden field to each form**

Inside each `<form method="post" ...>` tag, add:

```php
<?php csrfField(); ?>
```

Example:

```php
<form method="post" action="/companies-create.php">
    <?php csrfField(); ?>
    <!-- rest of your inputs -->
</form>
```

3. **Validate the CSRF token in each POST handler**

At the top of every script that processes the form, after including your bootstrap/require-login, call:

```php
requireValidCsrfToken($_POST['csrf_token'] ?? null);
```

Example: `public/companies-create.php`:

```php
<?php
require __DIR__ . '/../includes/require-login.php';

use App\Models\Company;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireValidCsrfToken($_POST['csrf_token'] ?? null);

    $name = trim($_POST['name'] ?? '');

    if ($name === '') {
        flash('error', 'Company name is required.');
        redirect('/companies-create.php');
    }

    $company = new Company();
    $company->name = $name;
    $company->websiteUrl = $_POST['website_url'] ?? null;
    $company->industry = $_POST['industry'] ?? null;
    $company->notesMd = $_POST['notes_md'] ?? null;
    $company->insert();

    flash('success', 'Company created.');
    redirect('/companies.php');
}

// GET: show form
require __DIR__ . '/../includes/header.php';
?>
    <h2>Create Company</h2>
    <form method="post">
        <?php csrfField(); ?>
        <!-- your inputs -->
    </form>
<?php
require __DIR__ . '/../includes/footer.php';
```

4. **Test CSRF behavior**

For at least one form:

* Load the form page in your browser.
* Open your browser’s developer tools.
* Manually remove the `csrf_token` field from the form using the Elements inspector or change its value.
* Submit the form.
* Confirm that you see the “Invalid CSRF token” message and that no database changes occur.

5. **Extend protection to delete actions**

If you have “delete” links that currently use GET requests like:

```php
<a href="/companies-delete.php?id=123">Delete</a>
```

Refactor them into small POST forms:

```php
<form method="post" action="/companies-delete.php" style="display:inline;">
    <?php csrfField(); ?>
    <input type="hidden" name="id" value="<?= (int)$company->id ?>">
    <button type="submit" onclick="return confirm('Delete this company?');">
        Delete
    </button>
</form>
```

Then in `companies-delete.php`:

```php
<?php
require __DIR__ . '/../includes/require-login.php';

use App\Models\Company;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo 'Method not allowed.';
    exit;
}

requireValidCsrfToken($_POST['csrf_token'] ?? null);

$id = (int)($_POST['id'] ?? 0);
$company = Company::find($id);

if (!$company) {
    flash('error', 'Company not found.');
    redirect('/companies.php');
}

$company->delete();
flash('success', 'Company deleted.');
redirect('/companies.php');
```

6. **Commit and push**

Commit with a message like `Discovery Project 9-5` and push.

---

With these five discovery projects, you now have:

* Session-aware pages that know who is logged in
* A secure login/logout flow using password hashing
* A simple dashboard that surfaces data from multiple tables for the current user
* CSRF protection for state-changing forms across your app

In your final project, you can reuse and extend these patterns to flesh out the full CRM/social experience. 
