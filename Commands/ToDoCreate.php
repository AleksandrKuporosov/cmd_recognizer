<?php

namespace app\Commands;

class ToDoCreate extends AbstractCommand
{
    public static function getData(): array
    {
        return file(__DIR__ . '/data/todo.create.csv');
    }

    public function getClass(): string
    {
        return 'todo.create';
    }

    public function processCommand(string $commandString)
    {
        $words = $this->getWords($commandString);
        return array_values(array_diff($words, $this->stopWords));
    }
}