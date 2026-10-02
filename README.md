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

The project follows an **Incremental / Vertical Slice** development approach, allowing each business capability to be implemented, tested, documented, and stabilized before moving to the next domain.

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
* Secure authentication
* Role-based authorization
* Automated testing
* Maintainable and scalable code
* Incremental feature development

---

## Development Principles

The project follows several practical development principles:

* **YAGNI** — implement only what is currently needed.
* **Minimal Refactoring** — avoid unnecessary changes to stable code.
* **Test Before Moving Forward** — verify each completed feature before continuing.
* **Incremental Development** — build the system in small, controlled vertical slices.
* **Stable API Design** — avoid unnecessary breaking changes.
* **Simple Solutions** — prefer clear and maintainable implementations over unnecessary complexity.
* **No Repeated Work** — completed and verified features should not be reimplemented without a specific reason.

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

The current API includes:

* Authentication
* Service Management
* Role-based authorization for protected resources

---

# Authentication API

Authentication is implemented using **Laravel Sanctum**.

## Register

```http
POST /api/v1/auth/register
```

Creates a new user account and returns an authentication token.

---

## Login

```http
POST /api/v1/auth/login
```

Authenticates a user and returns an authentication token.

---

## Current User

```http
GET /api/v1/auth/me
```

Returns the currently authenticated user.

This endpoint requires a valid Sanctum authentication token.

---

## Logout

```http
POST /api/v1/auth/logout
```

Logs out the authenticated user and revokes the current authentication token.

This endpoint requires authentication.

---

## Authentication Header

Protected API endpoints use the following authorization header:

```http
Authorization: Bearer {token}
```

---

# Authorization

The API uses Laravel Policies and Gates for authorization.

The current role system contains two roles:

| Role     | Description                                           |
| -------- | ----------------------------------------------------- |
| `admin`  | Administrator with management permissions             |
| `author` | Author role intended for content-related capabilities |

The current Service Management authorization policy allows only administrators to manage services.

### Service Authorization

| User   | Access to Services |
| ------ | ------------------ |
| Guest  | Not authenticated  |
| Author | Forbidden          |
| Admin  | Authorized         |

Protected service endpoints require both:

1. Authentication
2. Authorization

Authorization is enforced through `ServicePolicy`.

---

# Service Management API

Service Management is currently the main completed business domain of the API.

## Endpoints

| Method | Endpoint                     | Authentication | Description        |
| ------ | ---------------------------- | -------------- | ------------------ |
| GET    | `/api/v1/services`           | Required       | List services      |
| GET    | `/api/v1/services/{service}` | Required       | Retrieve a service |
| POST   | `/api/v1/services`           | Required       | Create a service   |
| PUT    | `/api/v1/services/{service}` | Required       | Update a service   |
| DELETE | `/api/v1/services/{service}` | Required       | Delete a service   |

Service endpoints are protected by both Sanctum authentication and the corresponding authorization policy.

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

Ascending order:

```text
sort=sort_order
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

### Validation Ru
