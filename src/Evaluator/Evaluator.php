<?php

namespace subzeta\Ruling\Evaluator;

use nicoSWD\Rules\Rule;
use subzeta\Ruling\Context;
use subzeta\Ruling\Operator\ComparisonOperator;
use subzeta\Ruling\Operator\LogicalOperator;
use subzeta\Ruling\RuleCollection;

class Evaluator
{
    public function assert(RuleCollection $rules, Context $context): bool
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

    public function valid(RuleCollection $rules, Context $context): bool
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

    public function interpret(RuleCollection $rules, Context $context): array
    {
        return $this->build($rules, $context);
    }

    private function build(RuleCollection $rules, Context $context): array
    {
        return array_map(
            function($rule) use ($context) {
                return $this->prepare($rule, $context);
            },
            $rules->get()
        );
    }

    private function prepare(string $rule, Context $context): string
    {
        $replacements = array_merge((new ComparisonOperator())->all(), (new LogicalOperator())->all(), $context->get());

        return str_replace(array_keys($replacements), array_values($replacements), $rule);
    }
}
