# Checkout single subscription - .NET

<details>
<summary>Enabling Stripe Tax</summary>

   In the [`Controllers/PaymentsController.cs`](./Controllers/PaymentsController.cs) file you will find the following code commented out
   ```csharp
   // AutomaticTax = new SessionAutomaticTaxOptions { Enabled = true },
   ```

   Uncomment this line of code and the sales tax will be automatically calculated during the checkout.

   Make sure you previously went through the set up of Stripe Tax: [Set up Stripe Tax](https://stripe.com/docs/tax/set-up) and you have your products and prices updated with tax behavior and optionally tax codes: [Docs - Update your Products and Prices](https://stripe.com/docs/tax/checkout#product-and-price-setup)
</details>

## How to run

```sh
dotnet run Program.cs
```
