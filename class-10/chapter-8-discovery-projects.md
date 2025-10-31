---
layout: default
title: 10.1 Chapter 8 Discovery Projects — PDO Models
nav_order: 1
---

# 10.1 Chapter 8 Discovery Projects — PDO Models

In chapter 7 you modeled the database tables (companies, contacts, projects, users, profiles, project_members, activities, posts, comments, follows, reactions). Now we will build PHP classes to represent those tables and add class methods that fetch rows via PDO using fetchObject() or FETCH_CLASS. We'll use static methods for fetching and instance methods for inserting and updating. We’ll also include a small integration script to insert, update, delete, and select across several models.

Notes:

- We'll use one shared PDO connection via a small `Database` class.
- We'll use camelCase properties in PHP; alias snake_case columns in SQL as needed.

## Discovery Project 8-1 — Shared PDO connection and folder setup

### Goal

Create a single shared PDO connection that all models will reuse.

### Steps

1. In your discovery projects repo, create `classes/Database.php`.
2. Add the following (MySQL/MariaDB DSN shown; swap for SQLite if desired):

```php
<?php
declare(strict_types=1);

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

3. If your `composer.json` already autoloads `App\` from `classes/`, run `composer dump-autoload`.
4. Quick smoke test: create `public/pdo-test.php` to `require 'vendor/autoload.php';` and call `App\Database::get();`.

## Discovery Project 8-2 — Company and Project models (CRUD + fetch)

### Goal

Model `companies` and `projects` with camelCase properties, static fetchers, and instance `insert()`, `update()`, `delete()` methods. Use `fetchObject()` or `FETCH_CLASS` when selecting.

### Files

Create `classes/Models/Company.php` and `classes/Models/Project.php`.

```php
<?php
declare(strict_types=1);

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
declare(strict_types=1);

namespace App\Models;

use App\Database;
use PDO;

final class Project
{
    public ?int $id = null;
    public int $companyId;
    public string $slug;
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
}
```

## Discovery Project 8-3 — User and Profile models (fetch joins, 1:1)

### Goal

Represent `users` and `profiles`. Provide fetchers and CRUD for `users`; fetch `Profile` by `user_id` and an example join that hydrates a view class.

### Files

Create `classes/Models/User.php` and `classes/Models/Profile.php`.

```php
<?php
declare(strict_types=1);

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
        $st = Database::get()->query('SELECT id, email, password_hash AS passwordHash, display_name AS displayName, role, active, created_at AS createdAt, updated_at AS updatedAt, deleted_at AS deletedAt FROM users WHERE active = 1 ORDER BY display_name');
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
        $st = Database::get()->prepare('DELETE FROM users WHERE id = :id');
        $st->execute([':id' => $this->id]);
    }
}
```

```php
<?php
declare(strict_types=1);

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

    public static function findByUserId(int $userId): ?self
    {
        $sql = 'SELECT user_id AS userId, bio_md AS bioMd, website_url AS websiteUrl, location, avatar_url AS avatarUrl
                FROM profiles WHERE user_id = :uid';
        $st = Database::get()->prepare($sql);
        $st->execute([':uid' => $userId]);
        return $st->fetchObject(self::class) ?: null;
    }

    public function upsert(): void
    {
        // Simple upsert: try update; if 0 rows, insert
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
declare(strict_types=1);

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
                ORDER BY u.display_name';
        $st = Database::get()->query($sql);
        return $st->fetchAll(PDO::FETCH_CLASS, self::class);
    }
}
```

## Discovery Project 8-4 — Posts and comments (fetch and write)

### Goal

Model `posts` and `comments` with static fetchers and instance `insert()/update()/delete()`.

### Files

Create `classes/Models/Post.php` and `classes/Models/Comment.php`.

```php
<?php
declare(strict_types=1);

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
declare(strict_types=1);

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

## Discovery Project 8-5 — Integration script: insert, update, delete, select

### Goal

Write a standalone script that uses the classes above to demonstrate inserting, updating, deleting, and selecting data across multiple tables.

### Steps

1. Create `scripts/demo-crud.php` at your project root.
2. Paste the following and run it in your Codespace terminal with `php scripts/demo-crud.php`.

```php
<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use App\Models\Profile;
use App\Models\Post;
use App\Models\Comment;

echo "== INSERT ==\n";
$acme = new Company();
$acme->name = 'Acme, Inc.';
$acme->websiteUrl = 'https://acme.example';
$acme->industry = 'Manufacturing';
$acme->notesMd = 'Top client.';
$acme->insert();
echo "Company #{$acme->id} created\n";

$user = new User();
$user->email = 'jane.doe@example.com';
$user->passwordHash = password_hash('secret', PASSWORD_DEFAULT);
$user->displayName = 'Jane Doe';
$user->role = 'staff';
$user->active = 1;
$user->insert();
echo "User #{$user->id} created\n";

$profile = new Profile();
$profile->userId = (int)$user->id;
$profile->bioMd = 'Hello from Jane!';
$profile->websiteUrl = 'https://janedoe.dev';
$profile->upsert();
echo "Profile for user #{$user->id} upserted\n";

$proj = new Project();
$proj->companyId = (int)$acme->id;
$proj->slug = 'acme-website-redesign';
$proj->title = 'Website Redesign';
$proj->summaryMd = 'Update brand + CMS.';
$proj->status = 'active';
$proj->visibility = 'project';
$proj->insert();
echo "Project #{$proj->id} created\n";

$post = new Post();
$post->projectId = (int)$proj->id;
$post->authorId = (int)$user->id;
$post->activityId = null;
$post->bodyMd = 'Kickoff meeting scheduled for Monday.';
$post->visibility = 'project';
$post->insert();
echo "Post #{$post->id} created\n";

$comment = new Comment();
$comment->postId = (int)$post->id;
$comment->authorId = (int)$user->id;
$comment->bodyMd = 'I will share the agenda later today.';
$comment->insert();
echo "Comment #{$comment->id} created\n";

echo "\n== SELECT ==\n";
// Companies list
foreach (Company::all() as $c) {
    echo "- Company: {$c->name}\n";
}

// Find by slug
$fetched = Project::findBySlug('acme-website-redesign');
echo $fetched ? "Found project: {$fetched->title}\n" : "Project not found\n";

// Project posts
foreach (Post::forProject((int)$proj->id) as $p) {
    echo "Post #{$p->id}: " . substr($p->bodyMd, 0, 40) . "...\n";
}

echo "\n== UPDATE ==\n";
$acme->industry = 'Aerospace';
$acme->update();
echo "Company #{$acme->id} industry updated\n";

$post->updateBody('Kickoff moved to Tuesday. Agenda attached.');
echo "Post #{$post->id} body updated\n";

echo "\n== DELETE ==\n";
$comment->delete();
echo "Comment #{$comment->id} deleted\n";

$post->delete();
echo "Post #{$post->id} deleted\n";

$proj->delete();
echo "Project #{$proj->id} deleted\n";

$acme->delete();
echo "Company #{$acme->id} deleted\n";

// Optional: clean up user (may cascade or be restricted based on your FKs)
// $user->delete();
```

### Validate and commit

- Run the script above to verify CRUD operations.
- Commit with a message such as “Discovery Project 8-5” and push.

---

Tips

- For all fetchers, prefer prepared statements with named parameters.
- Use `fetchObject(ClassName::class)` for single rows and `fetchAll(PDO::FETCH_CLASS, ClassName::class)` for collections.
- When property names differ from column names, alias in SQL to match the PHP property names.
