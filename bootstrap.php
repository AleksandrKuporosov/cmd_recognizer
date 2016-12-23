<?php

require __DIR__ . '/vendor/autoload.php';

spl_autoload_register(function ($class) {
    $class = explode('\\', $class);
    $root = array_shift($class);
    if ('app' === $root) {
        require_once __DIR__ . '/' . implode('/', $class) . '.php';
    }
});