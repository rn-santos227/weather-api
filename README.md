# Weather API

## Overview

This project is a simple Weather API built using **Laravel 11+** and **PHP 8+** as part of a PHP Developer take-home exam.

The API accepts a city name, retrieves current weather data from an external provider, and optionally serves cached results to reduce repeated external requests. Basic validation, error handling, and automated tests are included.

## Requirements
- PHP 8.1 or higher
- Laravel 11 or higher
- Composer
- SQLite (default) or any Laravel-supported database


## Dependencies
- predis/predis 3.3 or higher

## Setup Instructions

### 1. Clone the repository

```bash
git clone <repository-url>
cd weather-api
```

### 2. Install dependencies

```bash
composer install
```

### 3. Environment configuration

Create the environment file:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Ensure the SQLite database file exists:

```bash
touch database/database.sqlite
```

Update the database configuration in `.env` if necessary:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

---

### 4. Configure OpenWeather API access

This application uses the **OpenWeather API** as the external weather data provider.

Add your API key to the `.env` file:

```env
OPENWEATHER_API_KEY=your_openweather_api_key_here
```

The following defaults are already provided and can be adjusted if needed:

```env
OPENWEATHER_BASE_URL=https://api.openweathermap.org/data/2.5
OPENWEATHER_UNITS=metric
```

You can obtain an API key by creating a free account at:

```
https://openweathermap.org/api
```

---

### 5. Run migrations

```bash
php artisan migrate
```

### 6. Start the application

```bash
php artisan serve
```

The API will be available at:

```
http://localhost:8000
```

---

## API Endpoints

### Health Check

```
GET /api/health
```

Response:

```json
{
  "status": "ok"
}
```

---

### Live Weather

```
GET /api/weather/{city}
```

Example:

```
/api/weather/new-york
```

Returns live weather data from the external provider.

---

### Cached Weather

```
GET /api/weather/{city}/cached
```

Returns cached weather data when available.
If no cached data exists, the API fetches live data and stores it in cache.

---

## Input Handling & Validation

* City names are normalized (trimming whitespace, collapsing multiple spaces or hyphens, consistent casing).
* Invalid input returns a validation error response.
* City normalization is handled at the routing level before reaching the controller.

---

## Error Handling

The API includes proper error handling for:

* Invalid or malformed requests (HTTP 422)
* Missing routes (HTTP 404)
* External API failures with controlled error responses

Internal exceptions are not exposed directly to the client.

---

## Automated Tests

The project includes automated **feature tests** that cover:

* Request validation and normalization
* API response structure
* Cached vs live weather behavior

External API calls are mocked to keep tests deterministic and fast.

### Run tests

Run General Test

```bash
php artisan test
```

Run Weather Request Test

```bash
php artisan test --filter=WeatherRequestTest
```

Run Weather Response Test

```bash
php artisan test --filter=WeatherResponseTest
```

---

## Implementation Notes

* Laravel Form Requests are used for request validation.
* City input is normalized and validated via a Form Request and consumed from validated data in the controller.
* External API logic is isolated in a dedicated service/client layer.
* Caching is implemented using Laravel’s cache abstraction.
* Tests focus on observable behavior rather than internal implementation details.

---

## Step by Step Development Approach

* Set up the Laravel project and its necessary files.
* Added OpenWeather API configuration to the application config files.
* Created a Weather domain object to represent an immutable weather snapshot for a city and format the data into a consistent API response.
* Created an OpenWeather client to handle OpenWeather API operations separately from the Weather service.
* Created a Weather service to separate business logic from the Weather controller.
* Implemented caching in the Weather service.
* Created a Weather request to handle validation before the request is processed by the controller.
* Ensured controllers remain slim and focused only on orchestration.
* Added route limiting to the API routes to protect them from request abuse.
* Created Weather request tests to ensure requests reaching the endpoints are handled correctly.
* Created Weather response tests to ensure responses returned by the endpoints are accurate.
