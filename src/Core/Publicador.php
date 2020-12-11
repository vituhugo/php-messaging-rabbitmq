<?php


namespace Mensageria\Core;


use Mensageria\Core\Contratos\PublicadorContrato;
use Mensageria\Eventos\Publicavel;

class Publicador
{
    /**
     * @var PublicadorContrato
     */
    private $adaptador;

    /**
     * @var Config
     */
    private $config;

    public function __construct(PublicadorContrato $adaptador, Config $config)
    {
        $this->adaptador = $adaptador;
        $this->config = $config;
    }

    public function publicar(Publicavel $disparo, $publisher_name = 'default') {
        $this->adaptador->publicar($disparo, $publisher_name ?: 'default');
    }
}
