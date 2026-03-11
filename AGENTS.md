# AGENTS.md - Agent Coding Guidelines

This file provides guidelines for agentic coding agents operating in this Laravel 12 repository.

## Project Structure

This is a Laravel 12 API backend. The frontend is a separate React/TypeScript project located at `/Users/ocarrasco/Sites/pro-motors-ui`.

## Language

All documentation, code comments, variable names, and commit messages must be in English.

---

## Commands

### Backend (Laravel API)

```bash
# Run all tests (compact output)
php artisan test --compact

# Run single test file
php artisan test --compact tests/Feature/ExampleTest.php

# Run single test by name filter
php artisan test --compact --filter=testName

# Run all tests with coverage
php artisan test --coverage

# Code formatting (Laravel Pint)
vendor/bin/pint

# Format only changed files
vendor/bin/pint --dirty
```

### Frontend (React + TypeScript)

```bash
# Development server (from /Users/ocarrasco/Sites/pro-motors-ui)
npm run dev

# Build for production
npm run build

# Run lint
npm run lint
```

### Full Stack Development

```bash
# Full dev environment (server + queue + logs + vite)
composer run dev
```

---

## Architecture Patterns

This application follows a layered architecture with clear separation of concerns:

### 1. Repositories (Data Layer)
- **Location**: `app/Data/Repositories/`
- **Pattern**: Implement repository interfaces from `app/Domain/Interfaces/Repositories/`
- **Purpose**: Handle all database operations
- **Example**: `CompanyRepository` implements `CompanyRepositoryInterface`

```php
use App\Domain\Interfaces\Repositories\CompanyRepositoryInterface;

final class CompanyRepository implements CompanyRepositoryInterface
{
    public function findById(int $id): ?Company { }
    public function create(array $data): Company { }
    public function update(Company $company, array $data): Company { }
    public function delete(Company $company): bool { }
}
```

### 2. DTOs (Data Transfer Objects)
- **Location**: `app/Application/DTOs/`
- **Pattern**: Use PHP 8 readonly classes with constructor property promotion
- **Purpose**: Validate and transfer data between layers
- **Must include**: `fromArray()` and `toArray()` methods
- **Validation**: Validate data in constructor, throw `InvalidArgumentException` on invalid data

```php
final readonly class CreateCompanyDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public ?StatusEnum $status = StatusEnum::ACTIVE,
    ) {
        if (empty($this->name)) {
            throw new InvalidArgumentException('Company Name is required');
        }
    }

    public static function fromArray(array $data): self { }
    public function toArray(): array { }
}
```

### 3. Services (Application Layer)
- **Location**: `app/Application/Services/`
- **Pattern**: Use interfaces in `app/Domain/Interfaces/Services/`, implement in `app/Application/Services/`
- **Purpose**: Business logic orchestration
- **Dependency Injection**: Inject repository interfaces, not implementations

```php
use App\Domain\Interfaces\Services\CompanyServiceInterface;

final class CompanyService implements CompanyServiceInterface
{
    public function __construct(
        private CompanyRepositoryInterface $repository,
    ) { }

    public function create(CreateCompanyDTO $data): Company { }
}
```

### 4. Policies (Authorization)
- **Location**: `app/Policies/`
- **Pattern**: Create policies for all models that need authorization
- **Use**: Spatie permissions via `PermissionsEnum`
- **Always use**: `$user->can(PermissionEnum::PERMISSION_NAME)` for authorization checks

```php
class CompanyPolicy
{
    public function view(User $user, Company $company): bool
    {
        return $user->can(PermissionsEnum::VIEW_COMPANY)
            && $company->isAccessibleBy($user);
    }

    public function update(User $user, Company $company): bool
    {
        return $user->can(PermissionsEnum::EDIT_COMPANY)
            && $company->isEditableBy($user);
    }
}
```

### 5. Permissions Enum
- **Location**: `app/Domain/ValueObjects/Enums/PermissionsEnum.php`
- **Use**: Always define permissions in this enum for consistency

---

## Code Style Guidelines

### PHP General
- Always use curly braces for control structures, even single-line statements
- Use PHP 8 constructor property promotion: `public function __construct(public Type $property) { }`
- Never have empty `__construct()` with zero parameters (unless private)
- Use explicit return type declarations on all methods and functions
- Use appropriate PHP type hints for parameters

### Imports
- Group imports by type (native, packages, local)
- Order: Laravel, Spatie, custom packages, App namespace
- Example:
```php
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use App\Domain\ValueObjects\Enums\StatusEnum;
use App\Support\Traits\Blamable;
```

### Naming Conventions
- **Classes**: PascalCase (e.g., `Company`, `UserController`)
- **Methods/variables**: camelCase (e.g., `getFullName()`, `$companyId`)
- **Constants**: UPPER_SNAKE_CASE
- **Enums**: TitleCase keys (e.g., `StatusEnum::ACTIVE`)
- **Database columns/tables**: snake_case
- Use descriptive names: `isRegisteredForDiscounts`, not `discount()`

