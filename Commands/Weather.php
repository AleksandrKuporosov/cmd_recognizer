<?php

namespace app\Commands;

class Weather extends AbstractCommand
{
    public static function getData(): array
    {
        return file(__DIR__ . '/data/weather.csv');
    }

    public function getClass(): string
    {
        return 'weather';
    }

    public function processCommand(string $command)
    {
        echo 'Weather: ' . $command, "\n";
    }
}