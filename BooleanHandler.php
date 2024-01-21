<?php

namespace FpDbTest;

use Exception;

class BooleanHandler implements HandlerInterface
{
    public function prepare($value): string
    {
        if (!is_bool($value)) {
            throw new Exception('Invalid value type.');
        }

        return $value ? '1' : '0';
    }
}