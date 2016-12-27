<?php

use app\Commands\AbstractCommand;
use app\Commands\ToDoBuy;
use app\Commands\ToDoCreate;
use app\Commands\ToDoList;
use app\Helpers\Text;
use Camspiers\StatisticalClassifier\Classifier\ComplementNaiveBayes;
use Camspiers\StatisticalClassifier\DataSource\DataArray;

/**
 * todo: write tests for all commands
 *
 * Class CommandsTest
 */
class CommandsTest extends BaseTestCase
{
    /** @var AbstractCommand[] */
    protected $commands;

    /** @var ComplementNaiveBayes */
    protected $classifier;

    public function setUp()
    {
        parent::setUp();

        $morphy = $this->container['morphy']();
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

        $this->commands = $commands;
        $this->classifier = new ComplementNaiveBayes($source);
    }

    /**
     * @covers ToDoBuy::getTargets()
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

        $targets = $todoBuy->getTargets($command);
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
                ['колбаса'],
            ],
            [
                'надо купить морковь',
                'todo.buy',
                ['морковь'],
            ],
            [
                'купить корову',
                'todo.buy',
                ['корова'],
            ],
        ];
    }

    /**
     * @covers Text::normalizeWords()
     * @dataProvider normalizeTargetProvider
     * @param string $target
     * @param string $expected
     */
    public function testNormalizeCommand(
        string $target,
        string $expected
    ) {
        $normalized = Text::normalizeWords($this->container['morphy'](), [$target]);
        self::assertSame($expected, $normalized[0]);
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