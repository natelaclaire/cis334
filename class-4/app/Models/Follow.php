<?php
declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;
use LogicException;

final class Follow
{
    private int $followerId;
    private int $followeeId;
    private \DateTimeImmutable $createdAt;

    /** @param array<string,mixed> $row */
    public static function fromArray(array $row): self
    {
        $obj = new self();
        $obj->followerId = array_key_exists('follower_id', $row) ? (int)$row['follower_id'] : 0;
        $obj->followeeId = array_key_exists('followee_id', $row) ? (int)$row['followee_id'] : 0;
        $obj->createdAt = isset($row['created_at']) ? new DateTimeImmutable((string)$row['created_at']) : new DateTimeImmutable('now');
        return $obj;
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'follower_id' => $this->followerId,
            'followee_id' => $this->followeeId,
            'created_at' => $this->createdAt->format('c'),
        ];
    }

    /** @return string[] */
    public function validate(): array
    {
        $errors = [];
        return $errors;
    }

    public function isSelfFollow(): bool { return $this->followerId === $this->followeeId; }

    /** @return static|null */
    public static function findById(int $id): ?self { throw new LogicException('Not implemented: wire to PDO.'); }
    /** @return static[] */
    public static function search(array $filters): array { throw new LogicException('Not implemented: wire to PDO.'); }
    public function save(): void { throw new LogicException('Not implemented: wire to PDO.'); }
    public function delete(): void { throw new LogicException('Not implemented: wire to PDO.'); }

    public function getFollowerId(): int { return $this->followerId; }
    public function setFollowerId(int $followerId): void { $this->followerId = $followerId; }

    public function getFolloweeId(): int { return $this->followeeId; }
    public function setFolloweeId(int $followeeId): void { $this->followeeId = $followeeId; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): void { $this->createdAt = $createdAt; }

}