<?php

class BaseTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Pimple\Container
     */
    protected $container;

    public function setUp()
    {
        parent::setUp();

        $this->container = require __DIR__ . '/../config/dependencies.php';
    }
}