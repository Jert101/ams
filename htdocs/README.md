# Laravel Shared Hosting Setup

This directory contains the public files for the Laravel application. The Laravel core files are located in the parent directory.

## Directory Structure

```
/
├── app/                  # Laravel application code
├── bootstrap/            # Laravel bootstrap files
├── config/               # Laravel configuration files
├── database/             # Laravel database migrations, seeds, etc.
├── htdocs/               # Public files (THIS DIRECTORY)
│   ├── build/            # Vite build assets
│   ├── css/              # CSS files
│   ├── js/               # JavaScript files
│   ├── img/              # Image files
│   ├── .htaccess         # Apache rewrite rules
│   └── index.php         # Laravel front controller
├── resources/            # Laravel resources (views, assets, etc.)
├── routes/               # Laravel routes
├── storage/              # Laravel storage
└── vendor/               # Composer dependencies
```

## How This Works

1. All requests go to the `htdocs/index.php` file
2. This file loads the Laravel application from the parent directory
3. The `.htaccess` file ensures all routes are properly handled

## Deployment Notes

- Do not modify the `index.php` file unless you know what you're doing
- Keep the Laravel core files outside the web-accessible directory for security
- Assets like CSS, JS, and images should be placed in their respective directories in `htdocs/` 