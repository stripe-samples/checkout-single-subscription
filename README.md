# Using Checkout for subscriptions

[Checkout](https://stripe.com/docs/payments/checkout) is a pre-built payment page that lets you accept cards and Apple Pay. [Billing](https://stripe.com/docs/billing) is a suite of APIs that lets you model complex subscription plans. You can combine the two products to get a subscription payment page up and running without the need of a server. 

When your customer is ready to pay, use [Stripe.js](https://stripe.com/docs/web) with the ID of your Billing [Plan](https://stripe.com/docs/api/plans) to redirect them to your Checkout page.

<img src="./checkout-demo.gif" alt="A gif of the Checkout payment page rendering" align="center">

**Demo**

See the sample [live](https://4iupj.sse.codesandbox.io/) or [fork](https://codesandbox.io/s/stripe-sample-checkout-single-subscription-4iupj) the Node implementation on CodeSandbox.

The demo is running in test mode -- use `4242424242424242` as a test card number with any CVC + future expiration date.

Use the `4000000000003220` test card number to trigger a 3D Secure challenge flow.

Read more about testing on Stripe at https://stripe.com/docs/testing.

**Features:**

- Localization in 14 different languages 🌍
- Built-in Apple Pay support 🍎
- Built-in dynamic 3D Secure (ready for SCA) 🔔
- Plans to support more payment methods 🔮

For more features see the [Checkout documentation](https://stripe.com/docs/payments/checkout/subscriptions).

There are two integrations: [client-only](./client-only) and [client-and-server](./client-and-server). The client-and-server integration uses the [Checkout Sessions API](https://stripe.com/docs/api/checkout/sessions) for additional functionality.

<!-- prettier-ignore -->
|     | client-only | client-and-server
:--- | :---: | :---:
🔨 **Prebuilt checkout page.** Create a payment page that is customizable with your business' name and logo. | ✅  | ✅ |
🖥️ **Define plans in Dashboard or via API.** Create a plan with either the Stripe Dashboard or API. | ✅  | ✅ |
🔢 **Start subscription for an existing Customer.** Use [Customers](https://stripe.com/docs/api/customers) to keep track of additional customer data.  | ❌  | ✅ |

## How to run locally

This recipe includes [5 server implementations](server/README.md) in our most popular languages.

If you want to run the recipe locally, copy the .env.example file to your own .env file in this directory:

```
cp .env.example .env
```

You will need a Stripe account with its own set of [API keys](https://stripe.com/docs/development#api-keys).

## FAQ

Q: Why did you pick these frameworks?

A: We chose the most minimal framework to convey the key Stripe calls and concepts you need to understand. These demos are meant as an educational tool that helps you roadmap how to integrate Stripe within your own system independent of the framework.

Q: Can you show me how to build X?

A: We are always looking for new recipe ideas, please email dev-samples@stripe.com with your suggestion!

## Author(s)

[@adreyfus-stripe](https://twitter.com/adrind)
