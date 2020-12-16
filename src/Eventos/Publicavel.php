<?php

namespace Mensageria\Eventos;

use Mensageria\Comportamentos\JsonSerializable;
use Mensageria\Core\Mensagem;

class Publicavel extends Mensagem implements JsonSerializable
{
    protected $transferObject = '';

    /**
     * @var mixed
     */
    protected $body;

    protected function headers() {
        return array(
            'x-redelivered-count' => 0
        );
    }

    public function setContentType($contentType) {
        $this->properties['content_type'] = $contentType;
    }

    public function jsonSerialize($recursiveLevel = 2) {
        if ($this->getBody() instanceof JsonSerializable) return $this->getBody()->jsonSerialize($recursiveLevel);
        return json_decode(json_encode($this->getBody()), 1);
    }

    public function getRoutingKey() {
        if (!$this->routingKey) $this->routingKey = $this->routingKeyMagica();
        return $this->routingKey;
    }

    public function setRoutingKey($routingKey) {
        $this->routingKey = $routingKey;
    }

    private function routingKeyMagica() {
        $pedacos = explode("\\", get_class($this));
        $name = str_replace("Mensagem", "", $pedacos[count($pedacos)-1]);
        return ltrim(strtolower(preg_replace('/[A-Z]/', '.$0', lcfirst($name) )), '.');
    }

    public function getBody() {
        if ($this->transferObject) $this->body = new $this->transferObject($this->bodyRaw);
        if (!$this->body) $this->body = $this->bodyRaw;
        return $this->body;
    }
}
