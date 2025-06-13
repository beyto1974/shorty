# Shorty
A very simple REST based URL shortener microservice.

## Setup
Required .env-variables:
- APP_URL

Optional .env-variables:
- MASTER_TOKEN: To access global stats endpoint
- HANDLE_LENGTH (defaults to 6)
- HANDLE_ALPHABET (defaults to pugx/shortid-php value, must be 64 characters long)

Database:
- Set the database .env-variables.
- Run the migration.

## Deploy
- Use by preference [https://deployer.org/](Deployer).
- Run the following to build the doc:
```bash
pnpm build-doc
```

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

## Other features
- Status page returing {'ok': true}
- Web user interface on /ui/ for bulk shortener creation.

## Security suggestions
- /doc/ and /ui/ can be protected/forbidden.

## TODO
- Custom 404 page.
- Master endpoints for user and token CRUD.
- Pass handle on create.