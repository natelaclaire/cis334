# Class ↔ Table Mapping

_Generated: 2025-09-15T21:44:56+00:00_

---

## User → `users`

| Property | Column | Type |
|---|---|---|
| `id` | `id` | `int` |
| `email` | `email` | `string` |
| `passwordHash` | `password_hash` | `string` |
| `displayName` | `display_name` | `string` |
| `role` | `role` | `string` |
| `createdAt` | `created_at` | `\DateTimeImmutable` |
| `updatedAt` | `updated_at` | `\DateTimeImmutable` |
| `deletedAt` | `deleted_at` | `?\DateTimeImmutable` |

## Profile → `profiles`

| Property | Column | Type |
|---|---|---|
| `userId` | `user_id` | `int` |
| `bioMd` | `bio_md` | `string` |
| `websiteUrl` | `website_url` | `?string` |
| `location` | `location` | `?string` |
| `avatarUrl` | `avatar_url` | `?string` |

## Company → `companys`

| Property | Column | Type |
|---|---|---|
| `id` | `id` | `int` |
| `name` | `name` | `string` |
| `websiteUrl` | `website_url` | `?string` |
| `industry` | `industry` | `?string` |
| `notesMd` | `notes_md` | `?string` |
| `createdAt` | `created_at` | `\DateTimeImmutable` |
| `updatedAt` | `updated_at` | `\DateTimeImmutable` |

## Contact → `contacts`

| Property | Column | Type |
|---|---|---|
| `id` | `id` | `int` |
| `companyId` | `company_id` | `?int` |
| `firstName` | `first_name` | `string` |
| `lastName` | `last_name` | `string` |
| `email` | `email` | `?string` |
| `phone` | `phone` | `?string` |
| `title` | `title` | `?string` |
| `notesMd` | `notes_md` | `?string` |
| `createdAt` | `created_at` | `\DateTimeImmutable` |
| `updatedAt` | `updated_at` | `\DateTimeImmutable` |

## Activity → `activitys`

| Property | Column | Type |
|---|---|---|
| `id` | `id` | `int` |
| `contactId` | `contact_id` | `int` |
| `userId` | `user_id` | `?int` |
| `type` | `type` | `string` |
| `subject` | `subject` | `string` |
| `dueAt` | `due_at` | `?\DateTimeImmutable` |
| `completedAt` | `completed_at` | `?\DateTimeImmutable` |
| `notesMd` | `notes_md` | `?string` |
| `createdAt` | `created_at` | `\DateTimeImmutable` |

## Project → `projects`

| Property | Column | Type |
|---|---|---|
| `id` | `id` | `int` |
| `ownerId` | `owner_id` | `?int` |
| `slug` | `slug` | `string` |
| `title` | `title` | `string` |
| `summaryMd` | `summary_md` | `?string` |
| `status` | `status` | `string` |
| `visibility` | `visibility` | `string` |
| `createdAt` | `created_at` | `\DateTimeImmutable` |
| `updatedAt` | `updated_at` | `\DateTimeImmutable` |

## ProjectMember → `projectmembers`

| Property | Column | Type |
|---|---|---|
| `projectId` | `project_id` | `int` |
| `userId` | `user_id` | `int` |
| `role` | `role` | `string` |
| `addedAt` | `added_at` | `\DateTimeImmutable` |

## Post → `posts`

| Property | Column | Type |
|---|---|---|
| `id` | `id` | `int` |
| `projectId` | `project_id` | `int` |
| `authorId` | `author_id` | `?int` |
| `bodyMd` | `body_md` | `string` |
| `createdAt` | `created_at` | `\DateTimeImmutable` |
| `updatedAt` | `updated_at` | `?\DateTimeImmutable` |
| `visibility` | `visibility` | `string` |

## Comment → `comments`

| Property | Column | Type |
|---|---|---|
| `id` | `id` | `int` |
| `postId` | `post_id` | `int` |
| `authorId` | `author_id` | `?int` |
| `bodyMd` | `body_md` | `string` |
| `createdAt` | `created_at` | `\DateTimeImmutable` |

## Follow → `follows`

| Property | Column | Type |
|---|---|---|
| `followerId` | `follower_id` | `int` |
| `followeeId` | `followee_id` | `int` |
| `createdAt` | `created_at` | `\DateTimeImmutable` |

## Reaction → `reactions`

| Property | Column | Type |
|---|---|---|
| `id` | `id` | `int` |
| `userId` | `user_id` | `int` |
| `postId` | `post_id` | `?int` |
| `commentId` | `comment_id` | `?int` |
| `type` | `type` | `string` |

