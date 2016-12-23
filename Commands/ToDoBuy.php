<?php

namespace app\Commands;

class ToDoBuy extends AbstractCommand
{
    public static function getData(): array
    {
        return file(__DIR__ . '/data/todo.buy.csv');
    }

    public function getClass(): string
    {
        return 'todo.buy';
    }

    public function processCommand(string $commandString)
    {
        $words = $this->getWords($commandString);
        return array_values(array_diff($words, $this->stopWords));
    }
}