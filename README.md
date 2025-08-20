# Translation Management Service

An API-driven Laravel service for managing multilingual translations at scale.  
Supports CRUD operations, tagging, locale exports, and token-based authentication.  
Designed for high performance (<200 ms per request, JSON export <500 ms with 100k+ records).

---

## Features
- Manage translations for multiple locales (`en`, `fr`, `es`, …).
- Tag translations by context (`web`, `mobile`, `desktop`).
- REST API for create, update, view, search.
- JSON export endpoint for frontend apps (Vue, React, etc).
- Token-based authentication (Bearer tokens).
- Scalable database schema with indexes and full-text search.
- Seeder to generate 100k+ translations for performance testing.
- Docker setup (Nginx + PHP-FPM + MySQL + Redis).
- Redis caching with ETag and cache-tag invalidation.
- OpenAPI (Swagger) docs included.
- PHPUnit tests for functionality and performance.

---

## Requirements
- PHP 8.3+
- Composer
- MySQL 8
- Redis 7
- Docker (optional)

---

## Setup

1. Clone repo  
   ```
   git clone https://github.com/murtaza1904/translation-management-service.git
   cd translation-management-service

2. Install dependencies
    ```
    composer install

3. Copy environment file and configure
    ```
    cp .env.example .env
    php artisan key:generate
    ```
    Update .env with your database and Redis settings.

4. Run migrations and seeders
    ```
    php artisan migrate --seed
    ```
    Seeder will create default locales, tags, and demo translations.

## Running with Docker

1. Build and start services:
    ```
    docker-compose up -d
    ```

2. http://localhost:8080

## Usage
All endpoints are prefixed with `/api/v1`.

Include the header:
```
Authorization: Bearer <token>
```

## Endpoints
- `GET /api/v1/translations` — list/search translations
- `POST /api/v1/translations` — create translation
- `GET /api/v1/translations/{id}` — view translation
- `PUT /api/v1/translations/{id}` — update translation
- `DELETE /api/v1/translations/{id}` — delete translation
- `GET /api/v1/export/{locale}` — stream JSON export

### Example request
```
    curl -H "Authorization: Bearer <token>" http://localhost:8080/api/v1/export/en
```

## Testing

### Run the test suite:
```
php artisan test
```

### Tests cover:
- CRUD endpoints
- Export performance
- Authentication
- Cache behavior

## API Documentation
OpenAPI (Swagger) spec is available at: docs/openapi.yaml

You can visualize it using [Swagger UI](https://swagger.io/tools/swagger-ui/)
    
or import it into [Postman](https://www.postman.com/).


## Design Notes
- Standards: Follows PSR-12 coding standards and SOLID principles.
- Schema: Translations normalized into locales, translations, tags.
- Performance:
- Indexed keys and full-text search.
- Export implemented as streamed response with chunked DB reads.
- Redis tag cache + ETag ensures CDN-friendly and always-fresh responses.
- Security: Token-based authentication with hashed storage.
- Scalability: Tested with 100k+ translations, export <500 ms.