# xCloud Server Management

## Track Chosen

**Track A**

## Setup Instructions

This project uses **Laravel** for the backend and **MySQL** as the database, containerized with **Docker** for easy setup and reproducibility.

### Prerequisites

- **Docker** and **Docker Compose** installed on your machine.

### Steps

1. **Clone the repository**:
   ```bash
   git clone https://github.com/hafizulislamhfz/xcloud-server-management.git
   cd xcloud-server-management
   ```

2. **Copy environment file**:
   ```
   cp .env.example .env
   ```

4. **Build and start containers** using Docker Compose:
    ```bash
   ./vendor/bin/sail up -d
   ```
   - Access the app at `http://localhost`.

5. **Enter the app container bash:**
   ```bash
   ./vendor/bin/sail bash
   ```

6. **Generate app key**:
   ```bash
   php artisan key:generate
   ```

7. **Run migrations & seed**:
   ```bash
   php artisan migrate --seed
   ```
    This will:

    - Create all database tables.
    - Seed **500 dummy servers**.
    - Create a default user with credentials:

      **Email**: `test@example.com`

      **Password**: `password`

The API is ready for testing. Import the Postman collection: [Download Postman Collection](https://drive.google.com/file/d/1jH4EZVjzQxk1djyW0gbqN0oM4y3AxaO6/view?usp=sharing).

## AI Collaboration Process

I used AI tools to enhance productivity while ensuring I validated and refined their output.

### Tools Used

- **ChatGPT**: Generated initial test cases.
- **GitHub Copilot**: Provided inline code completions in VS Code.

### What I Asked & Why

- **ChatGPT**: Requested "unit test cases for validating server creation in Laravel with PHPUnit, covering edge cases like invalid IP, duplicate names, and resource limits" to bootstrap testing and save time on boilerplate.
- **GitHub Copilot**: Used for auto-completions by writing comments to get relevant code snippets for Laravel controllers, models, and validation.

### What I Accepted vs. Rewrote

- **ChatGPT**: Accepted ~70% of test cases (happy paths, simple failures). Rewrote ~30% to align with Laravel's Eloquent ORM (e.g., provider-specific uniqueness) and fix issues.
- **Copilot**: Accepted most completions for routine code (e.g., controller methods, validation rules), rewrote ~30% for Laravel conventions or custom logic.

## Debugging Journey

I addressed two debugging challenges:

1. **Slow Query (5k+ records)**:
   - **Issue**: `/api/v1/servers` endpoint lagged with large datasets due to unindexed queries and full table scans.
   - **Solution**: 
     - Added **MySQL indexes** in the migration:
       ```php
       $table->unique(['provider', 'name'], 'provider_name_unique');
       $table->index('provider');
       $table->index('status');
       ```
     - Implemented **pagination** to limit returned records per request, reducing data transfer and memory usage.


2. **Duplicate IPs Under Concurrency**:
   - **Issue**: Concurrent `POST /api/v1/servers` requests could create duplicate `ip_address` entries due to race conditions.
   - **Solution**: 
     - Added a **unique index** on `ip_address` in the database migration:
       ```php
       $table->string('ip_address')->unique();
       ```
     - Added **Laravel validation** to check uniqueness before inserting:
       ```php
       'ip_address' => ['required', 'ipv4', 'unique:servers,ip_address'],
       ```
     - The API returns a **422 Unprocessable Entity** with a clear message if validation fails.
   - **Outcome**: Duplicate IPs are prevented at both the application and database levels, ensuring data integrity even under concurrency.


## Tech Decisions & Trade-offs

- **Stack**: Chose Laravel for rapid API development with built-in features (Eloquent ORM, validation, middleware) and MySQL for relational integrity and indexing support.

- **Auth**: Implemented **token-based authentication**. Users log in with email/password and receive a token, which must be included in the `Authorization: Bearer {token}` header for protected API requests. This keeps authentication simple and functional for the challenge.

- **Rate Limiting**: Applied Laravel's `throttle` middleware (10 req/min) to prevent abuse while allowing testing flexibility.

- **Trade-off**: Skipped caching (e.g., Redis) to simplify setup, relying on MySQL indexes for performance. This may limit scalability for very high traffic but reduces complexity.

- **Testing**: Focused on unit tests for validation and feature tests for API flows using PHPUnit. Limited exhaustive edge-case tests to fit within time constraints.

## Time Spent

- **Total**: ~5.5 hours
  - Setup and scaffolding: 1 hour
  - API development: 2 hours
  - Testing: 1 hours
  - Debugging: 1 hour
  - Documentation: 0.5 hours