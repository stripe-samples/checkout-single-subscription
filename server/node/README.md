# Checkout single subscription
An [Express server](http://expressjs.com) implementation

## Requirements
* Node v10+
* [Configured .env file](../README.md)

## How to run

1. Install dependencies

```
npm install
```

<details>
<summary>Enabling Stripe Tax</summary>

   In the [`server.js`](./server.js) file you will find the following code commented out
   ```js
   // automatic_tax: { enabled: true }
   ```

   Uncomment this line of code and the sales tax will be automatically calculated during the checkout.

   Make sure you previously went through the set up of Stripe Tax: [Set up Stripe Tax](https://stripe.com/docs/tax/set-up) and you have your products and prices updated with tax behavior and optionally tax codes: [Docs - Update your Products and Prices](https://stripe.com/docs/tax/checkout#product-and-price-setup)
</details>

2. Run the application

```
npm start
```

3. Go to `localhost:4242` to see the demo