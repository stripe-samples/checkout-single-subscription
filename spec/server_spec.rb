require './spec_helper.rb'

RSpec.describe 'server APIs' do
  it 'works as expected' do
    # loads index page
    resp = get('/')
    expect(resp).not_to be_nil

    resp = get_json('/setup')
    expect(resp).to have_key('publishableKey')
    expect(resp).to have_key('basicPrice')
    expect(resp).to have_key('proPrice')

    # Without price
    resp, status = post_json('/create-checkout-session', {
      priceId: ''
    })
    expect(status).to eq(400)
    expect(resp).to have_key('error')

    # With valid price
    resp, status = post_json('/create-checkout-session', {
      priceId: 'price_1HKBC8CZ6qsJgndJJX88EC5l'
    })
    expect(status).to eq(200)
    expect(resp).to have_key('sessionId')

    resp2 = get_json("/checkout-session?sessionId=#{resp['sessionId']}")
    expect(status).to eq(200)
    expect(resp2).to have_key('id')
    expect(resp2['id']).to eq(resp['sessionId'])
  end
end
