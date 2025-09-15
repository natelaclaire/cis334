<?php
declare(strict_types=1);

namespace App\Models;

use LogicException;

final class Profile
{
    private int $userId;
    private string $bioMd;
    private ?string $websiteUrl;
    private ?string $location;
    private ?string $avatarUrl;

    /** @param array<string,mixed> $row */
    public static function fromArray(array $row): self
    {
        $obj = new self();
        $obj->userId = array_key_exists('user_id', $row) ? (int)$row['user_id'] : 0;
        $obj->bioMd = array_key_exists('bio_md', $row) ? (string)$row['bio_md'] : '';
        $obj->websiteUrl = array_key_exists('website_url', $row) ? ($row['website_url'] === null ? null : (string)$row['website_url']) : null;
        $obj->location = array_key_exists('location', $row) ? ($row['location'] === null ? null : (string)$row['location']) : null;
        $obj->avatarUrl = array_key_exists('avatar_url', $row) ? ($row['avatar_url'] === null ? null : (string)$row['avatar_url']) : null;
        return $obj;
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'bio_md' => $this->bioMd,
            'website_url' => $this->websiteUrl,
            'location' => $this->location,
            'avatar_url' => $this->avatarUrl,
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

    public function getUserId(): int { return $this->userId; }
    public function setUserId(int $userId): void { $this->userId = $userId; }

    public function getBioMd(): string { return $this->bioMd; }
    public function setBioMd(string $bioMd): void { $this->bioMd = $bioMd; }

    public function getWebsiteUrl(): ?string { return $this->websiteUrl; }
    public function setWebsiteUrl(?string $websiteUrl): void { $this->websiteUrl = $websiteUrl; }

    public function getLocation(): ?string { return $this->location; }
    public function setLocation(?string $location): void { $this->location = $location; }

    public function getAvatarUrl(): ?string { return $this->avatarUrl; }
    public function setAvatarUrl(?string $avatarUrl): void { $this->avatarUrl = $avatarUrl; }

}