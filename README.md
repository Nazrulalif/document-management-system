# Document Management System (DMS)

<p align="center">
    <a href="https://laravel.com" target="_blank">
        <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg"
            width="400" alt="Laravel Logo">
    </a>
</p>

## About DMS

The Document Management System (DMS) is a comprehensive solution designed for corporate entities to efficiently manage
and control their documents and records across various companies under their umbrella. This system integrates seamlessly
with the existing Corporate Information System (CIS), enhancing its capabilities by adding robust document version
control, secure file storage, and user access management.

The DMS allows for the streamlined upload, organization, and retrieval of documents, ensuring that every version of a
document is tracked and accessible, thereby facilitating better compliance and audit trails. By centralizing document
management, the DMS helps companies maintain accurate records, manage sensitive information securely, and provide
controlled access to essential corporate documents. This ensures that all critical documentation is easily accessible
and properly managed, supporting the efficient operation and oversight of multiple business entities.

## Prerequisites

Before you begin, ensure you have met the following requirements:

-   PHP >= 8.1
-   Laravel 10
-   Laragon(preferred)/XAMPP
-   Gemini
-   Mailtrap (If to use email delivery platform to test, send and control email infrastructure in one place)
-   Microsoft Azure SSO
-   SFTP (If to use SFTP, ensure your machine has install openSSH Server)

## Installation situation

-   Install in localhost (personal PC)
-   Install in KCTECH Server using SQL SERVER

## Installation in localhost (personal PC)

**OPTION 1: Download zip file**

**OPTION 2: Clone repository into your local file:**

```bash
git clone {{ Clone with HTTPS }}
```

Follow these steps to install and set up the DMS:

1. **Rename `.env.example` to `.env`**