### Models
- Use `$fillable` for mass assignment with `@var list<string>` PHPDoc
- Use `$hidden` for serialization with `@var list<string>` PHPDoc
- Define casts in `casts()` method (Laravel 12 style), not `$casts` property
- Always add return type hints to relationship methods
- Use eager loading to prevent N+1 queries
- Define scopes with type-hinted parameters: `scopeAccessibleBy(Builder $query, User $user): Builder`

### Controllers
- Use Form Request classes for validation (create with `php artisan make:request`)
- Return proper HTTP responses: `response()->json()`, `response()->noContent()`
- Use dependency injection for services/repositories
- Inject services through interfaces: `public function __construct(private CompanyServiceInterface $service) { }`

### Enums
- Use backed enums when values are needed: `enum StatusEnum: string`
- Place in `app/Domain/ValueObjects/Enums/`
- Group related cases with PHPDoc comments
- Use `EnumCaster` for casting in DTOs

### Database/Migrations
- Use Eloquent relationships before raw `DB::`; prefer `Model::query()` or relationship methods
- queries
- Avoid When modifying columns, include ALL attributes (they will be dropped otherwise)
- Use proper migration naming: `create_companies_table`, `add_status_to_companies`

### API Resources
- Use Eloquent API Resources for transformations
- Group in `app/Http/Resources/`
- Use `whenLoaded()` for conditional relationships

### Error Handling
- Use exceptions with proper HTTP status codes
- Throw `ValidationException` for validation errors
- Use `abort(404)` or `abort(403)` for not found/forbidden
- Let exceptions propagate; don't catch unnecessarily

### Testing (PHPUnit)
- All tests must be PHPUnit classes (not Pest)
- Create with `php artisan make:test --phpunit NameTest`
- Use `--unit` flag for unit tests, default is feature tests
- Use model factories: `User::factory()->create()`
- Use `$this->faker` or `fake()` for fake data
- Test happy path, failure paths, and edge cases

### Configuration
- Never use `env()` outside config files
- Use `config('key')` everywhere else
- Define environment variables in `.env` and reference in config files

### Middleware (Laravel 12)
- Registered in `bootstrap/app.php` using `Application::configure()->withMiddleware()`
- Not in `app/Http/Kernel.php` (does not exist)

### Traits
- Custom traits in `app/Support/Traits/`
- Use descriptive trait names: `Blamable`, `HasSlug`

### Comments
- Prefer PHPDoc blocks over inline comments
- Never add unnecessary comments explaining obvious code
- Use PHPDoc for complex array shapes

---

## Git Guidelines

### Branch Strategy (Gitflow)
- **Main branches**: `main` (production), `dev` (integration)
- **Feature branches**: `feature/TICKET-description` (from `dev`)
- **Hotfix branches**: `hotfix/TICKET-description` (from `main`)
- **Release branches**: `release/v1.x.x` (from `dev`)

### Commit Messages (Conventional Commits)
- Keep commit messages under 50 characters total, prioritize clarity over completeness
- Format: `type(scope): description`
- Types: `feat`, `fix`, `docs`, `style`, `refactor`, `test`, `chore`, `perf`, `ci`, `build`
- Describe what was changed and why, not how it was implemented
- Example: `fix: resolve login timeout to improve user experience`
- Write commit subjects in imperative mood (e.g., "Add", "Fix", "Update")
- Always include the issue number: `type: description (#123)` when relevant

### Stash Messages
- Include the current feature or task: `WIP: feature-name - specific changes`
- Start with action verbs: "Adding", "Fixing", "Updating", "Removing", "Refactoring"
- Format: `[branch-name] brief description of stashed changes`

### Pull Requests
- Include a "How to Test" section with specific steps to verify the changes
- Include a "Breaking Changes" section if any exist, clearly explaining impact and migration steps
- Include a "Review Focus" section highlighting specific areas for reviewers

### Commit Organization
- Each commit should represent one logical change that could be reverted independently
- Never mix refactoring, formatting, or cleanup changes with new features or bug fixes
- Order commits logically so each builds on the previous one (bisect-friendly)

### Explaining Changes
- Format explanations as bullet points, one per major change or file modified
- Always mention which components, modules, or areas of the codebase were affected
- Explain technical concepts in simple terms that junior developers can understand

---

## Artisan Commands

Always use `--no-interaction` flag:

```bash
php artisan make:model ModelName --no-interaction
php artisan make:controller ControllerName --no-interaction
php artisan make:migration create_table_name --no-interaction
php artisan make:request FormRequestName --no-interaction
php artisan make:policy PolicyName --no-interaction
php artisan make:service ServiceName --no-interaction
php artisan make:dto DtoName --no-interaction
```
