<?php

return array(
    'driver' => 'rabbitmq',
    'drivers' => array(
        'rabbitmq' => array(
            'consumers' => array(
                'default' => array(
                    'stop_on_error' => true,
                    'queue_name' => 'default',
                    'tag' => '',
                    'no_local' => false,
                    'exclusive' => false,
                    'connection' => 'default',
                )
            ),
            'publishers' => array(
                'exchange' => ''
            ),
            'connections' => array(
                'default' => array(
                    'host' => 'rabbitmq',
                    'port' => 5672,
                    'user' => 'guest',
                    'password' => 'guest',
                    'vhost' => '/',

                    'insist' => false,
                    'login_method' => 'AMQPLAIN',
                    'login_response' => null,
                    'locale' => 'en_US',
                    'timeout' => 30.0,
                    'read_write_timeout' => 30.0,
                    'context' => null,
                    'keepalive' => false,
                    'heartbeat' => 15,
                )
            )
        )
    ),
);
