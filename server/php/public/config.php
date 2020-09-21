<?php

require_once 'shared.php';

echo json_encode(['publishableKey' => $config['stripe_publishable_key'], 'basicPrice' => $config['basic_price_id'], 'proPrice' => $config['pro_price_id']]);
