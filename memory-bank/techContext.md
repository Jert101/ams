# Technical Context: AMS (Attendance Monitoring System)

## Technologies Used

### Backend
- **PHP 8.1+**: Core programming language
- **Laravel 10.x**: PHP framework for web application development
- **MySQL 8.0**: Primary database system
- **Composer**: PHP dependency management
- **Laravel Sanctum**: API token authentication
- **Laravel Policies**: Authorization management
- **Laravel Queue**: Background job processing
- **PHPMailer**: Email notification system for absent members
- **QR Code Libraries**: For generating and scanning QR codes

### Frontend
- **Blade**: Laravel's templating engine
- **JavaScript**: Client-side scripting
- **Tailwind CSS**: Utility-first CSS framework for responsive design
- **Alpine.js**: Lightweight JavaScript framework for interactivity
- **Chart.js**: Data visualization for attendance reports
- **HTML5 QR Code Scanner**: For scanning member QR codes
- **SweetAlert2**: Improved JavaScript popups

### Development Tools
- **Git**: Version control
- **XAMPP**: Local development environment
- **PHPUnit**: Testing framework
- **Laravel Vite**: Asset compilation
- **npm**: JavaScript package management

## Development Setup

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL 8.0
- Node.js and npm
- XAMPP or similar local server environment

### Installation Steps
1. Clone the repository
2. Run `composer install`
3. Copy `.env.example` to `.env` and configure database settings
4. Run `php artisan key:generate`
5. Run `php artisan migrate --seed`
6. Run `npm install && npm run dev`
7. Serve the application with `php artisan serve`

## Technical Constraints
- **PHP Version Compatibility**: Must support PHP 8.1+
- **Browser Support**: Must work on modern browsers (Chrome, Firefox, Safari, Edge)
- **Mobile Responsiveness**: All interfaces must be responsive
- **Performance**: Asset listing pages must load within 2 seconds for up to 10,000 assets
- **Security**: Must follow OWASP security guidelines
- **Accessibility**: Must meet WCAG 2.1 AA standards

## Dependencies

### Core Laravel Packages
- **laravel/sanctum**: API authentication
- **laravel/tinker**: REPL for Laravel
- **spatie/laravel-permission**: Role and permission management
- **maatwebsite/excel**: Excel import/export functionality
- **barryvdh/laravel-dompdf**: PDF generation
- **intervention/image**: Image manipulation

### Frontend Dependencies
- **bootstrap**: UI framework
- **jquery**: JavaScript library
- **datatables.net**: Interactive tables
- **chart.js**: Charts and graphs
- **select2**: Enhanced select boxes
- **sweetalert2**: Improved alerts and modals

### Development Dependencies
- **phpunit/phpunit**: Testing framework
- **fakerphp/faker**: Fake data generation for testing
- **laravel/sail**: Docker development environment
- **mockery/mockery**: Mocking framework for testing

## Environment Configuration
The application requires the following environment variables to be set in the `.env` file:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ams
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="no-reply@ams.com"

QUEUE_CONNECTION=database
```

*This document provides the technical foundation for the AMS project, detailing the technologies, setup procedures, and dependencies required for development and deployment.*
