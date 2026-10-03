# KimiagarSoft Backend

REST API backend for **KimiagarSoft**, built with Laravel and PostgreSQL.

The project follows an **API-first**, **Incremental / Vertical Slice** architecture and is designed to serve a future frontend based on **Next.js + TypeScript**.

---

## Tech Stack

* **Laravel 12**
* **PHP 8.2+**
* **PostgreSQL**
* **Laravel Sanctum**
* **REST API**
* **API Versioning**
* **Laravel Policies / Gates**
* **Form Requests**
* **API Resources**
* **Feature Tests**
* **Git / GitHub**

---

## Architecture

```text
Client
  │
  ▼
REST API
  │
  ├── Authentication
  ├── Authorization
  ├── Services
  ├── Projects
  └── Articles
        │
        ▼
   Laravel Application
        │
        ├── Controllers
        ├── Form Requests
        ├── Services
        ├── Policies
        ├── Resources
        └── Models
                │
                ▼
            PostgreSQL
```

### Development Flow

Each feature is developed as a complete vertical slice:

```text
Requirement
    ↓
Database
    ↓
Migration
    ↓
Model
    ↓
Relationship
    ↓
Request / Validation
    ↓
Service
    ↓
Controller
    ↓
Resource
    ↓
Route
    ↓
Authorization
    ↓
Tests
    ↓
Manual Verification
    ↓
Documentation
```

---

# Requirements

Make sure the following are installed:

* PHP 8.2 or newer
* Composer
* PostgreSQL
* Git

Check PHP:

```bash
php -v
```

Check Composer:

```bash
composer -V
```

---

# Installation

Clone the repository:

```bash
git clone https://github.com/KimiagarSoft/kimiagarsoft-backend.git
```

Enter the project directory:

```bash
cd kimiagarsoft-backend
```

Install dependencies:

```bash
composer install
```

Create the environment file:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Configure the PostgreSQL connection in `.env`.

Example:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=kimiagarsoft
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Run migrations:

```bash
php artisan migrate
```

Seed the database:

```bash
php artisan db:seed
```

---

# Running the Application

Start the Laravel development server:

```bash
php artisan serve
```

The application will normally be available at:

```text
http://127.0.0.1:8000
```

The API base path is:

```text
/api/v1
```

Example:

```text
http://127.0.0.1:8000/api/v1/services
```

---

# Authentication

Authentication is implemented using **Laravel Sanctum**.

All protected endpoints require:

```http
Authorization: Bearer YOUR_TOKEN
```

## Register

```http
POST /api/v1/auth/register
```

Example request:

```json
{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password",
    "password_confirmation": "password"
}
```

## Login

```http
POST /api/v1/auth/login
```

A successful login returns an authentication token.

## Current User

```http
GET /api/v1/auth/me
```

Requires authentication.

## Logout

```http
POST /api/v1/auth/logout
```

Requires authentication.

The current Sanctum token is revoked after logout.

---

# Authorization

The current application defines two roles:

| Role     | Description         |
| -------- | ------------------- |
| `admin`  | Administrative user |
| `author` | Content author      |

The `User` model provides:

```php
$user->isAdmin();
$user->isAuthor();
```

Authorization is implemented using Laravel Policies and Gates.

---

# API Overview

All version 1 endpoints use:

```text
/api/v1
```

| Domain         | Method | Endpoint              |
| -------------- | ------ | --------------------- |
| Authentication | POST   | `/auth/register`      |
| Authentication | POST   | `/auth/login`         |
| Authentication | GET    | `/auth/me`            |
| Authentication | POST   | `/auth/logout`        |
| Services       | GET    | `/services`           |
| Services       | GET    | `/services/{service}` |
| Services       | POST   | `/services`           |
| Services       | PUT    | `/services/{service}` |
| Services       | DELETE | `/services/{service}` |
| Projects       | GET    | `/projects`           |
| Projects       | GET    | `/projects/{project}` |
| Projects       | POST   | `/projects`           |
| Projects       | PUT    | `/projects/{project}` |
| Projects       | DELETE | `/projects/{project}` |
| Articles       | GET    | `/articles`           |
| Articles       | GET    | `/articles/{article}` |
| Articles       | POST   | `/articles`           |
| Articles       | PUT    | `/articles/{article}` |
| Articles       | DELETE | `/articles/{article}` |

