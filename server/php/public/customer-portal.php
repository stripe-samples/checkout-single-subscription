<?php

require '../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// For sample support and debugging. Not required for production:
\Stripe\Stripe::setAppInfo(
  "stripe-samples/checkout-single-subscription",
  "0.0.3",
  "https://github.com/stripe-samples/checkout-single-subscription"
);

\Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
  echo 'Invalid request';
  exit;
}

// For demonstration purposes, we're using the Checkout session to retrieve the customer ID.
// Typically this is stored alongside the authenticated user in your database.
$checkout_session = \Stripe\Checkout\Session::retrieve($_POST['sessionId']);
$stripe_customer_id = $checkout_session->customer;

// This is the URL to which users are redirected after managing their billing
// with the customer portal.
$return_url = $_ENV['DOMAIN'];

$session = \Stripe\BillingPortal\Session::create([
  'customer' => $stripe_customer_id,
  'return_url' => $return_url,
]);

header("HTTP/1.1 303 See Other");
header("Location: " . $session->url);
