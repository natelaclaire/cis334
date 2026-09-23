<?php
declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;
use LogicException;

final class Activity
{
    private int $id;
    private int $contactId;
    private ?int $userId;
    private string $type;
    private string $subject;
    private ?\DateTimeImmutable $dueAt;
    private ?\DateTimeImmutable $completedAt;
    private ?string $notesMd;
    private \DateTimeImmutable $createdAt;

    /** @param array<string,mixed> $row */
    public static function fromArray(array $row): self
    {
        $obj = new self();
        $obj->id = array_key_exists('id', $row) ? (int)$row['id'] : 0;
        $obj->contactId = array_key_exists('contact_id', $row) ? (int)$row['contact_id'] : 0;
        $obj->userId = array_key_exists('user_id', $row) ? ($row['user_id'] === null ? null : (int)$row['user_id']) : null;
        $obj->type = array_key_exists('type', $row) ? (string)$row['type'] : '';
        $obj->subject = array_key_exists('subject', $row) ? (string)$row['subject'] : '';
        $obj->dueAt = (isset($row['due_at']) && $row['due_at'] !== null) ? new DateTimeImmutable((string)$row['due_at']) : null;
        $obj->completedAt = (isset($row['completed_at']) && $row['completed_at'] !== null) ? new DateTimeImmutable((string)$row['completed_at']) : null;
        $obj->notesMd = array_key_exists('notes_md', $row) ? ($row['notes_md'] === null ? null : (string)$row['notes_md']) : null;
        $obj->createdAt = isset($row['created_at']) ? new DateTimeImmutable((string)$row['created_at']) : new DateTimeImmutable('now');
        return $obj;
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'contact_id' => $this->contactId,
            'user_id' => $this->userId,
            'type' => $this->type,
            'subject' => $this->subject,
            'due_at' => $this->dueAt ? $this->dueAt->format('c') : null,
            'completed_at' => $this->completedAt ? $this->completedAt->format('c') : null,
            'notes_md' => $this->notesMd,
            'created_at' => $this->createdAt->format('c'),
        ];
    }

    /** @return string[] */
    public function validate(): array
    {
        $errors = [];
        if (!in_array($this->type, ['call','email','meeting','task'], true)) { $errors[] = 'Invalid activity type.'; }
        if ($this->subject === '') { $errors[] = 'Activity subject is required.'; }
        return $errors;
    }

    public function isCompleted(): bool { return $this->completedAt !== null; }

    /** @return static|null */
    public static function findById(int $id): ?self { throw new LogicException('Not implemented: wire to PDO.'); }
    /** @return static[] */
    public static function search(array $filters): array { throw new LogicException('Not implemented: wire to PDO.'); }
    public function save(): void { throw new LogicException('Not implemented: wire to PDO.'); }
    public function delete(): void { throw new LogicException('Not implemented: wire to PDO.'); }

    public function getId(): int { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }

    public function getContactId(): int { return $this->contactId; }
    public function setContactId(int $contactId): void { $this->contactId = $contactId; }

    public function getUserId(): ?int { return $this->userId; }
    public function setUserId(?int $userId): void { $this->userId = $userId; }

    public function getType(): string { return $this->type; }
    public function setType(string $type): void { $this->type = $type; }

    public function getSubject(): string { return $this->subject; }
    public function setSubject(string $subject): void { $this->subject = $subject; }

    public function getDueAt(): ?\DateTimeImmutable { return $this->dueAt; }
    public function setDueAt(?\DateTimeImmutable $dueAt): void { $this->dueAt = $dueAt; }

    public function getCompletedAt(): ?\DateTimeImmutable { return $this->completedAt; }
    public function setCompletedAt(?\DateTimeImmutable $completedAt): void { $this->completedAt = $completedAt; }

    public function getNotesMd(): ?string { return $this->notesMd; }
    public function setNotesMd(?string $notesMd): void { $this->notesMd = $notesMd; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): void { $this->createdAt = $createdAt; }

}