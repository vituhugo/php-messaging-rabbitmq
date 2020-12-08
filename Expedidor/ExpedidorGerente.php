<?php

namespace Mensageria\Expedidor;

use Mensageria\Mensagem;

class ExpedidorGerente
{
    protected static $instance;
    /**
     * @var ExpedidorContrato
     */
    protected static $expedidorRegistrado;
    /**
     * @var ExpedidorContrato
     */
    protected $expedidor;

    protected function __construct(ExpedidorContrato $expedidor) {
        $this->expedidor = $expedidor;
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self(self::$expedidorRegistrado);
        }
        return self::$instance;
    }

    public function despachar(Mensagem $msg)
    {
        $this->expedidor->despachar($msg);
    }

    public static function registrarExpedidor(ExpedidorContrato $expedidor) {
        self::$expedidorRegistrado = $expedidor;
    }
}