<?php

namespace FpDbTest;

use Exception;

class DefaultHandler implements HandlerInterface
{
    protected function getClassHandler(mixed $value): HandlerInterface
    {
        $type = gettype($value);

        $class = ucfirst($type) . 'Handler';

        if (!class_exists($class)) {
            throw new Exception('Invalid value type.');
        }

        return new $class();
    }

    public function prepare($value): string
    {
        $handler = $this->getClassHandler($value);

        return $handler->prepare($value);
    }
}