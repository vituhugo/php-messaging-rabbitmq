<?php


namespace Mensageria;


use Mensageria\Adaptadores\RabbitMQAdaptador;
use Mensageria\Core\Conector\RabbitMQConector;
use Mensageria\Core\Config;
use Mensageria\Core\Consumidor;
use Mensageria\Core\Contratos\ConsumidorContrato;
use Mensageria\Core\Contratos\PublicadorContrato;
use Mensageria\Core\Publicador;
use Mensageria\Core\Roteador\Roteador;
use Mensageria\Eventos\Publicavel;
use Mensageria\Excessoes\AdaptadorNaoEncontrado;

class Mensageria
{
    /**
     * @var Config
     */
    protected $config;

    protected $instances;
    /**
     * @var Consumidor
     */
    private $consumidor;
    /**
     * @var Publicador
     */
    private $publicador;
    /**
     * @var Roteador
     */
    protected $roteador;

    protected $driver;
    /**
     * @var ConsumidorContrato|null
     */
    private $consumidorAdaptador;
    /**
     * @var PublicadorContrato|null
     */
    private $publicadorAdaptador;

    /**
     * Mensageria constructor.
     *
     * @param Config $config
     * @param ConsumidorContrato $consumidorAdaptador
     * @param PublicadorContrato $publicadorAdaptador
     * @throws AdaptadorNaoEncontrado
     */
    public function __construct(Config $config = null, ConsumidorContrato $consumidorAdaptador = null, PublicadorContrato $publicadorAdaptador = null)
    {
        $configuracao_padrao = new Config(require __DIR__.'/config.php');

        $this->config = $configuracao_padrao->merge($config ?: array());
        $this->driver = $this->config->get('driver');
        $this->consumidorAdaptador = $consumidorAdaptador;
        $this->publicadorAdaptador = $publicadorAdaptador;
    }

    public function habilitarRoteador(Roteador $roteador) {
        $this->roteador = $roteador;
    }

    protected function getConsumidor() {
        if (!$this->consumidor) $this->carregarConsumidor();
        return $this->consumidor;
    }

    protected function getPublicador() {
        if (!$this->publicador) $this->carregarPublicador();
        return $this->publicador;
    }

    /**
     * @param callable $afterRoute
     * @param $errorHandler
     * @param null $constumer_name
     */
    public function consumir(callable $afterRoute, callable $errorHandler = null, $constumer_name = null) {
        $this->getConsumidor()->consumir($afterRoute, $errorHandler, $constumer_name);
    }

    /**
     * @param Publicavel $publicavel
     * @param null $publisher_name
     */
    public function publicar(Publicavel $publicavel, $publisher_name = null) {
        $this->getPublicador()->publicar($publicavel, $publisher_name);
    }

    private function carregarConsumidor()
    {
        $consumidor = new Consumidor(
            $this->getConsumidorAdaptador(),
            $this->config->get("drivers.{$this->driver}.consumers"),
            $this->roteador
        );

        $this->consumidor = $consumidor;
    }

    private function carregarPublicador()
    {
        $publicador = new Publicador(
            $this->getPublicadorAdaptador(),
            $this->config->get("drivers.{$this->driver}.publishers")
        );
        $this->publicador = $publicador;
    }

    private function getPublicadorAdaptador() {
        if (!$this->publicadorAdaptador && !$this->consumidorAdaptador instanceof PublicadorContrato) {
            return $this->publicadorAdaptador = $this->criarPublicadorAdaptador();
        } if (!$this->publicadorAdaptador) {
            return $this->publicadorAdaptador = $this->consumidorAdaptador;
        }
        return $this->publicadorAdaptador;
    }

    private function getConsumidorAdaptador()
    {
        if (!$this->consumidorAdaptador && !$this->publicadorAdaptador instanceof ConsumidorContrato) {
            return $this->consumidorAdaptador = $this->criarConsumidorApdatador();
        } if (!$this->consumidorAdaptador) {
            return $this->consumidorAdaptador = $this->publicadorAdaptador;
        }
        return $this->consumidorAdaptador;
    }

    private function criarConsumidorApdatador() {
        if ($this->driver === 'rabbitmq') {
            $conector = new RabbitMQConector($this->config->get("drivers.{$this->driver}"));
            return new RabbitMQAdaptador($conector, $this->config->get("drivers.{$this->driver}"));
        }
        throw new AdaptadorNaoEncontrado("Driver não encontrado.");
    }

    private function criarPublicadorAdaptador()
    {
        if ($this->driver === 'rabbitmq') {
            $conector = new RabbitMQConector($this->config->get("drivers.{$this->driver}"));
            return new RabbitMQAdaptador($conector, $this->config->get("drivers.{$this->driver}"));
        }
        throw new AdaptadorNaoEncontrado("Driver não encontrado.");
    }
}
