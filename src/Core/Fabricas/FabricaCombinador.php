<?php


namespace Mensageria\Core\Fabricas;


use Mensageria\Core\Roteador\Combinador;

class FabricaCombinador
{
    public static function factory($key, $controllerOrAction) {
        return self::factoryByActionOrController($key, $controllerOrAction);
    }

    public static function factoryByActionOrController($key, $controllerOrAction) {
        if ($controllerOrAction instanceof \Closure) return self::newClosure($key, $controllerOrAction);
        strtolower($controllerOrAction);
        if (strstr($controllerOrAction, '@')) {
            return self::newActionWithController($key, $controllerOrAction);
        }
        return self::newController($key, $controllerOrAction);
    }

    protected static function newActionWithController($key, $controllerAction)
    {
        $routePattern = self::getRoutePattern($key);
        $parametros = self::buscadorDeParametros($key);
        list($controller, $action) = explode('@', $controllerAction);
        return new Combinador($routePattern, $controller, $action, $parametros);
    }

    protected static function newController($key, string $controller)
    {
        $routePattern = self::getRoutePattern($key);
        $parametros = self::buscadorDeParametros($key);
        $action = self::buscadorDeAction($key);

        return new Combinador($routePattern, $controller, $action, $parametros);
    }

    private static function buscadorDeParametros($key) {
        $parametersPositions = array_keys(array_filter(explode('.', $key), function($route_piece) {
            return $route_piece === '*';
        }));

        if ($parametersPositions) return array();

        return function($routingKey) use ($parametersPositions) {
            $route_in_pieces = explode('.', $routingKey);
            return array_map( function ($position) use ($route_in_pieces) {
                return $route_in_pieces[$position];
            },$parametersPositions);
        };
    }

    private static function getRoutePattern($key) {
        $key_pieces = explode(".", $key);
        $pattern_pieces = array_map(function($key_piece) {
            if (in_array($key_piece, array('*', '{action}'))) {
                return '([^\.]*)';
            }
            return $key_piece;
        },$key_pieces);

        return "/".implode('\.', $pattern_pieces)."/";
    }

    /**
     * @param $key
     * @return \Closure
     */
    protected static function buscadorDeAction($key)
    {
        $actionPosition = array_search("{action}", explode('.', $key));
        $action = function ($route) use ($actionPosition) {
            $route_in_pieces = explode('.', $route);
            return $route_in_pieces[$actionPosition];
        };
        return $action;
    }

    private static function newClosure($key, \Closure $action)
    {
        $routePattern = self::getRoutePattern($key);
        $parametros = self::buscadorDeParametros($key);

        $combinador = new Combinador($routePattern, null, $action, $parametros);
        $combinador->setIsClosure(true);
        return $combinador;
    }
}
