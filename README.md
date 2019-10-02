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

There are two integrations: `client-only` and `client-and-server`. The following are instructions on how to run the `client-and-server` integration: 

This sample includes 5 server implementations in Node, Ruby, Python, Java, and PHP.

Follow the steps below to run locally.

**1. Clone the repository:**

```
git clone https://github.com/stripe-samples/checkout-single-subscription
```

**2. Create Products and Plans on Stripe** 

This sample requires two [Plan](https://stripe.com/docs/api/plans/object) IDs to create the Checkout page. Products and Plans are objects on Stripe that lets you model a subscription. 

You can create Products and Plans [in the dashboard](https://dashboard.stripe.com/products) or via [the API](https://stripe.com/docs/api/plans/create). Create two Plans to run this sample. 

**3. Copy the .env.example to a .env file:**

```
cp .env.example .env
```

You will need a Stripe account in order to run the demo. Once you set up your account, go to the Stripe [developer dashboard](https://stripe.com/docs/development#api-keys) to find your API keys.

```
STRIPE_PUBLIC_KEY=<replace-with-your-publishable-key>
STRIPE_SECRET_KEY=<replace-with-your-secret-key>
```

`STATIC_DIR` tells the server where to the client files are located and does not need to be modified unless you move the server files.

`BASIC_PLAN_ID` requires a Plan ID for a "basic" subscription.

`PRO_PLAN_ID` requires a Plan ID for a "pro" subscription.

`DOMAIN` is the domain of your website, where Checkout will redirect back to after the customer completes the payment on the Checkout page. 

**4. Follow the server instructions on how to run:**

Pick the server language you want and follow the instructions in the server folder README on how to run.

For example, if you want to run the Node server:

```
cd client-and-server/server/node # there's a README in this folder with instructions
npm install
npm start
```

**5. [Optional] Run a webhook locally:**

You can use the Stripe CLI to easily spin up a local webhook.

First [install the CLI](https://stripe.com/docs/stripe-cli) and [link your Stripe account](https://stripe.com/docs/stripe-cli#link-account).

```
stripe listen --forward-to localhost:4242/webhook
```

The CLI will print a webhook secret key to the console. Set `STRIPE_WEBHOOK_SECRET` to this value in your .env file.

You should see events logged in the console where the CLI is running.

When you are ready to create a live webhook endpoint, follow our guide in the docs on [configuring a webhook endpoint in the dashboard](https://stripe.com/docs/webhooks/setup#configure-webhook-settings). 


## FAQ

Q: Why did you pick these frameworks?

A: We chose the most minimal framework to convey the key Stripe calls and concepts you need to understand. These demos are meant as an educational tool that helps you roadmap how to integrate Stripe within your own system independent of the framework.

Q: Can you show me how to build X?

A: We are always looking for new sample ideas, please email dev-samples@stripe.com with your suggestion!

## Author(s)

[@adreyfus-stripe](https://twitter.com/adrind)
