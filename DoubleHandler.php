<?php

namespace FpDbTest;

use Exception;

class DoubleHandler implements HandlerInterface
{
    public function prepare($value): string
    {
        if (!is_float($value)) {
            throw new Exception('Invalid value type.');
        }

        return (string) $value;
    }
}