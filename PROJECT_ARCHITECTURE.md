# Project Architecture Analysis

## 1. Project overview
This project is a legacy PHP admin dashboard for portfolio/project management. It is structured as a monolithic application where each page is a controller and data handler at the same time.

Core technical traits:
- PHP + MySQL via mysqli
- Session-based authentication
- Page-level business logic mixed with HTML rendering
- Shared config file for DB connection
- AdminLTE-based UI assets
- Spreadsheet import/export support via PHPSpreadsheet

## 2. Current architecture pattern
The application does not use a modern MVC, layered, or service-based architecture. Instead, the pattern is roughly:

1. Include shared config and library files
2. Start session
3. Read request data from $_POST or $_GET
4. Run direct SQL queries
5. Render page HTML and tables
6. Handle create/edit/delete flows inline in the same file

Typical pattern:
- [config/db_conf.php](config/db_conf.php): central DB connection setup
- [index.php](index.php): dashboard shell and initial view
- [form.php](form.php): login logic + role-based access setup
- [projects.php](projects.php): list projects and render table
- [add-newproject.php](add-newproject.php): insert project data
- [edit_projects.php](edit_projects.php): update project data

This is a classic procedural monolith, which is quick to build but harder to scale and secure.

## 3. File structure and responsibilities

### Core config and app bootstrapping
- [config/db_conf.php](config/db_conf.php): database credentials, connection, error handling
- [index.php](index.php): dashboard entry point
- [form.php](form.php): login page and authorization logic
- [includes/header.php](includes/header.php): common layout header
- [includes/topbar.php](includes/topbar.php): top navigation
- [includes/sidebar.php](includes/sidebar.php): menu and permission-aware navigation
- [includes/footer.php](includes/footer.php): footer layout
- [includes/lib/main_lib.php](includes/lib/main_lib.php): general helper functions / session utilities

### User and role management
- [add-user.php](add-user.php): create new user
- [edit-user.php](edit-user.php): edit user details
- [add-roles.php](add-roles.php): create roles
- [edit_role.php](edit_role.php): edit role record
- [manage_roles.php](manage_roles.php): role listing and management
- [manage_permissions.php](manage_permissions.php): permission handling
- [show-users.php](show-users.php): display users

### Project, lead, client and contract management
- [projects.php](projects.php): project listing
- [all_projects.php](all_projects.php): all project records view
- [add-newproject.php](add-newproject.php): project creation
- [edit_projects.php](edit_projects.php): project editing
- [clients.php](clients.php): client list
- [add_client.php](add_client.php): add client
- [edit_client.php](edit_client.php): update client
- [leads.php](leads.php): lead management
- [add_lead.php](add_lead.php): add lead
- [edit_lead.php](edit_lead.php): edit lead
- [contracts.php](contracts.php): contract list
- [add_contract.php](add_contract.php): add contract
- [assignments.php](assignments.php): assignment records
- [add-assignment.php](add-assignment.php): create assignment

### Tasks, milestones and estimations
- [tasks.php](tasks.php): tasks grid/list
- [add-task.php](add-task.php): create task
- [save-task.php](save-task.php): task save flow
- [sub-tasks.php](sub-tasks.php): subtask operations
- [get-task.php](get-task.php): fetch task details
- [get-milestones.php](get-milestones.php): fetch milestones
- [save-milestone.php](save-milestone.php): persist milestone
- [delete-milestone.php](delete-milestone.php): remove milestone
- [estimations.php](estimations.php): estimate list
- [add-estimation.php](add-estimation.php): create estimation
- [edit-estimation.php](edit-estimation.php): edit estimation
- [estimation_success_handler.php](estimation_success_handler.php): estimation finalization logic
- [update_estimation_field.php](update_estimation_field.php): update estimation fields
- [addToEstimation.php](addToEstimation.php): add data into estimation workflow

### Brands, plugins, content and portal pages
- [brands.php](brands.php): brand listing
- [add-brand.php](add-brand.php): create brand
- [edit-brand.php](edit-brand.php): update brand
- [plugins.php](plugins.php): plugin list
- [add-plugin.php](add-plugin.php): create plugin
- [edit-plugin.php](edit-plugin.php): edit plugin
- [content.php](content.php): content view
- [portfolio.php](portfolio.php): portfolio page data

