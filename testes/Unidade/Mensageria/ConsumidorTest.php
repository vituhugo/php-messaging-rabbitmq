<?php


namespace Unidade\Mensageria;


use Mensageria\Core\Config;
use Mensageria\Core\Consumidor;
use Mensageria\Fachadas\Roteador;
use Testes\Mock\Adaptadores\RabbitMQAdaptador;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use Testes\Mock\AMQP\ChannelStream;

class ConsumidorTest extends TestCase
{
    /**
     * @var bool
     */
    private $entrouNoRoteador;

    protected function setUp()
    {
        $mensagem_para_disparo = new AMQPMessage('{}', array('content_type' => 'application/json'));
        $mensagem_para_disparo->delivery_info = array('routing_key' => 'set.up', 'delivery_tag' => '', 'exchange' => 'mercantil', 'channel' => new ChannelStream());
        RabbitMQAdaptador::registrar($mensagem_para_disparo);
    }

    public function testConsumidorAceitaMensagem() {
        // TODO
    }

    public function testConsumidorRejeitaMensagem() {
        // TODO
    }


    public function testConsumidorComControlador() {
        // TODO
    }

    public function testConsumidorComClosure() {
        // TODO
    }

    public function testConsumeCallback() {
        $this->registrarMensagem('qualquer.rota');

        $respostaControlador = 'deu certo';

        $entrouNaAction = false;

        Roteador::ligar('qualquer.rota', function($req) use ($respostaControlador, &$entrouNaAction) {
            $entrouNaAction = true;
            return $respostaControlador;
        });

        $adaptor = new RabbitMQAdaptador();
        $consumidor = new Consumidor($adaptor, new Config(), Roteador::getInstance()->getRoteadorReal());

        $callbackResponse = false;
        $consumidor->consumir(function($response) use (&$callbackResponse) {
            $callbackResponse = $response;
        }, function($ex) { throw $ex; });

        $this->assertTrue($entrouNaAction, 'Não entrou na action');
        $this->assertTrue( !!$callbackResponse,'Callback não foi acionado .');
        $this->assertEquals( $callbackResponse, $respostaControlador, 'Resposta do roteador diferente da esperada.');
    }

    private function registrarMensagem($routingKey, $body = "{}") {
        $mensagem_para_disparo = new AMQPMessage($body, array('content_type' => 'application/json'));
        $mensagem_para_disparo->delivery_info = array('routing_key' => $routingKey, 'delivery_tag' => '', 'exchange' => 'mercantil', 'channel' => new ChannelStream());
        RabbitMQAdaptador::registrar($mensagem_para_disparo);
    }
}
