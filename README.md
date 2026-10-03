# KimiagarSoft Backend

Backend API پروژه KimiagarSoft، ساخته‌شده با Laravel و PostgreSQL با رویکرد API-first و معماری Incremental / Vertical Slice.

این پروژه به‌عنوان Backend مستقل برای وب‌سایت KimiagarSoft توسعه داده شده و در آینده می‌تواند توسط Frontend مبتنی بر Next.js و TypeScript مصرف شود.

---

## Architecture

```text
Client
  │
  ▼
REST API
  │
  ├── Authentication
  │
  ├── Authorization
  │
  ├── Services
  │
  ├── Projects
  │
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

### Development Architecture

The project follows an incremental and vertical-slice development approach.

Typical feature flow:

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

## Technology Stack

* Laravel 12
* PHP 8.2+
* PostgreSQL
* Laravel Sanctum
* REST API
* API versioning with `/api/v1`
* Form Requests
* API Resources
* Policies / Gates
* Feature Tests
* PHPUnit / Laravel Testing
* Git / GitHub

---

## Project Goals

The backend is designed to provide:

* Authentication
* Role-based authorization
* Service management
* Portfolio project management
* Article management
* Validation
* Pagination
* Filtering
* Sorting
* Searching
* Consistent API Resources
* Automated test coverage

The frontend is planned separately using:

* Next.js
* TypeScript

---

# Requirements

Before running the project, make sure the following are installed:

* PHP 8.2 or newer
* Composer
* PostgreSQL
* Git

Verify PHP:

```bash
php -v
```

Verify Composer:

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

Install PHP dependencies:

```bash
composer install
```

Create the environment file:

```bash
copy .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Configure the PostgreSQL database in `.env`.

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

The API will normally be available at:

```text
http://127.0.0.1:8000
```

The API base path is:

```text
/api/v1
```

Therefore, for example:

```text
http://127.0.0.1:8000/api/v1/services
```

---

# Authentication

Authentication is implemented using Laravel Sanctum.

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

The login endpoint returns a Sanctum authentication token.

Use the token in subsequent protected requests:

```http
Authorization: Bearer YOUR_TOKEN
```

## Current User

```http
GET /api/v1/auth/me
```

Authentication required.

## Logout

```http
POST /api/v1/auth/logout
```

Authentication required.

The current authentication token is revoked.

---

# Authorization

The application currently uses two roles:

| Role     | Description                |
| -------- | -------------------------- |
| `admin`  | Full administrative access |
| `author` | Content author             |

Role information is stored through the `users.role_id` relationship.

The `User` model provides:

```php
$user->isAdmin();
$user->isAuthor();
```

Authorization is implemented through Laravel Policies and Gates.

---

# Service Management

Service management provides CRUD operations for website services.

All service endpoints require authentication and authorization.

## Endpoints

| Method | Endpoint                     | Description    |
| ------ | ---------------------------- | -------------- |
| GET    | `/api/v1/services`           | List services  |
| GET    | `/api/v1/services/{service}` | Show service   |
| POST   | `/api/v1/services`           | Create service |
| PUT    | `/api/v1/services/{service}` | Update service |
| DELETE | `/api/v1/services/{service}` | Delete service |

The current Service Policy allows administrative users to manage services.

---

## List Services

```http
GET /api/v1/services
```

The service listing supports:

* Pagination
* Filtering
* Sorting
* Searching

Pagination size:

```text
10 items per page
```

---

## Service Query Parameters

### Search

Search services by supported searchable fields.

Example:

```text
GET /api/v1/services?search=web
```

### Status Filter

```text
GET /api/v1/services?status=published
```

### Category Filter

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

---

## Get a Service

```http
GET /api/v1/services/{service}
```

Example:

```text
GET /api/v1/services/1
```

---

## Create a Service

```http
POST /api/v1/services
```

Example request:

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

---

## Update a Service

```http
PUT /api/v1/services/{service}
```

The request follows the same validation rules as service creation, while allowing the current record's slug to remain unchanged.

---

## Delete a Service

```http
DELETE /api/v1/services/{service}
```

Successful deletion returns:

```http
204 No Content
```

---

## Service Resource

Service responses use `ServiceResource`.

The response contains:

