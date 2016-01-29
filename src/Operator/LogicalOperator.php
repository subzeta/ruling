<?php

namespace subzeta\Ruling\Operator;

class LogicalOperator
{
    /**
     * @return array
     */
    public function all()
    {
        return [
            ' and ' => ' && ',
            ' or ' => ' || '
        ];
    }
}