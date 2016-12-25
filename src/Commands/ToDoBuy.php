<?php

namespace app\Commands;

class ToDoBuy extends AbstractCommand
{
    public static function getClass(): string
    {
        return 'todo.buy';
    }
}