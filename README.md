# Shorty
A very simple URL shortener microservice.

## Setup
Optional .env-variables:
- HANDLE_LENGTH
- HANDLE_ALPHABET

## Access
Create a user and a token for each application.

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