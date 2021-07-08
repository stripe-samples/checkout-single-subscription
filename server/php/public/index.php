<?php
require '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$basicPrice = $_ENV['BASIC_PRICE_ID'];
if (!$basicPrice) {
  http_response_code(500);
  echo "You must set a Price ID in the .env file. Please see the README";
  exit;
}

$proPrice = $_ENV['PRO_PRICE_ID'];
if (!$proPrice) {
  http_response_code(500);
  echo "You must set a Price ID in the .env file. Please see the README";
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>Stripe Checkout Sample</title>
    <meta name="description" content="A demo of Stripe Payment Intents" />

    <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="css/normalize.css" />
    <link rel="stylesheet" href="css/global.css" />
    <!-- Load Stripe.js on your website. -->
    <script src="https://js.stripe.com/v3/"></script>
  </head>

  <body>
    <div class="sr-root">
      <div class="sr-main" style="display: flex;">
        <div class="sr-container">
          <section class="container basic-photo">
            <div>
              <h1>Basic subscription</h1>
              <div class="pasha-image">
                <img
                  src="https://picsum.photos/280/320?random=4"
                  width="140"
                  height="160"
                />
              </div>
            </div>
            <form action="/create-checkout-session.php" method="POST">
              <input type="hidden" name="priceId" value="<?= $basicPrice ?>" />
              <button>$5.00</button>
            </form>
          </section>
          <section class="container pro-photo">
            <div>
              <h1>Pro subscription</h1>
              <h4>3 photos per week</h4>
              <div class="pasha-image-stack">
                <img
                  src="https://picsum.photos/280/320?random=1"
                  width="105"
                  height="120"
                  alt="Sample Pasha image 1"
                />
                <img
                  src="https://picsum.photos/280/320?random=2"
                  width="105"
                  height="120"
                  alt="Sample Pasha image 2"
                />
                <img
                  src="https://picsum.photos/280/320?random=3"
                  width="105"
                  height="120"
                  alt="Sample Pasha image 3"
                />
              </div>
            </div>
            <form action="/create-checkout-session.php" method="POST">
              <input type="hidden" name="priceId" value="<?= $proPrice ?>" />
              <button>$12.00</button>
            </form>
          </section>
        </div>
        <div id="error-message"></div>
      </div>
    </div>
  </body>
</html>