```json
{
    "id": 1,
    "service_category_id": 1,
    "title": "Web Design",
    "slug": "web-design",
    "short_description": "Professional website design services",
    "description": "Complete website design and development services.",
    "status": "published",
    "sort_order": 1,
    "published_at": "2026-10-01T10:00:00.000000Z",
    "created_at": "2026-10-01T09:00:00.000000Z",
    "updated_at": "2026-10-01T09:00:00.000000Z"
}
```

---

# Portfolio Projects

Projects represent portfolio items displayed by the website.

## Endpoints

| Method | Endpoint                     | Description    |
| ------ | ---------------------------- | -------------- |
| GET    | `/api/v1/projects`           | List projects  |
| GET    | `/api/v1/projects/{project}` | Show project   |
| POST   | `/api/v1/projects`           | Create project |
| PUT    | `/api/v1/projects/{project}` | Update project |
| DELETE | `/api/v1/projects/{project}` | Delete project |

All project endpoints currently require authentication and policy authorization.

---

## List Projects

```http
GET /api/v1/projects
```

Projects are:

* Ordered by `sort_order`
* Paginated
* Limited to 10 records per page

Example:

```text
GET /api/v1/projects?page=2
```

---

## Create a Project

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

---

## Update a Project

```http
PUT /api/v1/projects/{project}
```

The project slug must remain unique.

---

## Delete a Project

```http
DELETE /api/v1/projects/{project}
```

Successful deletion returns:

```http
204 No Content
```

---

## Project Resource

Project responses use `ProjectResource`.

Fields:

```json
{
    "id": 1,
    "title": "Company Website",
    "slug": "company-website",
    "short_description": "Corporate website development",
    "description": "A complete corporate website project.",
    "status": "published",
    "sort_order": 1,
    "published_at": "2026-10-01T10:00:00.000000Z",
    "created_at": "2026-10-01T09:00:00.000000Z",
    "updated_at": "2026-10-01T09:00:00.000000Z"
}
```

---

# Articles

Articles provide the content management layer for website articles.

Articles belong to users through `user_id`.

## Endpoints

| Method | Endpoint                     | Description    |
| ------ | ---------------------------- | -------------- |
| GET    | `/api/v1/articles`           | List articles  |
| GET    | `/api/v1/articles/{article}` | Show article   |
| POST   | `/api/v1/articles`           | Create article |
| PUT    | `/api/v1/articles/{article}` | Update article |
| DELETE | `/api/v1/articles/{article}` | Delete article |

All article endpoints require authentication and policy authorization.

---

# Article Authorization

Article authorization currently distinguishes between administrators and authors.

### Admin

An administrator can manage articles according to the Article Policy.

### Author

An author can:

* Create articles
* View articles according to policy
* Update their own articles

The Article Service also restricts the article listing for authors to articles belonging to the authenticated user.

This means an author listing request automatically applies:

```text
user_id = authenticated_user_id
```

Administrators are not restricted by this author ownership filter.

---

# List Articles

```http
GET /api/v1/articles
```

Pagination:

```text
10 articles per page
```

The article listing supports:

* Ownership filtering for authors
* Status filtering
* Searching
* Sorting
* Pagination

---

## Article Status Filter

```text
GET /api/v1/articles?status=published
```

Supported statuses:

```text
draft
published
```

---

## Article Search

Search is performed against:

* `title`
* `slug`

Example:

```text
GET /api/v1/articles?search=laravel
```

---

## Article Sorting

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

Direction:

```text
asc
desc
```

Any direction other than `desc` is treated as `asc`.

---

## Combined Article Query

Query parameters can be combined.

Example:

```text
GET /api/v1/articles?status=published&search=laravel&sort=created_at&direction=desc&page=2
```

For an author, the ownership restriction is applied automatically in addition to the supplied filters.

---

## Get an Article

```http
GET /api/v1/articles/{article}
```

---

## Create an Article

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

The authenticated user's ID is automatically assigned to the article as `user_id`.

---

## Update an Article

```http
PUT /api/v1/articles/{article}
```

The slug must remain unique.

The Article Policy determines whether the authenticated user is authorized to update the article.

---

## Delete an Article

```http
DELETE /api/v1/articles/{article}
```

Successful deletion returns:

