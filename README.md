<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# HanShelfAPI

HanShelfAPI is a library management system built with Laravel. It allows users to manage books, loans, shelves, and view statistics about the most popular books.

## Features

- User authentication (register, login, logout)
- Book management (add, update, delete, view books)
- Loan management (borrow and return books)
- Shelf management (add, update, delete, view shelves and their books)
- Statistics (view most popular books)

## Installation

1. Clone the repository:
    ```sh
    git clone https://github.com/yourusername/HanShelfAPI.git
    cd HanShelfAPI
    ```

2. Install dependencies:
    ```sh
    composer install
    ```

3. Copy the `.env.example` file to `.env` and configure your environment variables:
    ```sh
    cp .env.example .env
    ```

4. Generate an application key:
    ```sh
    php artisan key:generate
    ```

5. Run the migrations:
    ```sh
    php artisan migrate
    ```

6. Seed the database (optional):
    ```sh
    php artisan db:seed
    ```

7. Start the development server:
    ```sh
    php artisan serve
    ```

## API Endpoints

### Authentication

- `POST /api/auth/register` - Register a new user
- `POST /api/auth/login` - Login a user
- `POST /api/auth/logout` - Logout a user (requires authentication)

### Books

- `GET /api/books` - Get all books (requires authentication)
- `POST /api/books` - Add a new book (requires authentication and admin role)
- `PUT /api/books/{id}` - Update a book (requires authentication and admin role)
- `DELETE /api/books/{id}` - Delete a book (requires authentication and admin role)
- `GET /api/books/search` - Search for books (requires authentication)

### Loans

- `POST /api/loans` - Borrow a book (requires authentication)
- `POST /api/loans/{id}/return` - Return a borrowed book (requires authentication)
- `GET /api/loans/history/all` - Get all loan history (requires authentication and admin role)

### Users

- `GET /api/users/{id}/loans` - Get loan history of a specific user (requires authentication)
- `GET /api/users/loans` - Get loan history of the authenticated user (requires authentication)

### Shelves

- `GET /api/shelf` - Get all shelves (requires authentication)
- `POST /api/shelf` - Add a new shelf (requires authentication and admin role)
- `PUT /api/shelf/{id}` - Update a shelf (requires authentication and admin role)
- `DELETE /api/shelf/{id}` - Delete a shelf (requires authentication and admin role)
- `GET /api/shelf/{id}/books` - Get books in a specific shelf (requires authentication)

### Statistics

- `GET /api/stats/popular-books` - Get the most popular books (requires authentication)

## Contributing

Thank you for considering contributing to HanShelfAPI! Please follow the [contribution guide](https://laravel.com/docs/contributions) for Laravel.

## License

HanShelfAPI is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
