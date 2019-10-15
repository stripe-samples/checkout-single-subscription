<?php

require_once 'shared.php';

echo json_encode(['publicKey' => $config['stripe_publishable_key'], 'basicPlan' => $config['basic_plan_id'], 'proPlan' => $config['pro_plan_id']]);
