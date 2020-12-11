<?php


namespace Mensageria\Core\Roteador;


use Mensageria\Core\Fabricas\FabricaCombinador;
use Mensageria\Excessoes\RotaNaoEncontrada;

class Mapeador
{
    /**
     * @var Combinador[]
     */
    protected $map = array();

    /**
     * Mapeador constructor.
     * @param array $binds
     */
    public function __construct(array $binds = array())
    {
        foreach ($binds as $key => $controllerOrAction) {
            $this->map[] = FabricaCombinador::factory($key, $controllerOrAction);
        }
    }

    public function add($key, $controllerOrAction) {
        $this->map[] = FabricaCombinador::factory($key, $controllerOrAction);
    }

    public function getMap() {
        return $this->map;
    }

    /**
     * @param $routingKey
     * @return bool|Combinador
     */
    public function buscar($routingKey)
    {
        foreach($this->map as $pos => $item) {
            if ($item->combine($routingKey)) return $item;
        }
        return false;
    }

    public function buscarOuFalhar($routingKey) {
        $combinador = $this->buscar($routingKey);
        if (false === $combinador) throw new RotaNaoEncontrada($routingKey);

         return $combinador;
    }

    public function get(int $routeIndex)
    {
        return $this->map[$routeIndex];
    }
}
