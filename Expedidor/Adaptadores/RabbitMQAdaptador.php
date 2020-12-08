<?php

namespace Mensageria\Expedidor\Adaptadores;

use Mensageria\Conector\ConnectorInterface;
use Mensageria\Expedidor\ExpedidorContrato;
use Mensageria\Mensagem;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQAdaptador implements ExpedidorContrato
{
    protected $conector;

    public function __construct(ConnectorInterface $conector)
    {
        $this->conector = $conector;
    }

    public function despachar(Mensagem $msg)
    {
        $this->conector->open();
        $channel = $this->conector->getChannel();

        $channel->basic_publish(
            new AMQPMessage(json_encode($msg->jsonSerialize()), array('content_type' => 'application/json')),
            $msg->getExchange(),
            $msg->getRoutingKey()
        );
    }
}