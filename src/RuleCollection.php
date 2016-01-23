<?php

namespace subzeta\Ruling;

class RuleCollection
{
    /**
     * @param string[]
     */
    private $rules;

    /**
     * @param string[] $rules
     */
    public function __construct($rules)
    {
        $this->rules = is_array($rules) ? $rules : [$rules];
    }

    /**
     * @return string[]
     */
    public function get()
    {
        return $this->rules;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        foreach ($this->get() as $rule) {
            if (empty($rule) || !is_string($rule)) {
                return false;
            }
        }

        return true;
    }
}