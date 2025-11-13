# New Project Template

A modern, production-ready Laravel 12 starter template for building RESTful APIs and web applications. This template provides a well-structured foundation with built-in authentication (token-based via Laravel Sanctum), role & permission management (Spatie), API resources, example controllers, database migrations, seeders, and a minimal Vite-powered frontend setup.

---

## 🎯 Key Features

-   ✅ **Laravel 12** with PHP 8.2+
-   ✅ **Token-Based Authentication** – Laravel Sanctum for secure API access
-   ✅ **Role & Permission Management** – Spatie Laravel Permission (v6.21) integration
-   ✅ **RESTful API Structure** – Organized controllers with API resources
-   ✅ **Consistent JSON Responses** – Custom `ApiResponse` trait for standardized response formats
-   ✅ **Advanced Filtering** – Users can be filtered by role, permission, or search query
-   ✅ **Database Seeding** – Admin user and permission seeders included
-   ✅ **Pest Testing** – Modern testing framework with example tests
-   ✅ **Vite + Tailwind CSS** – Frontend build tool with CSS framework
-   ✅ **Form Request Validation** – Dedicated request classes for input validation
-   ✅ **API Resources** – Structured response transformers for models

---

## 📋 Project Purpose

This template is ideal for developers and teams building:

-   **Secure REST APIs** with token authentication and role-based access control
-   **Admin dashboards** or back-office applications
-   **Microservices** with permission-based authorization
-   **SPA backends** (React, Vue, Angular compatible)
-   **Rapid prototypes** with production-grade structure

---

## 🏗️ Architecture Overview

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Controller.php              # Base controller
│   │   └── Api/
│   │       ├── UserController.php      # User CRUD operations
│   │       └── Auth/
│   │           ├── AuthController.php              # Login, Register, Logout, Refresh
│   │           └── RolePermissionController.php    # Role & Permission management
│   ├── Requests/
│   │   ├── RoleRequest.php             # Role validation
│   │   └── PermissionRequest.php       # Permission validation
│   └── Resources/
│       └── Api/
│           ├── UserResource.php        # User JSON response transformer
│           ├── RoleResource.php        # Role JSON response transformer
│           └── PermissionResource.php  # Permission JSON response transformer
├── Models/
│   └── User.php                        # User model with HasRoles, filters, and API tokens
├── Traits/
│   └── ApiResponse.php                 # Reusable trait for consistent API responses
└── Providers/
    └── AppServiceProvider.php          # Application service provider
