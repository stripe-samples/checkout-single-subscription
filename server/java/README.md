# Checkout single subscription

## Requirements
* Maven
* Java

<details>
<summary>Enabling Stripe Tax</summary>

   In the [`Server.java`](./src/main/java/com/stripe/sample/Server.java) file you will find the following code commented out
   ```java
   // .setAutomaticTax(SessionCreateParams.AutomaticTax.builder().setEnabled(true).build())
   ```

   Uncomment this line of code and the sales tax will be automatically calculated during the checkout.

   Make sure you previously went through the set up of Stripe Tax: [Set up Stripe Tax](https://stripe.com/docs/tax/set-up) and you have your products and prices updated with tax behavior and optionally tax codes: [Docs - Update your Products and Prices](https://stripe.com/docs/tax/checkout#product-and-price-setup)
</details>

1. Build the jar
```
mvn package
```

2. Run the packaged jar
```
java -cp target/sample-jar-with-dependencies.jar com.stripe.sample.Server
```

3. Go to `localhost:4242` in your browser to see the demo
