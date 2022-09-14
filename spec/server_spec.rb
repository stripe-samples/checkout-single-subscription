require 'dotenv'
Dotenv.load

RSpec.describe 'server APIs' do
  describe '/config' do
    it 'returns expected values' do
      # loads index page
      resp = get('/')
      expect(resp).not_to be_nil

      config = get_json('/config')
      expect(config).to have_key('publishableKey')
      expect(config).to have_key('basicPrice')
      expect(config).to have_key('proPrice')
    end
  end

  describe '/create-checkout-session' do
    it 'fails with 400 without a price ID' do
      # Without price
      begin
        response = RestClient.post(
          "#{SERVER_URL}/create-checkout-session",
          {priceId: ''},
          {max_redirects: 0}
        )
      rescue => e
        expect(e.http_code).to eq(400), e.to_s
      end
    end

    it 'returns a redirect with a valid price ID' do
      config = get_json('/config')

      # With valid price
      response = RestClient.post(
        "#{SERVER_URL}/create-checkout-session",
        {priceId: config['basicPrice']},
        {max_redirects: 0}
      )
      # RestClient will follow the redirect, but we can get the first response
      # from the server from the `history`.
      redirect_response = response.history.first

      # Asserts the right HTTP status code for the redirect
      expect(redirect_response.code).to eq(303)

      # Pull's the Checkout session ID out of the Location header
      # to assert the right configuration on the created session.
      redirect_url = redirect_response.headers[:location]
      expect(redirect_url).to start_with("https://checkout.stripe.com/c/pay/cs_test")
      match = redirect_url.match(".*(?<session_id>cs_test.*)#.*")
      session_id = match[:session_id]
      session = Stripe::Checkout::Session.retrieve({
        id: session_id,
        expand: ['line_items']
      })
      expect(session.line_items.first.price.id).to eq(config['basicPrice'])
    end
  end

  describe '/checkout-session' do
    it 'fetches the checkout session' do
      # When using the running app, the completed Checkout session will have a customer,
      # but for testing we need to create a session with a customer attached
      session_with_customer = Stripe::Checkout::Session.create(
        success_url: ENV['DOMAIN'] + '/success.html?session_id={CHECKOUT_SESSION_ID}',
        cancel_url: ENV['DOMAIN'] + '/canceled.html',
        mode: 'subscription',
        line_items: [{
          quantity: 7,
          price: ENV['BASIC_PRICE_ID'],
        }],
      )
      resp2 = get_json("/checkout-session?sessionId=#{session_with_customer.id}")
      expect(resp2).to have_key('id')
    end
  end

  describe '/customer-portal' do
    it 'creates a customer portal session' do
      customer = Stripe::Customer.list(limit: 1).data.first
      expect(customer).not_to be_nil

      # When using the running app, the completed Checkout session will have a customer,
      # but for testing we need to create a session with a customer attached
      session_with_customer = Stripe::Checkout::Session.create(
        customer: customer['id'],
        success_url: "#{ENV['DOMAIN']}/success.html?session_id={CHECKOUT_SESSION_ID}",
        cancel_url: "#{ENV['DOMAIN']}/canceled.html",
        mode: 'subscription',
        line_items: [{
          quantity: 1,
          price: ENV['BASIC_PRICE_ID'],
        }],
      )

      # With valid price
      response = RestClient.post(
        "#{SERVER_URL}/customer-portal",
        {sessionId: session_with_customer.id},
        {max_redirects: 0}
      )
      # RestClient will follow the redirect, but we can get the first response
      # from the server from the `history`.
      redirect_response = response.history.first

      # Asserts the right HTTP status code for the redirect
      expect(redirect_response.code).to eq(303)

      # Pull's the Checkout session ID out of the Location header
      # to assert the right configuration on the created session.
      redirect_url = redirect_response.headers[:location]
      expect(redirect_url).to start_with("https://billing.stripe.com/p/session")
    end
  end
end
