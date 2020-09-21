<?php

require_once 'shared.php';

$domain_url = $config['domain'];
// Create new Checkout Session for the order
// Other optional params include:
// [billing_address_collection] - to display billing address details on the page
// [customer] - if you have an existing Stripe Customer ID
// [payment_intent_data] - lets capture the payment later
// [customer_email] - lets you prefill the email input in the form
// For full details see https://stripe.com/docs/api/checkout/sessions/create

// ?session_id={CHECKOUT_SESSION_ID} means the redirect will have the session ID set as a query param
$checkout_session = \Stripe\Checkout\Session::create([
	'success_url' => $domain_url . '/success.html?session_id={CHECKOUT_SESSION_ID}',
	'cancel_url' => $domain_url . '/canceled.html',
	'payment_method_types' => ['card'],
	'mode' => 'subscription',
	'line_items' => [[
	  'price' => $body->priceId,
	  'quantity' => 1,
	]]
]);
  
echo json_encode(['sessionId' => $checkout_session['id']]);
