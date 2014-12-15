<?php

class Config {
    protected static $_instance;
    public $config;

    private function __construct() {
        $this->config = include(CONFIG_PATH . 'config.php');

        // use local config
        if (!empty($_SERVER['PLATFORM']) && file_exists(CONFIG_PATH . $_SERVER['PLATFORM'] . '/config.php')) {
            $localConf = include(CONFIG_PATH . $_SERVER['PLATFORM'] . '/config.php');
            if (is_array($localConf) && !empty($localConf)) {
                $this->config = array_merge( $this->config, $localConf);
            }
        }
    }

    private function __clone() {}

    public static function getInstance() {
        // проверяем актуальность экземпляра
        if (null === self::$_instance) {
            // создаем новый экземпляр
            self::$_instance = new self();
        }
        // возвращаем созданный или существующий экземпляр
        return self::$_instance;
    }

    public function get($param) {
        if (!empty($param) && isset($this->config[$param])) {
            return $this->config[$param];
        }
        return false;
    }
} 