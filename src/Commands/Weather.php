<?php

namespace app\Commands;

class Weather extends AbstractCommand
{
    public static function getClass(): string
    {
        return 'weather';
    }
}