---

# Services

The Service domain provides CRUD operations for website services.

## Endpoints

### List Services

```http
GET /api/v1/services
```

The service listing supports:

* Pagination
* Filtering
* Sorting
* Searching

The default page size is:

```text
10 records
```

### Get a Service

```http
GET /api/v1/services/{service}
```

### Create a Service

```http
POST /api/v1/services
```

Example:

```json
{
    "service_category_id": 1,
    "title": "Web Design",
    "slug": "web-design",
    "short_description": "Professional website design services",
    "description": "Complete website design and development services.",
    "status": "published",
    "sort_order": 1,
    "published_at": "2026-10-01 10:00:00"
}
```

### Update a Service

```http
PUT /api/v1/services/{service}
```

### Delete a Service

```http
DELETE /api/v1/services/{service}
```

Successful deletion returns:

```text
204 No Content
```

## Service Querying

### Search

```text
GET /api/v1/services?search=web
```

### Filter by Status

```text
GET /api/v1/services?status=published
```

### Filter by Category

```text
GET /api/v1/services?service_category_id=1
```

### Sorting

```text
GET /api/v1/services?sort=title&direction=asc
```

### Pagination

```text
GET /api/v1/services?page=2
```

Query parameters can be combined.

Example:

```text
GET /api/v1/services?status=published&search=web&page=2
```

## Service Response

Service responses are transformed using `ServiceResource`.

Available fields:

```text
id
service_category_id
title
slug
short_description
description
status
sort_order
published_at
created_at
updated_at
```

---

# Projects

The Projects domain manages portfolio projects.

## Endpoints

### List Projects

```http
GET /api/v1/projects
```

Projects are:

* Ordered by `sort_order`
* Paginated
* Returned 10 records per page

Example:

```text
GET /api/v1/projects?page=2
```

### Get a Project

```http
GET /api/v1/projects/{project}
```

### Create a Project

```http
POST /api/v1/projects
```

Example:

```json
{
    "title": "Company Website",
    "slug": "company-website",
    "short_description": "Corporate website development",
    "description": "A complete corporate website project.",
    "status": "published",
    "sort_order": 1,
    "published_at": "2026-10-01 10:00:00"
}
```

### Update a Project

```http
PUT /api/v1/projects/{project}
```

### Delete a Project

```http
DELETE /api/v1/projects/{project}
```

Successful deletion returns:

```text
204 No Content
```

## Project Response

Project responses are transformed using `ProjectResource`.

Available fields:

```text
id
title
slug
short_description
description
status
sort_order
published_at
created_at
updated_at
```

---

# Articles

The Articles domain provides content management functionality.

Articles belong to users through `user_id`.

## Endpoints

### List Articles

```http
GET /api/v1/articles
```

The article listing supports:

* Authorization-based ownership
* Status filtering
* Searching
* Sorting
* Pagination

The default page size is:

```text
10 records
```

### Get an Article

```http
GET /api/v1/articles/{article}
```

### Create an Article

```http
POST /api/v1/articles
```

Example:

```json
{
    "title": "Learning Laravel",
    "slug": "learning-laravel",
    "short_description": "Introduction to Laravel development",
    "content": "Article content goes here.",
    "status": "published",
    "sort_order": 1,
    "published_at": "2026-10-01 10:00:00"
}
```

The authenticated user's ID is automatically assigned to the article.

### Update an Article

```http
PUT /api/v1/articles/{article}
```

### Delete an Article

```http
DELETE /api/v1/articles/{article}
```

Successful deletion returns:

```text
204 No Content
```

---

# Article Authorization

Article access is controlled through `ArticlePolicy`.

## Admin

Administrators have administrative article management permissions according to the policy.

## Author

Authors can work with their own articles according to the policy.

When an author requests the article list, the service automatically restricts the query to that author's articles.

Conceptually:

```text
Author
  ↓
GET /api/v1/articles
  ↓
Only articles belonging to authenticated user
```

---

# Article Querying

## Filter by Status

```text
GET /api/v1/articles?status=published
```

Supported statuses:

```text
draft
published
```

## Search

Article search is performed against:

* `title`
* `slug`

Example:

```text
GET /api/v1/articles?search=laravel
```

## Sorting

Supported sort fields:

