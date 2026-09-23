<?php
declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;
use LogicException;

final class Post
{
    private int $id;
    private int $projectId;
    private ?int $authorId;
    private string $bodyMd;
    private \DateTimeImmutable $createdAt;
    private ?\DateTimeImmutable $updatedAt;
    private string $visibility;

    /** @param array<string,mixed> $row */
    public static function fromArray(array $row): self
    {
        $obj = new self();
        $obj->id = array_key_exists('id', $row) ? (int)$row['id'] : 0;
        $obj->projectId = array_key_exists('project_id', $row) ? (int)$row['project_id'] : 0;
        $obj->authorId = array_key_exists('author_id', $row) ? ($row['author_id'] === null ? null : (int)$row['author_id']) : null;
        $obj->bodyMd = array_key_exists('body_md', $row) ? (string)$row['body_md'] : '';
        $obj->createdAt = isset($row['created_at']) ? new DateTimeImmutable((string)$row['created_at']) : new DateTimeImmutable('now');
        $obj->updatedAt = (isset($row['updated_at']) && $row['updated_at'] !== null) ? new DateTimeImmutable((string)$row['updated_at']) : null;
        $obj->visibility = array_key_exists('visibility', $row) ? (string)$row['visibility'] : '';
        return $obj;
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->projectId,
            'author_id' => $this->authorId,
            'body_md' => $this->bodyMd,
            'created_at' => $this->createdAt->format('c'),
            'updated_at' => $this->updatedAt ? $this->updatedAt->format('c') : null,
            'visibility' => $this->visibility,
        ];
    }

    /** @return string[] */
    public function validate(): array
    {
        $errors = [];
        if ($this->bodyMd === '') { $errors[] = 'Post body is required.'; }
        return $errors;
    }

    /** @return int[] */
    public function getCommentIds(): array { return []; } // repository will populate later

    /** @return static|null */
    public static function findById(int $id): ?self { throw new LogicException('Not implemented: wire to PDO.'); }
    /** @return static[] */
    public static function search(array $filters): array { throw new LogicException('Not implemented: wire to PDO.'); }
    public function save(): void { throw new LogicException('Not implemented: wire to PDO.'); }
    public function delete(): void { throw new LogicException('Not implemented: wire to PDO.'); }

    public function getId(): int { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }

    public function getProjectId(): int { return $this->projectId; }
    public function setProjectId(int $projectId): void { $this->projectId = $projectId; }

    public function getAuthorId(): ?int { return $this->authorId; }
    public function setAuthorId(?int $authorId): void { $this->authorId = $authorId; }

    public function getBodyMd(): string { return $this->bodyMd; }
    public function setBodyMd(string $bodyMd): void { $this->bodyMd = $bodyMd; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): void { $this->createdAt = $createdAt; }

    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): void { $this->updatedAt = $updatedAt; }

    public function getVisibility(): string { return $this->visibility; }
    public function setVisibility(string $visibility): void { $this->visibility = $visibility; }

}