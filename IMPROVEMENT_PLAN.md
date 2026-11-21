# Improvement Plan

## 1. Immediate security fixes

### Move database credentials to environment variables
The current project stores credentials directly in [config/db_conf.php](config/db_conf.php). This should be replaced by a `.env` setup.

Recommended approach:
- create `.env` locally and keep it out of Git
- create `.env.example` as a template for all developers
- load config values with PHP dotenv or a lightweight local loader

### Remove or restrict adminer copies
There are multiple DB admin copies:
- [adminer/index.php](adminer/index.php)
- [adminer2/index.php](adminer2/index.php)
- [adminer3/index.php](adminer3/index.php)
- [adminer4/index.php](adminer4/index.php)
- [adminer5/index.php](adminer5/index.php)
- [adminer6/index.php](adminer6/index.php)

These should be:
- removed from production
- disabled behind local-only access
- replaced with a single secured admin tool available only in development

## 2. Code architecture improvements

### Convert the project to a modular structure
Current state:
- page files do business logic and UI together
- many direct SQL calls in same file

Target structure:
- `public/` = entry scripts
- `app/Controllers/` = request handling
- `app/Models/` = data logic
- `app/Services/` = business workflows
- `app/Repositories/` = DB access
- `config/` = environment and app configuration
- `includes/` = shared templates and helpers

### Centralize DB layer
Create a single connection bootstrap and repository pattern:
- `config/database.php`
- `app/Repositories/BaseRepository.php`
- `app/Repositories/UserRepository.php`
- `app/Repositories/ProjectRepository.php`
- `app/Repositories/LeadRepository.php`

This will remove duplicated SQL logic and reduce bugs.

## 3. Business-logic cleanups

### Authentication/authorization
Move login and permission flow into:
- `AuthService`
- `RoleAccessService`
- `PermissionChecker`

This will keep the access logic separate from the HTML page code.

### CRUD and import workflows
Organize the app around domain modules such as:
- users
- roles
- permissions
- brands
- projects
- clients
- leads
- contracts
- tasks
- estimations
- import/importer service

This reduces duplicate code and makes it easier to maintain.

## 4. Security hardening
- add password hashing checks for all user creation flows
- use prepared statements instead of string concatenation
- validate all request data
- add CSRF tokens on POST forms
- set secure HTTP headers
- restrict file upload type/size
- disable PHP error display in production
- use proper session management and timeout controls

## 5. Developer workflow improvements
- keep `.env` out of source control
- add `.env.example` for setup
- enforce code standards with PHP linting or static analysis
- create a simple database migration system
- add debug logs for production without exposing SQL errors
- add unit/integration tests for login, project create, task create, estimation update

## 6. Suggested new files to add
- `.env.example`
- `config/app.php`
- `config/database.php`
- `app/Services/AuthService.php`
- `app/Services/PermissionService.php`
- `app/Repositories/UserRepository.php`
- `app/Repositories/ProjectRepository.php`
- `app/Controllers/AuthController.php`
- `app/Controllers/ProjectController.php`
- `database/migrations/`
- `tests/`

## 7. Recommended order of work
1. secure config and environment variables
2. clean up DB access layer
3. isolate auth and role logic
4. move project/task CRUD into repositories/services
5. remove or restrict adminer exposure
6. add validation and security controls
7. build a test suite and deployment checklist

## 8. Recommended next step
The next practical step is to convert the current hardcoded DB configuration into a `.env`-based pattern and then group the duplicate page logic into a small service/repository layer. That will give the project a stable foundation without a full rewrite.
