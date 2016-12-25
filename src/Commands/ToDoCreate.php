<?php

namespace app\Commands;

class ToDoCreate extends AbstractCommand
{
    public static function getClass(): string
    {
        return 'todo.create';
    }
}