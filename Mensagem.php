<?php namespace Mensageria;



use Mensageria\Comportamentos\JsonSerializable;
use PhpAmqpLib\Wire\AMQPTable;

abstract class Mensagem implements JsonSerializable
{
//    /**
//     * @var Config
//     */
//    protected static $config;

    protected $transferObject = '';
    protected $routingKey = '';
    protected $exchange = '';
    protected $properties;
    protected $headers;
    protected $body;

    public function __construct($body, $properties = array())
    {
        if ($this->transferObject) $body = new $this->transferObject($body);
        $this->body = $body;
        $this->configProperties($properties);
        $this->setHeaders();
    }

    protected function configProperties($newProperties) {
//        $properties['expiration'] = self::$config->get('expire');
//        $properties['timestamps'] = time();
//        $properties['expiration'] += $properties['timestamps'];
//        $this->properties = $properties + $newProperties;
    }

    protected function setHeaders() {
        $this->headers = new AMQPTable(array(
            'x-redelivered-count' => 0
        ));
    }

    public function getContentType() {
        return $this->properties['content_type'];
    }

    public function setContentType($contentType) {
        $this->properties['content_type'] = $contentType;
    }

    public function jsonSerialize($recursiveLevel = 2) {
        if ($this->body instanceof JsonSerializable) return $this->body->jsonSerialize($recursiveLevel);
        return json_decode(json_encode($this->body), 1);
    }

    public function getRoutingKey() {
        if (!$this->routingKey) $this->routingKey = $this->magicRoutingKey();
        return $this->routingKey;
    }

    private function magicRoutingKey() {
        $pedacos = explode("\\", get_class($this));
        $name = str_replace("Mensagem", "", $pedacos[count($pedacos)-1]);
        return ltrim(strtolower(preg_replace('/[A-Z]/', '.$0', lcfirst($name) )), '.');
    }

    public function getExchange() {
        if (!$this->exchange) return 'mercantil'; //TODO implementar config
        return $this->exchange;
    }
}