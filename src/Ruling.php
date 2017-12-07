<?php

namespace subzeta\Ruling;

use subzeta\Ruling\Callback\FailCallback;
use subzeta\Ruling\Callback\SuccessCallback;
use subzeta\Ruling\Evaluator\Evaluator;

class Ruling
{
    /** @var Context */
    private $context;

    /** @var RuleCollection */
    private $rules;

    /** @var Callback */
    private $successCallback;

    /** @var Callback */
    private $failCallback;

    /** @var Evaluator */
    private $evaluator;

    public function __construct()
    {
        $this->evaluator = new Evaluator();
    }

    public function given($context): self
    {
        $this->context = new Context($context);

        return $this;
    }

    public function when($rules): self
    {
        $this->rules = new RuleCollection($rules);

        return $this;
    }

    public function then($callback): self
    {
        $this->successCallback = new SuccessCallback($callback);

        return $this;
    }

    public function otherwise($callback): self
    {
        $this->failCallback = new FailCallback($callback);

        return $this;
    }

    public function execute()
    {
        return $this->evaluator->assert($this->rules, $this->context) ?
            $this->success()->call() :
            $this->fail()->call();
    }

    public function interpret(): array
    {
        return $this->evaluator->interpret($this->rules, $this->context);
    }

    private function success()
    {
        return $this->successCallback ?? new SuccessCallback();
    }

    private function fail()
    {
        return $this->failCallback ?? new FailCallback();
    }
}
