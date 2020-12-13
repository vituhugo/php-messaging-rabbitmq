<?php


namespace Mensageria\Core;


class Config implements \ArrayAccess {

    protected $config;

    /**
     * Caso queira ler um arquivo, pode-se passar $config ou $padrao como uma string
     * @param array|string|Config $config
     * @param array|string|Config $padrao
     */
    public function __construct($config = array(), $padrao = array()) {
        $this->config = $this->normalizar($config, $padrao);
    }

    public function toArray() {
        return $this->config;
    }

    public function merge($config) {
        if ($config instanceof Config) $config = $config->toArray();

        return new Config(array_replace_recursive($this->config, $config));
    }
    public function get($path = "", $padrao = null) {
        if ("" === $path) return $this;

        $params = explode(".", $path);

        $config = $this->config;
        foreach ($params as $param) {
            if (!isset($config[$param])) return $padrao;
            $config = $config[$param];
        }

        return is_array($config) ? new Config($config) : $config;
    }

    private function normalizar($config, $padrao)
    {
        $configs = array_map(function($config) {
            if ($config instanceof Config) {
                return $config->get();
            }
            if (is_string($config)) {
                if (!file_exists($config)) throw new \Exception("Arquivo de configuração não encontrado. ". $config);
                return require $config;
            }

            return $config;
        }, array($config, $padrao));

        return call_user_func_array('array_merge', array_reverse($configs));
    }

    public function offsetExists($offset)
    {
        return !!$this->get($offset);
    }

    public function offsetGet($offset)
    {
        $config = $this->get($offset);
        return is_array($config) ? new Config($config) : $config;
    }

    public function offsetSet($offset, $value)
    {
        throw new \Exception("Você não pode alterar as configurações.");
    }

    public function offsetUnset($offset)
    {
        throw new \Exception("Você não pode alterar as configurações.");
    }
}
