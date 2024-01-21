<?php

namespace FpDbTest;

use Exception;

class ArrayHandler implements HandlerInterface
{
    protected DefaultHandler $default;

    public function __construct()
    {
        $this->default = new DefaultHandler();
    }

    /**
     * @param array $value
     * @return array
     */
    protected function prepareArray(array $value): array
    {
        $result = [];

        foreach ($value as $item) {
            $result[] = $this->default->prepare($item);
        }

        return $result;
    }

    /**
     * @param array $value
     * @return string
     */
    protected function out(array $value): string
    {
        return implode(', ', $value);
    }

    /**
     * Preparation of an array for request
     *
     * @param array $value
     * @return string
     */
    public function prepare($value): string
    {
        if (!is_array($value)) {
            throw new Exception('Invalid value type.');
        }

        $value = $this->prepareArray($value);

        return $this->out($value);
    }

    /**
     * Check if the array is associative
     *
     * @param array $value
     * @return boolean
     */
    static function isAssoc(array $value): bool
    {
        return array_keys($value) !== range(0, count($value) - 1);
    }
}