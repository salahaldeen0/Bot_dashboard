# Bot Dashboard API Documentation

## Base URL

```
https://ip-teamway.com/api
```

## Authentication

Currently, no authentication is required for these endpoints.

## Endpoints

### 1. API Test

**GET** `/test`

Test API connectivity and get basic information.

**Response:**

```json
{
    "message": "API is working!",
    "base_url": "https://ip-teamway.com",
    "timestamp": "2025-10-20T10:30:00.000000Z",
    "version": "1.0.0"
}
```

### 2. API Information

**GET** `/info`

Get comprehensive API information and available endpoints.

**Response:**

```json
{
    "api_name": "Bot Dashboard API",
    "version": "1.0.0",
    "base_url": "https://ip-teamway.com",
    "endpoints": {
        "GET /api/test": "Test API connectivity",
        "GET /api/info": "API information",
        "GET /api/apps": "Get all apps (with pagination and search)",
        "GET /api/apps/{id}": "Get specific app by ID",
        "GET /api/apps/stats": "Get apps statistics"
    },
    "timestamp": "2025-10-20T10:30:00.000000Z"
}
```

### 3. Get All Apps

**GET** `/apps`

Retrieve all apps with pagination and filtering options.

**Query Parameters:**

-   `page` (integer, optional): Page number (default: 1)
-   `per_page` (integer, optional): Items per page (default: 15)
-   `search` (string, optional): Search in app_name, description, or database_type
-   `database_type` (string, optional): Filter by database type

**Example Request:**

```
GET /api/apps?page=1&per_page=10&search=mysql&database_type=mysql
```

**Response:**

```json
{
    "success": true,
    "data": {
        "apps": [
            {
                "id": 1,
                "app_name": "Sample App",
                "description": "A sample application",
                "phone_number": "+1234567890",
                "database_type": "mysql",
                "database_name": "sample_db",
                "port": 3306,
                "host": "localhost",
                "username": "user",
                "created_at": "2025-10-20T10:30:00.000000Z",
                "updated_at": "2025-10-20T10:30:00.000000Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "last_page": 1,
            "per_page": 15,
            "total": 1,
            "from": 1,
            "to": 1
        }
    },
    "message": "Apps retrieved successfully",
    "timestamp": "2025-10-20T10:30:00.000000Z",
    "base_url": "https://ip-teamway.com"
}
```

### 4. Get Single App

**GET** `/apps/{id}`

Retrieve a specific app by its ID.

**Path Parameters:**

-   `id` (integer, required): The app ID

**Example Request:**

```
GET /api/apps/1
```

**Response:**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "app_name": "Sample App",
        "description": "A sample application",
        "phone_number": "+1234567890",
        "database_type": "mysql",
        "database_name": "sample_db",
        "port": 3306,
        "host": "localhost",
        "username": "user",
        "created_at": "2025-10-20T10:30:00.000000Z",
        "updated_at": "2025-10-20T10:30:00.000000Z"
    },
    "message": "App retrieved successfully",
    "timestamp": "2025-10-20T10:30:00.000000Z",
    "base_url": "https://ip-teamway.com"
}
```

### 5. Get Apps Statistics

**GET** `/apps/stats`

Get statistical information about the apps.

**Response:**

```json
{
    "success": true,
    "data": {
        "total_apps": 10,
        "database_types": [
            {
                "database_type": "mysql",
                "count": 5
            },
            {
                "database_type": "postgresql",
                "count": 3
            },
            {
                "database_type": "sqlite",
                "count": 2
            }
        ],
        "recent_apps": [
            {
                "id": 10,
                "app_name": "Latest App",
                "created_at": "2025-10-20T10:30:00.000000Z"
            }
        ]
    },
    "message": "App statistics retrieved successfully",
    "timestamp": "2025-10-20T10:30:00.000000Z",
    "base_url": "https://ip-teamway.com"
}
```

## Error Responses

All endpoints return consistent error responses:

```json
{
    "success": false,
    "message": "Error description",
    "timestamp": "2025-10-20T10:30:00.000000Z"
}
```

**Common HTTP Status Codes:**

-   `200` - Success
-   `404` - Resource not found
-   `500` - Internal server error

## Response Headers

All API responses include these headers:

-   `Content-Type: application/json`
-   `Access-Control-Allow-Origin: *`
-   `X-API-Version: 1.0.0`
-   `X-API-Base-URL: https://ip-teamway.com`

## App Data Structure

Each app object contains the following fields:

| Field           | Type      | Description                                |
| --------------- | --------- | ------------------------------------------ |
| `id`            | integer   | Unique identifier                          |
| `app_name`      | string    | Name of the application                    |
| `description`   | string    | Description of the app (nullable)          |
| `phone_number`  | string    | Contact phone number (nullable)            |
| `database_type` | string    | Type of database (mysql, postgresql, etc.) |
| `database_name` | string    | Name of the database                       |
| `port`          | integer   | Database port number                       |
| `host`          | string    | Database host                              |
| `username`      | string    | Database username                          |
| `password`      | string    | Database password (hidden in responses)    |
| `created_at`    | timestamp | Creation timestamp                         |
| `updated_at`    | timestamp | Last update timestamp                      |

## Notes

-   The `password` field is hidden in all API responses for security
-   All timestamps are in ISO 8601 format (UTC)
-   Pagination is available for the apps listing endpoint
-   Search functionality works across app_name, description, and database_type fields
