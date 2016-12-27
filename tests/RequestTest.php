<?php

error_reporting(15);
ini_set('display_errors', 1);

class RequestTest extends SlimTestCase
{
    /**
     * @dataProvider requestProvider
     * @param string $command
     * @param array $expectedResponse
     */
    public function testRequestCommand(
        string $command,
        array $expectedResponse
    ) {
        $response = $this->post('/command', $command);
        self::assertSame(json_encode($expectedResponse), $response);
    }

    public function requestProvider(): array
    {
        return [
            [
                'напомни купить молоко',
                [
                    'ok' => true,
                    'class' => 'todo.buy',
                    'targets' => ['молоко']
                ],
            ],
            [
                'добавь колбасу в список покупок',
                [
                    'ok' => true,
                    'class' => 'todo.buy',
                    'targets' => ['колбаса']
                ],
            ],
            [
                'надо купить морковь',
                [
                    'ok' => true,
                    'class' => 'todo.buy',
                    'targets' => ['морковь']
                ],
            ],
            [
                'купить корову',
                [
                    'ok' => true,
                    'class' => 'todo.buy',
                    'targets' => ['корова']
                ],
            ],
        ];
    }
}