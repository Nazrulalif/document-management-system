# Document Management System (DMS)

<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

## About DMS

The Document Management System (DMS) is a comprehensive solution designed for corporate entities to efficiently manage and control their documents and records across various companies under their umbrella. This system integrates seamlessly with the existing Corporate Information System (CIS), enhancing its capabilities by adding robust document version control, secure file storage, and user access management.

The DMS allows for the streamlined upload, organization, and retrieval of documents, ensuring that every version of a document is tracked and accessible, thereby facilitating better compliance and audit trails. By centralizing document management, the DMS helps companies maintain accurate records, manage sensitive information securely, and provide controlled access to essential corporate documents. This ensures that all critical documentation is easily accessible and properly managed, supporting the efficient operation and oversight of multiple business entities.

## Prerequisites

Before you begin, ensure you have met the following requirements:

-   PHP >= 8.1
-   Laravel 10
-   Laragon/XAMPP
-   Gemini
-   Mailtrap (If to use email delivery platform to test, send and control email infrastructure in one place)

## Installation

**OPTION 1: Download zip file**

**OPTION 2: Clone repository into your local file:**

```bash
git clone {{ Clone with HTTPS }}
```

Follow these steps to install and set up the DMS:

1. **Rename `.env.example` to `.env`**

2. **Run the Composer install**
   _(download composer `exe` if you don't have it installed yet.https://getcomposer.org/download/)_

```bash
composer install
```

3. **Run the Composer update:**

```bash
composer update
```

4. **Migrate database:**

```bash
php artisan migrate
```

5. **Seed the factory for system roles:**

```bash
php artisan db:seed role
```

6. **Paste the Gemini API key in the .env file.**
   _(Refer to the section "Get Gemini API" below)_

7. **Paste the Mailtrap username and password in the .env file.**
   _(Refer to the section "Get Mailtrap API" below)_

8. **Generate app key:**

```bash
php artisan key:generate
```

9. **Clear configuration:**

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

10. **Run the Laravel development server:**

```bash
php artisan serve
```

11. **Run the mail queue:**

```bash
php artisan queue:work
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
