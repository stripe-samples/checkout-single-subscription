# Using Checkout for a subscription with PHP

## Requirements
* PHP

## How to run

1. Run composer to set up dependencies

```
composer install
```

2. Copy .env.example from the root of the project to .env in the server directory and replace with your Stripe API keys and set a Basic and Pro price

```
cp .env.example server/php/.env
```

3. Run the server locally

```
cd public
php -S localhost:4242
```

4. Go to localhost:4242
