<?php

require __DIR__ . '/bootstrap.php';

use Camspiers\StatisticalClassifier\Classifier\ComplementNaiveBayes;
use Camspiers\StatisticalClassifier\DataSource\DataArray;
use app\Commands;

$textCommand = 'надо купить молока';

/** @var \app\Commands\AbstractCommand[] $commands */
$commands = [
    new Commands\ToDoBuy(Commands\ToDoBuy::getData()),
    new Commands\ToDoCreate(Commands\ToDoCreate::getData()),
    new Commands\ToDoList(Commands\ToDoList::getData()),
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
$classifier->prepareModel();

foreach ($commands as $command) {
    if ($predictedClass === $command->getClass()) {
        $command->processCommand($textCommand);
        break;
    }
}