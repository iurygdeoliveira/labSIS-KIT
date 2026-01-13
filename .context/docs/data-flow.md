---
status: template
generated: 2026-01-13
---

# Data Flow & Integrations

## Overview
LabSIS-KIT implements a Laravel-based multi-tenant SaaS architecture with clear data flow patterns.

## Primary Data Flows

### 1. Authentication Flow
- User accesses `/admin` or `/tenant/{slug}`
- Laravel Sanctum authenticates session
- Filament checks permissions via Spatie Laravel Permission
- User assigned to active tenant context

### 2. Media Upload Flow
1. User uploads file via Filament FileUpload component
2. `MediaService::uploadMedia()` validates and stores
3. Spatie Media Library converts and optimizes
4. For videos: `VideoMetadataService` extracts metadata via FFmpeg
5. Thumbnail generated and stored
6. Record saved to `media` table with tenant isolation

### 3. Permission Check Flow
- User attempts action (e.g., view user)
- `UserPolicy` checks via `can('users.view')`
- Spatie queries `permissions` table filtered by `team_id`
- Action allowed/denied

## External Integrations
- **S3/Minio**: Object storage for media files
- **FFmpeg**: Video processing (via shell exec)
- **SMTP/Mailgun**: Mail delivery

## Observability
- Laravel Pulse tracks request performance
- Query logs via Debugbar (dev only)
- Application logs in `storage/logs/`

---
*Review and enhance with project-specific flows.*
