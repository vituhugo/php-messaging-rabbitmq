<?php

namespace Mensageria\Expedidor;


use Mensageria\Mensagem;

interface ExpedidorContrato
{
    public function despachar(Mensagem $msg);
}