<?php

namespace Mensageria\Core\Contratos;


use PhpAmqpLib\Channel\AMQPChannel;

interface ConectorContrato
{
    public function open($connection_name);

    public function close();
}
