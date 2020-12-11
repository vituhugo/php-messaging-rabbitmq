<?php


namespace Mensageria\Core\Contratos;


interface MensagemTradutorContrato
{

    /**
     * @param $request
     */
    public static function traduzirMensagem($request);

}
