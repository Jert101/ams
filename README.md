<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development/)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Attendance Monitoring System

A QR-based attendance monitoring system for tracking member attendance at mass services.

## Features

- User management with different role types (Admin, Officer, Secretary, Member)
- QR code generation and scanning for attendance tracking
- Real-time attendance monitoring
- Automated absence detection and notification system
- Email notifications for members with 3 or 4 consecutive absences
- Detailed attendance reports and analytics
- Mobile-friendly interface with Tailwind CSS

## User Roles and Features

### Admin
- Manage user accounts (create, edit, delete)
- Create and manage events
- View attendance reports
- Overall system administration

### Officer
- Scan member QR codes to record attendance
- Approve attendance records
- View attendance reports

### Secretary
- Generate comprehensive attendance reports
  - Date range reports with attendance statistics
  - Member-specific attendance reports
  - Export attendance data to CSV
- Monitor member absences
  - Filter members with 3 or 4 consecutive absences
  - View detailed absence information
- Send notifications to members
  - Automatic email notifications for consecutive absences
  - Manual notification sending for individual or bulk members
- Oversee attendance workflow

### Member
- View personal attendance history
- Access personal QR code for attendance
- Receive absence notifications
- Update personal information

## System Workflow

1. Admin sets up events (masses) in the system
2. Members register and receive unique QR codes
3. At mass services, Officers scan QR codes to mark attendance
4. System automatically marks absences for members who didn't attend
5. Secretary monitors attendance:
   - System automatically counts consecutive absences
   - Members with 3 consecutive absences receive counseling notification
   - Members with 4 consecutive absences receive warning notification
6. Email notifications are automatically sent to members requiring attention

## Technical Components

### Frontend
- Tailwind CSS for responsive UI design
- JavaScript for QR code generation and scanning
- Chart.js for attendance visualization

### Backend
- Laravel PHP framework
- PHPMailer for email notifications
- Database models for Users, Events, Attendance, Notifications, etc.
- Role-based access control

### Automation
- Scheduled tasks for absence monitoring
- Automatic consecutive absence detection
- Automatic email notifications for absence alerts
- QR code generation and management

## Installation

1. Clone the repository
```
git clone https://github.com/yourusername/attendance-monitoring.git
```

2. Install dependencies
```
composer install
npm install
```

3. Configure environment
```
cp .env.example .env
php artisan key:generate
```

4. Configure database settings in .env file
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=attendance_db
DB_USERNAME=root
DB_PASSWORD=
```

5. Configure email settings in .env file
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

6. Run migrations and seed the database
```
php artisan migrate --seed
```

7. Generate QR codes for all members
```
php artisan qrcodes:generate
```

8. Start the development server
```
php artisan serve
npm run dev
```

9. Set up scheduled tasks (using cron on Linux/macOS or Task Scheduler on Windows)
```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Scheduled Commands

The system uses the following scheduled commands:

1. `php artisan attendances:mark-absences` - Marks absences for past events
2. `php artisan absences:check` - Checks for consecutive absences and creates notifications
3. `php artisan notifications:send` - Sends email notifications for consecutive absences

## License

The Attendance Monitoring System is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
