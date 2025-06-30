 <h1>RalphCity Apartment Booking System</h1>

This repository contains the source code for **RalphCity Apartment**, a web-based apartment and hotel booking platform. The application is built with [Laravel](https://laravel.com/) and provides management features for clients and staff to handle bookings, payments and apartment availability.

- **URL:** [https://ralphcityapt.com](https://ralphcityapt.com)
- **Author:** Kingsley Aguagwa

## Features

- Client and staff user roles with separate dashboards
- Apartment categories and sub‑categories with availability management
- Online booking with pending state and countdown timers
- Payment processing (bank transfer, cash or card via Paystack)
- Account balance top‑up and transaction history
- Cancellation and refund handling
- Email notifications for login, payments and top‑ups

## Installation

1. **Clone the repository**
   ```bash
   git clone <repo-url>
   cd hotel_booking
   ```
2. **Install PHP dependencies** (requires PHP 8.2+ and Composer)
   ```bash
   composer install
   ```
3. **Install Node dependencies** (for Tailwind/Vite assets)
   ```bash
   npm install
   ```
4. **Copy the environment file and generate an application key**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
5. **Configure the `.env` file** with your database credentials and optional Paystack keys.
6. **Run migrations and seeders**
   ```bash
   php artisan migrate
   # php artisan db:seed   # (if you have seeders)
   ```
7. **Build assets**
   ```bash
   npm run build   # or npm run dev for hot reload
   ```
8. **Serve the application**
   ```bash
   php artisan serve
   ```
   The site will be available at `http://localhost:8000` by default.

## Repository Structure

- `app/` – Laravel controllers, models, and mail classes
- `resources/views/` – Blade templates for pages and emails
- `routes/web.php` – Web routes defining client and staff flows
- `config/` – Application configuration files
- `database/` – Migration files to create tables
- `public/` – Entry point and compiled assets

## Adding Images to the README

Images stored in the repository can be displayed using the standard Markdown syntax:


![Sample apartment]([images/hotel1.jpg](https://github.com/ugosonic/hotel_booking/blob/main/images/hotel1.jpg?raw=true)
samplle apartment


This will embed the image from the `images/` directory when viewing the README on GitHub.

## Contributing

Pull requests are welcome. Please fork the project and open an issue to discuss your proposed changes before submitting a PR.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

