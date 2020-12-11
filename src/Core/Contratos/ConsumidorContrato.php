<?php


namespace Mensageria\Core\Contratos;


use Mensageria\Core\Config;

interface ConsumidorContrato
{
    public function consumir(callable $callable, $consumer_name);
}
