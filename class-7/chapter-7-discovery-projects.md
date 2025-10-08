---
layout: default
title: 7.1 Chapter 7 Discovery Projects
nav_order: 1
---

# 7.1 Chapter 7 Discovery Projects

## Discovery Project 7-1

### Goal

This week we need to prepare your discovery projects repository to support MariaDB and phpMyAdmin. To do that, you'll add some files to the `.devcontainer` folder in your existing repository.

### Steps

1. Open your discovery projects Codespace and expand the `.devcontainer` folder.
2. Inside the folder, you should see one file named `devcontainer.json`. Open it.
3. Replace the contents of `devcontainer.json` with the contents of [this file](http://natelaclaire.me/cis334/class-7/example-devcontainer.json). Note that this file refers to a `docker-compose.yml` file, which we'll create next.
4. Inside the same folder, create a new file named `docker-compose.yml`.
5. Place the contents of [this file](http://natelaclaire.me/cis334/class-7/example-docker-compose.yml) into the new file. This file refers to a file named `Dockerfile`, so now we need to create that.
6. Create another new file inside the same folder, this one called `Dockerfile` (no extension).
7. Place the contents of [this file](http://natelaclaire.me/cis334/class-7/example-Dockerfile.txt) into the new file.
8. Next, we need to set up our shell scripts for dumping and loading the databases. Create a new file in the root of your repository called `dump-mysql.sh` and place the contents of [this file](http://natelaclaire.me/cis334/class-7/example-dump-mysql.txt) inside.
9. Create a new file in the root of your repository called `load-mysql.sh` and place the contents of [this file](http://natelaclaire.me/cis334/class-7/example-load-mysql.txt) inside.
10. Make the two shell scripts executable by typing the following into the Terminal and pressing Enter after each line:

```sh
sudo chmod a+x ./dump-mysql.sh
sudo chmod a+x ./load-mysql.sh
```

11. Save, stage, commit, and sync your changes. Use a commit message such as "Discovery Project 7-1".
12. Perform a Full Rebuild of the Codespace container, which will cause the database and phpMyAdmin images to be added and loaded.

## Discovery Project 7-2

### Goal

In this project, we'll create a new database and modify our shell scripts in order to backup and restore the new database.

### Create the Database

1. On the Ports tab, click the button to open port 8181 in the browser.
2. Sign in with the `root` username and `mariadb` password.
3. Click the Databases tab.
4. In the **Create Database** box, enter `crm` as the database name.
5. Choose `utf8mb4_unicode_ci` from the drop-down to indicate the collation.
6. Click Create.
7. Click User Accounts.
8. Click Add User Account.
9. Specify `crm` as the username, `localhost` as the host name, and use the button to generate a random password.
10. Make note of the password.
11. Leave all other fields as-is.
12. Click Go.
13. When the confirmation that you have added a new user appears, copy the SQL code shown and store it somewhere, adding the password that you selected in place of the asterisks.
14. Click the Database button right before Change Password.
15. Select the `crm` database and click Go.
16. Click Check All and click Go.
17. Copy the SQL code that appears and store it with the other SQL code.

### Create a SQL Script

We'll need our `load-mysql.sh` script to recreate the user permissions, so let's set up a SQL script to do that.

1. Create a new file in the `sql` folder called `crm-user.sql`.
2. Paste the two SQL statements from above into the file and save it.
3. Add `IF NOT EXISTS` after `CREATE USER` to avoid any error messages if the user account already exists.

### Modify the Shell Scripts

Now we'll update our shell scripts to backup and restore the new database and also recreate the new user account and permissions.

1. Open `dump-mysql.sh` in VS Code.
2. Copy the second line (which begins with `mysqldump`) and paste a copy on the next line.
3. Modify the new line, replacing `mydb` with `crm` in both places.
4. Save the file.
5. Open `load-mysql.sh`.
6. Copy the second line (which begins with `mysql`) and paste two copies on the next two lines.
7. On the first of the new lines, change `mydb.sql` to `crm-user.sql`.
8. On the second of the new lines, change `mydb.sql` to `crm.sql`.
9. Save the file.
10. In the Terminal, type `./dump-mysql.sh` and press Enter.
11. Check to ensure that a new `crm.sql` file was created in the `sql` folder.
12. In the Terminal, type `./load-mysql.sh` and press Enter.
13. The only error that you should see will be that the user can't be created, which is because it already exists.
14. Save, stage, commit, and sync your changes. Use a commit message such as "Discovery Project 7-1".

## Discovery Project 7-3

In our remaining discovery projects, we'll be building a small social network and customer relationship management (CRM) system into our discovery projects Web site.

### Goal

In this discovery project, we'll create our first three database tables for the social network and CRM using **phpMyAdmin**:

* `companies`
* `contacts`
* `projects`

We’re **not** adding foreign key constraints yet, but we’ll include the columns that will become keys later.

### Before you start (one-time prefs)

1. Log into phpMyAdmin → left sidebar: click **crm** to select the database.
2. Top bar → **Operations** → set **Collation** to `utf8mb4_general_ci` (or `utf8mb4_unicode_ci`) and default **Storage Engine** to `InnoDB`. Save.

### Table 1: `companies`

**Columns**

* `id` INT, **AI**, **PK**
* `name` VARCHAR(150) **NOT NULL**
* `website_url` VARCHAR(255) NULL
* `industry` VARCHAR(100) NULL
* `notes_md` TEXT NULL
* `created_at` DATETIME **NOT NULL** (Default: `CURRENT_TIMESTAMP`)
* `updated_at` DATETIME **NOT NULL** (Default: `CURRENT_TIMESTAMP` → on update: `CURRENT_TIMESTAMP`)

**phpMyAdmin steps**

1. With **crm** selected → **SQL** or **Structure → Create table** → name: `companies`.
2. Add the columns above.
3. Set `id` as **A_I** (Auto Increment) and **Primary** (index dropdown).
4. For `created_at`/`updated_at`, set the default and “on update” as noted.
5. **Save**.

### Table 2: `contacts`

**Columns**

* `id` INT, **AI**, **PK**
* `company_id` INT NULL  ← will reference `companies.id` later
* `user_id` INT NULL  ← will reference `users.id` later
* `first_name` VARCHAR(80) **NOT NULL**
* `last_name` VARCHAR(80) **NOT NULL**
* `email` VARCHAR(255) NULL
* `phone` VARCHAR(40) NULL
* `title` VARCHAR(100) NULL
* `notes_md` TEXT NULL
* `created_at` DATETIME **NOT NULL** (Default: `CURRENT_TIMESTAMP`)
* `updated_at` DATETIME **NOT NULL** (Default: `CURRENT_TIMESTAMP` → on update: `CURRENT_TIMESTAMP`)

**Indexes (no FKs yet)**

* Add a **INDEX** on `company_id` (regular index).
* Add a **INDEX** on `user_id` (regular index).
* Optional: add a **UNIQUE** index on `email` if you want to prevent duplicates.

**phpMyAdmin steps**

1. **Structure → Create table** → name: `contacts`.
2. Add the columns above.
3. Mark `id` as **A_I** and **Primary**.
4. In the **Indexes** section, add **INDEX** on `company_id` and `user_id` (type: INDEX, separate indexes - not a composite index).
5. (Optional) Add **UNIQUE** on `email`.
6. **Save**.

### Table 3: `projects`

**Columns**

* `id` INT, **AI**, **PK**
* `company_id` INT NULL ← will reference `companies.id` later
* `slug` VARCHAR(120) **NOT NULL** (will be unique)
* `title` VARCHAR(150) **NOT NULL**
* `summary_md` TEXT NULL
* `status` ENUM('active','paused','archived') **NOT NULL** DEFAULT 'active'
* `visibility` ENUM('public','private','unlisted') **NOT NULL** DEFAULT 'public'
* `created_at` DATETIME **NOT NULL** (Default: `CURRENT_TIMESTAMP`)
* `updated_at` DATETIME **NOT NULL** (Default: `CURRENT_TIMESTAMP` → on update: `CURRENT_TIMESTAMP`)

**Indexes (no FKs yet)**

* Add **UNIQUE** on `slug`.
* Add **INDEX** on `company_id`.

**phpMyAdmin steps**

1. **Structure → Create table** → name: `projects`.
2. Add the columns above.
3. Mark `id` as **A_I** and **Primary**.
4. In **Indexes**, add **UNIQUE** on `slug` and **INDEX** on `company_id`.
5. **Save**.

### How they relate (conceptually, without constraints yet)

* **Company → Contacts**: one **company** can have many **contacts**. (`contacts.company_id` → `companies.id`)
* **User → Projects**: one **user** can own many **projects**. (`projects.owner_id` → `users.id`)
  *Note:* The `users` table isn’t part of this assignment; we’re just preparing the column.

We’ll enforce these relationships with **foreign key constraints** in a later project. For now, the integer columns and helpful indexes are enough to keep moving.

### Quick sanity checks

* **Structure** tab for each table: confirm PK/AI and indexes are present.
* **Insert** a sample `company` then a `contact` with its `company_id` set to that `company.id`.
* **Insert** a `project` and confirm the **unique `slug`** check works.

### Export, Stage, Commit, and Sync

Remember to run `./dump-mysql.sh`, stage and commit the changes with a sensible commit message such as "Discovery Project 7-3", and sync the commits.

## Discovery Project 7-4

### Goal

In **phpMyAdmin**, we'll create three more tables in database **`crm`**:

* `users`
* `profiles`
* `project_members`

We’re still **not** adding foreign key constraints yet, but we’ll include columns and indexes that prepare for them.

### Before you start (one-time prefs)

1. Log into phpMyAdmin → left sidebar: click **crm** to select the database.
2. Top bar → **Operations** → confirm **Collation** is `utf8mb4_general_ci` (or `utf8mb4_unicode_ci`) and **Storage Engine** is `InnoDB`. Save.

### Table 1: `users`

**Columns**

* `id` INT UNSIGNED, **AI**, **PK**
* `email` VARCHAR(255) **NOT NULL**
* `password_hash` VARCHAR(255) **NOT NULL**
* `display_name` VARCHAR(100) **NOT NULL**
* `role` ENUM('admin','staff','client') **NOT NULL** DEFAULT 'staff'
* `active` BIT **NOT NULL**
* `created_at` DATETIME **NOT NULL** (Default: `CURRENT_TIMESTAMP`)
* `updated_at` DATETIME **NOT NULL** (Default: `CURRENT_TIMESTAMP` → on update: `CURRENT_TIMESTAMP`)
* `deleted_at` DATETIME NULL

**Indexes**

* **UNIQUE** on `email`

**phpMyAdmin steps**

1. With **crm** selected → **Structure → Create table** → name: `users`.
2. Add the columns above. Mark `id` as **A_I** and **Primary**.
3. In **Indexes**, set **UNIQUE** on `email`.
4. Save.

### Table 2: `profiles`

> One-to-one with `users`. We’ll model this by making `user_id` the **primary key** for `profiles`. (No FK yet.)

**Columns**

* `user_id` INT UNSIGNED **NOT NULL**  ← will reference `users.id` later
* `bio_md` TEXT NULL
* `website_url` VARCHAR(255) NULL
* `location` VARCHAR(120) NULL
* `avatar_url` VARCHAR(255) NULL

**Indexes**

* Set **PRIMARY KEY** on `user_id` (this enforces the 1:1 shape even before FKs).

**phpMyAdmin steps**

1. **Structure → Create table** → name: `profiles`.
2. Add the columns above.
3. In **Indexes**, set **PRIMARY** on `user_id`.
4. Save.

### Table 3: `project_members`

> Join/bridge table for a many-to-many between `projects` and `users`.
> (You created `projects` in 7-1; we’ll link them later with FKs.)

**Columns**

* `project_id` INT UNSIGNED **NOT NULL**  ← will reference `projects.id` later
* `user_id` INT UNSIGNED **NOT NULL**     ← will reference `users.id` later
* `role` ENUM('owner','collaborator','viewer') **NOT NULL** DEFAULT 'collaborator'
* `added_at` DATETIME **NOT NULL** (Default: `CURRENT_TIMESTAMP`)

**Indexes**

* **PRIMARY KEY** on the **composite** (`project_id`, `user_id`)
  (This prevents duplicate memberships and is ideal even before we add FKs.)

**phpMyAdmin steps**

1. **Structure → Create table** → name: `project_members`.
2. Add the columns above.
3. In **Indexes**, choose **PRIMARY** and select both `project_id` and `user_id` for a composite PK.
4. Save.

### How they relate (conceptually, without constraints yet)

* **Users ↔ Profiles**: one-to-one. Each user has at most one profile. (`profiles.user_id` → `users.id`)
  Setting `profiles.user_id` as **PRIMARY KEY** enforces “one profile per user” shape now; we’ll add the FK later.
* **Projects ↔ Users** via **project_members**: many-to-many.
  A project can have many users; a user can be on many projects. The composite primary key on (`project_id`,`user_id`) prevents duplicates. Later we’ll add FKs to `projects.id` and `users.id`.

### Quick sanity checks

* In **Structure** for `profiles`, confirm **Primary** is on `user_id` (no auto-increment here).
* In **Structure** for `project_members`, confirm **Primary** shows both `project_id` and `user_id`.
* Insert a sample **user**, then a **profile** using that user’s `id` as `user_id`.
* Insert a sample **project** (from 7-1), then add a **project_members** row linking the project and user.

We’ll wire up **foreign keys** (and cascade rules) next, once all participating tables are in place.

### Export, Stage, Commit, and Sync

Remember to run `./dump-mysql.sh`, stage and commit the changes with a sensible commit message such as "Discovery Project 7-4", and sync the commits.

## Discovery Project 7-5

### Goal

In **phpMyAdmin**, we'll create our remaining five tables in database **`crm`**:

* `activities`
* `posts`
* `follows`
* `comments`
* `reactions`

We’re still **not** adding foreign key constraints yet. We’ll include the columns that will become keys and add helpful indexes.

### Before you start (one-time prefs)

1. Log into phpMyAdmin → left sidebar: click **crm**.
2. Top bar → **Operations** → confirm **Collation** `utf8mb4_general_ci` (or `utf8mb4_unicode_ci`) and **Storage Engine** `InnoDB`. Save.

### Table 1: `activities`

> CRM to-dos connected to contacts and owned by a user.

**Columns**

* `id` INT UNSIGNED, **AI**, **PK**
* `contact_id` INT UNSIGNED **NOT NULL**  ← will reference `contacts.id` later
* `user_id` INT UNSIGNED NULL             ← will reference `users.id` later (owner; allow NULL to preserve history if a user is deleted later)
* `type` ENUM('call','email','meeting','task') **NOT NULL**
* `subject` VARCHAR(150) **NOT NULL**
* `due_at` DATETIME NULL
* `completed_at` DATETIME NULL
* `notes_md` TEXT NULL
* `created_at` DATETIME **NOT NULL** DEFAULT `CURRENT_TIMESTAMP`

**Indexes (no FKs yet)**

* **INDEX** on `contact_id`
* **INDEX** on `user_id`
* (Optional) **INDEX** on `due_at` for reminders views

**phpMyAdmin steps**

1. **Structure → Create table** → name: `activities`.
2. Add columns above; mark `id` as **A_I** and **Primary**.
3. In **Indexes**, add **INDEX** on `contact_id`, **INDEX** on `user_id`.
4. Save.

### Table 2: `posts`

> Project updates written by a user on a project.

**Columns**

* `id` INT UNSIGNED, **AI**, **PK**
* `project_id` INT UNSIGNED **NOT NULL**   ← will reference `projects.id` later
* `author_id` INT UNSIGNED NULL            ← will reference `users.id` later (allow NULL to preserve orphaned posts if user removed)
* `activity_id` INT UNSIGNED NULL          ← will reference `activities.id` later (allow NULL because not all posts will relate to activities)
* `body_md` TEXT **NOT NULL**
* `created_at` DATETIME **NOT NULL** DEFAULT `CURRENT_TIMESTAMP`
* `updated_at` DATETIME NULL
* `visibility` ENUM('public','project','private') **NOT NULL** DEFAULT 'project'

**Indexes**

* **INDEX** on `project_id`
* **INDEX** on `author_id`
* **INDEX** on `activity_id`
* (Optional) **INDEX** on `(project_id, created_at)` for feeds

**phpMyAdmin steps**

1. **Structure → Create table** → name: `posts`.
2. Add columns; set `id` as **A_I** and **Primary**.
3. Add indexes on `project_id`, `author_id`, and `activity_id`.
4. Save.

### Table 3: `follows`

> Social graph: who follows whom.

**Columns**

* `follower_id` INT UNSIGNED **NOT NULL**  ← will reference `users.id` later
* `followee_id` INT UNSIGNED **NOT NULL**  ← will reference `users.id` later
* `created_at` DATETIME **NOT NULL** DEFAULT `CURRENT_TIMESTAMP`

**Indexes**

* **PRIMARY KEY** on composite (`follower_id`, `followee_id`) to prevent duplicates
* (Optional) **INDEX** on `followee_id` to query “who follows me?”

**phpMyAdmin steps**

1. **Structure → Create table** → name: `follows`.
2. Add columns.
3. In **Indexes**, set **PRIMARY** and select both `follower_id` + `followee_id` (composite).
4. (Optional) add **INDEX** on `followee_id`.
5. Save.

### Table 4: `comments`

> Comments attached to posts.

**Columns**

* `id` INT UNSIGNED, **AI**, **PK**
* `post_id` INT UNSIGNED **NOT NULL**     ← will reference `posts.id` later
* `author_id` INT UNSIGNED NULL           ← will reference `users.id` later
* `body_md` TEXT **NOT NULL**
* `created_at` DATETIME **NOT NULL** DEFAULT `CURRENT_TIMESTAMP`

**Indexes**

* **INDEX** on `post_id`
* **INDEX** on `author_id`

**phpMyAdmin steps**

1. **Structure → Create table** → name: `comments`.
2. Add columns; set `id` as **A_I** and **Primary**.
3. Add indexes on `post_id` and `author_id`.
4. Save.

### Table 5: `reactions`

> Simple reactions to either a post **or** a comment.

**Columns**

* `id` INT UNSIGNED, **AI**, **PK**
* `user_id` INT UNSIGNED **NOT NULL**      ← will reference `users.id` later
* `post_id` INT UNSIGNED NULL              ← will reference `posts.id` later
* `comment_id` INT UNSIGNED NULL           ← will reference `comments.id` later
* `type` ENUM('like','celebrate','insight') **NOT NULL**

**Indexes**

* **INDEX** on `user_id`
* **INDEX** on `post_id`
* **INDEX** on `comment_id`

*Note:* We won’t enforce the “exactly one of (`post_id`,`comment_id`) must be non-NULL” rule yet; we’ll address it later.

**phpMyAdmin steps**

1. **Structure → Create table** → name: `reactions`.
2. Add columns; set `id` as **A_I** and **Primary**.
3. Add indexes on `user_id`, `post_id`, `comment_id`.
4. Save.

### How they relate (conceptually, without constraints yet)

* **Activities → Contacts** (many-to-one): `activities.contact_id` → `contacts.id`
  **Activities → Users** (many-to-one): `activities.user_id` → `users.id`
* **Posts → Projects**, **Posts → Users**, and **Posts → Activities** (many-to-one): `posts.project_id` → `projects.id`, `posts.author_id` → `users.id`, `posts.activity_id` → `activities.id`
* **Comments → Posts** and **Comments → Users** (many-to-one): `comments.post_id` → `posts.id`, `comments.author_id` → `users.id`
* **Follows** is a self-referencing join: (`follower_id` → `users.id`, `followee_id` → `users.id`)
* **Reactions**: one user reacts to **either** a post or a comment.

We’ll add the actual **foreign key constraints** (and cascade rules) after all tables are in place.

### Quick sanity checks

* Insert a **post** and a few **comments**; verify indexes show up under **Structure → Indexes**.
* Insert a few **follows** rows; ensure duplicates aren’t allowed (composite PK).
* Insert a **reaction** with only `post_id` populated, then another with only `comment_id`; both should insert successfully.
* Insert an **activity** and later set `completed_at` to confirm your workflow fields behave as expected.

### Export, Stage, Commit, and Sync

Remember to run `./dump-mysql.sh`, stage and commit the changes with a sensible commit message such as "Discovery Project 7-5", and sync the commits.

## Discovery Project 7-6

### Goal

In **phpMyAdmin**, add **foreign key constraints** in database **`crm`** to connect the tables you created in 7-1, 7-2, and 7-3.

We’ll set sensible **ON DELETE/ON UPDATE** rules to preserve data integrity and match the use-cases we’ve outlined.

### Before you start (one-time checks)

1. **Select DB:** Log into phpMyAdmin → left sidebar → click **crm**.
2. **Storage engine:** Confirm all tables use **InnoDB** (Structure tab shows “InnoDB”). If any table is MyISAM, use **Operations → Storage Engine → InnoDB → Go**.
3. **Column types must match:** FK columns and referenced PK columns must be the same type/unsigned/length (e.g., `INT UNSIGNED` → `INT UNSIGNED`).
4. **Indexes:** phpMyAdmin will create needed indexes automatically when you add a FK, but it’s fine if you already added them.

### How to add a foreign key in phpMyAdmin (pattern)

For each relationship below, repeat this flow:

1. Left sidebar → click the **child** table (the table that has the FK column).
2. Top tabs → **Structure** → (near bottom) **Relation view** (or **Designer →** click the table and add relation).
3. In the **Foreign key constraints** section:

   * **Column:** choose the FK column (e.g., `company_id`).
   * **Referenced database:** `crm`.
   * **Referenced table:** choose the parent table (e.g., `companies`).
   * **Referenced column:** usually `id`.
   * **On delete:** set as listed below.
   * **On update:** set **CASCADE** (unless noted otherwise).
4. Click **Save**.
5. phpMyAdmin may ask to create an index—accept.

> Tip: If you don’t see **Relation view**, go to **Operations → Table options → Storage Engine = InnoDB**, save, then return.

---

### Relationships to create (with recommended actions)

#### Profiles → Users (1:1)

* **Table:** `profiles`
* **Column:** `user_id` → **users(id)**
* **On delete:** **CASCADE** (delete profile if user is removed)
* **On update:** **CASCADE**

#### Contacts → Companies (M:1)

* **Table:** `contacts`
* **Column:** `company_id` → **companies(id)**
* **On delete:** **SET NULL** (keep contact even if company is removed)
* **On update:** **CASCADE**

#### Projects → Users (owner) (M:1)

* **Table:** `projects`
* **Column:** `company_id` → **companies(id)**
* **On delete:** **SET NULL** (project can remain if company is removed)
* **On update:** **CASCADE**

#### Project Members (M:N via join table)

* **Table:** `project_members`
* **Column:** `project_id` → **projects(id)**

  * **On delete:** **CASCADE** (remove memberships when project goes)
  * **On update:** **CASCADE**
* **Column:** `user_id` → **users(id)**

  * **On delete:** **CASCADE** (remove memberships when user goes)
  * **On update:** **CASCADE**

#### Posts → Projects / Users / Activities (M:1)

* **Table:** `posts`
* **Column:** `project_id` → **projects(id)**

  * **On delete:** **CASCADE** (delete posts if project is deleted)
  * **On update:** **CASCADE**
* **Column:** `author_id` → **users(id)**

  * **On delete:** **SET NULL** (keep post; author may be gone)
  * **On update:** **CASCADE**
* **Column:** `activity_id` → **activities(id)**

  * **On delete:** **CASCADE** (delete posts if activity is deleted)
  * **On update:** **CASCADE**

#### Comments → Posts / Users (M:1)

* **Table:** `comments`
* **Column:** `post_id` → **posts(id)**

  * **On delete:** **CASCADE** (delete comments with their post)
  * **On update:** **CASCADE**
* **Column:** `author_id` → **users(id)**

  * **On delete:** **SET NULL** (keep comment for thread continuity)
  * **On update:** **CASCADE**

#### Activities → Contacts / Users (M:1)

* **Table:** `activities`
* **Column:** `contact_id` → **contacts(id)**

  * **On delete:** **CASCADE** (remove activities if contact is removed)
  * **On update:** **CASCADE**
* **Column:** `user_id` → **users(id)**

  * **On delete:** **SET NULL** (historical activities remain if owner leaves)
  * **On update:** **CASCADE**

#### Follows → Users (self-join)

* **Table:** `follows`
* **Column:** `follower_id` → **users(id)**

  * **On delete:** **CASCADE** (clean edges when user goes)
  * **On update:** **CASCADE**
* **Column:** `followee_id` → **users(id)**

  * **On delete:** **CASCADE**
  * **On update:** **CASCADE**

#### Reactions → Users / Posts / Comments (M:1)

* **Table:** `reactions`
* **Column:** `user_id` → **users(id)**

  * **On delete:** **CASCADE** (remove reactions if user is removed)
  * **On update:** **CASCADE**
* **Column:** `post_id` → **posts(id)**

  * **On delete:** **CASCADE** (remove reactions if post goes)
  * **On update:** **CASCADE**
* **Column:** `comment_id` → **comments(id)**

  * **On delete:** **CASCADE** (remove reactions if comment goes)
  * **On update:** **CASCADE**

> Note: `reactions.post_id` and `reactions.comment_id` are both nullable. We’ll enforce “exactly one is non-NULL” later.

### Quick sanity checks

* **Designer** view: verify relationship lines appear between the tables.
* Try deleting a **company** with contacts:

  * The **contacts.company_id** should become **NULL** (no delete).
* Try deleting a **project**: its **posts** (and their **comments** and **reactions**) should be removed via CASCADE.
* Try deleting a **user**:

  * Their **profile** should be deleted (CASCADE).
  * Their **project_members** links and **follows** edges should be deleted (CASCADE).
  * Their authored **posts/comments** should remain but with **author_id = NULL**.
  * Their **activities** ownership should become **NULL**.
  * Their **reactions** should be deleted (CASCADE).

### Common errors & fixes

* **Cannot add foreign key constraint (errno 150/1215):**
  Types don’t match (e.g., `INT` vs `INT UNSIGNED`), engine isn’t InnoDB, or collations differ. Align types/unsigned and ensure InnoDB.
* **Index needed:**
  phpMyAdmin will prompt to create; accept the prompt.
* **Existing orphaned rows:**
  If child rows contain values that don’t exist in the parent, the FK add will fail. Clean or temporarily set the column to NULL, then add the FK.

Once these are in place, your schema has enforceable integrity that matches the application’s behavior.

### Export, Stage, Commit, and Sync

Remember to run `./dump-mysql.sh`, stage and commit the changes with a sensible commit message such as "Discovery Project 7-6", and sync the commits.

## Discovery Project 7-7

This one is all about adding the **remaining constraints** (primarily `CHECK` constraints) so MySQL enforces more of your business rules. Because phpMyAdmin doesn’t have a GUI for `CHECK`, we'll just copy and paste the provided SQL.

### Goal

In **phpMyAdmin → crm → SQL**, run the statements below to add constraints that ensure:

* Names/titles aren’t empty strings
* Emails are lowercase and (lightly) validated
* Timestamps make sense (e.g., `updated_at ≥ created_at`)
* Users can’t follow themselves
* A reaction targets **either** a post **or** a comment (not both / not neither)
* Slugs stick to a safe pattern

> Works on **MySQL 8.0.16+** (where `CHECK` constraints are enforced). If you’re on an older server, MySQL parses but ignores `CHECK`; you can still run these now and they’ll work when you upgrade.

---

### Step 1 — Run these SQL statements

> In phpMyAdmin: select **crm** → **SQL** → paste everything below → **Go**.

```sql
/* ---------------------------
   COMPANIES
   --------------------------- */
ALTER TABLE companies
  ADD CONSTRAINT chk_companies_name_nonempty
  CHECK (name <> '');

/* ---------------------------
   CONTACTS
   --------------------------- */
ALTER TABLE contacts
  ADD CONSTRAINT chk_contacts_names_nonempty
    CHECK (first_name <> '' AND last_name <> ''),
  ADD CONSTRAINT chk_contacts_email_format
    CHECK (email IS NULL OR email REGEXP '^[^@\\s]+@[^@\\s]+\\.[^@\\s]+$');

/* ---------------------------
   USERS
   --------------------------- */
/* Keep emails stored in lowercase for consistency */
ALTER TABLE users
  ADD CONSTRAINT chk_users_email_lower
    CHECK (email = LOWER(email));

/* ---------------------------
   PROJECTS
   --------------------------- */
ALTER TABLE projects
  ADD CONSTRAINT chk_projects_title_nonempty
    CHECK (title <> ''),
  /* enforce friendly slugs: lowercase letters, digits, hyphens */
  ADD CONSTRAINT chk_projects_slug_pattern
    CHECK (slug REGEXP '^[a-z0-9-]+$');

/* ---------------------------
   POSTS
   --------------------------- */
ALTER TABLE posts
  /* body cannot be empty or all whitespace */
  ADD CONSTRAINT chk_posts_body_nonempty
    CHECK (LENGTH(TRIM(body_md)) > 0),
  /* updated_at must not precede created_at */
  ADD CONSTRAINT chk_posts_updated_after_created
    CHECK (updated_at IS NULL OR updated_at >= created_at);

/* ---------------------------
   COMMENTS
   --------------------------- */
ALTER TABLE comments
  ADD CONSTRAINT chk_comments_body_nonempty
    CHECK (LENGTH(TRIM(body_md)) > 0);

/* ---------------------------
   ACTIVITIES
   --------------------------- */
ALTER TABLE activities
  ADD CONSTRAINT chk_activities_subject_nonempty
    CHECK (subject <> ''),
  /* due_at and completed_at cannot be before created_at */
  ADD CONSTRAINT chk_activities_due_after_created
    CHECK (due_at IS NULL OR due_at >= created_at),
  ADD CONSTRAINT chk_activities_completed_after_created
    CHECK (completed_at IS NULL OR completed_at >= created_at);

/* ---------------------------
   FOLLOWS
   --------------------------- */
/* prevent a user from following themselves */
ALTER TABLE follows
  ADD CONSTRAINT chk_follows_not_self
    CHECK (follower_id <> followee_id);

/* ---------------------------
   REACTIONS
   --------------------------- */
/* exactly one of (post_id, comment_id) must be non-NULL */
ALTER TABLE reactions
  ADD CONSTRAINT chk_reactions_exactly_one_target
    CHECK ( (post_id IS NULL) <> (comment_id IS NULL) );
```

---

### Step 2 — What each constraint does (teach-through)

* **`chk_companies_name_nonempty`**: Disallows `''` (empty string) for company names. `NOT NULL` alone would still allow `''`; this prevents that.
* **`chk_contacts_names_nonempty`**: Requires both `first_name` and `last_name` to be non-empty strings (again, beyond `NOT NULL`).
* **`chk_contacts_email_format`**: If `email` is provided, it must vaguely look like `local@domain.tld`. It’s intentionally simple—more validation belongs in PHP.
* **`chk_users_email_lower`**: Enforces canonical lowercase storage so lookups/uniques behave consistently (e.g., `FOO@EXAMPLE.COM` becomes invalid if not lowercase).
* **`chk_projects_title_nonempty`**: Prevents empty project titles.
* **`chk_projects_slug_pattern`**: Slug must match `^[a-z0-9-]+$` (lowercase letters, digits, hyphens). Good for URLs and consistent with future routing.
* **`chk_posts_body_nonempty`** / **`chk_comments_body_nonempty`**: Require real content after trimming whitespace.
* **`chk_posts_updated_after_created`**: `updated_at` can be `NULL`, but if present it cannot be earlier than `created_at`.
* **`chk_activities_subject_nonempty`**: Activities must have a subject.
* **`chk_activities_due_after_created`** / **`chk_activities_completed_after_created`**: Due/completed timestamps, when provided, cannot precede creation time.
* **`chk_follows_not_self`**: Blocks self-follows (even though the composite PK already prevents duplicates).
* **`chk_reactions_exactly_one_target`**: Enforces XOR: a reaction is **either** for a post **or** for a comment (not both, not neither).

---

### Step 3 — Quick validation ideas

* Try inserting a `follow` with the same follower and followee → should fail with a check error.
* Insert a `reaction` with both `post_id` and `comment_id` set (or both `NULL`) → should fail.
* Insert a `project` with an uppercase or underscored slug (e.g., `My_Project`) → should fail.
* Insert a `user` with mixed-case email → should fail unless it’s lowercase.
* Update a `post` so `updated_at` is before `created_at` → should fail.

---

### Notes and trade-offs (for discussion)

* **Email validation**: We keep it intentionally loose in SQL; do stricter checks in PHP.
* **Uniqueness of reactions** (e.g., one reaction per user per target) isn’t added here because MySQL can’t do **partial unique** indexes without extra modeling (generated columns or triggers). We’ll revisit if needed.
* If your server is **older than MySQL 8.0.16**, `CHECK` constraints are parsed but not enforced. Your INSERT/UPDATEs will still succeed. In that case, lean on PHP validation until your environment is 8.0.16+.

### Export, Stage, Commit, and Sync

Remember to run `./dump-mysql.sh`, stage and commit the changes with a sensible commit message such as "Discovery Project 7-7", and sync the commits.
