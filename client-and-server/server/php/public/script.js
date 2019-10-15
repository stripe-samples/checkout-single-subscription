// Create a Checkout Session with the selected plan ID
var createCheckoutSession = function(planId) {
  return fetch("/create-checkout-session.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({
      planId: planId
    })
  }).then(function(result) {
    return result.json();
  });
};

// Handle any errors returned from Checkout
var handleResult = function(result) {
  if (result.error) {
    var displayError = document.getElementById("error-message");
    displayError.textContent = result.error.message;
  }
};

/* Get your Stripe public key to initialize Stripe.js */
fetch("/config.php")
  .then(function(result) {
    return result.json();
  })
  .then(function(json) {
    var publicKey = json.publicKey;
    var basicPlanId = json.basicPlan;
    var proPlanId = json.proPlan;

    var stripe = Stripe(publicKey);
    // Setup event handler to create a Checkout Session when button is clicked
    document
      .getElementById("basic-plan-btn")
      .addEventListener("click", function(evt) {
        createCheckoutSession(basicPlanId).then(function(data) {
          // Call Stripe.js method to redirect to the new Checkout page
          stripe
            .redirectToCheckout({
              sessionId: data.sessionId
            })
            .then(handleResult);
        });
      });

    // Setup event handler to create a Checkout Session when button is clicked
    document
      .getElementById("pro-plan-btn")
      .addEventListener("click", function(evt) {
        createCheckoutSession(proPlanId).then(function(data) {
          // Call Stripe.js method to redirect to the new Checkout page
          stripe
            .redirectToCheckout({
              sessionId: data.sessionId
            })
            .then(handleResult);
        });
      });
  });
