<?php

require '../vendor/autoload.php';
$config = parse_ini_file('../config.ini');

if (!$config) {
	http_response_code(500);
	echo json_encode([ 'error' => 'Internal server error.' ]);
	exit;
}

\Stripe\Stripe::setApiKey($config['stripe_secret_key']);

$session = \Stripe\BillingPortal\Session::create([
  'return_url' => $config['domain'],
  'customer' => $config['customer'],
]);

header("Location: " . $session->url, true, 302);
exit();
