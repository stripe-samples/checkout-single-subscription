require 'dotenv'
Dotenv.load

RSpec.describe 'server APIs' do
  it 'works as expected' do
    # loads index page
    resp = get('/')
    expect(resp).not_to be_nil

    config = get_json('/setup')
    expect(config).to have_key('publishableKey')
    expect(config).to have_key('basicPrice')
    expect(config).to have_key('proPrice')

    # Without price
    resp, status = post_json('/create-checkout-session', {
      priceId: ''
    })
    expect(status).to eq(400), resp.to_s
    expect(resp).to have_key('error')

    # With valid price
    resp, status = post_json('/create-checkout-session', {
      priceId: config['basicPrice']
    })
    expect(status).to eq(200), resp.to_s
    expect(resp).to have_key('sessionId')

    resp2 = get_json("/checkout-session?sessionId=#{resp['sessionId']}")
    expect(status).to eq(200), resp2.to_s
    expect(resp2).to have_key('id')
    expect(resp2['id']).to eq(resp['sessionId'])

    customer = Stripe::Customer.list(limit: 1).data.first
    expect(customer).not_to be_nil

    # When using the running app, the completed Checkout session will have a customer,
    # but for testing we need to create a session with a customer attached
    session_with_customer = Stripe::Checkout::Session.create(
      customer: customer['id'],
      success_url: ENV['DOMAIN'] + '/success.html?session_id={CHECKOUT_SESSION_ID}',
      cancel_url: ENV['DOMAIN'] + '/canceled.html',
      payment_method_types: ['card'],
      mode: 'subscription',
      line_items: [{
        quantity: 1,
        price: ENV['BASIC_PRICE_ID'],
      }],
    )

    # Create customer portal session
    resp, status = post_json('/customer-portal', {
      sessionId: session_with_customer['id'],
    })
    expect(status).to eq(200), resp.to_s
    expect(resp).to have_key('url')
  end
end
