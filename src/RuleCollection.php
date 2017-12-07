<?php

namespace subzeta\Ruling;

class RuleCollection
{
    private $rules = [];

    public function __construct($rules)
    {
        $this->rules = is_array($rules) ? $rules : [$rules];
    }

    public function get(): array
    {
        return $this->rules;
    }

    public function valid(): bool
    {
        foreach ($this->get() as $rule) {
            if (empty($rule) || !is_string($rule)) {
                return false;
            }
        }

        return true;
    }
}