<?php

namespace FpDbTest;

use Exception;

class IntegerHandler implements HandlerInterface
{
    public function prepare($value): string
    {
        if (!is_int($value) && !is_bool($value)) {
            throw new Exception('Invalid value type.');
        }

        return is_int($value) ? (string) $value : (new BooleanHandler())->prepare($value);
    }
}