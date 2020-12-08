<?php

namespace Mensageria;

use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class Ouvinte
{
    public function ouvir() {
        $channel = $this->getConnection()->channel();
        $this->info('Escutando a fila: '. config('queue.connections.rabbitmq.name'));

        $routes = require base_path('routes/routing_keys.php');
        $callback = function (AMQPMessage $req) use ($routes) {

            if (!isset($routes[$req->getRoutingKey()])) {
                Log::debug("Mensagem não entregue, rota não encontrada", ['routing_key' => $req->getRoutingKey()]);
                $req->ack();
                return;
            }

            [$className, $action] = $routes[$req->getRoutingKey()];

            try {

                if (!class_exists($className)) {
                    throw new \Exception('A rota não encontrou a classe. ('. $className. ')');
                }

                if (!is_callable(array($className, $action))) {
                    throw new \Exception("Action $action não foi encontrada no controller $className");
                }

                $data = $req->body;
                $properties = $req->get_properties();
                if (isset($properties['content_type']) && $properties['content_type'] === 'application/json') {
                    $data = json_decode($req->body);
                    if (empty($data) && $req->body_size > 0) {
                        throw new \Exception('Json está em formato inválido ('. $req->body. ')');
                    }
                }

                $response = (new $className())->$action($data);
                if (!is_array($response)) $response = [$response];
                array_map(array('Mail', 'send'), $response);
                $req->ack();
            } catch (\Exception $exception) {
                report($exception);
                $req->get('application_headers');
                $req->set('application_headers', new AMQPTable([
                    'x-redelivered-count' => ''
                ]));
                $req->reject();
            }
        };

        $channel->basic_consume(config('queue.connections.rabbitmq.name'), '', false, false, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }
}