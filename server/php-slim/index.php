<?php
use Slim\Http\Request;
use Slim\Http\Response;
use Stripe\Stripe;

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

require './config.php';

$app = new \Slim\App;

// Instantiate the logger as a dependency
$container = $app->getContainer();
$container['logger'] = function ($c) {
  $settings = $c->get('settings')['logger'];
  $logger = new Monolog\Logger($settings['name']);
  $logger->pushProcessor(new Monolog\Processor\UidProcessor());
  $logger->pushHandler(new Monolog\Handler\StreamHandler(__DIR__ . '/logs/app.log', \Monolog\Logger::DEBUG));
  return $logger;
};

$app->add(function ($request, $response, $next) {
    Stripe::setApiKey(getenv('STRIPE_SECRET_KEY'));
    return $next($request, $response);
});

$app->get('/', function (Request $request, Response $response, array $args) {
    return $response->write(file_get_contents(getenv('STATIC_DIR') . '/index.html'));
});

$app->get('/setup', function (Request $request, Response $response, array $args) {
  $pub_key = getenv('STRIPE_PUBLISHABLE_KEY');
  return $response->withJson([
    'publishableKey' => $pub_key,
    'basicPrice' => getenv('BASIC_PRICE_ID'),
    'proPrice' => getenv('PRO_PRICE_ID')
  ]);
});

// Fetch the Checkout Session to display the JSON result on the success page
$app->get('/checkout-session', function (Request $request, Response $response, array $args) {
  $id = $request->getQueryParams()['sessionId'];
  $checkout_session = \Stripe\Checkout\Session::retrieve($id);

  return $response->withJson($checkout_session);
});


$app->post('/create-checkout-session', function(Request $request, Response $response, array $args) {
  $domain_url = getenv('DOMAIN');
  $body = json_decode($request->getBody());

  // Create new Checkout Session for the order
  // Other optional params include:
  // [billing_address_collection] - to display billing address details on the page
  // [customer] - if you have an existing Stripe Customer ID
  // [payment_intent_data] - lets capture the payment later
  // [customer_email] - lets you prefill the email input in the form
  // For full details see https://stripe.com/docs/api/checkout/sessions/create
  // ?session_id={CHECKOUT_SESSION_ID} means the redirect will have the session ID set as a query param
  try {
    $checkout_session = \Stripe\Checkout\Session::create([
      'success_url' => $domain_url . '/success.html?session_id={CHECKOUT_SESSION_ID}',
      'cancel_url' => $domain_url . '/canceled.html',
      'payment_method_types' => ['card'],
      'mode' => 'subscription',
      'line_items' => [[
        'price' => $body->priceId,
        'quantity' => 1,
      ]],
    ]);
  } catch (Exception $e) {
    return $response->withJson([
      'error' => [
        'message' => $e->getError()->message,
      ],
    ], 400);
  }

  return $response->withJson(['sessionId' => $checkout_session['id']]);
});

$app->post('/customer-portal', function(Request $request, Response $response) {
  $body = json_decode($request->getBody());
  // For demonstration purposes, we're using the Checkout session to retrieve the customer ID. 
  // Typically this is stored alongside the authenticated user in your database. 
  $checkout_session = \Stripe\Checkout\Session::retrieve($body->sessionId);
  $stripe_customer_id = $checkout_session->customer;

  // This is the URL to which the user will be redirected after they have
  // finished managing their billing in the portal.
  $return_url = getenv('DOMAIN');

  $session = \Stripe\BillingPortal\Session::create([
    'customer' => $stripe_customer_id,
    'return_url' => $return_url,
  ]);

  return $response->withJson(['url' => $session->url]);
});

$app->post('/webhook', function(Request $request, Response $response) {
    $logger = $this->get('logger');
    $event = $request->getParsedBody();
    // Parse the message body (and check the signature if possible)
    $webhookSecret = getenv('STRIPE_WEBHOOK_SECRET');
    if ($webhookSecret) {
      try {
        $event = \Stripe\Webhook::constructEvent(
          $request->getBody(),
          $request->getHeaderLine('stripe-signature'),
          $webhookSecret
        );
      } catch (\Exception $e) {
        return $response->withJson([ 'error' => $e->getMessage() ])->withStatus(403);
      }
    } else {
      $event = $request->getParsedBody();
    }
    $type = $event['type'];
    $object = $event['data']['object'];

    if($type == 'checkout.session.completed') {
      $logger->info('🔔  Payment succeeded! ');
    }

    return $response->withJson([ 'status' => 'success' ])->withStatus(200);
});

$app->run();
