<?php


namespace Mensageria\Core\Roteador;


use Mensageria\Core\Config;
use Mensageria\Core\Contratos\ConsumidorContrato;
use Mensageria\Core\Contratos\MensagemTradutorContrato;
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
    /**
     * @var Config|null
     */
    private $config;

    public function __construct(ConsumidorContrato $adaptador = null, Config $config = null) {
        $this->rotas = new Mapeador;
        $this->adaptador = $adaptador;
        $this->config = $config;
    }

    public function setAdaptador(ConsumidorContrato $adaptador) {
        $this->adaptador = $adaptador;
    }

    public function ligar($key, $controladorOuAction) {
        $this->rotas->add($key, $controladorOuAction);
    }

    public function resolver($routingKey, $resposta) {
        $combinador = $this->rotas->buscarOuFalhar($routingKey);

        if ($this->adaptador && $this->adaptador instanceof MensagemTradutorContrato) {
            $resposta = call_user_func(array($this->adaptador, 'traduzirMensagem'), $resposta);
        }

        $action = $combinador->getAction();
        $controller = $combinador->getControlador();
        $parametros = array($resposta) + $combinador->getParametros();

        $controller = $controller ? $this->config->get('roteador.controller_namespace').$controller : $controller;

        return call_user_func_array($controller ? array(new $controller(), $action) : $action, $parametros);
    }

    /**
     * @param AMQPMessage $message
     */
    public function conversorMensagem(AMQPMessage $message) {
        return FabricaConsumivel::fabricarComAMQPMessage($message);
    }

    public function setConfig(Config $config)
    {
        $this->config = $config;
        return $this;
    }
}
