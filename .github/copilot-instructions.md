<!-- Copilot instructions for Loan-api repository -->
# Copilot / AI Agent Instructions

This project is a Laravel API (Laravel 12, PHP 8.2) implementing a Loan Management System. Below are focused, actionable notes to help AI coding agents be productive immediately.

- Architecture summary: Controller -> Service -> Repository -> Eloquent Model. Controllers live under `app/Http/Controllers/Api/V1`, services under `app/Services/V1`, repositories under `app/Repositories` with interfaces in `app/Repositories/Contracts` and models in `app/Models`.
- Dependency injection: repository interfaces are bound to implementations in [app/Providers/RepositoryServiceProvider.php](app/Providers/RepositoryServiceProvider.php). Use the interface type-hints (e.g. `UserRepositoryInterface`) when resolving dependencies.

- Authentication: Laravel Sanctum is used. Token creation happens in services (see `AuthService::login`/`register` in `app/Services/V1/User/AuthService.php`). Protect API routes using `auth:sanctum` for users and `auth:admin` for admin routes.

- API surface: main routes are in `routes/api.php` under `v1/user` and `v1/admin`. Use these routes as canonical examples for endpoints, middleware, and route grouping.

- Dataflow & error handling: business logic lives in services. Services may throw `ValidationException` for standard validation/permission errors — callers (controllers) rely on Laravel exception handling. Keep controller methods thin: validate -> call service -> return resource/response.

- Repositories: follow interface signatures in `app/Repositories/Contracts/*`. Typical methods: `find`, `create`, `update`, `paginate`, `search`, and domain-specific helpers like `getUserLoans`.

- Examples to reference when implementing or changing features:
  - Service example: `app/Services/V1/User/AuthService.php` (token creation, password checks).
  - Repository example: `app/Repositories/UserRepository.php` (hashing, search, pagination).
  - DI bindings: `app/Providers/RepositoryServiceProvider.php` (how services are constructed).

- Tests & local workflow:
  - Tests use Pest/PHPUnit. Run tests with `composer test` (runs `php artisan test`). PHPUnit runs with sqlite in-memory by default per `phpunit.xml`.
  - Development run: `composer run dev` launches server, queue listener, and frontend dev task concurrently. Alternatively, run `php artisan serve` for just the API.
  - Typical setup: `composer install`, copy `.env.example` to `.env` (on Windows: `copy .env.example .env`), then `php artisan key:generate` and `php artisan migrate` (composer post-create scripts may run some of these automatically).

- Conventions and gotchas specific to this repo:
  - Namespaces follow `App\Services\V1\{User|Admin}` and mirror routes under `v1/*`.
  - Repositories may accept model instances in constructors (see `UserRepository`), while many services are manually bound in `RepositoryServiceProvider` with `make()` calls.
  - Use repository interfaces (not concrete classes) in Service constructors to keep behavior consistent with the provider bindings.
  - Some services update tokens and device fields directly on the model (see `AuthService::login`) — be careful when changing token or device management.

- Integrations and external packages:
  - `laravel/sanctum` for auth tokens.
  - `darkaonline/l5-swagger` for API docs.
  - `pestphp/pest` for tests and `laravel/pint` for formatting.

- What to update in PRs / behavior expectations:
  - Keep controllers thin; add business rules in services.
  - Add/update repository interfaces when introducing new repository methods and update `RepositoryServiceProvider` bindings if needed.
  - Update tests (unit/feature) when changing behavior; tests run under sqlite/in-memory so avoid DB-specific SQL during tests.

If anything above is unclear or you want more examples (controller -> service -> repository code snippets), tell me which area to expand and I will iterate.
