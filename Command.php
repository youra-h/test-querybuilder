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
    /**
     * Pattern for search placeholders in query
     * @var string
     */
    private string $pattern = '/\?#|\\?d|\?f|\?a|\?condition|\?/';
    /**
     * Pattern for search conditional blocks in query
     * @var string
     */
    private string $patternConditional = '/\{[^}]*\}/';
    /**
     * Query
     * @var string
     */
    private string $query;
    /**
     * Arguments
     * @var array
     */
    private array $args;
    /**
     * Handlers
     * @var array
     */
    private array $handlers = [];
    /**
     * Conditional blocks
     * @var array
     */
    private array $conditionalBlocks = [];

    /**
     * Command constructor.
     *
     * @param string $query
     * @param array $args
     */
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
            '?condition' => function ($value) {
                if ($value instanceof StdClass) {
                    return new EmptyHandler();
                } else {
                    $block = array_shift($this->conditionalBlocks);
                    $block = str_replace(['{', '}'], '', $block);
                
                    return (new Command($block, [$value]));
                }
            },
        ];
    }

    public function __toString(): string
    {
        return $this->query;
    }

    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * Get matches by pattern
     *
     * @param string $query
     * @param string $pattern
     * @return array
     */
    private function getMatches(string $query, string $pattern): array
    {
        $matches = [];
        preg_match_all($pattern, $query, $matches);
        return $matches[0];
    }

    /**
     * Process query
     *
     * @param string $query
     * @param array $args
     * @return string
     * @throws Exception
     */
    private function processQuery($query, $args)
    {
        $matches = $this->getMatches($query, $this->pattern);
        
        if (count($matches) !== count($args)) {
            throw new Exception('Count of arguments does not match.');
        }

        foreach ($matches as $key => $match) {
            if (isset($this->handlers[$match])) {
                $value = $args[$key];
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

        return $query;
    }

    /**
     * Create query
     *
     * @return string
     * @throws Exception
     */
    public function create(): string
    {
        $query = $this->query;        

        $this->conditionalBlocks = $this->getMatches($query, $this->patternConditional);

        foreach ($this->conditionalBlocks as $block) {
            $query = str_replace($block, '?condition', $query);            
        }

        $query = $this->processQuery($query, $this->args);

        return $query;
    }

    /**
     * Prepare query
     *
     * @param mixed $args
     * @return string
     * @throws Exception
     */
    public function prepare(mixed $args): string
    {
        if (!is_array($args)) {
            $args = [$args];
        }

        $this->args = $args;

        return $this->create();
    }
}