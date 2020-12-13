<?php


namespace Testes\Mock\Adaptadores;


use Mensageria\Core\Contratos\ConsumidorContrato;
use Mensageria\Core\Contratos\PublicadorContrato;
use Mensageria\Eventos\Publicavel;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQAdaptador implements ConsumidorContrato, PublicadorContrato
{

    /**
     * @var AMQPMessage
     */
    protected static $msg;

    public static function registrar(AMQPMessage $msg)
    {
        self::$msg = $msg;
    }

    public function consumir(\Closure $callable, $consumer_name = null)
    {
        $msg = self::$msg ?: new AMQPMessage();
        $callable && $callable($msg);
    }

    public function publicar(Publicavel $msg, $publisher_name = null) {}
}
