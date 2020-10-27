const urlParams = new URLSearchParams(window.location.search);
const sessionId = urlParams.get("session_id")
let customerId;

if (sessionId) {
  fetch("/checkout-session?sessionId=" + sessionId)
    .then(function(result){
      return result.json()
    })
    .then(function(session){
      // We store the customer ID here so that we can pass to the
      // server and redirect to customer portal. Note that, in practice
      // this ID should be stored in your database when you receive
      // the checkout.session.completed event. This demo does not have
      // a database, so this is the workaround. This is *not* secure.
      // You should use the Stripe Customer ID from the authenticated
      // user on the server.
      customerId = session.customer;

      var sessionJSON = JSON.stringify(session, null, 2);
      document.querySelector("pre").textContent = sessionJSON;
    })
    .catch(function(err){
      console.log('Error when fetching Checkout session', err);
    });

  // In production, this should check CSRF, and not pass the session ID.
  // The customer ID for the portal should be pulled from the 
  // authenticated user on the server.
  const manageBillingForm = document.querySelector('#manage-billing-form');
  manageBillingForm.addEventListener('submit', function(e) {
    e.preventDefault();
    fetch('/customer-portal', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        sessionId: sessionId
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        window.location.href = data.url;
      })
      .catch((error) => {
        console.error('Error:', error);
      });
  });
}
