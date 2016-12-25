<?php

use app\Commands\AbstractCommand;
use app\Commands\ToDoBuy;
use app\Commands\ToDoCreate;
use app\Commands\ToDoList;
use Camspiers\StatisticalClassifier\Classifier\ComplementNaiveBayes;
use Camspiers\StatisticalClassifier\DataSource\DataArray;
use Slim\Http\Request;
use Slim\Http\Response;

$app = new \Slim\App($container);
$app->post('/', function (Request $request, Response $response) {
    $textCommand = $request->getBody()->getContents();

    $morphy = $this->morphy;
    /** @var AbstractCommand[] $commands */
    $commands = [
        'todo.buy' => new ToDoBuy(
            ToDoBuy::getData(),
            $morphy
        ),
        'todo.create' => new ToDoCreate(
            ToDoCreate::getData(),
            $morphy
        ),
        'todo.list' => new ToDoList(
            ToDoList::getData(),
            $morphy
        ),
    ];

    $source = new DataArray();
    foreach ($commands as $command) {
        $class = $command->getClass();
        foreach ($command->getCommands() as $cmd) {
            $source->addDocument($class, $cmd);
        }
    }

    $classifier = new ComplementNaiveBayes($source);

    $predictedClass = $classifier->classify($textCommand);
    if (!array_key_exists($predictedClass, $commands)) {
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