# Document Management System (DMS)

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![Laravel](https://img.shields.io/badge/Framework-Laravel-red)](https://laravel.com)

Document Management System (DMS) is a Laravel-based web application for corporate document management across multiple organizations. It provides document upload/versioning, sharing, tagging, auditing, SSO support and integration points for SFTP and AI services.

## Table of contents

-   [Features](#features)
-   [Tech stack](#tech-stack)
-   [Prerequisites](#prerequisites)
-   [Quickstart (local)](#quickstart-local)
-   [Demo Accounts](#demo-accounts)
-   [User Roles](#user-roles)
-   [Configuration notes](#configuration-notes)
-   [Running the app](#running-the-app)
-   [Testing](#testing)
-   [Deployment notes (SQL Server / Windows)](#deployment-notes-sql-server--windows)
-   [Environment variables highlights](#environment-variables-highlights)
-   [Contributing](#contributing)
-   [License](#license)

## Features

-   Document upload, versioning and storage
-   Folder management and sharing (documents & folders)
-   Starred documents and folders
-   Document tagging and search
-   Audit logs and user activity tracking
-   Import users via CSV
-   SAML / Azure SSO and Microsoft integration
-   Optional SFTP storage driver
-   Gemini AI integration (optional)

## Tech stack

-   Backend: PHP (Laravel 10)
-   Frontend: Blade + optional Vite + JS
-   Database: MySQL / PostgreSQL / SQLite / SQL Server (configurable)

## Prerequisites

-   PHP >= 8.1
-   Composer
-   Node.js & npm (optional, for asset building)
-   Database: SQLite (default for local dev in `.env.example`) or MySQL / SQL Server

## Quickstart (local)

Clone the repository and install dependencies:

```bash
git clone <repo-url>
cd document-management-system
composer install
```

Copy the environment file and set credentials:

```bash
cp .env.example .env
# Edit .env to set DB and other settings
```

Generate the app key, migrate and seed the database:

```bash
php artisan key:generate
php artisan migrate
php artisan db:seed
```

This will seed:
- **Roles**: Super Admin, Company Admin, Contributor, Viewer
- **Organizations**: Headquarters and 3 departments (Sales, IT, HR)
- **Users**: 10 demo users across all role types (see [Demo Accounts](#demo-accounts))

Create the storage symlink for public uploads:

```bash
php artisan storage:link
```

Install frontend dependencies and build (optional for development):

```bash
npm install
npm run dev
```

Start the development server:

```bash
php artisan serve --port=8080
```

Run the queue worker (if email/async jobs are used):

```bash
php artisan queue:work
```

Notes:

-   If you use SQLite for local dev, ensure `DB_CONNECTION=sqlite` in `.env` and an empty DB file or configured path.
-   For SQL Server deployment follow the Windows-specific steps below.

## Demo Accounts

After running the seeders, you can log in with these demo accounts (all use password: `password`):

| Role | Email | Description |
|------|-------|-------------|
| **Super Admin** | superadmin@example.com | Full system access, manages all organizations and users |
| **Company Admin** | admin@example.com | Manages users, documents, and folders within Sales Department |
| **Company Admin** | admin.it@example.com | Manages users, documents, and folders within IT Department |
| **Contributor** | company@example.com | Can create, edit, and manage documents in Sales Department |
| **Contributor** | contributor.it@example.com | Can create, edit, and manage documents in IT Department |
| **Viewer** | viewer1@example.com | Read-only access in Sales Department |
| **Viewer** | viewer2@example.com | Read-only access in IT Department |
| **Viewer** | viewer3@example.com | Read-only access in HR Department |

**Default Password**: `password` (for all demo accounts)

## User Roles

The system supports 4 role types with different permission levels:

1. **Super Admin**
   - Full system access and control
   - Can manage all organizations, users, and settings
   - Access to all documents across all organizations

2. **Company Admin**
   - Organization-level administrator
   - Can manage users, documents, and folders within their organization
   - Can configure organization settings

3. **Contributor**
   - Can create, edit, and manage documents and folders
   - Can upload new documents and create new versions
   - Can share documents within their organization

4. **Viewer**
   - Read-only access to documents and folders
   - Can view and download documents based on permissions
   - Cannot create, edit, or delete documents

## Configuration notes

-   Check `config/filesystems.php` and `FILESYSTEM_DISK` in `.env` to choose between `public` and `sftp` storage.
-   SFTP configuration lives in `.env` (SFTP_HOST, SFTP_USERNAME, SFTP_PASSWORD, SFTP_PORT, SFTP_ROOT).
-   Gemini AI integration requires `GEMINI_API_KEY` in `.env`.
-   Azure SSO requires `AZURE_CLIENT_ID`, `AZURE_CLIENT_SECRET`, `AZURE_REDIRECT_URI` and `AZURE_TENANT_ID`.

After changing `.env` values, clear cached config:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

## Running tests

Run the test suite with:

```bash
php artisan test
```

## Environment variables highlights

Key variables from `.env.example` you will likely set:

-   `DB_CONNECTION`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
-   `FILESYSTEM_DISK` (public or sftp)
-   `SFTP_HOST`, `SFTP_USERNAME`, `SFTP_PASSWORD`, `SFTP_PORT`, `SFTP_ROOT`
-   `GEMINI_API_KEY` (for AI features)
-   `AZURE_CLIENT_ID`, `AZURE_CLIENT_SECRET`, `AZURE_REDIRECT_URI`, `AZURE_TENANT_ID` (for Azure SSO)
-   Mail settings (MAIL_MAILER, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, MAIL_ENCRYPTION)

See `.env.example` for full details and example values.

## Contributing

Contributions are welcome. Please follow these steps:

1. Fork the repository
2. Create a feature branch: `git checkout -b feat/my-feature`
3. Commit changes and push: `git push origin feat/my-feature`
4. Open a pull request describing the change and include any migration notes or config updates.

Please open an issue to discuss larger changes before implementing them.

## License

This project is licensed under the MIT License — see the [LICENSE](LICENSE) file for details.

## Acknowledgements

-   Built with Laravel
-   Uses Gemini for optional AI features
-   Thanks to contributors and the open-source community
