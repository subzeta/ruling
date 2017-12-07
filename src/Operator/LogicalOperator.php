<?php

namespace subzeta\Ruling\Operator;

class LogicalOperator
{
    public function all(): array
    {
        return [
            ' and ' => ' && ',
            ' or ' => ' || '
        ];
    }
}