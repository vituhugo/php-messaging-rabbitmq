<?php


namespace Mensageria\Core;

use Exception;
use Mensageria\Core\Contratos\ConsumidorContrato;
use Mensageria\Core\Roteador\Roteador;
use PhpAmqpLib\Message\AMQPMessage;

class Consumidor
{
    /**
     * @var ConsumidorContrato
     */
    private $adaptador;
    /**
     * @var Config
     */
    private $config;

    /**
     * @var callable
     */
    private $callback;
    /**
     * @var Roteador
     */
    protected $roteador;
    /**
     * @var callable
     */
    private $errorHandler;

    public function __construct(ConsumidorContrato $adaptador, Config $config, Roteador $roteador = null)
    {
        $this->roteador = $roteador;
        $this->adaptador = $adaptador;
        $this->config = $config;
    }

    /**
     * @param callable $afterRoute
     * @param callable $errorHandler
     * @param string $consumer_name
     */
    public function consumir($afterRoute = null, $errorHandler = null, $consumer_name = 'default') {
        $this->callback = $afterRoute;
        $this->errorHandler = $errorHandler;
        $this->adaptador->consumir(array($this, 'naMensagem'), $consumer_name ?: 'default');
    }

    /**
     * @param AMQPMessage $mensagem
     * @throws Exception
     */
    public function naMensagem(AMQPMessage $mensagem) {
        try {
            $resposta = $this->roteador
                ? $this->roteador->resolver($mensagem->get('routing_key'), $mensagem)
                : $mensagem;

            $this->callback && call_user_func($this->callback, $resposta);
            $mensagem->delivery_info['channel']->basic_ack($mensagem->delivery_info['delivery_tag']);
        } catch (Exception $exception) {
            $this->errorHandler && call_user_func($this->errorHandler, $exception);
            if ($this->config->get('consumer.stop_on_error')) throw $exception;
        }
    }
}
