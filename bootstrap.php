<?php
require_once(dirname(__FILE__) . '/vendor/autoload.php');

function config($key = null) {
    static $config;

    if (!isset($config)) {
        $config = require_once(dirname(__FILE__) . '/config.php');
    }

    if (isset($key)) {
        return $config[$key];
    }

    return $config;
}
