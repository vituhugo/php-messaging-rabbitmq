<?php
namespace Mensageria\Adaptadores;

use Mensageria\Core\Conector\RabbitMQConector as Conector;
use Mensageria\Core\Config;
use Mensageria\Core\Contratos\ConectorContrato;
use Mensageria\Core\Contratos\ConsumidorContrato;
use Mensageria\Core\Contratos\PublicadorContrato;
use Mensageria\Core\Fabricas\FabricaConsumivel;
use Mensageria\Eventos\Publicavel;
use Mensageria\Excessoes\ObjetoDeConsumoInvalido;
use PhpAmqpLib\Message\AMQPMessage;
use Mensageria\Core\Contratos\MensagemTradutorContrato;

class RabbitMQAdaptador implements ConsumidorContrato, PublicadorContrato, MensagemTradutorContrato
{
    /**
     * @var Conector
     */
    private $conector;
    /**
     * @var Config
     */
    private $config;

    public function __construct(ConectorContrato $conector, Config $config)
    {
        $this->conector = $conector;
        $this->config = $config;
    }

    /**
     * @param callable $callback
     * @param string $consumer_name
     */
    public function consumir($callback, $consumer_name)
    {
        $config_customer = $this->config->get("consumers.$consumer_name");
        $canal = $this->conector->open($config_customer['connection']);

        $canal->queue_declare($config_customer['queue_name'], false, true, $config_customer['exclusive'], false);

        $canal->basic_consume(
            $config_customer['queue_name'],
            $config_customer['tag'],
            $config_customer['no_local'],
            false,
            $config_customer['exclusive'],
            false,
            $callback);

        while (true) {
            $canal->wait();
        }
    }


    /**
     * @param Publicavel $msg
     * @param $publisher_name
     */
    public function publicar(Publicavel $msg, $publisher_name)
    {
        $config_publisher = $this->config->get("publishers.$publisher_name");
        $canal = $this->conector->open($config_publisher['connection']);

        $body = json_encode($msg->jsonSerialize());
        $properties = ($config_publisher['properties'] ?: array()) + $msg->getProperties();
        $canal->basic_publish(
            new AMQPMessage($body, $properties),
            $msg->getExchange() ?: $config_publisher['exchange'],
            $msg->getRoutingKey() ?: $config_publisher['routing_key']
        );
    }

    /**
     * @param $request
     * @return \Mensageria\Eventos\Consumivel
     * @throws ObjetoDeConsumoInvalido
     */
    public static function traduzirMensagem($request)
    {
        if (!$request instanceof AMQPMessage) throw new ObjetoDeConsumoInvalido("Não é um AMQPMessage");
        return FabricaConsumivel::fabricarComAMQPMessage($request);
    }
}
