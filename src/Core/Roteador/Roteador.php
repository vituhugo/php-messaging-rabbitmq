<?php


namespace Mensageria\Core\Roteador;


use Mensageria\Core\Contratos\ConsumidorContrato;
use Mensageria\Core\Fabricas\FabricaConsumivel;
use PhpAmqpLib\Message\AMQPMessage;

class Roteador
{
    /**
     * @var Mapeador
     */
    protected $rotas;
    /**
     * @var ConsumidorContrato
     */
    private $adaptador;

    public function __construct(ConsumidorContrato $adaptador = null) {
        $this->rotas = new Mapeador;
        $this->adaptador = $adaptador;
    }

    public function setAdaptador(ConsumidorContrato $adaptador) {
        $this->adaptador = $adaptador;
    }

    public function ligar($key, $controladorOuAction) {
        $this->rotas->add($key, $controladorOuAction);
    }

    public function resolver($routingKey, $resposta) {
        $combinador = $this->rotas->buscarOuFalhar($routingKey);

        if ($this->adaptador && is_callable(array($this->adaptador, 'converterConsumivel'))) {
            call_user_func(array($this->adaptador, 'converterConsumivel'), $resposta);
        }

        $action = $combinador->getAction();
        $controller = $combinador->getControlador();
        $parametros = array($resposta) + $combinador->getParametros();

        return call_user_func_array($controller ? array($controller, $action) : $action, $parametros);
    }

    /**
     * @param AMQPMessage $message
     */
    public function conversorMensagem(AMQPMessage $message) {
        return FabricaConsumivel::fabricarComAMQPMessage($message);
    }
}
