# Checkout single subscription

A [Sinatra](http://sinatrarb.com/) implementation.

## Requirements
* Ruby v2.4.5+
* [Configured .env file](../README.md)

## How to run

<details>
<summary>Enabling Stripe Tax</summary>

   In the [`server.rb`](./server.rb) file you will find the following code commented out
   ```ruby
   # automatic_tax: { enabled: true },
   ```

   Uncomment this line of code and the sales tax will be automatically calculated during the checkout.

   Make sure you previously went through the set up of Stripe Tax: [Set up Stripe Tax](https://stripe.com/docs/tax/set-up) and you have your products and prices updated with tax behavior and optionally tax codes: [Docs - Update your Products and Prices](https://stripe.com/docs/tax/checkout#product-and-price-setup)
</details>

1. Install dependencies
```
bundle install
```

2. Run the application
```
ruby server.rb
```

3. Go to `localhost:4242` in your browser to see the demo