<?php


namespace Mensageria\Core\Roteador;


class Combinador
{
    protected $padrao;
    protected $controlador;
    protected $action;
    protected $routingKey;
    protected $parametros;
    protected $isClosure = false;

    /**
     * Ligador constructor.
     * @param $padrao
     * @param $controlador
     * @param $action
     */
    public function __construct($padrao, $controlador, $action, $parametros)
    {
        $this->padrao = $padrao;
        $this->controlador = $controlador;
        $this->action = $action;
        $this->parametros = $parametros;
    }


    public function combine($routingKey)
    {
        $match = preg_match($this->padrao, $routingKey);
        if ($match) $this->routingKey = $routingKey;
        return $match;
    }

    /**
     * @return mixed
     */
    public function getRoutingKey()
    {
        return $this->routingKey;
    }

    public function getAction()
    {
        if ($this->isClosure()) return $this->action;
        $action = $this->action;
        return is_callable($action) ? $action($this->routingKey) : $action;
    }

    public function getControlador()
    {
        return $this->controlador;
    }

    public function getParametros()
    {
        $parametros = $this->parametros;
        return is_callable($parametros) ? $parametros($this->routingKey) : $parametros;
    }

    public function setIsClosure($isClosure)
    {
        $this->isClosure = $isClosure;
    }

    public function isClosure()
    {
        return $this->isClosure;
    }
}
