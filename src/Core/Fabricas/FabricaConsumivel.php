<?php


namespace Mensageria\Core\Fabricas;


use Mensageria\Eventos\Consumivel;
use PhpAmqpLib\Message\AMQPMessage;

class FabricaConsumivel
{
    public static function fabricarComAMQPMessage(AMQPMessage $mensagem) {
        $req = new Consumivel($mensagem->body, $mensagem->getRoutingKey());
        $req->setProperties($mensagem->get_properties());
        $req->setHeaders($mensagem->has('application_headers') ? $mensagem->get('application_headers') : array());

        return $req;
    }
}
