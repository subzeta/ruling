<?php

namespace subzeta\Ruling\Operator;

class ComparisonOperator
{
    /**
     * @return array
     */
    public function all()
    {
        return [
            ' is greater than ' => ' > ',
            ' is greater or equal to ' => ' >= ',
            ' is less than ' => ' < ',
            ' is less or equal to ' => ' <= ',
            ' not same as ' => ' !== ',
            ' same as ' => ' === ',
            ' is equal to ' => ' == ',
            ' is not equal to ' => ' != ',
            ' is not ' => ' != ',
            ' isn\'t ' => ' != ',
            ' isn"t ' => ' != ',
            ' is ' => ' == ',
            ' contained in ' => ' in ',
            ' in ' => ' in ',
        ];
    }
}