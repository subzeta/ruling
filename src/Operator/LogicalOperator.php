<?php

namespace subzeta\Ruling\Operator;

class LogicalOperator
{
    /**
     * @return array
     */
    public function getAll()
    {
        return [
            ' and ' => ' && ',
            ' or ' => ' || '
        ];
    }
}