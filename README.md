# CRM System

A comprehensive Customer Relationship Management (CRM) system built with Laravel and Filament, designed to streamline business operations and enhance customer relationship management.

## Features

### 📊 Dashboard & Analytics

-   Real-time statistics overview
-   Interactive charts for projects, clients, and invoices
-   Performance metrics and KPIs
-   Activity logs and audit trails

### 👥 Client Management

-   Complete client profiles with contact information
-   Client status tracking (Interested, Negotiating, Active, Finished, Paused)
-   File attachments and document management
-   Internal notes and communication history
-   Reminder system for follow-ups

### 🏗️ Project Management

-   Project lifecycle management (Pending, In Progress, Completed, Delayed)
-   Team assignment with roles (Developer, Manager, Designer, Member)
-   Project timeline and milestone tracking
-   Client-project associations
-   File and document management

### ✅ Task Management

-   Task creation and assignment
-   Status tracking (Pending, In Progress, Done, Delayed)
-   Priority levels and deadlines
-   Team collaboration features
-   Calendar integration for task scheduling

### 💰 Invoice Management

-   Invoice generation and tracking
-   Payment status monitoring (Unpaid, Partially Paid, Paid)
-   Client and project billing
-   Due date management
-   Financial reporting

### 🔐 User Management & Permissions

-   Role-based access control using Spatie Permissions
-   User activity tracking
-   Multi-language support (English/Arabic)
-   Secure authentication system

### 📅 Calendar Integration

-   Task scheduling and visualization
-   Event management
-   Deadline tracking
-   Team calendar coordination

### 🔔 Notification System

-   Real-time notifications
-   Notification reminders
-   System alerts
-   Custom notification preferences

## Technology Stack

-   **Framework**: Laravel 12.x
-   **Admin Panel**: Filament 3.x
-   **Database**: MySQL/SQLite
-   **Frontend**: Livewire, Alpine.js, Tailwind CSS
-   **Authentication**: Laravel Sanctum
-   **Permissions**: Spatie Laravel Permission
-   **Activity Logging**: Spatie Laravel Activity Log
-   **Calendar**: Filament FullCalendar
-   **File Management**: Laravel Storage

## Requirements

-   PHP 8.2 or higher
-   Composer
-   Node.js & NPM
-   MySQL 5.7+ or SQLite
-   Web server (Apache/Nginx)

## Installation

1. **Clone the repository**

    ```bash
    git clone https://github.com/aldod400/CRM.git
    cd crm-system
    ```

2. **Install PHP dependencies**

    ```bash
    composer install
    ```

3. **Install Node.js dependencies**

    ```bash
    npm install
    ```

4. **Environment configuration**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5. **Database setup**

    ```bash
    php artisan migrate
    php artisan db:seed
    ```

6. **Storage linking**

    ```bash
    php artisan storage:link
    ```

7. **Build assets**

    ```bash
    npm run build
    ```

8. **Start the development server**
    ```bash
    php artisan serve
    ```

## Configuration

### Database Configuration

Update your `.env` file with database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crm_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## Usage

### Default Admin Access

After seeding the database, you can access the admin panel with:

-   **URL**: `http://127.0.0.1:8000`
-   **Email**: admin@admin.com
-   **Password**: password

### Creating Users

1. Navigate to the admin panel
2. Go to Users section
3. Create new users with appropriate roles
4. Assign permissions based on responsibilities

### Managing Clients

1. Add new clients with complete information
2. Track client status throughout the sales pipeline
3. Upload relevant documents and files
4. Set reminders for follow-ups

### Project Workflow

1. Create projects linked to clients
2. Assign team members with specific roles
3. Break down projects into manageable tasks
4. Track progress and update status
5. Generate invoices upon completion

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/new-feature`)
3. Commit your changes (`git commit -am 'Add new feature'`)
4. Push to the branch (`git push origin feature/new-feature`)
5. Create a Pull Request

## Security

-   Regular security updates
-   Data encryption
-   Secure authentication
-   Input validation and sanitization
-   CSRF protection
-   SQL injection prevention

## Testing

Run the test suite:

```bash
php artisan test
```

## Performance Optimization

-   Database query optimization
-   Caching strategies
-   Asset minification
-   Image optimization
-   CDN integration ready

## Backup & Maintenance

-   Regular database backups
-   Log rotation
-   Performance monitoring
-   Security patches
-   System health checks

## Acknowledgments

-   Laravel Framework Team
-   Filament Team
-   Spatie for amazing Laravel packages
-   All contributors and supporters

---

**Built with ❤️ by [Abdelrahman Elghonemy]**

© 2025 Your Abdelrahman Elghonemy. All rights reserved.
