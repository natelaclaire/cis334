<?php
declare(strict_types=1);

/**
 * CIS 334 – Discovery Project 1
 * Model Stub Generator
 *
 * Usage:
 *   php generate_models.php
 *
 * Output:
 *   app/Models/*.php
 *   docs/mapping.md
 *   README.md
 */

$baseDir   = __DIR__;
$modelsDir = $baseDir . '/app/Models';
$docsDir   = $baseDir . '/docs';

@mkdir($modelsDir, 0777, true);
@mkdir($docsDir, 0777, true);

$now = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format(DATE_ATOM);

/**
 * Schema definition:
 *   [ propertyName, phpType, nullable, dbColumn ]
 * phpType may be: int, string, \DateTimeImmutable
 */
$classes = [
    'User' => [
        ['id','int',false,'id'],
        ['email','string',false,'email'],
        ['passwordHash','string',false,'password_hash'],
        ['displayName','string',false,'display_name'],
        ['role','string',false,'role'], // admin|staff|client
        ['createdAt','\DateTimeImmutable',false,'created_at'],
        ['updatedAt','\DateTimeImmutable',false,'updated_at'],
        ['deletedAt','\DateTimeImmutable',true,'deleted_at'],
    ],
    'Profile' => [
        ['userId','int',false,'user_id'],
        ['bioMd','string',false,'bio_md'],
        ['websiteUrl','string',true,'website_url'],
        ['location','string',true,'location'],
        ['avatarUrl','string',true,'avatar_url'],
    ],
    'Company' => [
        ['id','int',false,'id'],
        ['name','string',false,'name'],
        ['websiteUrl','string',true,'website_url'],
        ['industry','string',true,'industry'],
        ['notesMd','string',true,'notes_md'],
        ['createdAt','\DateTimeImmutable',false,'created_at'],
        ['updatedAt','\DateTimeImmutable',false,'updated_at'],
    ],
    'Contact' => [
        ['id','int',false,'id'],
        ['companyId','int',true,'company_id'],
        ['firstName','string',false,'first_name'],
        ['lastName','string',false,'last_name'],
        ['email','string',true,'email'],
        ['phone','string',true,'phone'],
        ['title','string',true,'title'],
        ['notesMd','string',true,'notes_md'],
        ['createdAt','\DateTimeImmutable',false,'created_at'],
        ['updatedAt','\DateTimeImmutable',false,'updated_at'],
    ],
    'Activity' => [
        ['id','int',false,'id'],
        ['contactId','int',false,'contact_id'],
        ['userId','int',true,'user_id'],
        ['type','string',false,'type'], // call|email|meeting|task
        ['subject','string',false,'subject'],
        ['dueAt','\DateTimeImmutable',true,'due_at'],
        ['completedAt','\DateTimeImmutable',true,'completed_at'],
        ['notesMd','string',true,'notes_md'],
        ['createdAt','\DateTimeImmutable',false,'created_at'],
    ],
    'Project' => [
        ['id','int',false,'id'],
        ['ownerId','int',true,'owner_id'],
        ['slug','string',false,'slug'],
        ['title','string',false,'title'],
        ['summaryMd','string',true,'summary_md'],
        ['status','string',false,'status'], // active|paused|archived
        ['visibility','string',false,'visibility'], // public|private|unlisted
        ['createdAt','\DateTimeImmutable',false,'created_at'],
        ['updatedAt','\DateTimeImmutable',false,'updated_at'],
    ],
    'ProjectMember' => [
        ['projectId','int',false,'project_id'],
        ['userId','int',false,'user_id'],
        ['role','string',false,'role'], // owner|collaborator|viewer
        ['addedAt','\DateTimeImmutable',false,'added_at'],
    ],
    'Post' => [
        ['id','int',false,'id'],
        ['projectId','int',false,'project_id'],
        ['authorId','int',true,'author_id'],
        ['bodyMd','string',false,'body_md'],
        ['createdAt','\DateTimeImmutable',false,'created_at'],
        ['updatedAt','\DateTimeImmutable',true,'updated_at'],
        ['visibility','string',false,'visibility'], // public|project|private
    ],
    'Comment' => [
        ['id','int',false,'id'],
        ['postId','int',false,'post_id'],
        ['authorId','int',true,'author_id'],
        ['bodyMd','string',false,'body_md'],
        ['createdAt','\DateTimeImmutable',false,'created_at'],
    ],
    'Follow' => [
        ['followerId','int',false,'follower_id'],
        ['followeeId','int',false,'followee_id'],
        ['createdAt','\DateTimeImmutable',false,'created_at'],
    ],
    'Reaction' => [
        ['id','int',false,'id'],
        ['userId','int',false,'user_id'],
        ['postId','int',true,'post_id'],
        ['commentId','int',true,'comment_id'],
        ['type','string',false,'type'], // like|celebrate|insight
    ],
];

