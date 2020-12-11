<?php


namespace Testes;

use Mensageria\Core\Config;
use Mensageria\Core\Roteador\Roteador;
use Testes\Mock\Adaptadores\RabbitMQAdaptador;
use PhpAmqpLib\Message\AMQPMessage;
use Testes\Mock\AMQP\ChannelStream;
use Testes\Mock\AMQP\Connector;

define("ROOT_TEST", __DIR__);
class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Mensageria\Mensageria
     */
    protected $mensageria;

    /**
     * @var Roteador
     */
    protected $roteador;

    public function setUp() {
        $this->roteador = new Roteador();
        $this->mensageria = new \Mensageria\Mensageria(new Config(), new RabbitMQAdaptador());
    }

    protected function registrarMensagem($routingKey, $body = "{}") {
        $mensagem_para_disparo = new AMQPMessage($body, array('content_type' => 'application/json'));
        $mensagem_para_disparo->delivery_info = array('routing_key' => $routingKey, 'delivery_tag' => '', 'exchange' => 'mercantil', 'channel' => new ChannelStream());
        RabbitMQAdaptador::registrar($mensagem_para_disparo);
    }
}
