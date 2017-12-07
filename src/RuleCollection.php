<?php

namespace subzeta\Ruling;

use subzeta\Ruling\Exception\InvalidRuleException;

class RuleCollection
{
    private $rules = [];

    public function __construct($rules)
    {
        if (!is_array($rules)) {
            $rules = [$rules];
        }

        if (!$this->valid($rules)) {
            throw new InvalidRuleException('Rule must be a string or an array of strings.');
        }

        $this->rules = $rules;
    }

    public function get(): array
    {
        return $this->rules;
    }

    public function valid($rules): bool
    {
        foreach ($rules as $rule) {
            if (empty($rule) || !is_string($rule)) {
                return false;
            }
        }

        return true;
    }
}