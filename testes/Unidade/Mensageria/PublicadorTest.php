<?php


namespace Unidade\Mensageria;


use Mensageria\Eventos\Publicavel;
use Testes\TestCase;

class PublicadorTest extends TestCase
{
    public function testPublicar() {
        $this->mensageria->publicar(new Publicavel(array("Nova Mensagem")));
    }
}
