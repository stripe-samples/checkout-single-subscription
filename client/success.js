const urlParams = new URLSearchParams(window.location.search);
const sessionId = urlParams.get("session_id")

if (sessionId) {
  fetch("/checkout-session?sessionId=" + sessionId)
    .then(function(result){
      return result.json()
    })
    .then(function(session){
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
