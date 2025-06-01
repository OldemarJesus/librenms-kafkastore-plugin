<?php

return [
    'kafka_enabled' => true,
    'kafka_brokers' => 'kafka:9092',
    'kafka_device_fields' => 'inserted,overwrite_ip,community,authlevel,authname,authpass,authalgo,cryptopass,cryptoalgo,snmpver,port,transport,timeout,retries,sysContact,version,hardware,features,location_id,os,ignore,agent_uptime,purpose,serial,icon,max_depth,disable_notify,ignore_status,attribs',
    'kafka_device_groups' => '',
    'kafka_device_measurements' => 'icmp-perf,poller-perf',
    'kafka_topic' => 'librenms',
    'kafka_idempotency' => false,
    'kafka_ssl_enabled' => false,
    'kafka_ssl_keystore_location' => '',
    'kafka_ssl_keystore_password' => '',
    'kafka_batch_size' => 25,
    'kafka_buffer_max_size' => 100_000,
    'kafka_flush_timeout' => 50,
    'kafka_linger' => 500,
    'kafka_required_acks' => 0,
    'kafka_debug_enabled' => true,
    'kafka_security_debug' => '',
];
