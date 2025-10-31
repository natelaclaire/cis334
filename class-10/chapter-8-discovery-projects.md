---
layout: default
title: 10.1 Chapter 8 Discovery Projects — PDO Models
nav_order: 1
---

# 10.1 Chapter 8 Discovery Projects — PDO Models

In chapter 7 you modeled the database tables (companies, contacts, projects, users, profiles, project_members, activities, posts, comments, follows, reactions). Now we will build PHP classes to represent those tables and add class methods that fetch rows via PDO using fetchObject() or FETCH_CLASS. We'll use static methods for fetching and instance methods for inserting and updating. We’ll also include a small integration script to insert, update, delete, and select across several models.

Notes:

- We'll use one shared PDO connection via a small `Database` class.
- We'll use camelCase properties in PHP and alias snake_case columns in SQL as needed.
- We could use [strict typing](https://inspector.dev/why-use-declarestrict_types1-in-php-fast-tips/) for these files, but since we haven't for the earlier discovery projects, we'll keep things consistent. I encourage you to read about strict types and consider enforcing the feature in your own projects, though.

## Discovery Project 8-1 — Shared PDO connection and folder setup

### Goal

Create a single shared PDO connection that all models will reuse.

### Steps

1. In your discovery projects repo, create `classes/Database.php`.
2. Add the following code:

```php
<?php
namespace App;

use PDO;

final class Database
{
    private static ?PDO $pdo = null;

    public static function get(): PDO
    {
        if (!self::$pdo) {
            $dsn  = 'mysql:host=db;dbname=mydb;charset=utf8mb4';
            $user = 'mariadb';
            $pass = 'mariadb';
            self::$pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
        return self::$pdo;
    }
}
```

3. Quick [smoke test](https://en.wikipedia.org/wiki/Smoke_testing_(software)): create `public/pdo-test.php` to `require __DIR__.'/../vendor/autoload.php';` and call `App\Database::get();`.
4. Assuming you don't see any errors, commit your changes with a message like "Discovery Project 8-1", push to your repository, and then you're ready to move on to create your model classes.

## Discovery Project 8-2 — Company, Contact, and Project models (CRUD + fetch)

### Goal

Model `companies`, `contacts`, and `projects` with camelCase properties, static fetchers, and instance `insert()`, `update()`, `delete()` methods. Use `fetchObject()` or `FETCH_CLASS` when selecting.

### Files

Create `classes/Models/Company.php`, `classes/Models/Contact.php`, and `classes/Models/Project.php`.

Notes:

- ID isn't nullable in the database, but we make it nullable in the class to represent new instances that haven't been inserted yet.
- For the `delete()` and `update()` methods, we check if `id` is `null` before attempting to proceed, to avoid errors.
- For brevity, we won't include all possible fetchers (e.g., by industry, by status); just the basics to get you started.
- In a real project, you might want to add error handling, logging, or validation, but we'll keep things simple, at least for now.
- In a real project, I would use detailed [PHPDoc](https://phpdoc.org/) comments to help IDEs and developers understand return types, parameters, etc., but for brevity, I've only included a few basic examples for the static methods that return arrays of objects. Note that PHP doesn't natively support [typed arrays](https://backendtea.com/post/php-typed-arrays/) yet, so the PHPDoc comments help clarify what type of objects will be in the returned arrays (see [this article for more about the ways you can denote an array of objects in PHPDoc](https://www.uptimia.com/questions/how-to-type-hint-an-array-of-objects-in-phpdoc)).

```php
<?php
namespace App\Models;

use App\Database;
use PDO;

final class Company
{
    public ?int $id = null;
    public string $name;
    public ?string $websiteUrl = null;
    public ?string $industry = null;
    public ?string $notesMd = null;
    public string $createdAt;
    public string $updatedAt;

    /** @return Company[] */
    public static function all(): array
    {
        $sql = 'SELECT
                    id,
                    name,
                    website_url AS websiteUrl,
                    industry,
                    notes_md AS notesMd,
                    created_at AS createdAt,
                    updated_at AS updatedAt
                FROM companies
                ORDER BY name';
        $st = Database::get()->query($sql);
        return $st->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function find(int $id): ?self
    {
        $sql = 'SELECT id, name, website_url AS websiteUrl, industry, notes_md AS notesMd, created_at AS createdAt, updated_at AS updatedAt
                FROM companies WHERE id = :id';
        $st = Database::get()->prepare($sql);
        $st->execute([':id' => $id]);
        return $st->fetchObject(self::class) ?: null;
    }

    public function insert(): void
    {
        $sql = 'INSERT INTO companies (name, website_url, industry, notes_md) VALUES (:name, :website_url, :industry, :notes_md)';
        $st = Database::get()->prepare($sql);
        $st->execute([
            ':name'        => $this->name,
            ':website_url' => $this->websiteUrl,
            ':industry'    => $this->industry,
            ':notes_md'    => $this->notesMd,
        ]);
        $this->id = (int)Database::get()->lastInsertId();
    }

    public function update(): void
    {
        if ($this->id === null) {
            throw new \LogicException('Cannot update without id');
        }
        $sql = 'UPDATE companies SET name = :name, website_url = :website_url, industry = :industry, notes_md = :notes_md WHERE id = :id';
        $st = Database::get()->prepare($sql);
        $st->execute([
            ':name'        => $this->name,
            ':website_url' => $this->websiteUrl,
            ':industry'    => $this->industry,
            ':notes_md'    => $this->notesMd,
            ':id'          => $this->id,
        ]);
    }

    public function delete(): void
    {
        if ($this->id === null) {
            return;
        }
        $st = Database::get()->prepare('DELETE FROM companies WHERE id = :id');
        $st->execute([':id' => $this->id]);
    }
}
```

```php
<?php
namespace App\Models;

use App\Database;
use PDO;

final class Contact
{
    public ?int $id = null;
    public int $companyId;
    public ?int $userId = null;
    public string $firstName;
    public ?string $lastName = null;
    public ?string $email = null;
    public ?string $phone = null;
    public ?string $title = null;
    public ?string $notesMd = null;
    public string $createdAt;
    public string $updatedAt;

    /** @return Contact[] */
    public static function forCompany(int $companyId): array
    {
        $sql = 'SELECT id, company_id AS companyId, user_id AS userId, first_name AS firstName, last_name AS lastName, email, phone, title, notes_md AS notesMd, created_at AS createdAt, updated_at AS updatedAt
                FROM contacts WHERE company_id = :cid ORDER BY first_name, last_name';
        $st = Database::get()->prepare($sql);
        $st->execute([':cid' => $companyId]);
        return $st->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function find(int $id): ?self
    {
        $st = Database::get()->prepare('SELECT id, company_id AS companyId, user_id AS userId, first_name AS firstName, last_name AS lastName, email, phone, title, notes_md AS notesMd, created_at AS createdAt, updated_at AS updatedAt FROM contacts WHERE id = :id');
        $st->execute([':id' => $id]);
        return $st->fetchObject(self::class) ?: null;
    }

    public function insert(): void
    {
        $sql = 'INSERT INTO contacts (company_id, user_id, first_name, last_name, email, phone, title, notes_md)
                VALUES (:company_id, :user_id, :first_name, :last_name, :email, :phone, :title, :notes_md)';
        $st = Database::get()->prepare($sql);
        $st->execute([
            ':company_id' => $this->companyId,
            ':user_id'    => $this->userId,
            ':first_name' => $this->firstName,
            ':last_name'  => $this->lastName,
            ':email'      => $this->email,
            ':phone'      => $this->phone,
            ':title'      => $this->title,
            ':notes_md'   => $this->notesMd,
        ]);
        $this->id = (int)Database::get()->lastInsertId();
    }

    public function update(): void
    {
        if ($this->id === null) {
            throw new \LogicException('Cannot update without id');
        }
        $sql = 'UPDATE contacts SET user_id = :user_id, first_name = :first_name, last_name = :last_name, email = :email, phone = :phone, title = :title, notes_md = :notes_md WHERE id = :id';
        $st = Database::get()->prepare($sql);
        $st->execute([
            ':user_id'    => $this->userId,
            ':first_name' => $this->firstName,
            ':last_name'  => $this->lastName,
            ':email'      => $this->email,
            ':phone'      => $this->phone,
            ':title'      => $this->title,
            ':notes_md'   => $this->notesMd,
            ':id'         => $this->id,
        ]);
    }

    public function delete(): void
    {
        if ($this->id !== null) {
            $st = Database::get()->prepare('DELETE FROM contacts WHERE id = :id');
            $st->execute([':id' => $this->id]);
        }
    }
}
```

```php
<?php
namespace App\Models;

use App\Database;
use PDO;

final class Project
{
    public ?int $id = null;
    public int $companyId;
    public ?string $slug; // will be auto-generated if null on insert
    public string $title;
    public ?string $summaryMd = null;
    public string $status;     // 'active' | 'paused' | 'archived'
    public string $visibility; // 'public' | 'private' | 'unlisted'
    public string $createdAt;
    public string $updatedAt;

    /** @return Project[] */
    public static function forCompany(int $companyId): array
    {
        $sql = 'SELECT id, company_id AS companyId, slug, title, summary_md AS summaryMd, status, visibility, created_at AS createdAt, updated_at AS updatedAt
                FROM projects WHERE company_id = :cid ORDER BY created_at DESC';
        $st = Database::get()->prepare($sql);
        $st->execute([':cid' => $companyId]);
        return $st->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function findBySlug(string $slug): ?self
    {
        $sql = 'SELECT id, company_id AS companyId, slug, title, summary_md AS summaryMd, status, visibility, created_at AS createdAt, updated_at AS updatedAt
                FROM projects WHERE slug = :slug';
        $st = Database::get()->prepare($sql);
        $st->execute([':slug' => $slug]);
        return $st->fetchObject(self::class) ?: null;
    }

    public function insert(): void
    {
        // Ensure slug is set
        if ($this->slug === null) {
            $this->generateSlug();
        }

        $sql = 'INSERT INTO projects (company_id, slug, title, summary_md, status, visibility)
                VALUES (:company_id, :slug, :title, :summary_md, :status, :visibility)';
        $st = Database::get()->prepare($sql);
        $st->execute([
            ':company_id' => $this->companyId,
            ':slug'       => $this->slug,
            ':title'      => $this->title,
            ':summary_md' => $this->summaryMd,
            ':status'     => $this->status,
            ':visibility' => $this->visibility,
        ]);
        $this->id = (int)Database::get()->lastInsertId();
    }

    public function update(): void
    {
        if ($this->id === null) {
            throw new \LogicException('Cannot update without id');
        }
        $sql = 'UPDATE projects SET title = :title, summary_md = :summary_md, status = :status, visibility = :visibility WHERE id = :id';
        $st = Database::get()->prepare($sql);
        $st->execute([
            ':title'      => $this->title,
            ':summary_md' => $this->summaryMd,
            ':status'     => $this->status,
            ':visibility' => $this->visibility,
            ':id'         => $this->id,
        ]);
    }

    public function delete(): void
    {
        if ($this->id === null) {
            return;
        }
        $st = Database::get()->prepare('DELETE FROM projects WHERE id = :id');
        $st->execute([':id' => $this->id]);
    }

    /** Generate a unique slug based on the title */
    public function generateSlug(): void
    {
        $baseSlug = strtolower(preg_replace('/[^a-z0-9]+/', '-', trim($this->title)));
        $slug = $baseSlug;
        $i = 1;
        while (self::findBySlug($slug) !== null) {
            $slug = $baseSlug . '-' . $i++;
        }
        $this->slug = $slug;
    }
}
```

### Add getFullName() to Contact

On your own, add a method to the `Contact` class called `getFullName()` that returns the contact's full name by combining `firstName` and `lastName`. If `lastName` is `null`, it should return just the `firstName`.

### Test, Commit, and Push

1. Create `demo-crud.php` at your project root and paste the following into it.

```php
<?php
require __DIR__ . '/vendor/autoload.php';

use App\Models\Company;
use App\Models\Project;
use App\Models\Contact;

echo "== INSERT ==\n";
$acme = new Company();
$acme->name = 'Acme, Inc.';
$acme->websiteUrl = 'https://acme.example';
$acme->industry = 'Manufacturing';
$acme->notesMd = 'Top client.';
$acme->insert();
echo "Company #{$acme->id} created\n";

$proj = new Project();
$proj->companyId = (int)$acme->id;
$proj->slug = 'acme-website-redesign';
$proj->title = 'Website Redesign';
$proj->summaryMd = 'Update brand + CMS.';
$proj->status = 'active';
$proj->visibility = 'public';
$proj->insert();
echo "Project #{$proj->id} created\n";

$contact = new Contact();
$contact->companyId = (int)$acme->id;
//$contact->userId = (int)$user->id; // optional owner
$contact->firstName = 'Alice';
$contact->lastName = 'Anderson';
$contact->email = 'alice@example.com';
$contact->title = 'Marketing Manager';
$contact->insert();
echo "Contact #{$contact->id} created\n";

echo "\n== SELECT ==\n";
// Companies list
foreach (Company::all() as $c) {
    echo "- Company: {$c->name}\n";
}

// Find by slug
$fetched = Project::findBySlug('acme-website-redesign');
echo $fetched ? "Found project: {$fetched->title}\n" : "Project not found\n";

// Contacts for company
foreach (Contact::forCompany((int)$acme->id) as $ct) {
    echo "Contact: {$ct->getFullName()} <{$ct->email}>\n";
}

echo "\n== UPDATE ==\n";
$acme->industry = 'Aerospace';
$acme->update();
echo "Company #{$acme->id} industry updated\n";

$proj->status = 'paused';
$proj->update();
echo "Project #{$proj->id} status updated\n";

echo "\n== DELETE ==\n";
$contact->delete();
echo "Contact #{$contact->id} deleted\n";

$proj->delete();
echo "Project #{$proj->id} deleted\n";

$acme->delete();
echo "Company #{$acme->id} deleted\n";
```

2. Run the demo script in your Codespace terminal with `php demo-crud.php`.
3. If everything works as expected, commit your changes with a message like "Discovery Project 8-2" and push to your repository.

## Discovery Project 8-3 — User and Profile models (fetch joins, 1:1)

### Goal

Represent `users` and `profiles`. Provide fetchers and CRUD for `users`; fetch `Profile` by `user_id` and an example join that hydrates a view class.

### Files

Create `classes/Models/User.php` and `classes/Models/Profile.php`.

```php
<?php
namespace App\Models;

use App\Database;
use PDO;

final class User
{
    public ?int $id = null;
    public string $email;
    public string $passwordHash;
    public string $displayName;
    public string $role; // 'admin'|'staff'|'client'
    public int $active;  // 0/1
    public string $createdAt;
    public string $updatedAt;
    public ?string $deletedAt = null;

    public static function find(int $id): ?self
    {
        $sql = 'SELECT id, email, password_hash AS passwordHash, display_name AS displayName, role, active, created_at AS createdAt, updated_at AS updatedAt, deleted_at AS deletedAt
                FROM users WHERE id = :id';
        $st = Database::get()->prepare($sql);
        $st->execute([':id' => $id]);
        return $st->fetchObject(self::class) ?: null;
    }

    /** @return User[] */
    public static function allActive(): array
    {
        $st = Database::get()->query('SELECT id, email, password_hash AS passwordHash, display_name AS displayName, role, active, created_at AS createdAt, updated_at AS updatedAt, deleted_at AS deletedAt FROM users WHERE active = 1 AND deleted_at IS NULL ORDER BY display_name');
        return $st->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public function insert(): void
    {
        $sql = 'INSERT INTO users (email, password_hash, display_name, role, active) VALUES (:email, :password_hash, :display_name, :role, :active)';
        $st = Database::get()->prepare($sql);
        $st->execute([
            ':email'         => $this->email,
            ':password_hash' => $this->passwordHash,
            ':display_name'  => $this->displayName,
            ':role'          => $this->role,
            ':active'        => $this->active,
        ]);
        $this->id = (int)Database::get()->lastInsertId();
    }

    public function update(): void
    {
        if ($this->id === null) {
            throw new \LogicException('Cannot update without id');
        }
        $sql = 'UPDATE users SET email = :email, password_hash = :password_hash, display_name = :display_name, role = :role, active = :active WHERE id = :id';
        $st = Database::get()->prepare($sql);
        $st->execute([
            ':email'         => $this->email,
            ':password_hash' => $this->passwordHash,
            ':display_name'  => $this->displayName,
            ':role'          => $this->role,
            ':active'        => $this->active,
            ':id'            => $this->id,
        ]);
    }

    public function delete(): void
    {
        if ($this->id === null) {
            return;
        }
        $st = Database::get()->prepare('UPDATE users SET deleted_at = NOW() WHERE id = :id');
        $st->execute([':id' => $this->id]);
    }

    public function restore(): void
    {
        if ($this->id === null) {
            return;
        }
        $st = Database::get()->prepare('UPDATE users SET deleted_at = NULL WHERE id = :id');
        $st->execute([':id' => $this->id]);
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isActive(): bool
    {
        return $this->active === 1 && !$this->isDeleted();
    }

    public function getProfile(): ?Profile
    {
        return Profile::findByUserId($this->id);
    }
}
```

```php
<?php
namespace App\Models;

use App\Database;
use PDO;

final class Profile
{
    public int $userId; // primary key in schema
    public ?string $bioMd = null;
    public ?string $websiteUrl = null;
    public ?string $location = null;
    public ?string $avatarUrl = null;

    public static function findByUserId(int $userId): self
    {
        $sql = 'SELECT user_id AS userId, bio_md AS bioMd, website_url AS websiteUrl, location, avatar_url AS avatarUrl
                FROM profiles WHERE user_id = :uid';
        $st = Database::get()->prepare($sql);
        $st->execute([':uid' => $userId]);
        $profile = $st->fetchObject(self::class);

        // If no profile exists, return an instance with just userId set, ready for upsert
        if (!$profile) {
            $profile = new self();
            $profile->userId = $userId;
        }

        return $profile;
    }

    public function upsert(): void
    {
        // Simple upsert: try update; if 0 rows, insert
        // In MySQL 8+, we could use INSERT ... ON DUPLICATE KEY UPDATE instead, but that isn't supported in all database platforms.
        $update = Database::get()->prepare('UPDATE profiles SET bio_md = :bio_md, website_url = :website_url, location = :location, avatar_url = :avatar_url WHERE user_id = :user_id');
        $update->execute([
            ':bio_md'     => $this->bioMd,
            ':website_url'=> $this->websiteUrl,
            ':location'   => $this->location,
            ':avatar_url' => $this->avatarUrl,
            ':user_id'    => $this->userId,
        ]);
        if ($update->rowCount() === 0) {
            $insert = Database::get()->prepare('INSERT INTO profiles (user_id, bio_md, website_url, location, avatar_url) VALUES (:user_id, :bio_md, :website_url, :location, :avatar_url)');
            $insert->execute([
                ':user_id'    => $this->userId,
                ':bio_md'     => $this->bioMd,
                ':website_url'=> $this->websiteUrl,
                ':location'   => $this->location,
                ':avatar_url' => $this->avatarUrl,
            ]);
        }
    }
}
```

Example: hydrate a combined view of user+profile (aliases → a dedicated view class):

```php
<?php
namespace App\Views;

use App\Database;
use PDO;

final class UserWithProfile
{
    public int $id;
    public string $displayName;
    public ?string $bioMd;
    public ?string $websiteUrl;

    /** @return UserWithProfile[] */
    public static function all(): array
    {
        $sql = 'SELECT u.id, u.display_name AS displayName, p.bio_md AS bioMd, p.website_url AS websiteUrl
                FROM users u LEFT JOIN profiles p ON p.user_id = u.id
                WHERE u.active = 1 AND u.deleted_at IS NULL
                ORDER BY u.display_name';
        $st = Database::get()->query($sql);
        return $st->fetchAll(PDO::FETCH_CLASS, self::class);
    }
}
```

### Create User From Contact Example

On your own, add a method to the `Contact` class to create a `User` from a `Contact`. It should:

- Create a new `User` instance.
- Set the `User`'s `email` from the `Contact`'s `email` (remember, you can use the `$this` keyword to refer to the current object).
- Accept the hashed password as a parameter.
- Set the `User`'s `displayName` from the `Contact`'s full name.
- Set the `User`'s `role` to `'client'` and `active` to `1`.
- Insert the `User` into the database.
- Associate the created `User`'s `id` with the `Contact`'s `userId` property and update the `Contact`.
- Return the created `User` instance.

### Test, Commit, and Push

1. Open `demo-crud.php` and add code to test the new `User` and `Profile` classes, as well as the new method on `Contact`. For example, you might create a new `User`, update their `Profile`, and fetch the combined view.
2. Run the demo script in your Codespace terminal with `php demo-crud.php`. Note that `email` must be unique in the `users` table, so you may need to adjust the email address used in your test code if you run it multiple times.
3. If everything works as expected, commit your changes with a message like "Discovery Project 8-3" and push to your repository.

## Discovery Project 8-4 — Posts, comments, activities, and follows (fetch and write)

### Goal

Model `posts`, `comments`, `activities`, and `follows` with static fetchers and instance `insert()/update()/delete()`.

### Files

Create `classes/Models/Post.php`, `classes/Models/Comment.php`, `classes/Models/Activity.php`, and `classes/Models/Follow.php`.

```php
<?php
namespace App\Models;

use App\Database;
use PDO;

final class Post
{
    public ?int $id = null;
    public int $projectId;
    public ?int $authorId = null;
    public ?int $activityId = null;
    public string $bodyMd;
    public string $createdAt;
    public ?string $updatedAt = null;
    public string $visibility; // 'public'|'project'|'private'

    /** @return Post[] */
    public static function forProject(int $projectId): array
    {
        $sql = 'SELECT id, project_id AS projectId, author_id AS authorId, activity_id AS activityId, body_md AS bodyMd, created_at AS createdAt, updated_at AS updatedAt, visibility
                FROM posts WHERE project_id = :pid ORDER BY created_at DESC';
        $st = Database::get()->prepare($sql);
        $st->execute([':pid' => $projectId]);
        return $st->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public function insert(): void
    {
        $sql = 'INSERT INTO posts (project_id, author_id, activity_id, body_md, visibility) VALUES (:project_id, :author_id, :activity_id, :body_md, :visibility)';
        $st = Database::get()->prepare($sql);
        $st->execute([
            ':project_id' => $this->projectId,
            ':author_id'  => $this->authorId,
            ':activity_id'=> $this->activityId,
            ':body_md'    => $this->bodyMd,
            ':visibility' => $this->visibility,
        ]);
        $this->id = (int)Database::get()->lastInsertId();
    }

    public function updateBody(string $newBody): void
    {
        if ($this->id === null) {
            throw new \LogicException('Cannot update without id');
        }
        $this->bodyMd = $newBody;
        $st = Database::get()->prepare('UPDATE posts SET body_md = :body_md, updated_at = NOW() WHERE id = :id');
        $st->execute([':body_md' => $this->bodyMd, ':id' => $this->id]);
    }

    public function delete(): void
    {
        if ($this->id !== null) {
            $st = Database::get()->prepare('DELETE FROM posts WHERE id = :id');
            $st->execute([':id' => $this->id]);
        }
    }
}
```

```php
<?php
namespace App\Models;

use App\Database;
use PDO;

final class Activity
{
    public ?int $id = null;
    public int $contactId;
    public ?int $userId = null;
    public string $type;       // 'call'|'email'|'meeting'|'task'
    public string $subject;
    public ?string $dueAt = null;
    public ?string $completedAt = null;
    public ?string $notesMd = null;
    public string $createdAt;

    /** @return Activity[] */
    public static function forContact(int $contactId): array
    {
        $sql = 'SELECT id, contact_id AS contactId, user_id AS userId, type, subject, due_at AS dueAt, completed_at AS completedAt, notes_md AS notesMd, created_at AS createdAt
                FROM activities WHERE contact_id = :cid ORDER BY created_at DESC';
        $st = Database::get()->prepare($sql);
        $st->execute([':cid' => $contactId]);
        return $st->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public function insert(): void
    {
        $sql = 'INSERT INTO activities (contact_id, user_id, type, subject, due_at, completed_at, notes_md)
                VALUES (:contact_id, :user_id, :type, :subject, :due_at, :completed_at, :notes_md)';
        $st = Database::get()->prepare($sql);
        $st->execute([
            ':contact_id'   => $this->contactId,
            ':user_id'      => $this->userId,
            ':type'         => $this->type,
            ':subject'      => $this->subject,
            ':due_at'       => $this->dueAt,
            ':completed_at' => $this->completedAt,
            ':notes_md'     => $this->notesMd,
        ]);
        $this->id = (int)Database::get()->lastInsertId();
    }

    public function markCompleted(string $completedAt = null): void
    {
        if ($this->id === null) {
            throw new \LogicException('Cannot update without id');
        }
        $this->completedAt = $completedAt ?? date('Y-m-d H:i:s');
        $st = Database::get()->prepare('UPDATE activities SET completed_at = :completed_at WHERE id = :id');
        $st->execute([':completed_at' => $this->completedAt, ':id' => $this->id]);
    }

    public function delete(): void
    {
        if ($this->id !== null) {
            $st = Database::get()->prepare('DELETE FROM activities WHERE id = :id');
            $st->execute([':id' => $this->id]);
        }
    }
}
```

```php
<?php
namespace App\Models;

use App\Database;
use PDO;

final class Comment
{
    public ?int $id = null;
    public int $postId;
    public ?int $authorId = null;
    public string $bodyMd;
    public string $createdAt;

    /** @return Comment[] */
    public static function forPost(int $postId): array
    {
        $sql = 'SELECT id, post_id AS postId, author_id AS authorId, body_md AS bodyMd, created_at AS createdAt FROM comments WHERE post_id = :pid ORDER BY created_at ASC';
        $st = Database::get()->prepare($sql);
        $st->execute([':pid' => $postId]);
        return $st->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public function insert(): void
    {
        $sql = 'INSERT INTO comments (post_id, author_id, body_md) VALUES (:post_id, :author_id, :body_md)';
        $st = Database::get()->prepare($sql);
        $st->execute([
            ':post_id'  => $this->postId,
            ':author_id'=> $this->authorId,
            ':body_md'  => $this->bodyMd,
        ]);
        $this->id = (int)Database::get()->lastInsertId();
    }

    public function delete(): void
    {
        if ($this->id !== null) {
            $st = Database::get()->prepare('DELETE FROM comments WHERE id = :id');
            $st->execute([':id' => $this->id]);
        }
    }
}
```

### Create Follow Model

On your own and using the earlier classes as reference, create the `Follow` model in `classes/Models/Follow.php` with the following specifications:

- Create camelCase properties to match the `follows` table columns (use appropriate types): `followerId`, `followeeId`, and `createdAt`.
- Implement a static method `followersOf(int $userId): array` that fetches all followers of a given user.
- Implement a static method `following(int $userId): array` that fetches all users that a given user is following.
- Implement an instance method `insert(): void` to add a new follow relationship.
- Implement an instance method `delete(): void` to remove a follow relationship.
- Use `PDO::FETCH_CLASS` to hydrate results into `Follow` instances.
- Use prepared statements for all database interactions.
- Use aliases in your SQL queries to map database columns to class properties.

### Test, Commit, and Push

1. Open `demo-crud.php` and add code to test the new `Post`, `Comment`, `Activity`, and `Follow` classes. For example, you might create a new `Post`, add a `Comment`, create an `Activity`, and establish a `Follow` relationship between two users.
2. Run the demo script in your Codespace terminal with `php demo-crud.php`.
3. If everything works as expected, commit your changes with a message like "Discovery Project 8-4" and push to your repository.

## Discovery Project 8-5 — Project Members and Reactions

### Goal

Create models for `project_members` and `reactions` with static fetchers and instance `insert()/delete()` methods.

### Steps

1. Create `classes/Models/ProjectMember.php` and `classes/Models/Reaction.php`.
2. Implement the following for `ProjectMember`:
   - Properties: `projectId`, `userId`, `role`, `addedAt`.
   - Static method `membersOf(int $projectId): array` to fetch all members of a project.
   - Instance method `insert(): void` to add a member to a project.
   - Instance method `delete(): void` to remove a member from a project.
   - Instance method `changeRole(string $newRole): void` to update a member's role (remember to update `$this->role`, too!).
   - Instance method `getUser(): ?User` to fetch the associated `User` object.
   - Use prepared statements and `PDO::FETCH_CLASS` for fetching.
   - Alias columns in SQL to match property names.
3. Implement the following for `Reaction`:
   - Properties: `id`, `postId`, `userId`, `commentId`, `type`.
   - Static method `forPost(int $postId): array` to fetch all reactions for a post.
   - Static method `forComment(int $commentId): array` to fetch all reactions for a comment.
   - Instance method `insert(): void` to add a reaction.
   - Instance method `delete(): void` to remove a reaction.
   - Use prepared statements and `PDO::FETCH_CLASS` for fetching.
   - Alias columns in SQL to match property names.

### Test, Commit, and Push

1. Open `demo-crud.php` and add code to test the new `ProjectMember` and `Reaction` classes. For example, you might add a member to a project and create reactions for a post and a comment.
2. Run the demo script in your Codespace terminal with `php demo-crud.php`.
3. If everything works as expected, commit your changes with a message like "Discovery Project 8-5" and push to your repository.

---

Tips

- For all fetchers, prefer prepared statements with named parameters.
- Use `fetchObject(ClassName::class)` for single rows and `fetchAll(PDO::FETCH_CLASS, ClassName::class)` for collections.
- When property names differ from column names, alias in SQL to match the PHP property names.
