<?php

namespace FpDbTest;

interface HandlerInterface
{
    /**
     * Prepare value for SQL query.
     *
     * @param mixed $value
     * @return string
     */
    public function prepare($value): string;
}