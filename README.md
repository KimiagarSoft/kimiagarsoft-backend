# KimiagarSoft Backend

Professional backend API for **KimiagarSoft**, built with **Laravel 12** and designed with an **API-first architecture**.

This project provides the backend foundation and REST API for the future KimiagarSoft frontend and other clients.

---

## Architecture

```text
Client
   │
   ▼
Next.js + TypeScript
   │
   ▼
REST API
   │
   ▼
Laravel 12
   │
   ▼
PostgreSQL
```

The backend and frontend are developed as separate applications.

---

## Tech Stack

| Technology      | Version / Role               |
| --------------- | ---------------------------- |
| PHP             | 8.2+                         |
| Laravel         | 12                           |
| API             | REST API                     |
| Database        | PostgreSQL                   |
| Authentication  | Laravel Sanctum              |
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

## Testing

Run the automated test suite:

```bash
php artisan test
```

---

## Project Structure

```text
app/
├── Http/
├── Models/
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

## Development Status

The project is currently under active development.

Current foundation includes:

* Laravel 12 project foundation
* PostgreSQL database
* API-first architecture
* REST API foundation
* Laravel Sanctum foundation
* Database migrations
* Eloquent models
* Model relationships
* Automated testing foundation

Upcoming development areas include:

* Authentication and authorization
* API Resources
* Request validation
* Business services
* Comprehensive automated testing
* API documentation
* Production deployment

---

## Frontend

The production frontend is planned as a separate application using:

**Next.js + TypeScript**

The frontend will communicate with this backend through the REST API.

---

## Project

**KimiagarSoft**

Website: https://kimiagarsoft.com

Repository: https://github.com/KimiagarSoft/kimiagarsoft-backend

---

## License

This project is proprietary software developed for **KimiagarSoft**.