2. **Run the Composer update:**
   _(download composer `exe` if you don't have it installed yet.https://getcomposer.org/download/)_

```bash
composer update
```

3. **Comment these line in `database/seeders/role.php`**

```bash
DB::unprepared('SET IDENTITY_INSERT roles ON');
```

```bash
DB::unprepared('SET IDENTITY_INSERT roles OFF');
```

4. **In `database/migration` need to Comment/Uncomment some foreign key based on server you use. It in this file:**

-   folders table
-   documents table
-   document versions table
-   starred folders table
-   starred documents table

5. **Migrate database:**

```bash
php artisan migrate
```

6. **Seed the factory for system roles:**

```bash
php artisan db:seed role
```

7. **Generate app key:**

```bash
php artisan key:generate
```

8. **Create uploads folders:**

```bash
php artisan storage:link
```

9. **Setup file sistem that you want to use in `.env`**

```bash
# use public to save file in other server
FILESYSTEM_DISK=sftp
```

```bash
# use public to save file in public file
FILESYSTEM_DISK=public
```

10. **Setup configuration SFTP in `.env` (Skip if use public filesystem)**

```bash
SFTP_HOST=127.0.0.1
SFTP_USERNAME=
SFTP_PASSWORD=
SFTP_PORT=
SFTP_ROOT=
```

11. **Paste the Gemini API key in the `.env` file.**
    _(Refer to the section "Get Gemini API" below)_

12. **[Optional] If using Mailtrap, Paste the Mailtrap username and password in the `.env` file.**
    _(Refer to the section "Get Mailtrap API" below)_

13. **Paste the Microsoft Azure client ID, client secret, redirect URI and tenant ID in the `.env` file.**
    _(Refer to the section "Get Microsoft Azure API Configuration" below)_

14. **Clear configuration:**

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

15. **Run the Laravel development server:**

```bash
php artisan serve --port=8080
```

16. **Run the mail queue:**

```bash
php artisan queue:work
```

## Installation in KCTECH Server using SQL Server

### NOTE: Laravel must install in `C:\Users\CISAdmin\Documents\applications\`.

**OPTION 1: Download zip file**

**OPTION 2: Clone repository:**

```bash
git clone {{ Clone with HTTPS }}
```

Follow these steps to install and set up the DMS:

1. **Rename `.env.example` to `.env`**

2. **Run the Composer update:**
   _(download composer `exe` if you don't have it installed yet.https://getcomposer.org/download/)_

```bash
C:\Users\CISAdmin\Documents\applications\php-8.1.30\php.exe C:\ProgramData\ComposerSetup\bin\composer.phar update
```

3. **Uncomment these line in `database/seeders/role.php` if using sqlsvr**

```bash
DB::unprepared('SET IDENTITY_INSERT roles ON');
```

```bash
DB::unprepared('SET IDENTITY_INSERT roles OFF');
```

4. **In `database/migration` need to Comment/Uncomment some foreign key based on server you use. It in this file:**

-   folders table
-   documents table
-   document versions table
-   starred folders table
-   starred documents table

5. **Migrate database:**

```bash
C:\Users\CISAdmin\Documents\applications\php-8.1.30\php.exe artisan migrate
```

6. **Seed the factory for system roles:**

```bash
C:\Users\CISAdmin\Documents\applications\php-8.1.30\php.exe artisan db:seed role
```

7. **Generate app key:**

```bash
C:\Users\CISAdmin\Documents\applications\php-8.1.30\php.exe artisan key:generate
```

8. **Create uploads folders:**

```bash
C:\Users\CISAdmin\Documents\applications\php-8.1.30\php.exe artisan storage:link
```

9. **Paste the Gemini API key in the .env file.**
   _(Refer to the section "Get Gemini API" below)_

10. **[Optional] If using Mailtrap, Paste the Mailtrap username and password in the `.env` file.**
    _(Refer to the section "Get Mailtrap API" below)_

11. **Paste the Microsoft Azure client ID, client secret, redirect URI and tenant ID in the `.env` file.**
    _(Refer to the section "Get Microsoft Azure API Configuration" below)_

12. **Clear configuration:**

```bash
C:\Users\CISAdmin\Documents\applications\php-8.1.30\php.exe artisan config:clear
C:\Users\CISAdmin\Documents\applications\php-8.1.30\php.exe artisan cache:clear
C:\Users\CISAdmin\Documents\applications\php-8.1.30\php.exe artisan route:clear
```

13. **Run the Laravel development server:**

```bash
C:\Users\CISAdmin\Documents\applications\php-8.1.30\php.exe artisan serve --port=8080
```

14. **Run the mail queue:**

```bash
C:\Users\CISAdmin\Documents\applications\php-8.1.30\php.exe artisan queue:work
```

## Get Gemini API

To obtain your Gemini API key, follow these instructions:

1. Sign up or log in to your Gemini account. (https://aistudio.google.com/app/apikey)
2. Navigate to the API section in your account settings.
3. Create a new API key and copy it.
4. Paste the key into your `.env` file under the GEMINI_API_KEY.

## Get Mailtrap API

To get your Mailtrap API credentials:

1. Log in to your Mailtrap account. (https://mailtrap.io/)
2. Go to the email testing > inboxes > my inbox > integration > SMTP.
3. Copy the host, username, password, port (2525 or 587) and encryption (tls) provided for SMTP.
4. Paste them into your `.env` file under the appropriate variables.

_p/s: run this command to check connection port with mailtrap:_

```bash
telnet sandbox.smtp.mailtrap.io 587
```

## Get Microsoft Azure API Configuration

To get your Microsoft Azure API credentials:

### Step 1: Register Your App in Microsoft Azure

1. Sign in to the Azure Portal. (https://azure.microsoft.com/)
2. In the left sidebar, go to Microsoft Entra ID.
3. Click App registrations > New registration.
4. Fill in the app details.

### Step 2: Get the Client ID and Tenant ID

1. After registering, go to Overview.
2. Copy the following:
    - Application (client) ID: This is your Client ID.
    - Directory (tenant) ID: This is your Tenant ID. (use 'common' for testing)

### Step 3: Create a Client Secret

1. In the Certificates & secrets section (left menu), click New client secret.
2. Add a description (e.g., MyAppSecret).
3. Set an expiration period (1 year, 2 years, etc.).
4. Click Add.
5. Copy the value of the secret immediately — it will not be shown again. This is your Client Secret.

### Step 4: Configure the Redirect URI

1. In the Authentication section (left menu), click Add a platform.
2. Choose Web.
3. Add the redirect URI (http://localhost:8000/auth/microsoft/callback).
4. Enable ID tokens (used for implicit and hybrid flows).
5. Click Save.

### Step 5: Paste Credentials in `.env` File

```bash
AZURE_CLIENT_ID=<your-client-id>
AZURE_CLIENT_SECRET=<your-client-secret>
AZURE_REDIRECT_URI=http://localhost:8000/auth/microsoft/callback
AZURE_TENANT_ID=<your-tenant-id>
```
