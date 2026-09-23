<?php
declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;
use LogicException;

final class Contact
{
    private int $id;
    private ?int $companyId;
    private string $firstName;
    private string $lastName;
    private ?string $email;
    private ?string $phone;
    private ?string $title;
    private ?string $notesMd;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    /** @param array<string,mixed> $row */
    public static function fromArray(array $row): self
    {
        $obj = new self();
        $obj->id = array_key_exists('id', $row) ? (int)$row['id'] : 0;
        $obj->companyId = array_key_exists('company_id', $row) ? ($row['company_id'] === null ? null : (int)$row['company_id']) : null;
        $obj->firstName = array_key_exists('first_name', $row) ? (string)$row['first_name'] : '';
        $obj->lastName = array_key_exists('last_name', $row) ? (string)$row['last_name'] : '';
        $obj->email = array_key_exists('email', $row) ? ($row['email'] === null ? null : (string)$row['email']) : null;
        $obj->phone = array_key_exists('phone', $row) ? ($row['phone'] === null ? null : (string)$row['phone']) : null;
        $obj->title = array_key_exists('title', $row) ? ($row['title'] === null ? null : (string)$row['title']) : null;
        $obj->notesMd = array_key_exists('notes_md', $row) ? ($row['notes_md'] === null ? null : (string)$row['notes_md']) : null;
        $obj->createdAt = isset($row['created_at']) ? new DateTimeImmutable((string)$row['created_at']) : new DateTimeImmutable('now');
        $obj->updatedAt = isset($row['updated_at']) ? new DateTimeImmutable((string)$row['updated_at']) : new DateTimeImmutable('now');
        return $obj;
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->companyId,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'phone' => $this->phone,
            'title' => $this->title,
            'notes_md' => $this->notesMd,
            'created_at' => $this->createdAt->format('c'),
            'updated_at' => $this->updatedAt->format('c'),
        ];
    }

    /** @return string[] */
    public function validate(): array
    {
        $errors = [];
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

    public function getCompanyId(): ?int { return $this->companyId; }
    public function setCompanyId(?int $companyId): void { $this->companyId = $companyId; }

    public function getFirstName(): string { return $this->firstName; }
    public function setFirstName(string $firstName): void { $this->firstName = $firstName; }

    public function getLastName(): string { return $this->lastName; }
    public function setLastName(string $lastName): void { $this->lastName = $lastName; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): void { $this->email = $email; }

    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(?string $phone): void { $this->phone = $phone; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(?string $title): void { $this->title = $title; }

    public function getNotesMd(): ?string { return $this->notesMd; }
    public function setNotesMd(?string $notesMd): void { $this->notesMd = $notesMd; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): void { $this->createdAt = $createdAt; }

    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): void { $this->updatedAt = $updatedAt; }

}