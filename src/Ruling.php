<?php

namespace subzeta\Ruling;

use subzeta\Ruling\Evaluator\Evaluator;
use subzeta\Ruling\Exception\InvalidCallbackException;
use subzeta\Ruling\Exception\InvalidContextException;
use subzeta\Ruling\Exception\InvalidRuleException;

class Ruling
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var RuleCollection
     */
    private $rules;

    /**
     * @var callable
     */
    private $successCallback;

    /**
     * @var callable
     */
    private $failCallback;

    /**
     * @var Evaluator
     */
    private $evaluator;

    /**
     * @desc constructor
     */
    public function __construct()
    {
        $this->evaluator = new Evaluator();
    }

    /**
     * @param string[] $context
     * @return self
     */
    public function given($context)
    {
        $this->context = new Context($context);

        return $this;
    }

    /**
     * @param string|string[] $rules
     * @return self
     */
    public function when($rules)
    {
        $this->rules = new RuleCollection($rules);

        return $this;
    }

    /**
     * @param callable $callback
     * @return self
     */
    public function then($callback)
    {
        $this->successCallback = $callback;

        return $this;
    }

    /**
     * @param callable $callback
     * @return self
     */
    public function otherwise($callback)
    {
        $this->failCallback = $callback;

        return $this;
    }

    /**
     * @return bool
     */
    public function evaluate()
    {
        $this->validate();

        return $this->evaluator->evaluate($this->rules, $this->context);
    }

    /**
     * @return callable|bool
     */
    public function execute()
    {
        return $this->evaluate() ?
            ($this->successCallback ? call_user_func($this->successCallback) : true) :
            ($this->failCallback ? call_user_func($this->failCallback) : false);
    }

    /**
     * @throws InvalidContextException
     * @throws InvalidRuleException
     * @throws InvalidCallbackException
     */
    private function validate()
    {
        if (!$this->context->valid()) {
            throw new InvalidContextException('Context must be an array with string keys and not null/blank values.');
        }
        if (!$this->rules->valid()) {
            throw new InvalidRuleException('Rule must be a string or an array of strings.');
        }
        if (!$this->evaluator->isValid($this->rules, $this->context)) {
            throw new InvalidRuleException('Rule format is not semantically valid.');
        }
        if ($this->successCallback !== null && !is_callable($this->successCallback)) {
            throw new InvalidCallbackException('Success callback must be callable.');
        }
        if ($this->failCallback !== null && !is_callable($this->failCallback)) {
            throw new InvalidCallbackException('Fail callback must be callable.');
        }
    }
}