```http
204 No Content
```

---

## Article Resource

Article responses use `ArticleResource`.

Fields:

```json
{
    "id": 1,
    "user_id": 1,
    "title": "Learning Laravel",
    "slug": "learning-laravel",
    "short_description": "Introduction to Laravel development",
    "content": "Article content goes here.",
    "status": "published",
    "sort_order": 1,
    "published_at": "2026-10-01T10:00:00.000000Z",
    "created_at": "2026-10-01T09:00:00.000000Z",
    "updated_at": "2026-10-01T09:00:00.000000Z"
}
```

---

# Validation

Validation is implemented using Laravel Form Requests.

Current domains use dedicated validation classes for create and update operations.

Examples:

```text
StoreProjectRequest
UpdateProjectRequest

StoreArticleRequest
UpdateArticleRequest
```

Common validation rules include:

* Required strings
* Maximum string length
* Unique slugs
* Allowed status values
* Integer sort order
* Date validation
* Nullable descriptions/content
* Unique slug validation during update while ignoring the current record

Supported content statuses:

```text
draft
published
```

---

# API Resources

API responses are standardized through Laravel API Resources.

Current resources:

```text
ServiceResource
ProjectResource
ArticleResource
```

Resources define the fields exposed by the API instead of returning raw Eloquent models directly.

---

# HTTP Responses

The API uses standard HTTP status codes.

Common responses include:

| Status | Meaning                              |
| ------ | ------------------------------------ |
| `200`  | Successful request                   |
| `201`  | Resource created                     |
| `204`  | Resource successfully deleted        |
| `401`  | Authentication required              |
| `403`  | Authenticated user is not authorized |
| `404`  | Resource not found                   |
| `422`  | Validation failed                    |

Protected endpoints require:

```http
Authorization: Bearer YOUR_TOKEN
```

---

# Pagination

List endpoints use Laravel pagination.

Current page size:

```text
10 records
```

Example:

```text
GET /api/v1/articles?page=2
```

Paginated responses include Laravel pagination metadata such as:

```json
{
    "current_page": 2,
    "last_page": 5,
    "per_page": 10,
    "total": 50
}
```

---

# Testing

The project uses automated tests to verify:

* Authentication
* Token authentication
* Authorization
* Policies
* CRUD operations
* Validation
* Pagination
* Filtering
* Sorting
* Searching
* API response resources
* Article ownership rules
* Project management
* Service management

Current full test-suite checkpoint:

```text
147 passed
471 assertions
0 failures
```

Run the complete test suite:

```bash
php artisan test
```

---

# Development Principles

The project follows these principles:

### Incremental Development

Features are implemented in small, complete vertical slices.

### YAGNI

Avoid implementing functionality before it is actually required.

### Minimal Refactoring

Do not refactor unrelated working code while implementing a feature.

### Test Before Moving Forward

A feature should be tested before moving to the next stage.

### No Daily Re-Debugging

Completed and verified functionality should not be repeatedly reopened without a concrete reason.

### Documentation Follows Implementation

Documentation is updated after the actual implementation has been verified.

---

# Current Domains

The current backend contains the following main domains:

```text
Authentication
    ├── Register
    ├── Login
    ├── Current User
    └── Logout

Authorization
    ├── Admin
    └── Author

Services
    ├── CRUD
    ├── Pagination
    ├── Filtering
    ├── Sorting
    └── Searching

Projects
    ├── CRUD
    └── Pagination

Articles
    ├── CRUD
    ├── Authorization
    ├── Ownership
    ├── Pagination
    ├── Filtering
    ├── Sorting
    └── Searching
```

---

# API Base URL

All version 1 API endpoints use:

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

The source code is maintained in the KimiagarSoft GitHub repository.

Repository:

```text
KimiagarSoft/kimiagarsoft-backend
```

Main branch:

```text
main
```

---

# Project Status

The current backend has completed:

* Project foundation
* Database foundation
* Authentication
* Authorization and roles
* Service Management
* Portfolio Projects
* Article Management
* Pagination
* Filtering
* Sorting
* Searching
* API Resources
* Automated testing

Documentation is maintained alongside the implementation.

Production-readiness tasks such as deployment configuration and CI/CD are handled separately from the current development phase.