```

### Key Components

#### **ApiResponse Trait** (`App\Traits\ApiResponse`)

Provides two methods for consistent API responses:

-   `successResponse($data, $message, $code)` – Returns `{ status, message, data, meta }`
-   `errorResponse($message, $code, $errors)` – Returns `{ status, message, errors }`

Automatically handles pagination metadata.

#### **User Model** (`App\Models\User`)

-   **Traits**: `HasFactory`, `Notifiable`, `HasApiTokens`, `HasRoles`
-   **Sanctum Guard**: Configured for token-based auth
-   **Filtering**: `scopeFilter()` supports role, permission, and search queries
-   **Attributes**: `name`, `email`, `password`

#### **Controllers**

-   **AuthController** – Authentication endpoints (register, login, logout, token refresh)
-   **UserController** – User management (list, show, update, delete) with filtering
-   **RolePermissionController** – Full CRUD for roles, permissions, and assignments

#### **Resources**

Transform Eloquent models into JSON with consistent formatting. Each resource handles serialization of roles, permissions, and timestamps.

---

## 🚀 Requirements

-   **PHP**: 8.2 or higher
-   **Composer**: Latest version
-   **Node.js**: 16+ and npm/yarn (for Vite)
-   **Database**: MySQL, PostgreSQL, or SQLite
-   **Git**: For version control

### Installed Dependencies

**Production**:

-   `laravel/framework` (^12.0)
-   `laravel/sanctum` (^4.0) – Token authentication
-   `spatie/laravel-permission` (^6.21) – Role & permission management
-   `laravel/tinker` (^2.10.1) – REPL shell

**Development**:

-   `pestphp/pest` (^3.8) – Testing framework
-   `laravel/sail` (^1.41) – Docker environment
-   `laravel/pint` (^1.24) – Code linter

---

## ⚡ Quick Start (Windows PowerShell)

### 1. Clone or Extract the Repository

```powershell
git clone <repository-url> <project-name>
cd <project-name>
```

### 2. Install PHP Dependencies

```powershell
composer install
```

### 3. Install Frontend Dependencies

```powershell
npm install
```

### 4. Setup Environment File

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

Edit `.env` with your database credentials:

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=new_project_template
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Run Database Migrations & Seeders

```powershell
php artisan migrate
php artisan db:seed
```

This creates the database schema and seeds an **admin user** with sample roles and permissions.

### 6. Build Frontend Assets

```powershell
npm run build    # Production build
npm run dev      # Development (watch mode)
```

### 7. Start the Development Server

```powershell
php artisan serve
```

The application will be available at **http://127.0.0.1:8000**

---

## 🔐 Authentication & Authorization

### Token-Based Authentication (Sanctum)

All API endpoints (except `/register` and `/login`) require a Bearer token in the `Authorization` header:

```
Authorization: Bearer <your-token-here>
```

**Obtaining a Token:**

1. Register or login via `/login` endpoint
2. Receive a plaintext token in response
3. Include the token in all subsequent requests

### Role & Permission System

The application uses **Spatie Laravel Permission** for RBAC (Role-Based Access Control):

-   **Roles**: Represent a collection of permissions (e.g., "admin", "editor")
-   **Permissions**: Individual actions (e.g., "create posts", "delete users")
-   **Assignment**: Assign roles or permissions directly to users or roles to permissions

Protect routes using middleware:

```php
Route::post('/admin/users', [UserController::class, 'store'])
    ->middleware('can:create users');
```

---

## 📡 API Endpoints

All endpoints are located in `routes/api.php`. Below is the complete endpoint map:

### Authentication (Public)

| Method | Endpoint    | Description             | Body                                               |
| ------ | ----------- | ----------------------- | -------------------------------------------------- |
| `POST` | `/register` | Register a new user     | `{ name, email, password, password_confirmation }` |
| `POST` | `/login`    | Login and receive token | `{ email, password }`                              |

### Authentication (Protected)

| Method | Endpoint      | Description          |
| ------ | ------------- | -------------------- |
| `POST` | `/v1/logout`  | Revoke current token |
| `POST` | `/v1/refresh` | Generate a new token |

### Users (Protected) – `/v1/users`

| Method   | Endpoint         | Description         | Query Params                                                         |
| -------- | ---------------- | ------------------- | -------------------------------------------------------------------- |
| `GET`    | `/v1/users`      | List all users      | `per_page=10`, `role=admin`, `permission=view_posts`, `search=john`  |
| `GET`    | `/v1/users/{id}` | Get a specific user | —                                                                    |
| `PATCH`  | `/v1/users/{id}` | Update user profile | `{ name, email, password, password_confirmation, current_password }` |
| `DELETE` | `/v1/users/{id}` | Delete a user       | —                                                                    |

### Roles (Protected) – `/v1/roles`

| Method   | Endpoint                       | Description                | Body                         |
| -------- | ------------------------------ | -------------------------- | ---------------------------- |
| `GET`    | `/v1/roles`                    | List all roles             | —                            |
| `POST`   | `/v1/roles`                    | Create a new role          | `{ name }`                   |
| `DELETE` | `/v1/roles/{role}`             | Delete a role              | —                            |
| `POST`   | `/v1/roles/{role}/permissions` | Assign permissions to role | `{ permissions: [1, 2, 3] }` |

### Permissions (Protected) – `/v1/permissions`

| Method   | Endpoint                       | Description             | Body       |
| -------- | ------------------------------ | ----------------------- | ---------- |
| `GET`    | `/v1/permissions`              | List all permissions    | —          |
| `POST`   | `/v1/permissions`              | Create a new permission | `{ name }` |
| `DELETE` | `/v1/permissions/{permission}` | Delete a permission     | —          |

### User Role & Permission Assignment (Protected)

| Method | Endpoint                      | Description               | Body                             |
| ------ | ----------------------------- | ------------------------- | -------------------------------- |
| `POST` | `/v1/users/{user}/role`       | Assign role to user       | `{ role: "admin" }`              |
| `POST` | `/v1/users/{user}/permission` | Assign permission to user | `{ permission: "create_posts" }` |

---

## 📝 Example API Requests

### Register a New User

```bash
curl -X POST http://127.0.0.1:8000/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Response (201)**:

