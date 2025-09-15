<?php
declare(strict_types=1);

namespace App\Models;

use LogicException;

final class Reaction
{
    private int $id;
    private int $userId;
    private ?int $postId;
    private ?int $commentId;
    private string $type;

    /** @param array<string,mixed> $row */
    public static function fromArray(array $row): self
    {
        $obj = new self();
        $obj->id = array_key_exists('id', $row) ? (int)$row['id'] : 0;
        $obj->userId = array_key_exists('user_id', $row) ? (int)$row['user_id'] : 0;
        $obj->postId = array_key_exists('post_id', $row) ? ($row['post_id'] === null ? null : (int)$row['post_id']) : null;
        $obj->commentId = array_key_exists('comment_id', $row) ? ($row['comment_id'] === null ? null : (int)$row['comment_id']) : null;
        $obj->type = array_key_exists('type', $row) ? (string)$row['type'] : '';
        return $obj;
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'post_id' => $this->postId,
            'comment_id' => $this->commentId,
            'type' => $this->type,
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

    public function getUserId(): int { return $this->userId; }
    public function setUserId(int $userId): void { $this->userId = $userId; }

    public function getPostId(): ?int { return $this->postId; }
    public function setPostId(?int $postId): void { $this->postId = $postId; }

    public function getCommentId(): ?int { return $this->commentId; }
    public function setCommentId(?int $commentId): void { $this->commentId = $commentId; }

    public function getType(): string { return $this->type; }
    public function setType(string $type): void { $this->type = $type; }

}