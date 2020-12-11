<?php


namespace Testes\Mock;


use Mensageria\Core\Contratos\ConectorContrato;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class Conector implements ConectorContrato
{

    private $data;

    /**
     * @var AMQPMessage
     */
    public $msg;
    public $exchange;
    public $routing_key;
    public $mandatory;
    public $immediate;
    public $ticket;

    public function open($driver = 'default')
    {
        return $this;
    }

    public function close()
    {
        // TODO: Implement close() method.
    }

    public function basic_consume($queue = '',
                                  $consumer_tag = '',
                                  $no_local = false,
                                  $no_ack = false,
                                  $exclusive = false,
                                  $nowait = false,
                                  $callback = null,
                                  $ticket = null,
                                  $arguments = array()
    ) {
        $this->data['queue'] = $queue;
        $this->data['consumer_tag'] = $consumer_tag;
        $this->data['no_local'] = $no_local;
        $this->data['no_ack'] = $no_ack;
        $this->data['exclusive'] = $exclusive;
        $this->data['nowait'] = $nowait;
        $this->data['callback'] = $callback;
        $this->data['ticket'] = $ticket;
        $this->data['arguments'] = $arguments;

        $callback && $callback($this->data);
    }

    public function basic_publish(
        $msg,
        $exchange = '',
        $routing_key = '',
        $mandatory = false,
        $immediate = false,
        $ticket = null
    ) {
        $this->msg = $msg;
        $this->exchange = $exchange;
        $this->routing_key = $routing_key;
        $this->mandatory = $mandatory;
        $this->immediate = $immediate;
        $this->ticket = $ticket;
    }

    public function wait() {
        throw new \Exception("Chegou ao final", 200);
    }
}
