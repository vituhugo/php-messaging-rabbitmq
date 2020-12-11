<?php


namespace Mensageria\Fachadas;

use Mensageria\Core\Roteador\Roteador as RouterAdaptor;
class Roteador
{
    /**
     * @var Roteador
     */
    protected static $instance;

    /**
     * @var RouterAdaptor
     */
    public $router;

    public function __construct(RouterAdaptor $router)
    {
        $this->router = $router;
    }

    public function getRoteadorReal() {
        return $this->router;
    }

    public static function getInstance() {
        if (!self::$instance) self::$instance = new self(new RouterAdaptor());
        return self::$instance;
    }

    public static function ligar($key, $controladorOuAction) {
        self::getInstance()->router->ligar($key, $controladorOuAction);
    }

    public static function resolver($routingKey, $parameters = array()) {
        return self::getInstance()->router->resolver($routingKey, $parameters);
    }
}
