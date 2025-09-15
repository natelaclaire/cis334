<?php
declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;
use LogicException;

final class Project
{
    private int $id;
    private ?int $ownerId;
    private string $slug;
    private string $title;
    private ?string $summaryMd;
    private string $status;
    private string $visibility;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    /** @param array<string,mixed> $row */
    public static function fromArray(array $row): self
    {
        $obj = new self();
        $obj->id = array_key_exists('id', $row) ? (int)$row['id'] : 0;
        $obj->ownerId = array_key_exists('owner_id', $row) ? ($row['owner_id'] === null ? null : (int)$row['owner_id']) : null;
        $obj->slug = array_key_exists('slug', $row) ? (string)$row['slug'] : '';
        $obj->title = array_key_exists('title', $row) ? (string)$row['title'] : '';
        $obj->summaryMd = array_key_exists('summary_md', $row) ? ($row['summary_md'] === null ? null : (string)$row['summary_md']) : null;
        $obj->status = array_key_exists('status', $row) ? (string)$row['status'] : '';
        $obj->visibility = array_key_exists('visibility', $row) ? (string)$row['visibility'] : '';
        $obj->createdAt = isset($row['created_at']) ? new DateTimeImmutable((string)$row['created_at']) : new DateTimeImmutable('now');
        $obj->updatedAt = isset($row['updated_at']) ? new DateTimeImmutable((string)$row['updated_at']) : new DateTimeImmutable('now');
        return $obj;
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'owner_id' => $this->ownerId,
            'slug' => $this->slug,
            'title' => $this->title,
            'summary_md' => $this->summaryMd,
            'status' => $this->status,
            'visibility' => $this->visibility,
            'created_at' => $this->createdAt->format('c'),
            'updated_at' => $this->updatedAt->format('c'),
        ];
    }

    /** @return string[] */
    public function validate(): array
    {
        $errors = [];
        if ($this->slug === '') { $errors[] = 'Slug is required.'; }
        if (!in_array($this->status, ['active','paused','archived'], true)) { $errors[] = 'Invalid project status.'; }
        if (!in_array($this->visibility, ['public','private','unlisted'], true)) { $errors[] = 'Invalid project visibility.'; }
        return $errors;
    }

    /** @return int[] */
    public function getMemberIds(): array { return []; } // repository will populate later
    public function isMember(int $userId): bool { return in_array($userId, $this->getMemberIds(), true); }

    /** @return static|null */
    public static function findById(int $id): ?self { throw new LogicException('Not implemented: wire to PDO.'); }
    /** @return static[] */
    public static function search(array $filters): array { throw new LogicException('Not implemented: wire to PDO.'); }
    public function save(): void { throw new LogicException('Not implemented: wire to PDO.'); }
    public function delete(): void { throw new LogicException('Not implemented: wire to PDO.'); }

    public function getId(): int { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }

    public function getOwnerId(): ?int { return $this->ownerId; }
    public function setOwnerId(?int $ownerId): void { $this->ownerId = $ownerId; }

    public function getSlug(): string { return $this->slug; }
    public function setSlug(string $slug): void { $this->slug = $slug; }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): void { $this->title = $title; }

    public function getSummaryMd(): ?string { return $this->summaryMd; }
    public function setSummaryMd(?string $summaryMd): void { $this->summaryMd = $summaryMd; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): void { $this->status = $status; }

    public function getVisibility(): string { return $this->visibility; }
    public function setVisibility(string $visibility): void { $this->visibility = $visibility; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): void { $this->createdAt = $createdAt; }

    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): void { $this->updatedAt = $updatedAt; }

}