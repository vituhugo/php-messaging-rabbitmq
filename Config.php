<?php


namespace Mensageria;


class Config {

    protected static $configData;
    protected static $instance;

    protected $queue = array('name' => '');
    protected $exchange = '';

    protected $messages = array(
        'priority' => 10,
        'expire' => 86400,
        'contentType' => 'application/json'
    );

    protected $_config = array(
        "ouvinte" => array(
            "filas" => array(
                'principal' => '',
                'erro' => ''
            ),
            "tentativas"   => 3,
        ),
        "exchange" => '',
        "mensagens" => array(
            'prioridade' => 10,
            'expiracao' => 86400,
            'contentType' => 'application/json'
        ),
        "conexao" => array(
            'host' => null,
            'port' => null,
            'user' => null,
            'password' => null,
            'vhost' => null
        )
    ) ;

    protected function __construct() {
        $this->setConfig(self::$configData);
    }

    public static function getInstance() {
        if (self::$instance) self::$instance = new self;
        return self::$instance;
    }

    public function setConfig($config) {
        $this->_config = array_merge_recursive($this->_config, $config);
    }

    public function get($name) {
        $name = str_replace(".", "_", $name);
        if (!isset($name)) { throw new \Exception('Configuração não encontrada'); }
        return $this->$name;
    }
}