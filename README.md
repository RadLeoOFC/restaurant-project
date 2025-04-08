# Restaurant Project

_A Laravel-based restaurant desk reservation and management system. This project includes interactive desk maps, multi-role user authentication, reservation handling, multilingual notifications, and reporting features._

***

## Features

### 1. Project Initialization and Setup
- Laravel installation and configuration
- Environment setup with MySQL database
- Basic routing and Blade views for homepage and dashboard

***

### 2. User Authentication and Roles
- Integrated Laravel Breeze for authentication
- Customized login/register forms
- Role management with CRUD and middleware

***

### 3. Desk Management
- Desk model with coordinates and capacity
- Full CRUD functionality for desks

***

### 4. Interactive Map Editor
- CSS Grid and Flexbox layout for interactive desk map
- Drag-and-drop desks with Interact.js
- CRUD directly on the map with AJAX persistence

***

### 5. Reservation Management
- Reservation model with validation
- Availability check and full CRUD

***

### 6. Customer Management
- Customer model with contact info and preferred language
- Full CRUD and linking to reservations

***

### 7. Notifications and Alerts
- Laravel notification system
- Reservation status notifications with multilingual templates

***

### 8. Localization and Multi-language Support
- Manage supported languages via CRUD
- JavaScript-based language switching and preference saving

***

### 9. External Desk Addition
- Separate model for external desks with limited functions
- Visual distinction on map

***

### 10. Reports and Analytics
- Daily/weekly/monthly analytics on reservations
- Chart.js-powered interactive reports with filtering

***

## Database Architecture

- _Users:_ id, name, email, password, role_id, created_at, updated_at
- _Roles:_ id, role_name
- _Desks:_ id, name, capacity, status, coordinates_x, coordinates_y, created_at, updated_at
- _Reservations:_ id, desk_id, customer_id, reservation_date, reservation_time, status, created_at, updated_at
- _Customers:_ id, name, email, phone, preferred_language, created_at, updated_at
- _Languages:_ id, code, name, created_at, updated_at
- _Translations:_ id, language_id, key, value, created_at, updated_at

***

## Installation

```
git clone https://your-repo-url.git
cd restaurant-project
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

***

## Documentation

- Laravel: https://laravel.com/docs
- Breeze: https://laravel.com/docs/starter-kits#laravel-breeze
- Chart.js: https://www.chartjs.org/docs/
- Interact.js: https://interactjs.io/

***

## Author

This project was created by _Radislav Lebedev_ as part of an educational internship to demonstrate working with the Laravel framework.

***

## License

MIT