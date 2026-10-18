# Laravel Starter Project Architecture

## Architecture
This project uses MVC architecture with REST API support.

## Folder Structure
- app/Models: Data models and database relationships.
- app/Http/Controllers/Api: API request handling.
- app/Http/Requests: Request validation.
- app/Http/Resources: API response formatting.
- routes/api.php: API route definitions.
- database/migrations: Database schema definitions.
- docs/architecture.md: Architecture documentation.

## Data Flow
Client sends an HTTP request to the API route.
The route directs the request to an API controller.
The controller validates input using a Form Request.
The controller interacts with the Model.
The result is formatted using an API Resource.
The API returns a JSON response to the client.

## Verification
Run the project using:
php artisan serve