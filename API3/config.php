<?php
namespace Core;
class S_CONFIG {

    private static $CONFIG = array();

    /**
     * Constructor de la configuración
     * @private
     * */
    private function __construct() {
        
    }

    private static function initConfig() {
        if (empty(S_CONFIG::$CONFIG)) {
            S_CONFIG::$CONFIG = array(
                // Web Configuration
                'title' => 'API - Universidad Latina de Costa Rica',
                'Web' => '',
                'DBHost' => '186.159.129.2',
                'DBase' => 'TERAPIAFISICA',
                'DBUser' => 'terapia',
                'DBPswd' => 'Terapia2019+',
                'UseMD5' => 1
            );
        }
    }

    /**
     * Devuelve el parametro de configuracion
     * @private
     */
    public static function getConfig($var) {
        S_CONFIG::initConfig();
        return (isset(S_CONFIG::$CONFIG[$var]) ? S_CONFIG::$CONFIG[$var] : NULL );
    }

}
