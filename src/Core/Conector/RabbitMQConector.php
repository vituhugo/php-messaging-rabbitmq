<?php

namespace Mensageria\Core\Conector;

use Mensageria\Core\Config;
use Mensageria\Core\Contratos\ConectorContrato;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQConector implements ConectorContrato
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var AMQPStreamConnection
     */
    protected $conector;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function getAMQPConnector() {
        return $this->conector;
    }

    public function open($connection_name)
    {
        if ($connection_name || !$this->conector) $this->carregarConector($connection_name ?: 'default');
        return $this->conector->channel();
    }

    private function carregarConector($connection_name) {
        if ($connection_name instanceof AMQPStreamConnection) {
            $this->conector = $connection_name;
            return;
        }

        $config = $this->config->get("connections.$connection_name");
        $this->conector = new AMQPStreamConnection($config['host'], $config['port'], $config['user'], $config['password'], $config['vhost']);
    }

    public function close()
    {
        $this->conector->channel()->close();
        $this->conector->close();
    }
}
