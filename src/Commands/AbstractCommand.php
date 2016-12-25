<?php

namespace app\Commands;

use app\Helpers\Text;
use phpMorphy;

abstract class AbstractCommand
{
    protected $commands = [];
    protected $stopWords = [];

    /** @var phpMorphy  */
    protected $morphy;

    abstract public static function getClass(): string;

    public function getTargets(string $commandString)
    {
        $words = Text::getWords($commandString);
        $targets = array_values(array_diff($words, $this->stopWords));
        return Text::normalizeWords($this->morphy, $targets);
    }

    public function __construct(array $data, phpMorphy $morphy)
    {
        foreach ($data as $row) {
            $row = explode(',', trim($row));
            $command = array_shift($row);
            $this->commands[] = $command;
            $this->stopWords = array_merge($this->stopWords, $row);
        }

        $this->stopWords = array_unique($this->stopWords);
        $this->morphy = $morphy;
    }

    public function getCommands()
    {
        return $this->commands;
    }

    public static function getData(): array
    {
        return file(__DIR__ . '/../../var/commands/' . static::getClass() . '.csv');
    }
}