```json
{
    "user": {
        "id": 2,
        "name": "John Doe",
        "email": "john@example.com",
        "created_at": "2025-11-13T10:30:00Z"
    },
    "token": "1|abc123xyz..."
}
```

### Login

```bash
curl -X POST http://127.0.0.1:8000/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

### Fetch Users with Pagination & Filtering

```bash
curl -X GET "http://127.0.0.1:8000/v1/users?per_page=5&role=admin&search=john" \
  -H "Authorization: Bearer 1|abc123xyz..."
```

**Response (200)**:

```json
{
    "status": "success",
    "message": "Users retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "Admin User",
            "email": "admin@example.com",
            "roles": ["admin"],
            "permissions": ["create posts", "edit posts"],
            "created_at": "2025-11-13T08:00:00Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "per_page": 5,
        "total": 1,
        "last_page": 1
    }
}
```

### Create a Role

```bash
curl -X POST http://127.0.0.1:8000/v1/roles \
  -H "Authorization: Bearer 1|abc123xyz..." \
  -H "Content-Type: application/json" \
  -d '{ "name": "editor" }'
```

---

## 🧪 Testing

The project uses **Pest** for testing. Tests are located in the `tests/` directory.

### Run All Tests

```powershell
composer test
```

Or directly with Pest:

```powershell
./vendor/bin/pest
```

### Run Tests in a Specific File

```powershell
./vendor/bin/pest tests/Feature/ExampleTest.php
```

### Run Tests with Coverage

```powershell
./vendor/bin/pest --coverage
```

### Example Test File

Tests can be feature tests (testing API behavior) or unit tests (testing individual classes). See `tests/Feature/ExampleTest.php` for examples.

---

## 🌱 Database Seeding

The `DatabaseSeeder` automatically runs when you execute `php artisan migrate:fresh --seed`.

### Available Seeders

-   **DatabaseSeeder** (`database/seeders/DatabaseSeeder.php`) – Main seeder orchestrator
-   **AdminUserSeeder** (`database/seeders/AdminUserSeeder.php`) – Creates a default admin user

### Seeding Data

```powershell
# Run all seeders
php artisan db:seed

# Run a specific seeder
php artisan db:seed --class=AdminUserSeeder

# Fresh migration with seeds (careful: deletes all data)
php artisan migrate:fresh --seed
```

### Custom Seeders

Create new seeders to populate default roles, permissions, or other static data:

```powershell
php artisan make:seeder RolePermissionSeeder
```

---

## ⚙️ Environment Configuration

Key `.env` variables:

```ini
# Application
APP_NAME="New Project Template"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=new_project_template
DB_USERNAME=root
DB_PASSWORD=

# Mail (optional)
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@example.com

