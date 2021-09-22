# Using Checkout for a subscription with PHP

## Requirements
* PHP

## How to run

<details>
<summary>Enabling Stripe Tax</summary>

   In the [`create-checkout-session.php`](./public/create-checkout-session.php) file you will find the following code commented out
   ```php
   // 'automatic_tax' => ['enabled' => true],
   ```

   Uncomment this line of code and the sales tax will be automatically calculated during the checkout.

   Make sure you previously went through the set up of Stripe Tax: [Set up Stripe Tax](https://stripe.com/docs/tax/set-up) and you have your products and prices updated with tax behavior and optionally tax codes: [Docs - Update your Products and Prices](https://stripe.com/docs/tax/checkout#product-and-price-setup)
</details>

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
