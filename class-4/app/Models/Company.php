<?php
declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;
use LogicException;

final class Company
{
    private int $id;
    private string $name;
    private ?string $websiteUrl;
    private ?string $industry;
    private ?string $notesMd;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    /** @param array<string,mixed> $row */
    public static function fromArray(array $row): self
    {
        $obj = new self();
        $obj->id = array_key_exists('id', $row) ? (int)$row['id'] : 0;
        $obj->name = array_key_exists('name', $row) ? (string)$row['name'] : '';
        $obj->websiteUrl = array_key_exists('website_url', $row) ? ($row['website_url'] === null ? null : (string)$row['website_url']) : null;
        $obj->industry = array_key_exists('industry', $row) ? ($row['industry'] === null ? null : (string)$row['industry']) : null;
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
            'name' => $this->name,
            'website_url' => $this->websiteUrl,
            'industry' => $this->industry,
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

    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }

    public function getWebsiteUrl(): ?string { return $this->websiteUrl; }
    public function setWebsiteUrl(?string $websiteUrl): void { $this->websiteUrl = $websiteUrl; }

    public function getIndustry(): ?string { return $this->industry; }
    public function setIndustry(?string $industry): void { $this->industry = $industry; }

    public function getNotesMd(): ?string { return $this->notesMd; }
    public function setNotesMd(?string $notesMd): void { $this->notesMd = $notesMd; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): void { $this->createdAt = $createdAt; }

    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): void { $this->updatedAt = $updatedAt; }

}