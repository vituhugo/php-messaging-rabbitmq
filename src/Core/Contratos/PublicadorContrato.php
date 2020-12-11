<?php

namespace Mensageria\Core\Contratos;


use Mensageria\Core\Config;
use Mensageria\Eventos\Publicavel;

interface PublicadorContrato
{
    public function publicar(Publicavel $msg, $connection_name);
}
