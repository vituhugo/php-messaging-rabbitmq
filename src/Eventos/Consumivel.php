<?php

namespace Mensageria\Eventos;


use Mensageria\Core\Mensagem;
use Mensageria\Excessoes\ErroDeDecodificacaoJSON;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class Consumivel extends Mensagem
{
    /**
     * @var mixed $bodyRaw
     */
    protected $bodyRaw;
    /**
     * @var array $propriedades
     */
    protected array $propriedades;

    /**
     * @var AMQPTable $cabecalhos
     */
    protected $cabecalhos;

    /**
     * @var string $routingKey
     */
    protected $routingKey;

    /**
     * @var AMQPMessage
     */
    protected $mensagem;

    public function __construct($body, $routingKey)
    {
        parent::__construct($body);
        $this->bodyRaw = $body;
        $this->routingKey = $routingKey;
    }

    public function setMensagemOriginal(AMQPMessage $mensagem)
    {
        $this->mensagem = $mensagem;
    }

    /**
     * @param mixed $propriedades
     */
    public function setProperties($propriedades): void
    {
        $this->propriedades = $propriedades;
    }

    /**
     * @param mixed $cabecalhos
     */
    public function setHeaders($cabecalhos): void
    {
        $this->cabecalhos = $cabecalhos;
    }

    /**
     * @return mixed
     * @throws ErroDeDecodificacaoJSON
     */
    public function getBody() {
        $this->body = $this->bodyRaw;
        if ($this->contentTypeIgualA('application/json')) {
            $this->body = json_decode($this->bodyRaw, 1);
            if (empty($body) && !empty($this->bodyRaw)) throw new ErroDeDecodificacaoJSON($this->bodyRaw);
        }
        return $this->body;
    }

    public function resolvida() {
        $this->mensagem->ack();
    }

    public function rejeitada() {
        $this->mensagem->reject();
    }
}