```text
sort_order
title
created_at
```

Example:

```text
GET /api/v1/articles?sort=title&direction=asc
```

Supported directions:

```text
asc
desc
```

## Pagination

```text
GET /api/v1/articles?page=2
```

## Combined Query

Query parameters can be combined:

```text
GET /api/v1/articles?status=published&search=laravel&sort=created_at&direction=desc&page=2
```

For authors, the ownership restriction is applied automatically in addition to these filters.

---

# Article Response

Article responses are transformed using `ArticleResource`.

Available fields:

```text
id
user_id
title
slug
short_description
content
status
sort_order
published_at
created_at
updated_at
```

---

# Validation

Validation is implemented through Laravel Form Requests.

Current request classes include:

```text
StoreProjectRequest
UpdateProjectRequest

StoreArticleRequest
UpdateArticleRequest
```

Validation includes:

* Required fields
* String validation
* Maximum length
* Unique slugs
* Status validation
* Integer `sort_order`
* Date validation
* Nullable descriptions/content

Supported content statuses:

```text
draft
published
```

When updating an existing resource, the current record is excluded from the slug uniqueness check.

---

# API Resources

The API uses Laravel API Resources to control the response structure.

Current resources:

```text
ServiceResource
ProjectResource
ArticleResource
```

This prevents the API from directly exposing raw Eloquent models.

---

# Pagination

List endpoints use Laravel's `LengthAwarePaginator`.

The current page size is:

```text
10 records
```

Example:

```text
GET /api/v1/articles?page=2
```

Paginated responses include standard Laravel pagination metadata such as:

```json
{
    "current_page": 2,
    "last_page": 5,
    "per_page": 10,
    "total": 50
}
```

---

# HTTP Status Codes

Common API responses include:

| Status | Meaning                 |
| ------ | ----------------------- |
| `200`  | Successful request      |
| `201`  | Resource created        |
| `204`  | Resource deleted        |
| `401`  | Authentication required |
| `403`  | Authorization denied    |
| `404`  | Resource not found      |
| `422`  | Validation failed       |

---

# Testing

The project uses automated tests for the main application domains.

Current test coverage includes:

* Authentication
* Sanctum token authentication
* Authorization
* Policies
* CRUD operations
* Validation
* Pagination
* Filtering
* Sorting
* Searching
* API Resources
* Article ownership
* Service Management
* Portfolio Projects
* Article Management

Run the complete test suite:

```bash
php artisan test
```

Current verified checkpoint:

```text
147 passed
471 assertions
0 failures
```

---

# Development Principles

## Incremental Development

Features are developed as small, complete vertical slices.

## YAGNI

Functionality is implemented when it is actually required.

## Minimal Refactoring

Unrelated working code is not refactored during feature development.

## Test Before Moving Forward

A feature should be tested before moving to the next stage.

## No Unnecessary Rework

Completed and verified functionality should not be reopened without a concrete reason.

## Documentation

Documentation is kept aligned with the actual implementation.

---

# Current Project Domains

```text
KimiagarSoft Backend
│
├── Authentication
│   ├── Register
│   ├── Login
│   ├── Current User
│   └── Logout
│
├── Authorization
│   ├── Admin
│   └── Author
│
├── Services
│   ├── CRUD
│   ├── Pagination
│   ├── Filtering
│   ├── Sorting
│   └── Searching
│
├── Projects
│   ├── CRUD
│   └── Pagination
│
└── Articles
    ├── CRUD
    ├── Authorization
    ├── Ownership
    ├── Pagination
    ├── Filtering
    ├── Sorting
    └── Searching
```

---

# API Base Path

All version 1 endpoints are available under:

```text
/api/v1
```

Examples:

```text
/api/v1/auth/login
/api/v1/services
/api/v1/projects
/api/v1/articles
```

---

# Repository

**GitHub:** `KimiagarSoft/kimiagarsoft-backend`

**Branch:** `main`

---

# Project Status

The following areas are currently implemented:

* Project foundation
* Database foundation
* Authentication
* Sanctum authentication
* Role-based authorization
* Service Management
* Portfolio Projects
* Article Management
* Pagination
* Filtering
* Sorting
* Searching
* API Resources
* Automated testing
* API documentation

Production-readiness tasks and CI/CD are intentionally handled separately from the current development phase.
