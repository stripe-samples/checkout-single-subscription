package main

import (
	"bytes"
	"encoding/json"
	"io"
	"io/ioutil"
	"log"
	"net/http"
	"os"

	"github.com/joho/godotenv"
	"github.com/stripe/stripe-go/v71"
	"github.com/stripe/stripe-go/v71/checkout/session"
	portalsession "github.com/stripe/stripe-go/v71/billingportal/session"
	"github.com/stripe/stripe-go/v71/webhook"
)

func main() {
	if err := godotenv.Load(); err != nil {
		log.Fatalf("godotenv.Load: %v", err)
	}

	stripe.Key = os.Getenv("STRIPE_SECRET_KEY")

	http.Handle("/", http.FileServer(http.Dir(os.Getenv("STATIC_DIR"))))
	http.HandleFunc("/setup", handleSetup)
	http.HandleFunc("/create-checkout-session", handleCreateCheckoutSession)
	http.HandleFunc("/checkout-session", handleCheckoutSession)
	http.HandleFunc("/customer-portal", handleCustomerPortal)
	http.HandleFunc("/webhook", handleWebhook)
	addr := "localhost:4242"
	log.Printf("Listening on %s ...", addr)
	log.Fatal(http.ListenAndServe(addr, nil))
}

func handleSetup(w http.ResponseWriter, r *http.Request) {
	if r.Method != "GET" {
		http.Error(w, http.StatusText(http.StatusMethodNotAllowed), http.StatusMethodNotAllowed)
		return
	}
	writeJSON(w, struct {
		PublishableKey string `json:"publishableKey"`
		BasicPrice string `json:"basicPrice"`
		ProPrice string `json:"proPrice"`
	}{
		PublishableKey: os.Getenv("STRIPE_PUBLISHABLE_KEY"),
		BasicPrice: os.Getenv("BASIC_PRICE_ID"),
		ProPrice: os.Getenv("PRO_PRICE_ID"),
	})
}

func handleCreateCheckoutSession(w http.ResponseWriter, r *http.Request) {
  if r.Method != "POST" {
    http.Error(w, http.StatusText(http.StatusMethodNotAllowed), http.StatusMethodNotAllowed)
    return
  }

  var req struct {
    Price string `json:"priceId"`
  }
  if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
    http.Error(w, err.Error(), http.StatusInternalServerError)
    log.Printf("json.NewDecoder.Decode: %v", err)
    return
  }

  params := &stripe.CheckoutSessionParams{
    SuccessURL: stripe.String(os.Getenv("DOMAIN") + "/success.html?session_id={CHECKOUT_SESSION_ID}"),
    CancelURL: stripe.String(os.Getenv("DOMAIN") + "/cancel.html"),
    PaymentMethodTypes: stripe.StringSlice([]string{
      "card",
    }),
    Mode: stripe.String(string(stripe.CheckoutSessionModeSubscription)),
    LineItems: []*stripe.CheckoutSessionLineItemParams{
      &stripe.CheckoutSessionLineItemParams{
        Price: stripe.String(req.Price),
        Quantity: stripe.Int64(1),
      },
    },

    // This is the ID of the Stripe Customer.  Typically you'll create the
    // customer object When the user signs up for your service and you can pull
    // this out of the database with the authenticated user.
    Customer: stripe.String(os.Getenv("CUSTOMER")),
  }

  s, err := session.New(params)
  if err != nil {
    w.WriteHeader(http.StatusBadRequest)
    writeJSON(w, struct {
      ErrorData string `json:"error"`
    }{
      ErrorData: "test",
    })
    return
  }

  writeJSON(w, struct {
    SessionID string `json:"sessionId"`
  }{
    SessionID: s.ID,
  })
}

func handleCheckoutSession(w http.ResponseWriter, r *http.Request) {
  if r.Method != "GET" {
    http.Error(w, http.StatusText(http.StatusMethodNotAllowed), http.StatusMethodNotAllowed)
    return
  }
  sessionID := r.URL.Query().Get("sessionId")
  s, _ := session.Get(sessionID, nil)
  writeJSON(w, s)
}

func handleCustomerPortal(w http.ResponseWriter, r *http.Request) {
  if r.Method != "POST" {
    http.Error(w, http.StatusText(http.StatusMethodNotAllowed), http.StatusMethodNotAllowed)
    return
  }
  params := &stripe.BillingPortalSessionParams{
    Customer: stripe.String(os.Getenv("CUSTOMER")),
    ReturnURL: stripe.String(os.Getenv("DOMAIN")),
  }
  s, _ := portalsession.New(params)

  http.Redirect(w, r, s.URL, 302)
}

func handleWebhook(w http.ResponseWriter, r *http.Request) {
	if r.Method != "POST" {
		http.Error(w, http.StatusText(http.StatusMethodNotAllowed), http.StatusMethodNotAllowed)
		return
	}
	b, err := ioutil.ReadAll(r.Body)
	if err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		log.Printf("ioutil.ReadAll: %v", err)
		return
	}

	event, err := webhook.ConstructEvent(b, r.Header.Get("Stripe-Signature"), os.Getenv("STRIPE_WEBHOOK_SECRET"))
	if err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		log.Printf("webhook.ConstructEvent: %v", err)
		return
	}

	if event.Type != "checkout.session.completed" {
		return
	}
}

func writeJSON(w http.ResponseWriter, v interface{}) {
	var buf bytes.Buffer
	if err := json.NewEncoder(&buf).Encode(v); err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		log.Printf("json.NewEncoder.Encode: %v", err)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	if _, err := io.Copy(w, &buf); err != nil {
		log.Printf("io.Copy: %v", err)
		return
	}
}
