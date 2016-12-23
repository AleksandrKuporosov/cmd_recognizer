<?php

namespace app\Commands;

class ToDoList extends AbstractCommand
{
    public static function getData(): array
    {
        return file(__DIR__ . '/data/todo.list.csv');
    }

    public function getClass(): string
    {
        return 'todo.list';
    }

    public function processCommand(string $commandString)
    {
        var_dump($this->getClass());
        $words = $this->getWords($commandString);
        $diff = array_diff($words, $this->stopWords);
        var_dump($diff);
    }
}