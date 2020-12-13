<?php


namespace Mensageria\Core\Contratos;


use Mensageria\Core\Config;

interface ConsumidorContrato
{
    public function consumir(\Closure $callable, $consumer_name);
}
