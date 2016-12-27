<?php

use app\Commands\AbstractCommand;
use Camspiers\StatisticalClassifier\Classifier\ComplementNaiveBayes;
use Slim\Http\Request;
use Slim\Http\Response;

$app = new \Slim\App($container);
$app->post('/command', function (Request $request, Response $response) {
    $textCommand = $request->getBody()->getContents();

    /** @var ComplementNaiveBayes $classifier */
    $classifier = $this->classifier;

    /** @var AbstractCommand[] $commands */
    $commands = $this->commands;

    $predictedClass = $classifier->classify($textCommand);
    if (!array_key_exists($predictedClass, $this->commands)) {
        return $response->withJson([
            'ok' => false,
            'error' => 'class not found',
        ]);
    }

    $commandClass = $commands[$predictedClass];
    $targets = $commandClass->getTargets($textCommand);

    return $response->withJson([
        'ok' => true,
        'class' => $predictedClass,
        'targets' => $targets,
    ]);
});

return $app;