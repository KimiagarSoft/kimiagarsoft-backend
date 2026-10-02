# KimiagarSoft Backend

Professional backend API for **KimiagarSoft**, built with **Laravel 12** and designed with an **API-first architecture**.

This project provides the backend foundation and REST API for the future KimiagarSoft frontend and other API clients.

---

## Architecture

```text
Client
   ↓
Next.js + TypeScript
   ↓
REST API
   ↓
Laravel 12
   ↓
PostgreSQL
```

The backend and frontend are developed as separate applications.

The project follows an incremental and vertical-slice development approach, with a focus on maintainability, testability, and minimal unnecessary complexity.

---

## Tech Stack

| Technology      | Version / Role               |
| --------------- | ---------------------------- |
| PHP             | 8.2+                         |
| Laravel         | 12                           |
| API             | REST API                     |
| Database        | PostgreSQL                   |
| Authentication  | Laravel Sanctum              |
| Authorization   | Laravel Policies / Gates     |
| Testing         | PHPUnit / Laravel Test Suite |
| Version Control | Git / GitHub                 |
| Future Frontend | Next.js + TypeScript         |

---

## Project Goals

KimiagarSoft Backend is being developed as a professional, maintainable, and scalable backend system.

The architecture focuses on:

* API-first development
* RESTful API design
* Separation of backend and frontend
* PostgreSQL database architecture
* Eloquent ORM
* Service-oriented business logic
* Secure authentication and authorization
* Automated testing
* Maintainable and scalable code
* Incremental feature development

---

## Requirements

Before running the project, make sure the following are installed:

* PHP 8.2 or higher
* Composer
* PostgreSQL
* Git

---

## Installation

Clone the repository:

```bash
git clone https://github.com/KimiagarSoft/kimiagarsoft-backend.git
cd kimiagarsoft-backend
```

Install Composer dependencies:

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

Run the database migrations:

```bash
php artisan migrate
```

---

## Running the Application

Start the Laravel development server:

```bash
php artisan serve
```

The application will then be available through the local Laravel development server.

---

# API

The backend exposes a versioned REST API under:

```text
/api/v1
```

The current API includes authentication and Service Management functionality.

---

# Authentication API

Authentication is implemented using **Laravel Sanctum**.

## Public Authentication Endpoints

| Method | Endpoint                | Description         |
| ------ | ----------------------- | ------------------- |
| POST   | `/api/v1/auth/register` | Register a user     |
| POST   | `/api/v1/auth/login`    | Authenticate a user |

These endpoints do not require an authentication token.

---

## Protected Authentication Endpoints

The following endpoints require a valid Sanctum authentication token:

| Method | Endpoint              | Description           |
| ------ | --------------------- | --------------------- |
| GET    | `/api/v1/auth/me`     | Retrieve current user |
| POST   | `/api/v1/auth/logout` | Revoke current token  |

Protected endpoints use:

```text
auth:sanctum
```

---

# Authorization

The application uses role-based authorization through Laravel Policies and Gates.

## Current Roles

| Role     | Purpose                                 |
| -------- | --------------------------------------- |
| `admin`  | Full administrative access              |
| `author` | Reserved for content/article management |

The initial authorization model intentionally keeps the permission system simple.

There are currently no separate permission records or permission-management UI.

---

## Service Authorization

Service Management is protected by `ServicePolicy`.

All Service API endpoints require authentication and authorization.

The current Service Management policy allows only users with the `admin` role to manage services.

### Authorization behavior

| User   | Result             |
| ------ | ------------------ |
| Guest  | `401 Unauthorized` |
| Author | `403 Forbidden`    |
| Admin  | Authorized         |

Authorization is applied to:

* View service list
* View a single service
* Create a service
* Update a service
* Delete a service

---

# Service Management API

## Endpoints

| Method | Endpoint                     | Description        |
| ------ | ---------------------------- | ------------------ |
| GET    | `/api/v1/services`           | List services      |
| GET    | `/api/v1/services/{service}` | Retrieve a service |
| POST   | `/api/v1/services`           | Create a service   |
| PUT    | `/api/v1/services/{service}` | Update a service   |
| DELETE | `/api/v1/services/{service}` | Delete a service   |

All Service endpoints require authentication and appropriate authorization.

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

### Search

Search services by title, short description, or description:

```http
GET /api/v1/services?search=design
```

### Filter by Status

```http
GET /api/v1/services?status=published
```

Supported values:

```text
draft
published
```

### Filter by Service Category

```http
GET /api/v1/services?service_category_id=1
```

### Sorting

Sort by an allowed field:

```http
GET /api/v1/services?sort=sort_order
```

Descending order:

```text
sort=-sort_order
```

Supported sort fields:

```text
sort_order
created_at
title
```

### Combining Parameters

Search, filtering, sorting, and pagination can be combined:

```http
GET /api/v1/services?search=design&status=published&sort=-sort_order
```

### Pagination

The service listing is paginated with 10 records per page.

Example:

```http
GET /api/v1/services?page=2
```

Paginated responses contain:

* `data`
* `links`
* `meta`

---

## Retrieve a Service

```http
GET /api/v1/services/{service}
```

Example:

