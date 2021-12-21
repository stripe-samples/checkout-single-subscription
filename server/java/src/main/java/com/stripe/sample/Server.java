package com.stripe.sample;

import java.nio.file.Paths;

import java.util.HashMap;
import java.util.Map;

import static spark.Spark.get;
import static spark.Spark.post;
import static spark.Spark.port;
import static spark.Spark.staticFiles;

import com.google.gson.Gson;
import com.google.gson.annotations.SerializedName;

import com.stripe.Stripe;
import com.stripe.model.Event;
import com.stripe.exception.*;
import com.stripe.net.Webhook;
import com.stripe.model.checkout.Session;
import com.stripe.param.checkout.SessionCreateParams;

import io.github.cdimascio.dotenv.Dotenv;

public class Server {
    private static Gson gson = new Gson();

    public static void main(String[] args) {
        port(4242);

        Dotenv dotenv = Dotenv.load();

        Stripe.apiKey = dotenv.get("STRIPE_SECRET_KEY");
        // For sample support and debugging, not required for production:
        Stripe.setAppInfo(
            "stripe-samples/checkout-single-subscription",
            "0.0.2",
            "https://github.com/stripe-samples/checkout-single-subscription"
        );


        staticFiles.externalLocation(
                Paths.get(Paths.get("").toAbsolutePath().toString(), dotenv.get("STATIC_DIR")).normalize().toString());

        get("/config", (request, response) -> {
            response.type("application/json");

            Map<String, Object> responseData = new HashMap<>();
            responseData.put("publishableKey", dotenv.get("STRIPE_PUBLISHABLE_KEY"));
            responseData.put("basicPrice", dotenv.get("BASIC_PRICE_ID"));
            responseData.put("proPrice", dotenv.get("PRO_PRICE_ID"));
            return gson.toJson(responseData);
        });

        // Fetch the Checkout Session to display the JSON result on the success page
        get("/checkout-session", (request, response) -> {
            response.type("application/json");

            String sessionId = request.queryParams("sessionId");
            Session session = Session.retrieve(sessionId);

            return gson.toJson(session);
        });

        post("/create-checkout-session", (request, response) -> {
            String domainUrl = dotenv.get("DOMAIN");

            // Create new Checkout Session for the order
            // Other optional params include:
            // [billing_address_collection] - to display billing address details on the page
            // [customer] - if you have an existing Stripe Customer ID
            // [payment_intent_data] - lets capture the payment later
            // [customer_email] - lets you prefill the email input in the form
            // [automatic_tax] - to automatically calculate sales tax, VAT and GST in the checkout page
            // For full details see https://stripe.com/docs/api/checkout/sessions/create

            // ?session_id={CHECKOUT_SESSION_ID} means the redirect will have the session ID
            // set as a query param
            SessionCreateParams params = new SessionCreateParams.Builder()
                .setSuccessUrl(domainUrl + "/success.html?session_id={CHECKOUT_SESSION_ID}")
                .setCancelUrl(domainUrl + "/canceled.html")
                .setMode(SessionCreateParams.Mode.SUBSCRIPTION)
                .addLineItem(new SessionCreateParams.LineItem.Builder()
                  .setQuantity(1L)
                  .setPrice(request.queryParams("priceId"))
                  .build()
                )
                // .setAutomaticTax(SessionCreateParams.AutomaticTax.builder().setEnabled(true).build()).
                .build();

            try {
                Session session = Session.create(params);
                response.redirect(session.getUrl(), 303);
                return "";
            } catch(Exception e) {
                Map<String, Object> messageData = new HashMap<>();
                messageData.put("message", e.getMessage());
                Map<String, Object> responseData = new HashMap<>();
                responseData.put("error", messageData);
                response.status(400);
                return gson.toJson(responseData);
            }
        });

        post("/customer-portal", (request, response) -> {
            // For demonstration purposes, we're using the Checkout session to retrieve the customer ID.
            // Typically this is stored alongside the authenticated user in your database.
            Session checkoutSession = Session.retrieve(request.queryParams("sessionId"));
            String customer = checkoutSession.getCustomer();
            String domainUrl = dotenv.get("DOMAIN");

            com.stripe.param.billingportal.SessionCreateParams params = new com.stripe.param.billingportal.SessionCreateParams.Builder()
                .setReturnUrl(domainUrl)
                .setCustomer(customer)
                .build();
            com.stripe.model.billingportal.Session portalSession = com.stripe.model.billingportal.Session.create(params);

            response.redirect(portalSession.getUrl(), 303);
            return "";
        });

        post("/webhook", (request, response) -> {
            String payload = request.body();
            String sigHeader = request.headers("Stripe-Signature");
            String endpointSecret = dotenv.get("STRIPE_WEBHOOK_SECRET");

            Event event = null;

            try {
                event = Webhook.constructEvent(payload, sigHeader, endpointSecret);
            } catch (SignatureVerificationException e) {
                // Invalid signature
                response.status(400);
                return "";
            }

            switch (event.getType()) {
                case "checkout.session.completed":
                    System.out.println("Payment succeeded!");
                    response.status(200);
                    return "";
                default:
                    response.status(200);
                    return "";
            }
        });
    }
}
