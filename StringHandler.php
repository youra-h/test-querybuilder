<?php

namespace FpDbTest;

class StringHandler implements HandlerInterface
{
    public function prepare($value): string
    {
        return '"' . htmlspecialchars($value, ENT_QUOTES) . '"';
    }
}