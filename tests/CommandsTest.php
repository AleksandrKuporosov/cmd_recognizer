<?php

use app\Commands\AbstractCommand;
use app\Commands\ToDoBuy;
use app\Commands\ToDoCreate;
use app\Commands\ToDoList;
use Camspiers\StatisticalClassifier\Classifier\ComplementNaiveBayes;
use Camspiers\StatisticalClassifier\DataSource\DataArray;

/**
 * todo: write tests for all commands
 *
 * Class CommandsTest
 */
class CommandsTest extends PHPUnit_Framework_TestCase
{
    /** @var AbstractCommand[] */
    protected $commands;

    /** @var ComplementNaiveBayes */
    protected $classifier;

    public function setUp()
    {
        /** @var AbstractCommand[] $commands */
        $commands = [
            'todo.buy' => new ToDoBuy(ToDoBuy::getData()),
            'todo.create' => new ToDoCreate(ToDoCreate::getData()),
            'todo.list' => new ToDoList(ToDoList::getData()),
        ];

        $source = new DataArray();
        foreach ($commands as $command) {
            $class = $command->getClass();
            foreach ($command->getCommands() as $cmd) {
                $source->addDocument($class, $cmd);
            }
        }

        $this->commands = $commands;
        $this->classifier = new ComplementNaiveBayes($source);
    }

    /**
     * @dataProvider todoBuyDataProvider
     * @param string $command
     * @param string $expectedClass
     * @param array $expectedTarget
     */
    public function testTodoBuy(
        string $command,
        string $expectedClass,
        array $expectedTarget
    ) {
        $predictedClass = $this->classifier->classify($command);

        self::assertSame($expectedClass, $predictedClass);

        $todoBuy = $this->commands[$predictedClass];

        $targets = $todoBuy->processCommand($command);
        self::assertSame($expectedTarget, $targets);
    }

    public function todoBuyDataProvider()
    {
        return [
            [
                'напомни купить молоко',
                'todo.buy',
                ['молоко'],
            ],
            [
                'добавь колбасу в список покупок',
                'todo.buy',
                ['колбасу'],
            ],
            [
                'надо купить морковь',
                'todo.buy',
                ['морковь'],
            ],
        ];
    }
}