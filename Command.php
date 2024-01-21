<?php

namespace FpDbTest;

use Exception;
use FpDbTest\ArrayHandler;
use FpDbTest\DoubleHandler;
use FpDbTest\DefaultHandler;
use FpDbTest\IntegerHandler;
use FpDbTest\ArrayListHandler;
use FpDbTest\ArrayAssocHandler;

class Command
{
    private string $query;
    private array $args;
    private array $handlers = [];

    public function __construct(string $query, array $args = [])
    {
        $this->query = $query;
        $this->args = $args;

        $this->handlers = [
            '?#' => new ArrayHandler(),
            '?d' => new IntegerHandler(),
            '?f' => new DoubleHandler(),
            '?a' => function ($value) {
                if (ArrayHandler::isAssoc($value)) {
                    return new ArrayAssocHandler();
                } else {
                    return new ArrayListHandler();
                }
            },
            '?'  => new DefaultHandler(),
        ];
    }

    public function __toString(): string
    {
        return $this->create();
    }

    public function getArgs(): array
    {
        return $this->args;
    }

    public function create(): string
    {
        $query = $this->query;        

        $matches = [];

        preg_match_all('/\?#|\\?d|\?f|\?a|\?/', $this->query, $matches);

        $matches = $matches[0];

        if (count($matches) !== count($this->args)) {
            throw new Exception('Count of arguments does not match.');
        }

        foreach ($matches as $key => $match) {
            if (isset($this->handlers[$match])) {
                $value = $this->args[$key];
                $handler = $this->handlers[$match];

                if (is_callable($handler)) {
                    $handler = $handler($value);
                }
                
                $param = $handler->prepare($value);

                $query = preg_replace('/' . preg_quote($match, '/') . '/', $param, $query, 1);
            } else {
                throw new Exception('Invalid query.');
            }
        }

        // Processing of conditional blocks
        // preg_match_all('/\{[^}]*\}/', $query, $matches);

        // $matches = $matches[0];

        // foreach ($matches as $match) {
        //     $query = str_replace($match, '', $query);
        // }

        return $query;
    }
}