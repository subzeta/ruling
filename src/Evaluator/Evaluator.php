<?php

namespace subzeta\Ruling\Evaluator;

use nicoSWD\Rules\Rule;
use subzeta\Ruling\Context;
use subzeta\Ruling\Operator\ComparisonOperator;
use subzeta\Ruling\Operator\LogicalOperator;
use subzeta\Ruling\RuleCollection;

class Evaluator
{
    /**
     * @param RuleCollection $rules
     * @param Context $context
     * @return bool
     */
    public function evaluate($rules, $context)
    {
        foreach ($rules->get() as $rule) {
            if (!(new Rule($this->prepare($rule, $context)))->isTrue()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param RuleCollection $rules
     * @param Context $context
     * @return bool
     */
    public function isValid($rules, $context)
    {
        foreach ($rules->get() as $rule) {
            if (!(new Rule($this->prepare($rule, $context)))->isValid()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $rule
     * @param Context $context
     * @return string
     */
    private function prepare($rule, $context)
    {
        $replacements = array_merge(
            (new ComparisonOperator())->getAll(),
            (new LogicalOperator())->getAll(),
            $context->get()
        );

        foreach ($replacements as $search => $replace) {
            $rule = str_replace($search, $replace, $rule);
        }

        return $rule;
    }
}
