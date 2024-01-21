<?php

namespace FpDbTest;

class NULLHandler  implements HandlerInterface
{
    public function prepare($value): string
    {
        return 'NULL';
    }
}