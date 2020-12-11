<?php


namespace Testes\Unidade\Adaptadores;


use Mensageria\Adaptadores\RabbitMQAdaptador;
use Mensageria\Core\Config;
use Mensageria\Eventos\Publicavel;
use Testes\Mock\Conector;

class RabbitMQAdaptadorTest extends TestCase
{
    /**
     * @var RabbitMQAdaptador
     */
    private $adaptador;
    /**
     * @var Conector
     */
    private $conector;
    /**
     * @var Config
     */
    private $config;

    protected function setUp()
    {
        $this->conector = new Conector();
        $this->config = new Config(array(
            'consumers' => array(
                'default' => array(
                    'stop_on_error' => true,
                    'queue_name' => 'default',
                    'tag' => '',
                    'no_local' => false,
                    'exclusive' => false,
                    'connection' => 'default',
                )
            ),
            'publishers' => array(
                'default' => array(
                    'exchange' => 'exchange_teste'
                )
            ),
            'connections' => array(
                'default' => array(
                    'host' => 'rabbitmq',
                    'port' => 5672,
                    'user' => 'guest',
                    'password' => 'guest',
                    'vhost' => '/',
                )
            )
        ));
        $this->adaptador = new RabbitMQAdaptador($this->conector, $this->config);
    }

    public function testPublicar() {
        $publicavel = new Publicavel(array("MENSAGEM TESTE!"));
        $publicavel->setRoutingKey("teste.key");
        $this->adaptador->publicar($publicavel, 'default');

        $this->assertEquals("[\"MENSAGEM TESTE!\"]", $this->conector->msg->getBody());
        $this->assertEquals('exchange_teste', $this->conector->exchange);
        $this->assertEquals('teste.key', $this->conector->routing_key);

        $properties = $this->conector->msg->get_properties();
        $this->assertEquals('application/json', $properties['content_type']);
    }

    public function testConsumir() {
        $thow = false;
        try {
        $this->adaptador->consumir(function ($mensagem) {

        }, 'default');
        } catch (\Exception $e) {
            $thow = true;
        }
        $this->assertTrue($thow, 'ErrorHandler não está funcionando.');
    }
}
