# Checkout single subscription - Go

<details>
<summary>Enabling Stripe Tax</summary>

   In the [`server.go`](./server.go) file you will find the following code commented out
   ```go
   // AutomaticTax: &stripe.CheckoutSessionAutomaticTaxParams{Enabled: stripe.Bool(true)},
   ```

   Uncomment this line of code and the sales tax will be automatically calculated during the checkout.

   Make sure you previously went through the set up of Stripe Tax: [Set up Stripe Tax](https://stripe.com/docs/tax/set-up) and you have your products and prices updated with tax behavior and optionally tax codes: [Docs - Update your Products and Prices](https://stripe.com/docs/tax/checkout#product-and-price-setup)
</details>

## How to run

1. Confirm `.env` configuration

The `.env` file should be in the server directory (the one with `server.go`)

If no `.env` file is found, copy the `.env.example` from the root to the server directory
and update keys as shown below.


Ensure the API keys are configured in `.env` in this directory. It should
include the following keys:

```yaml
# Stripe API keys - see https://stripe.com/docs/development/quickstart#api-keys
STRIPE_PUBLISHABLE_KEY=pk_test...
STRIPE_SECRET_KEY=sk_test...
# Required to verify signatures in the webhook handler.
# See README on how to use the Stripe CLI to test webhooks
STRIPE_WEBHOOK_SECRET=whsec_...

DOMAIN=http://localhost:4242
# Price ID for a recurring price
BASIC_PRICE_ID=price_xyz987...
# Price ID for a second recurring price
PRO_PRICE_ID=price_abc123...

# Path to front-end implementation. Note: PHP has it's own front end implementation.
STATIC_DIR=../../client/html
```

2. Install dependencies

From the server directory (the one with `server.go`) run:

```sh
go mod tidy
go mod vendor
```

3. Run the application

Again from the server directory run:

```sh
go run server.go
```

View in browser: [localhost:4242](http://localhost:4242)
