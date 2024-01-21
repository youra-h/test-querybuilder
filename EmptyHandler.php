<?php

namespace FpDbTest;

class EmptyHandler implements HandlerInterface
{
    public function prepare($value): string
    {
        return '';
    }
}