function renderClass(string $name, array $fields): string {
    $usesDate = array_reduce($fields, fn($c,$f)=>$c||($f[1]==='\DateTimeImmutable'), false);
    $lines   = [];
    $lines[] = "<?php";
    $lines[] = "declare(strict_types=1);";
    $lines[] = "";
    $lines[] = "namespace App\\Models;";
    $lines[] = "";
    if ($usesDate) { $lines[] = "use DateTimeImmutable;"; }
    $lines[] = "use LogicException;";
    $lines[] = "";
    $lines[] = "final class {$name}";
    $lines[] = "{";
    foreach ($fields as [$prop,$type,$nullable]) {
        $t = $nullable ? "?{$type}" : $type;
        $lines[] = "    private {$t} \${$prop};";
    }
    $lines[] = "";
    $lines[] = "    /** @param array<string,mixed> \$row */";
    $lines[] = "    public static function fromArray(array \$row): self";
    $lines[] = "    {";
    $lines[] = "        \$obj = new self();";
    foreach ($fields as [$prop,$type,$nullable,$col]) {
        if ($type === '\\DateTimeImmutable') {
            $lines[] = $nullable
                ? "        \$obj->{$prop} = (isset(\$row['{$col}']) && \$row['{$col}'] !== null) ? new DateTimeImmutable((string)\$row['{$col}']) : null;"
                : "        \$obj->{$prop} = isset(\$row['{$col}']) ? new DateTimeImmutable((string)\$row['{$col}']) : new DateTimeImmutable('now');";
        } elseif ($type === 'string') {
            $lines[] = $nullable
                ? "        \$obj->{$prop} = array_key_exists('{$col}', \$row) ? (\$row['{$col}'] === null ? null : (string)\$row['{$col}']) : null;"
                : "        \$obj->{$prop} = array_key_exists('{$col}', \$row) ? (string)\$row['{$col}'] : '';";
        } elseif ($type === 'int') {
            $lines[] = $nullable
                ? "        \$obj->{$prop} = array_key_exists('{$col}', \$row) ? (\$row['{$col}'] === null ? null : (int)\$row['{$col}']) : null;"
                : "        \$obj->{$prop} = array_key_exists('{$col}', \$row) ? (int)\$row['{$col}'] : 0;";
        } else {
            $lines[] = "        \$obj->{$prop} = \$row['{$col}'] ?? null;";
        }
    }
    $lines[] = "        return \$obj;";
    $lines[] = "    }";
    $lines[] = "";
    $lines[] = "    /** @return array<string,mixed> */";
    $lines[] = "    public function toArray(): array";
    $lines[] = "    {";
    $lines[] = "        return [";
    foreach ($fields as [$prop,$type,$nullable,$col]) {
        if ($type === '\\DateTimeImmutable') {
            $lines[] = $nullable
                ? "            '{$col}' => \$this->{$prop} ? \$this->{$prop}->format('c') : null,"
                : "            '{$col}' => \$this->{$prop}->format('c'),";
        } else {
            $lines[] = "            '{$col}' => \$this->{$prop},";
        }
    }
    $lines[] = "        ];";
    $lines[] = "    }";
    $lines[] = "";
    $lines[] = "    /** @return string[] */";
    $lines[] = "    public function validate(): array";
    $lines[] = "    {";
    $lines[] = "        \$errors = [];";
    if ($name === 'User') {
        $lines[] = "        if (\$this->email === '' || !filter_var(\$this->email, FILTER_VALIDATE_EMAIL)) { \$errors[] = 'Valid email required.'; }";
        $lines[] = "        if (\$this->displayName === '') { \$errors[] = 'Display name is required.'; }";
        $lines[] = "        if (!in_array(\$this->role, ['admin','staff','client'], true)) { \$errors[] = 'Role must be admin|staff|client.'; }";
    }
    if ($name === 'Project') {
        $lines[] = "        if (\$this->slug === '') { \$errors[] = 'Slug is required.'; }";
        $lines[] = "        if (!in_array(\$this->status, ['active','paused','archived'], true)) { \$errors[] = 'Invalid project status.'; }";
        $lines[] = "        if (!in_array(\$this->visibility, ['public','private','unlisted'], true)) { \$errors[] = 'Invalid project visibility.'; }";
    }
    if ($name === 'Post') {
        $lines[] = "        if (\$this->bodyMd === '') { \$errors[] = 'Post body is required.'; }";
    }
    if ($name === 'Comment') {
        $lines[] = "        if (\$this->bodyMd === '') { \$errors[] = 'Comment body is required.'; }";
    }
    if ($name === 'Activity') {
        $lines[] = "        if (!in_array(\$this->type, ['call','email','meeting','task'], true)) { \$errors[] = 'Invalid activity type.'; }";
        $lines[] = "        if (\$this->subject === '') { \$errors[] = 'Activity subject is required.'; }";
    }
    $lines[] = "        return \$errors;";
    $lines[] = "    }";
    $lines[] = "";
    if ($name === 'Activity') {
        $lines[] = "    public function isCompleted(): bool { return \$this->completedAt !== null; }";
        $lines[] = "";
    }
    if ($name === 'Follow') {
        $lines[] = "    public function isSelfFollow(): bool { return \$this->followerId === \$this->followeeId; }";
        $lines[] = "";
    }
    if ($name === 'Project') {
        $lines[] = "    /** @return int[] */";
        $lines[] = "    public function getMemberIds(): array { return []; } // repository will populate later";
        $lines[] = "    public function isMember(int \$userId): bool { return in_array(\$userId, \$this->getMemberIds(), true); }";
        $lines[] = "";
    }
    if ($name === 'Post') {
        $lines[] = "    /** @return int[] */";
        $lines[] = "    public function getCommentIds(): array { return []; } // repository will populate later";
        $lines[] = "";
    }
    $lines[] = "    /** @return static|null */";
    $lines[] = "    public static function findById(int \$id): ?self { throw new LogicException('Not implemented: wire to PDO.'); }";
    $lines[] = "    /** @return static[] */";
    $lines[] = "    public static function search(array \$filters): array { throw new LogicException('Not implemented: wire to PDO.'); }";
    $lines[] = "    public function save(): void { throw new LogicException('Not implemented: wire to PDO.'); }";
    $lines[] = "    public function delete(): void { throw new LogicException('Not implemented: wire to PDO.'); }";
    $lines[] = "";
    foreach ($fields as [$prop,$type,$nullable]) {
        $t = $nullable ? "?{$type}" : $type;
        $suffix = ucfirst($prop);
        $lines[] = "    public function get{$suffix}(): {$t} { return \$this->{$prop}; }";
        $lines[] = "    public function set{$suffix}({$t} \${$prop}): void { \$this->{$prop} = \${$prop}; }";
        $lines[] = "";
    }
    $lines[] = "}";
    return implode("\n", $lines);
}

