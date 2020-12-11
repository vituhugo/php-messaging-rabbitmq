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
                )
            )
        )
    ),
);
