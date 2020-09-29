<?php

require '../vendor/autoload.php';
$config = parse_ini_file('../config.ini');

if (!$config) {
	http_response_code(500);
	echo json_encode([ 'error' => 'Internal server error.' ]);
	exit;
}

\Stripe\Stripe::setApiKey($config['stripe_secret_key']);

// This is the ID of the Stripe Customer. Typically this is stored alongside
// the authenticated user in your database. For demonstration, we're using the
// config.
$stripe_customer_id = $config['customer'];

// This is the URL to which users are redirected after managing their billing
// with the customer portal.
$return_url = $config['domain'];

$session = \Stripe\BillingPortal\Session::create([
  'customer' => $stripe_customer_id,
  'return_url' => $return_url,
]);

header("Location: " . $session->url, true, 302);
exit();
