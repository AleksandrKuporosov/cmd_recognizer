<?php

use Slim\Http\Body;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\Uri;

class SlimTest extends BaseTest
{
    /** @var \Slim\App */
    protected $app;
    protected $request;
    protected $response;

    public function post($path, $data)
    {
        ob_start();

        $uri = Uri::createFromString('http://example.com' . $path);
        $headers = new Headers([
            'Content-Type' => 'application/json'
        ]);
        $env = Slim\Http\Environment::mock();
        $serverParams = $env->all();
        $body = new Body(fopen('php://temp', 'r+'));
        $body->write($data);
        $request = new Request('POST', $uri, $headers, [], $serverParams, $body);

        $container = require __DIR__ . '/../config/dependencies.php';
        $container['request'] = $request;
        $this->app = require __DIR__ . '/../app.php';
        $this->app->run();

        return ob_get_clean();
    }
}