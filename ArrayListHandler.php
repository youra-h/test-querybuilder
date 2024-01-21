<?php

namespace FpDbTest;

class ArrayListHandler extends ArrayHandler
{
    protected function out(array $value): string
    {
        return '(' . parent::out($value) . ')';
    }
}