foreach ($classes as $name => $fields) {
    $code = renderClass($name, $fields);
    file_put_contents("{$modelsDir}/{$name}.php", $code);
}

$mapping = "# Class ↔ Table Mapping\n\n_Generated: {$now}_\n\n---\n\n";
foreach ($classes as $name => $fields) {
    $table = strtolower($name . 's');
    $mapping .= "## {$name} → `{$table}`\n\n";
    $mapping .= "| Property | Column | Type |\n|---|---|---|\n";
    foreach ($fields as [$prop,$type,$nullable,$col]) {
        $mapping .= "| `{$prop}` | `{$col}` | `" . ($nullable ? '?' : '') . "{$type}` |\n";
    }
    $mapping .= "\n";
}
file_put_contents("{$docsDir}/mapping.md", $mapping);

$readme = <<<MD
# CIS 334 – Discovery Project 1 Starter

This bundle contains PHP class stubs and a mapping document for the ERD you will design in Discovery Project 1.

- PHP version: 8.2+ recommended
- Namespace: `App\\Models`
- Location: `app/Models/*.php`

## What’s included
- One class per entity with:
  - private typed properties
  - `fromArray()`, `toArray()`, `validate()`
  - helper stubs (e.g., `isCompleted()`, `isSelfFollow()`, etc. where relevant)
  - PDO placeholders (`findById`, `search`, `save`, `delete`) that throw `LogicException`
- `docs/mapping.md` – class ↔ table mapping scaffold

## How to use
1. Complete your ERD per the assignment sheet.
2. If you add/rename fields, update the generator’s \$classes array and re-run.
3. In a later project, wire these to PDO repositories and real tables.
4. Replace placeholder exceptions with working persistence logic.

*Generated {$now}.*
MD;

file_put_contents("{$baseDir}/README.md", $readme);

echo "Done. Files created under app/Models and docs/.";
