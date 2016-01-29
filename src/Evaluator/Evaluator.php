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
    public function assert($rules, $context)
    {
        return array_product(
            array_map(
                function($rule) {
                    return (new Rule($rule))->isTrue();
                },
                $this->interpret($rules, $context)
            )
        );
    }

    /**
     * @param RuleCollection $rules
     * @param Context $context
     * @return bool
     */
    public function valid($rules, $context)
    {
        return array_product(
            array_map(
                function($rule) {
                    return (new Rule($rule))->isValid();
                },
                $this->interpret($rules, $context)
            )
        );
    }

    /**
     * @param RuleCollection $rules
     * @param Context $context
     * @return string[]
     */
    public function interpret($rules, $context)
    {
        return $this->build($rules, $context);
    }

    /**
     * @param RuleCollection $rules
     * @param Context $context
     * @return string[]
     */
    private function build($rules, $context)
    {
        return array_map(
            function($rule) use ($context) {
                return $this->prepare($rule, $context);
            },
            $rules->get()
        );
    }

    /**
     * @param string $rule
     * @param Context $context
     * @return string
     */
    private function prepare($rule, $context)
    {
        $replacements = array_merge((new ComparisonOperator())->all(), (new LogicalOperator())->all(), $context->get());

        return str_replace(array_keys($replacements), array_values($replacements), $rule);
    }
}
