<?php

return [
    'kafka_enabled' => false,
    'kafka_brokers' => 'localhost:9092',
    'kafka_device_fields' => '',
    'kafka_device_groups' => '',
    'kafka_device_measurements' => '',
    'kafka_topic' => 'librenms',
    'kafka_idempotency' => false,
    'kafka_ssl_enabled' => false,
    'kafka_ssl_keystore_location' => '',
    'kafka_ssl_keystore_password' => '',
    'kafka_batch_size' => 25,
    'kafka_buffer_max_size' => 100_000,
    'kafka_flush_timeout' => 50,
    'kafka_linger' => 500,
    'kafka_required_acks' => -1,
    'kafka_debug_enabled' => false,
    'kafka_security_debug' => '',
];
