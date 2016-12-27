<?php

use app\Commands;
use Camspiers\StatisticalClassifier;
use Interop\Container\ContainerInterface as Container;

$c = [];
$c['morphy'] = function() {
    $opts = [
        'storage' => PHPMORPHY_STORAGE_FILE,
        'predict_by_suffix' => true,
        'predict_by_db' => true,
    ];

    $dir = __DIR__ . '/../var/dicts/ru_RU';
    $lang = 'ru_RU';

    return new phpMorphy($dir, $lang, $opts);
};

$c['commands'] = function (Container $c) {
    $morphy = $c->get('morphy');

    return [
        'todo.buy' => new Commands\ToDoBuy(
            Commands\ToDoBuy::getData(),
            $morphy
        ),
        'todo.create' => new Commands\ToDoCreate(
            Commands\ToDoCreate::getData(),
            $morphy
        ),
        'todo.list' => new Commands\ToDoList(
            Commands\ToDoList::getData(),
            $morphy
        ),
    ];
};

$c['classifier'] = function (Container $c) {
    /** @var Commands\AbstractCommand[] $commands */
    $commands = $c->get('commands');

    $source = new StatisticalClassifier\DataSource\DataArray();
    foreach ($commands as $command) {
        $class = $command->getClass();
        foreach ($command->getCommands() as $cmd) {
            $source->addDocument($class, $cmd);
        }
    }

    return new StatisticalClassifier\Classifier\ComplementNaiveBayes($source);
};

return $c;
