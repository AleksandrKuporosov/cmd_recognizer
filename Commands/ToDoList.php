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
        $words = $this->getWords($commandString);
        return array_values(array_diff($words, $this->stopWords));
    }
}