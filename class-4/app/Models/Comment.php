<?php
declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;
use LogicException;

final class Comment
{
    private int $id;
    private int $postId;
    private ?int $authorId;
    private string $bodyMd;
    private \DateTimeImmutable $createdAt;

    /** @param array<string,mixed> $row */
    public static function fromArray(array $row): self
    {
        $obj = new self();
        $obj->id = array_key_exists('id', $row) ? (int)$row['id'] : 0;
        $obj->postId = array_key_exists('post_id', $row) ? (int)$row['post_id'] : 0;
        $obj->authorId = array_key_exists('author_id', $row) ? ($row['author_id'] === null ? null : (int)$row['author_id']) : null;
        $obj->bodyMd = array_key_exists('body_md', $row) ? (string)$row['body_md'] : '';
        $obj->createdAt = isset($row['created_at']) ? new DateTimeImmutable((string)$row['created_at']) : new DateTimeImmutable('now');
        return $obj;
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'post_id' => $this->postId,
            'author_id' => $this->authorId,
            'body_md' => $this->bodyMd,
            'created_at' => $this->createdAt->format('c'),
        ];
    }

    /** @return string[] */
    public function validate(): array
    {
        $errors = [];
        if ($this->bodyMd === '') { $errors[] = 'Comment body is required.'; }
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

    public function getPostId(): int { return $this->postId; }
    public function setPostId(int $postId): void { $this->postId = $postId; }

    public function getAuthorId(): ?int { return $this->authorId; }
    public function setAuthorId(?int $authorId): void { $this->authorId = $authorId; }

    public function getBodyMd(): string { return $this->bodyMd; }
    public function setBodyMd(string $bodyMd): void { $this->bodyMd = $bodyMd; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): void { $this->createdAt = $createdAt; }

}