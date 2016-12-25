<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/umisoft/phpmorphy/src/phpMorphy.php';

spl_autoload_register(function ($class) {
    $classParts = explode('\\', $class);
    $root = array_shift($classParts);

    $targetFile = __DIR__ . '/' . $class . '.php';
    if (file_exists($targetFile)) {
        require_once $targetFile;
        return;
    }

    if ('app' === $root) {
        $targetFile = __DIR__ . '/src/' . implode('/', $classParts) . '.php';
        if (file_exists($targetFile)) {
            require_once $targetFile;
            return;
        }
    }
});

$container = require __DIR__ . '/config/dependencies.php';