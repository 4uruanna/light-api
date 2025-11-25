# Light API

Version: 1.0.0

## Prerequisites

| Dependency | Version |
|:-|:-|
|Php|8|
|Composer|2|

## Configuration
> All values below are consumed via the global variable `$_SERVER`

| Key | Require | Type | Default | Description |
|:-|:-|:-|:-|:-|
| ENVIRONMENT | False | `DEVELOPMENT`, `PRODUCTION` or `TEST` | `DEVELOPMENT` | Environment |
| CACHE_DIRECTORY | False | String | `NULL` | Works only on __production__. Specifies where all caches should be stores |
| DATE_TIMEZONE | False | String | `NULL` | Relate to [PHP timezones](https://www.php.net/manual/en/timezones.php) |
| ALLOW_CORS | False | Boolean | `FALSE` | Allow [cross-origin resource sharing (CORS)](https://developer.mozilla.org/en-US/docs/Web/HTTP/Guides/CORS) |
| CORS_ORIGIN | False | String | * ||
| CORS_HEADERS | False | String | * ||
| CORS_METHODS | False | String | GET, POST, PUT, PATCH, DELETE, OPTIONS ||
