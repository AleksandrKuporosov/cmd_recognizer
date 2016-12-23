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
        var_dump($this->getClass());
        $words = $this->getWords($commandString);
        $diff = array_diff($words, $this->stopWords);
        var_dump($diff);
    }
}