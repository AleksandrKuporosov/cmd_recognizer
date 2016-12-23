<?php

namespace app\Commands;

abstract class AbstractCommand
{
    protected $commands = [];
    protected $stopWords = [];

    abstract public function getClass(): string;
    abstract public function processCommand(string $command);

    public function __construct(array $data)
    {
        foreach ($data as $row) {
            $row = explode(',', trim($row));
            $command = array_shift($row);
            $this->commands[] = $command;
            $this->stopWords = array_merge($this->stopWords, $row);
        }

        $this->stopWords = array_unique($this->stopWords);
    }

    public function getCommands()
    {
        return $this->commands;
    }

    public function getWords($string)
    {
        return preg_split('/\s+/u', preg_replace('/[^а-яА-ЯёЁяЯ0-9\s]/u', '', mb_strtolower($string)));
    }
}