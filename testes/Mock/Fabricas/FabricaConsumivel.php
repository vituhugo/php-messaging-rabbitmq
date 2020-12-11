<?php


namespace Mock\Fabricas;


use Mensageria\Eventos\Consumivel;

class FabricaConsumivel
{
    public static function fabricarComAMQPMessage($data) {
        $body = isset($data['body']) ? $data['body'] : null;
        $routing_key = $data['routing_key'];

        $req = new Consumivel($body, $routing_key);
        $req->setProperties($data['properties'] ?: array());
        $req->setHeaders($data['application_headers']);
        return $req;
    }
}
