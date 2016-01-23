<?php

namespace subzeta\Ruling\Operator;

class ComparisonOperator
{
    /**
     * @return array
     */
    public function getAll()
    {
        return [
            'is greater than' => '>',
            'is greater or equal to' => '>=',
            'is less than' => '<',
            'is less or equal to' => '<=',
            'is equal to' => '==',
            'is not equal to' => '!=',
        ];
    }
}