# Session & Cache
SESSION_DRIVER=cookie
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
```

Refer to `config/app.php`, `config/auth.php`, and `config/sanctum.php` for detailed configuration options.

---

## 🛠️ Development Workflow

### Local Development Server with Auto-Reload

Use the custom `dev` Composer script to run the server, queue listener, Vite dev server, and logs simultaneously:

```powershell
composer run dev
```

This starts:

-   PHP artisan serve
-   Laravel queue listener
-   Laravel Pail (log viewer)
-   Vite dev server (frontend)

### Code Formatting & Linting

Format code with **Pint**:

```powershell
./vendor/bin/pint
```

### Tinker Shell

Interactive PHP shell for testing code:

```powershell
php artisan tinker
```

---

## 📂 File Structure

```
├── app/                          # Application code
│   ├── Http/Controllers/         # API & web controllers
│   ├── Http/Requests/            # Form request validation
│   ├── Http/Resources/           # API resource transformers
│   ├── Models/                   # Eloquent models
│   ├── Traits/                   # Reusable traits
│   └── Providers/                # Service providers
├── config/                       # Configuration files
├── database/
│   ├── migrations/               # Database schema migrations
│   ├── seeders/                  # Database seeders
│   └── factories/                # Model factories for testing
├── routes/
│   ├── api.php                   # API routes
│   ├── web.php                   # Web routes
│   └── console.php               # Artisan commands
├── resources/
│   ├── js/                       # JavaScript files
│   ├── css/                      # CSS & Tailwind
│   └── views/                    # Blade templates
├── tests/                        # Test suite (Pest)
├── storage/                      # File storage, logs, cache
├── bootstrap/                    # Framework bootstrapping
├── public/                       # Public assets
├── vite.config.js                # Vite configuration
├── phpunit.xml                   # PHPUnit test configuration
├── composer.json                 # PHP dependencies
└── package.json                  # Node.js dependencies
```

---

## 🔄 Continuous Integration / Deployment

To set up CI/CD (GitHub Actions, GitLab CI, etc.), add a workflow file:

1. Create `.github/workflows/test.yml` (for GitHub Actions)
2. Run tests on every push:
    ```yaml
    - run: composer install
    - run: composer test
    ```

---

## 📚 Useful Artisan Commands

```powershell
# Create a new controller
php artisan make:controller Api/ProductController --resource

# Create a model with migration
php artisan make:model Product -m

# Create a form request
php artisan make:request StoreProductRequest

# Create a resource class
php artisan make:resource ProductResource

# Create a test
php artisan make:test ProductTest --pest

# Clear application cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

---

## 🤝 Contributing

1. **Fork** the repository
2. Create a feature branch: `git checkout -b feature/your-feature`
3. Commit changes: `git commit -am 'Add new feature'`
4. Push to branch: `git push origin feature/your-feature`
5. Submit a **Pull Request**

### Guidelines

-   Follow **PSR-12** coding standards
-   Write tests for new features
-   Update documentation if needed
-   Keep commits focused and descriptive

---

## 📋 Checklist for New Features

-   [ ] Create models and migrations
-   [ ] Write API controllers
-   [ ] Create form request validation
-   [ ] Add API resources for responses
-   [ ] Define routes in `routes/api.php`
-   [ ] Write tests (feature & unit)
-   [ ] Update this README with endpoints
-   [ ] Seed any default data

---

## 🚨 Common Issues & Troubleshooting

### "No application encryption key has been specified"

```powershell
php artisan key:generate
```

### Database connection refused

Check `.env` database credentials and ensure your database service is running.

### Vite not loading correctly

```powershell
npm run dev
```

Ensure the Vite dev server is running.

### Sanctum tokens not working

Verify `config/sanctum.php` has the correct guard (`sanctum`) and check middleware in `routes/api.php`.

### Permission denied errors

Ensure `storage/` and `bootstrap/cache/` directories are writable:

```powershell
attrib -R storage bootstrap\cache /S
```

---

## 📜 License

This project is open-source software licensed under the MIT License. See `LICENSE` file for details.

---

## 👨‍💻 Support & Questions

For issues, questions, or suggestions:

-   Open an issue on GitHub
-   Check existing documentation in `docs/` (if available)
-   Review Laravel docs: https://laravel.com/docs

---

## 🎓 Learning Resources

-   [Laravel Documentation](https://laravel.com/docs)
-   [Laravel Sanctum](https://laravel.com/docs/sanctum)
-   [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission/v6/introduction)
-   [Pest PHP](https://pestphp.com)
-   [Tailwind CSS](https://tailwindcss.com)
-   [Vite](https://vitejs.dev)

---

**Last Updated**: November 2025  
**Laravel Version**: 12  
**PHP Version**: 8.2+
