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

## Documentation
Once the server is running the following documentation resources can be accessed:

### Interactive Documentation 
[http://localhost:8000/api/documentation](http://localhost:8000/api/documentation). 
Powered by DarkaOnLine/L5-Swagger package.


### Json File Documentation 
[http://localhost:8000/docs/api-docs.json](http://localhost:8000/docs/api-docs.json)

It can also be accessed in `app/storage/api-docs/api-docs.json`.

## Tests

Execute `php artisan test` to run tests. 

# 