### Import and master data workflows
- [import.php](import.php): import UI
- [import-process.php](import-process.php): data extraction logic
- [import_data.php](import_data.php): import processing entry
- [import-data-process.php](import-data-process.php): bulk processing
- [master.php](master.php): master table actions
- [master-form.php](master-form.php): master form
- [general.php](general.php): general data actions
- [general-form.php](general-form.php): general form
- [specific.php](specific.php): specific data actions
- [specific-form.php](specific-form.php): specific form
- [specific_archive.php](specific_archive.php): archived records
- [view_sheet.php](view_sheet.php): display spreadsheet-like views
- [load_data.php](load_data.php): load data into UI

### Miscellaneous and system utilities
- [check-ssl.php](check-ssl.php): SSL validation
- [newproject_dbcon.php](newproject_dbcon.php): alternate DB connection helper
- [select2links.php](select2links.php): UI link or lookup helper
- [uploads/](uploads/): uploaded files and media
- [vendor/](vendor/): dependency libraries
- [myadmin/](myadmin/): phpMyAdmin copy bundled with the project
- [adminer/](adminer/), [adminer2/](adminer2/), [adminer3/](adminer3/), [adminer4/](adminer4/), [adminer5/](adminer5/), [adminer6/](adminer6/): multiple DB administration tools placed in the project

## 4. Current business logic

### Authentication and access control
The login flow in [form.php](form.php) does the following:
- Reads username/email and password
- Escapes input with mysqli_real_escape_string
- Fetches user row from users
- Verifies password using password_verify
- Reads the user role from roles
- If the role is admin, sets full access
- Otherwise loads access rules from user_brand_role and then resolves brand/role/entity names from related tables
- Stores access information into $_SESSION['access'] and $_SESSION['is_admin']

This is a role-based access model but it is not centralized into proper authorization middleware or service logic.

### Brand-role-entity permission model
The sidebar in [includes/sidebar.php](includes/sidebar.php) builds allowed menus based on:
- $_SESSION['access']
- $_SESSION['is_admin']
- brand names, role names, entity names

This indicates the app has a multi-brand and multi-role permission system, but the logic is embedded in UI code instead of a dedicated permission layer.

### CRUD operations for main entities
From the file names and patterns, the app includes CRUD flows for:
- users
- roles
- brands
- plugins
- leads
- clients
- tasks
- milestones
- projects
- assignments
- contracts
- estimations

Most pages follow this procedural pattern:
- read POST data
- clean input
- build SQL query
- execute query
- redirect or show result

### Spreadsheet/import workflow
The project includes import logic for large dataset processing. [import-process.php](import-process.php) and related files read uploaded records and map them to database tables. This suggests a data-import pipeline for master/general/specific data sheets.

### Estimation workflow
There are dedicated files such as:
- [add-estimation.php](add-estimation.php)
- [edit-estimation.php](edit-estimation.php)
- [update_estimation_field.php](update_estimation_field.php)
- [estimation_success_handler.php](estimation_success_handler.php)

This indicates a custom business workflow for managing estimate entries, often with line-by-line updates and final submission handling.

## 5. Key architecture risks and anti-patterns
- Hardcoded DB credentials in [config/db_conf.php](config/db_conf.php)
- No .env or environment segmentation
- No central validation layer
- No CSRF protection on forms
- SQL queries embedded directly in each page
- Duplicate adminer instances expose DB admin panels
- Repeated or conflicting includes
- Mixed business logic and presentation in the same PHP files
- Unclear data model boundaries between master/general/specific records
- No test suite or automated QA

## 6. Recommended architecture direction
A cleaner long-term structure would be:

- public/ or root web entrypoints
- app/Controllers/
- app/Models/
- app/Services/
- app/Repositories/
- config/
- library/
- bootstrap.php
- .env
- .env.example

The current application should ideally be migrated into:
- authentication service
- authorization service
- database repository layer
- request validation layer
- UI templates separated from business logic

## 7. Summary
This project is a procedural PHP portfolio/admin application with strong operational business logic implemented directly in page files. It works as a monolithic application but is vulnerable to maintainability and security issues because credentials, access logic, and CRUD operations are spread across many files.

The highest-priority improvements are:
1. move credential management to .env
2. centralize database access
3. remove or restrict adminer copies
4. move business logic into domain modules
5. add validation, security headers, and tests
