<?php
declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;
use LogicException;

final class User
{
    private int $id;
    private string $email;
    private string $passwordHash;
    private string $displayName;
    private string $role;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;
    private ?\DateTimeImmutable $deletedAt;

    /** @param array<string,mixed> $row */
    public static function fromArray(array $row): self
    {
        $obj = new self();
        $obj->id = array_key_exists('id', $row) ? (int)$row['id'] : 0;
        $obj->email = array_key_exists('email', $row) ? (string)$row['email'] : '';
        $obj->passwordHash = array_key_exists('password_hash', $row) ? (string)$row['password_hash'] : '';
        $obj->displayName = array_key_exists('display_name', $row) ? (string)$row['display_name'] : '';
        $obj->role = array_key_exists('role', $row) ? (string)$row['role'] : '';
        $obj->createdAt = isset($row['created_at']) ? new DateTimeImmutable((string)$row['created_at']) : new DateTimeImmutable('now');
        $obj->updatedAt = isset($row['updated_at']) ? new DateTimeImmutable((string)$row['updated_at']) : new DateTimeImmutable('now');
        $obj->deletedAt = (isset($row['deleted_at']) && $row['deleted_at'] !== null) ? new DateTimeImmutable((string)$row['deleted_at']) : null;
        return $obj;
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'password_hash' => $this->passwordHash,
            'display_name' => $this->displayName,
            'role' => $this->role,
            'created_at' => $this->createdAt->format('c'),
            'updated_at' => $this->updatedAt->format('c'),
            'deleted_at' => $this->deletedAt ? $this->deletedAt->format('c') : null,
        ];
    }

    /** @return string[] */
    public function validate(): array
    {
        $errors = [];
        if ($this->email === '' || !filter_var($this->email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Valid email required.'; }
        if ($this->displayName === '') { $errors[] = 'Display name is required.'; }
        if (!in_array($this->role, ['admin','staff','client'], true)) { $errors[] = 'Role must be admin|staff|client.'; }
        return $errors;
    }

    /** @return static|null */
    public static function findById(int $id): ?self { throw new LogicException('Not implemented: wire to PDO.'); }
    /** @return static[] */
    public static function search(array $filters): array { throw new LogicException('Not implemented: wire to PDO.'); }
    public function save(): void { throw new LogicException('Not implemented: wire to PDO.'); }
    public function delete(): void { throw new LogicException('Not implemented: wire to PDO.'); }

    public function getId(): int { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): void { $this->email = $email; }

    public function getPasswordHash(): string { return $this->passwordHash; }
    public function setPasswordHash(string $passwordHash): void { $this->passwordHash = $passwordHash; }

    public function getDisplayName(): string { return $this->displayName; }
    public function setDisplayName(string $displayName): void { $this->displayName = $displayName; }

    public function getRole(): string { return $this->role; }
    public function setRole(string $role): void { $this->role = $role; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): void { $this->createdAt = $createdAt; }

    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): void { $this->updatedAt = $updatedAt; }

    public function getDeletedAt(): ?\DateTimeImmutable { return $this->deletedAt; }
    public function setDeletedAt(?\DateTimeImmutable $deletedAt): void { $this->deletedAt = $deletedAt; }

}