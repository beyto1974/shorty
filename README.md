# Shorty
A very simple REST based URL shortener microservice.

## Setup
Required .env-variables:
- APP_URL

Optional .env-variables:
- MASTER_TOKEN: To access global stats endpoint
- HANDLE_LENGTH (defaults to 6)
- HANDLE_ALPHABET (defaults to pugx/shortid-php value, must be 64 characters long)

## Example output
Where domain name is dev.shorty.pro:

- dev.shorty.pro/XTDD1u
- dev.shorty.pro/ukkwiK

## API usage

### Authentication
Create a user and a **bearer token** for each consumer.

Create user:
```php
User::factory()->create(['name' => 'Johny', 'email' => 'test@example.com'])
```

Create token:
```php
User::firstWhere('name', 'Johny')->createToken('My application description')->plainTextToken
```

Delete tokens when necessary.
```php
User::firstWhere('name', 'Johny')->tokens->map->delete();
```

Delete users when necessary (deleting all shorteners).
```php
Shortener::where('created_by_user_id', User::firstWhere('name', 'Johny')->id)->delete();
User::firstWhere('name', 'Johny')->delete()
```

### Endpoints
- PUT /shortener with body {original_url: string} and status code 201 or 422 ({essage: string, errors: laravel_validation_errors}).
- GET /shortener/{id} with body {id: number, original_url: string, handle: string, redirect_url: string} and status code 200 or 404.
- DELETE /shortener/{id} with status code 200.

## Other features
- Status page returing {'ok': true}