<?php

namespace app\Commands;

class ToDoList extends AbstractCommand
{
    public static function getClass(): string
    {
        return 'todo.list';
    }
}