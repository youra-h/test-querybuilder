<?php

namespace FpDbTest;

class ArrayAssocHandler extends ArrayHandler
{
    /**
     * @param array $value
     * @return array
     */
    protected function prepareArray(array $value): array
    {
        $result = [];

        foreach ($value as $key => $item) {
            $result[] = $key . ' = ' . $this->default->prepare($item);
        }

        return $result;
    }
}