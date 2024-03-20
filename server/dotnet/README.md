# Checkout single subscription - .NET

## Requirements

* [.NET SDK v8+](https://dotnet.microsoft.com/download/dotnet)
* [Configured .env file](../../README.md)

## How to run

1. Confirm `.env` configuration

Ensure the API keys are configured in `.env` in this directory. It should include the following keys:

```yaml
# Stripe API keys - see https://stripe.com/docs/development/quickstart#api-keys
STRIPE_PUBLISHABLE_KEY=pk_test...
STRIPE_SECRET_KEY=sk_test...

# Required to verify signatures in the webhook handler.
# See README on how to use the Stripe CLI to test webhooks
STRIPE_WEBHOOK_SECRET=whsec_...

# Path to front-end implementation. Note: PHP has it's own front end implementation.
STATIC_DIR=../../client/html
DOMAIN=http://localhost:4242
```

<details>
<summary>Enabling Stripe Tax</summary>

   In the [`Controllers/PaymentsController.cs`](./Controllers/PaymentsController.cs) file you will find the following code commented out
   ```csharp
   // AutomaticTax = new SessionAutomaticTaxOptions { Enabled = true },
   ```

   Uncomment this line of code and the sales tax will be automatically calculated during the checkout.

   Make sure you previously went through the set up of Stripe Tax: [Set up Stripe Tax](https://stripe.com/docs/tax/set-up) and you have your products and prices updated with tax behavior and optionally tax codes: [Docs - Update your Products and Prices](https://stripe.com/docs/tax/checkout#product-and-price-setup)
</details>


2.  Run the application

```sh
dotnet run
```
