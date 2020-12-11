<?php namespace Mensageria\Core;



use Mensageria\Comportamentos\JsonSerializable;
use PhpAmqpLib\Wire\AMQPTable;

abstract class Mensagem
{
//    /**
//     * @var Config
//     */
//    protected static $config;

    protected $routingKey = '';
    protected $exchange = '';
    protected $properties;
    protected $headers;
    protected $bodyRaw;
    protected $body;

    public function __construct($body)
    {
        $this->bodyRaw = $body;
        $this->setHeaders();
        $this->setProperties();
    }

    protected function headers() {
        return array();
    }

    private function setProperties() {
        $this->properties = array(
            'content_type' => 'application/json'
        );
    }

    private function setHeaders() {
        $this->headers = new AMQPTable($this->headers() + array(
            'x-redelivered-count' => 0
        ));
    }

    public function getProperties() {
        return $this->properties;
    }

    public function getHeaders() {
        return $this->headers;
    }

    public function getContentType() {
        return isset($this->properties['content_type']) ? $this->properties['content_type'] : null;
    }

    public function getRoutingKey() {
        return $this->routingKey;
    }

    public function getExchange() {
        return $this->exchange;
    }

    public function contentTypeIgualA($expected) {
        return $this->getContentType() === $expected;
    }

    abstract public function getBody();
}
