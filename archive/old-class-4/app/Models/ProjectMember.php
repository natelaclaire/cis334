<?php
declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;
use LogicException;

final class ProjectMember
{
    private int $projectId;
    private int $userId;
    private string $role;
    private \DateTimeImmutable $addedAt;

    /** @param array<string,mixed> $row */
    public static function fromArray(array $row): self
    {
        $obj = new self();
        $obj->projectId = array_key_exists('project_id', $row) ? (int)$row['project_id'] : 0;
        $obj->userId = array_key_exists('user_id', $row) ? (int)$row['user_id'] : 0;
        $obj->role = array_key_exists('role', $row) ? (string)$row['role'] : '';
        $obj->addedAt = isset($row['added_at']) ? new DateTimeImmutable((string)$row['added_at']) : new DateTimeImmutable('now');
        return $obj;
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'project_id' => $this->projectId,
            'user_id' => $this->userId,
            'role' => $this->role,
            'added_at' => $this->addedAt->format('c'),
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

    public function getProjectId(): int { return $this->projectId; }
    public function setProjectId(int $projectId): void { $this->projectId = $projectId; }

    public function getUserId(): int { return $this->userId; }
    public function setUserId(int $userId): void { $this->userId = $userId; }

    public function getRole(): string { return $this->role; }
    public function setRole(string $role): void { $this->role = $role; }

    public function getAddedAt(): \DateTimeImmutable { return $this->addedAt; }
    public function setAddedAt(\DateTimeImmutable $addedAt): void { $this->addedAt = $addedAt; }

}