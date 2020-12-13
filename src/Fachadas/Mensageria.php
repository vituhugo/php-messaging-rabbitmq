<?php


namespace Mensageria\Fachadas;

use Mensageria\Core\Config;
use Mensageria\Eventos\Publicavel;

/**
 * Class Mensageria
 * @package Mensageria\Fachadas
 *
 * @method static consumir(\Closure $callback = null, \Closure $handleException = null, Config $config = null)
 * @method static publicar(Publicavel $publicavel, Config $config = null)
 */
class Mensageria
{
    private static $resolveRouter;
    public $mensageria;

    protected static $resolve;
    /**
     * @var \Mensageria\Mensageria
     */
    protected static $instance;
    protected static $routerInstance;

    protected function __construct(\Mensageria\Mensageria $mensageria)
    {
        $this->mensageria = $mensageria;
    }

    /**
     * @param callable|null $resolver
     */
    public static function registrar(\Closure $resolver = null) {
        self::$resolve = function () use ($resolver) {
            return $resolver ? call_user_func($resolver) : new \Mensageria\Mensageria();
        };
    }

    public static function __callStatic($name, $arguments)
    {
        if (!self::$instance) {
            if (!self::$resolve) self::registrar();
            $mensageria = call_user_func(self::$resolve);
            self::$resolveRouter && call_user_func(self::$resolveRouter, $mensageria);
            self::$instance = new self($mensageria);
        }

        call_user_func_array(array(self::$instance->mensageria, $name), $arguments);
    }

    public static function habilitarRoteador()
    {
        self::$resolveRouter = function(\Mensageria\Mensageria $mensageria) {
            $r = Roteador::getInstance()->getRoteadorReal();
            $r->setConfig($mensageria->getConfig());
            $r->setAdaptador($mensageria->getConsumidorAdaptador());
            $mensageria->habilitarRoteador($r);
        };
    }
}