```http
GET /api/v1/services/1
```

A successful response uses the standard Laravel API Resource structure:

```json
{
    "data": {
        "id": 1,
        "service_category_id": 1,
        "title": "Web Design",
        "slug": "web-design",
        "short_description": "Professional web design services.",
        "description": "Full description of the service.",
        "status": "published",
        "sort_order": 1,
        "published_at": "2026-09-28T12:00:00.000000Z",
        "created_at": "2026-09-28T12:00:00.000000Z",
        "updated_at": "2026-09-28T12:00:00.000000Z"
    }
}
```

---

## Create a Service

```http
POST /api/v1/services
```

### Request Body

```json
{
    "service_category_id": 1,
    "title": "Web Design",
    "slug": "web-design",
    "short_description": "Professional web design services.",
    "description": "Full description of the service.",
    "status": "published",
    "sort_order": 1,
    "published_at": "2026-09-28 12:00:00"
}
```

### Validation Rules

| Field                 | Required | Rules                   |
| --------------------- | -------- | ----------------------- |
| `service_category_id` | Yes      | Integer, must exist     |
| `title`               | Yes      | String, max 255         |
| `slug`                | Yes      | String, max 255, unique |
| `short_description`   | No       | Nullable string         |
| `description`         | Yes      | String                  |
| `status`              | Yes      | `draft` or `published`  |
| `sort_order`          | Yes      | Integer, minimum 0      |
| `published_at`        | No       | Nullable date           |

---

## Update a Service

```http
PUT /api/v1/services/{service}
```

The update endpoint supports partial updates. Only the fields that need to be changed have to be provided.

Example:

```json
{
    "title": "Professional Web Design",
    "status": "published"
}
```

All update fields are optional.

The same validation rules apply to provided fields, while the `slug` uniqueness rule ignores the current service.

---

## Delete a Service

```http
DELETE /api/v1/services/{service}
```

A successful deletion returns:

```http
204 No Content
```

---

# Response Structure

## Single Resource

Single service responses use:

```json
{
    "data": {
        "id": 1
    }
}
```

## Collection

Service collections use:

```json
{
    "data": [
        {
            "id": 1
        }
    ]
}
```

## Paginated Collection

Paginated collections contain:

```json
{
    "data": [],
    "links": {},
    "meta": {}
}
```

The response contract is covered by automated feature tests.

---

# Error Responses

The API currently relies primarily on Laravel's standard HTTP error responses.

## Unauthenticated

Requests to protected endpoints without valid authentication return:

```http
401 Unauthorized
```

## Forbidden

Authenticated users without the required authorization return:

```http
403 Forbidden
```

## Validation Error

Invalid request data returns Laravel's standard validation error response:

```http
422 Unprocessable Content
```

## Resource Not Found

Requesting a resource that does not exist returns:

```http
404 Not Found
```

Error response standardization is intentionally deferred until the dedicated Error Handling phase.

---

# Testing

Run the complete automated test suite:

```bash
php artisan test
```

The current test suite covers:

* Authentication
* User registration
* User login
* Sanctum authentication
* Logout and token revocation
* User roles
* Authorization
* Service policies
* Service authorization
* Service creation
* Service update
* Service deletion
* Service retrieval
* Service listing
* Validation
* Not found handling
* Pagination
* Filtering
* Sorting
* Searching
* Response structure

The latest full test run:

```text
55 passed
172 assertions
0 failures
```

---

# Project Structure

```text
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       └── V1/
│   ├── Requests/
│   │   └── Services/
│   └── Resources/
├── Models/
├── Policies/
└── Services/

database/
├── migrations/
└── seeders/

routes/
└── api.php

tests/
├── Feature/
└── Unit/
```

The project follows Laravel conventions while keeping application and business logic organized for long-term maintainability.

---

# Development Status

The project is currently under active development.

## Completed

* Laravel 12 project foundation
* PostgreSQL database
* API-first architecture
* REST API foundation
* Laravel Sanctum authentication
* User registration
* User login
* Token authentication
* Logout and token revocation
* Role system
* Admin role
* Author role
* User-role relationships
* Service authorization policy
* Laravel Gate / Policy authorization
* Service Management CRUD
* Pagination
* Filtering
* Sorting
* Searching
* Form Request validation
* Service layer
* API Resources
* Response contract testing
* Automated feature testing

## Currently in Progress

* API documentation expansion
* Additional business domains

## Deferred

* Centralized error response standardization
* Security hardening
* Performance optimization
* CI/CD
* Production deployment
* Frontend handoff

---

# Development Principles

The project follows several development principles:

* YAGNI — build what is actually needed
* Minimal Refactoring
* Test Before Moving Forward
* Avoid repeating completed work
* Incremental / Vertical Slice development
* Keep the API stable and predictable
* Prefer simple solutions over unnecessary abstraction

---

# Frontend

The production frontend is planned as a separate application using:

**Next.js + TypeScript**

The frontend will communicate with this backend through the REST API.

---

# Project

**KimiagarSoft**

Website:

https://kimiagarsoft.com

Repository:

https://github.com/KimiagarSoft/kimiagarsoft-backend

---

# License

This project is proprietary software developed for **KimiagarSoft**.
