<?php

namespace Mensageria\Conector;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class Conector implements ConnectorInterface
{
    protected static $instance;
    /**
     * @var array
     */
    protected $config;

    /**
     * @var AMQPStreamConnection
     */
    protected $conector;

    protected function __construct(array $config)
    {
        $this->config = $config;
    }

    public static function getInstance(array $config) {
        if (!self::$instance) self::$instance = new self($config);

        return self::$instance;
    }

    public function open()
    {
        $config = $this->config['default'];
        $this->conector = new AMQPStreamConnection($config['host'], $config['port'], $config['user'], $config['password'], $config['vhost']);
    }

    /**
     * @return \PhpAmqpLib\Channel\AMQPChannel
     */
    public function getChannel()
    {
        return $this->conector->channel();
    }

    public function close()
    {
        $this->conector->close();
    }
}