# Payments

Payments is a simple payments system that provides an interface for handling multiple payment methods.


## Requirements 

1.  [Composer](http://getcomposer.org/).
2. PHP version `8.2`.

## Installation
1. Clone the repository from `https://github.com/marcosgrimm/pay`
2. Access the application directory.
3. Execute `composer install`.
4. Create the environment variables file: `cp .env.example .env`.
5. Execute `php artisan key:generate` to set the application key.
6. Execute `php artisan migrate --seed`. A question will be prompted if you want to create the database, answer `Yes` to create the `SQLite`database.
7. Execute `php artisan serve` to run the server. 

## Authentication

1. All API requests must contain a header using [Bearer Authentication](https://swagger.io/docs/specification/authentication/bearer-authentication/).
2. For this test purpose only a random token can be retrieved when using the endpoint [http://localhost:8000/demo-random-token](http://localhost:8000/demo-random-token
   ). 
   2. The response will be as the following:
      ```
      {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJtZXJjaGFudF9pZCI6IjdjM2M2MDU2LWNhMTgtNDIwZS1hNWU2LTE3YThlMTk4YmE1NSJ9.BdRe2U0MzyiDYQ9ATd9J4-30rvdyKwZf_psQDZ-Ddyo"
      }
      ```
   3. The token value (`eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJtZXJjaGFudF9pZCI6IjdjM2M2MDU2LWNhMTgtNDIwZS1hNWU2LTE3YThlMTk4YmE1NSJ9.BdRe2U0MzyiDYQ9ATd9J4-30rvdyKwZf_psQDZ-Ddyo`) needs to be included used in each API request.

## Documentation
Once the server is running the following documentation resources can be accessed:

### Interactive Documentation
[http://localhost:8000/api/documentation](http://localhost:8000/api/documentation).
Powered by DarkaOnLine/L5-Swagger package.

### Json File Documentation
[http://localhost:8000/docs/api-docs.json](http://localhost:8000/docs/api-docs.json)

It can also be accessed in `app/storage/api-docs/api-docs.json`.

## Tests

Execute `php artisan test` to run automated tests. 
