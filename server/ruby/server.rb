require 'stripe'
require 'sinatra'
require 'dotenv'
require './config_helper.rb'

# Copy the .env.example in the root into a .env file in this folder
Dotenv.load
ConfigHelper.check_env!

# For sample support and debugging, not required for production:
Stripe.set_app_info(
  'stripe-samples/checkout-single-subscription',
  version: '0.0.2',
  url: 'https://github.com/stripe-samples/checkout-single-subscription'
)
Stripe.api_version = '2020-08-27'
Stripe.api_key = ENV['STRIPE_SECRET_KEY']

set :static, true
set :public_folder, File.join(File.dirname(__FILE__), ENV['STATIC_DIR'])
set :port, 4242

get '/' do
  content_type 'text/html'
  send_file File.join(settings.public_folder, 'index.html')
end

get '/config' do
  content_type 'application/json'
  {
    publishableKey: ENV['STRIPE_PUBLISHABLE_KEY'],
    basicPrice: ENV['BASIC_PRICE_ID'],
    proPrice: ENV['PRO_PRICE_ID']
  }.to_json
end

# Fetch the Checkout Session to display the JSON result on the success page
get '/checkout-session' do
  content_type 'application/json'
  session_id = params[:sessionId]

  session = Stripe::Checkout::Session.retrieve(session_id)
  session.to_json
end

post '/create-checkout-session' do
  # Create new Checkout Session for the order
  # Other optional params include:
  # [billing_address_collection] - to display billing address details on the page
  # [customer] - if you have an existing Stripe Customer ID
  # [customer_email] - lets you prefill the email input in the form
  # For full details see https://stripe.com/docs/api/checkout/sessions/create
  # ?session_id={CHECKOUT_SESSION_ID} means the redirect will have the session ID set as a query param
  begin
    session = Stripe::Checkout::Session.create(
      success_url: ENV['DOMAIN'] + '/success.html?session_id={CHECKOUT_SESSION_ID}',
      cancel_url: ENV['DOMAIN'] + '/canceled.html',
      payment_method_types: ['card'],
      mode: 'subscription',
      line_items: [{
        quantity: 1,
        price: params['priceId'],
      }],
    )
  rescue => e
    halt 400,
        { 'Content-Type' => 'application/json' },
        { 'error': { message: e.error.message } }.to_json
  end

  redirect session.url, 303
end

post '/customer-portal' do
  # For demonstration purposes, we're using the Checkout session to retrieve the customer ID.
  # Typically this is stored alongside the authenticated user in your database.
  checkout_session_id = params['sessionId']
  checkout_session = Stripe::Checkout::Session.retrieve(checkout_session_id)

  # This is the URL to which users will be redirected after they are done
  # managing their billing.
  return_url = ENV['DOMAIN']

  session = Stripe::BillingPortal::Session.create({
    customer: checkout_session.customer,
    return_url: return_url
  })

  redirect session.url, 303
end

post '/webhook' do
  # You can use webhooks to receive information about asynchronous payment events.
  # For more about our webhook events check out https://stripe.com/docs/webhooks.
  webhook_secret = ENV['STRIPE_WEBHOOK_SECRET']
  payload = request.body.read
  if !webhook_secret.empty?
    # Retrieve the event by verifying the signature using the raw body and secret if webhook signing is configured.
    sig_header = request.env['HTTP_STRIPE_SIGNATURE']
    event = nil

    begin
      event = Stripe::Webhook.construct_event(
        payload, sig_header, webhook_secret
      )
    rescue JSON::ParserError => e
      # Invalid payload
      status 400
      return
    rescue Stripe::SignatureVerificationError => e
      # Invalid signature
      puts '⚠️  Webhook signature verification failed.'
      status 400
      return
    end
  else
    data = JSON.parse(payload, symbolize_names: true)
    event = Stripe::Event.construct_from(data)
  end
  # Get the type of webhook event sent - used to check the status of PaymentIntents.
  event_type = event['type']
  data = event['data']
  data_object = data['object']

  puts '🔔  Payment succeeded!' if event_type == 'checkout.session.completed'

  content_type 'application/json'
  {
    status: 'success'
  }.to_json
end
