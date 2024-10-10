# Weather Forecasting System API

This is a simple API built with Laravel to retrieve weather forecasts for specified cities. The API uses JWT-based authentication, integrates with a mocked weather service (using the service pattern), and incorporates caching for optimized performance. This project was built with **Test-Driven Development (TDD)**, ensuring all core functionalities are tested and reliable.

## Table of Contents

-   [Features](#features)
-   [Technologies](#technologies)
-   [Requirements](#requirements)
-   [Installation](#installation)
-   [API Endpoints](#api-endpoints)
-   [Testing](#testing)
-   [Caching](#caching)
-   [License](#license)

## Features

-   JWT-based user authentication
-   Weather data retrieval with caching for improved performance
-   Mocked external weather service using a service layer pattern
-   Fully tested with feature and unit tests to ensure functionality

## Technologies

-   **Laravel 11**
-   **SQLite (for testing)**
-   **JWT (JSON Web Token) Authentication**
-   **Redis** for caching
-   **PHPUnit** for testing

## Requirements

-   **PHP 8.2** or higher
-   **Composer** for PHP dependencies
-   **SQLite** for testing (optional, but recommended)
-   **Redis** for testing

## Installation

1. **Clone the repository:**

    ```bash
    git clone https://github.com/NourhanAymanElstohy/simple-weather-api.git
    cd simple-weather-api
    ```

2. Install the project dependencies using Composer:

    ```bash
    composer install
    ```

3. Create a copy of the `.env.example` file and rename it to `.env`. Update the necessary configuration values such as database credentials.

    ```bash
    cp .env.example .env
    ```

4. Generate an application key:

    ```bash
    php artisan key:generate
    ```

5. Generate an JWT Secret key:

    ```bash
    php artisan jwt:secret
    ```

6. Run the database migrations (**Set the database credentials in .env before migrating**):

    ```bash
    php artisan migrate
    ```

7. Start the development server:

    ```bash
    php artisan serve
    ```

8. Access the application in your web browser at `http://localhost:8000`.

## API Endpoints

The following API endpoints are available:

1.  **Register a New User**
    Endpoint: `POST /api/register`

-   Request:

```bash
{
    "name": "John Doe",
    "email": "johndoe@example.com",
    "password": "secret",
}
```

-   Response:

```bash
{
    "message": "User registered successfully",
    "token": "jwt_token"
}
```

2.  **User Login**
    Endpoint: `POST /api/login`

-   Request:

```bash
{
    "email": "johndoe@example.com",
    "password": "secret",
}
```

-   Response:

```bash
{
    "token": "jwt_token"
}
```

3.  **Get Weather Data (Authenticated)**
    Endpoint: `POST /api/weather?city=city_name`

-   Request:

    -   Header: `Authorization: Bearer jwt_token`
    -   URL Parameter: `city` (e.g., `/api/weather?city=Cairo`)

-   Response:

```bash
{
    "city": "Cairo",
    "temperature": "30°C",
    "humidity": "50%",
    "conditions": "Clear sky"
}
```

## Testing

This project follows **Test-Driven Development (TDD)** principles. Both **unit tests** and **feature tests** are provided.

-   Unit Tests:

    -   Located in `tests/Unit/WeatherServiceTest.php`
    -   Test the `WeatherService` class to ensure weather data is returned and caching works correctly.

-   Feature Tests:

    -   Located in `tests/Feature/AuthTest.php` and `tests/Feature/WeatherTest.php`
    -   Test the API endpoints for registration, login, and weather retrieval.

### Testing Configuration

-   Create a copy of the `.env` file and rename it to `.env.testing`. Update the necessary configuration values such as database credentials.

    ```bash
    cp .env .env.testing
    ```

-   To use an in-memory SQLite database during testing, update the `.env.testing` file (if not) with:

    DB_CONNECTION=sqlite
    DB_DATABASE=:memory:

### Running the Tests

To run all tests:

```bash
php artisan test
```

### About TDD

This API was developed using **Test-Driven Development (TDD)**:

-   Tests were written before the actual implementation to validate the expected behavior.
-   The process ensured that each feature met the requirements and that core functionality was validated before proceeding.
-   Both **unit tests** and **feature tests** were created to cover business logic and API functionality.

## API Documentation

-   You can access the Postman collection for the API [Weather-api](https://drive.google.com/file/d/1iMULw6TW6fcuyPE-HJPhIN1HFVC9sXp4/view?usp=drive_link).
-   And Environment for the Weather API [Weather-api-env](https://drive.google.com/file/d/1tjPl0-YtVOkQLjD03guP4sfV18IzZA8P/view?usp=sharing)

## Caching

Caching is implemented in the `WeatherService` class using Laravel's Cache facade to store weather data for 3600 minutes. This improves the performance of subsequent requests for the same city.

## License

Weather is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT). Feel free to use, modify, and distribute the code as per the terms of the license.
