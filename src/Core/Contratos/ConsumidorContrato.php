<?php


namespace Mensageria\Core\Contratos;


interface ConsumidorContrato
{
    public function consumir($callable, $consumer_name);
}
