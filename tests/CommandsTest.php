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

    /**
     * @dataProvider normalizeTargetProvider
     * @param string $command
     * @param string $expected
     */
    public function testNormalizeCommand(
        string $command,
        string $expected
    ) {

        // set some options
        $opts = [
            'storage' => PHPMORPHY_STORAGE_FILE,
            'predict_by_suffix' => true,
            'predict_by_db' => true,
        ];

       $dir = __DIR__ . '/../dicts/utf-8';
        $lang = 'ru_RU';

        $morphy = new phpMorphy($dir, $lang, $opts);

        $all_forms = $morphy->getAllForms('слова');
    }

    public function normalizeTargetProvider(): array
    {
        return [
            [
                'молока',
                'молоко'
            ],
            [
                'колбасу',
                'колбаса',
            ],
            [
                'моркови',
                'морковь',
            ],
        ];
    }
}