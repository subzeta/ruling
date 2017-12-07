<?php

namespace subzeta\Ruling\Callback;

use subzeta\Ruling\Exception\InvalidCallbackException;

abstract class BaseCallback
{
    private $callback;

    abstract protected function defaultCallback();

    public function __construct($callback = null)
    {
        if (is_null($this->defaultCallback())) {
            throw new InvalidCallbackException('Invalid default callback.');
        }

        if ($callback !== null && !is_callable($callback)) {
            throw new InvalidCallbackException('Callback must be callable.');
        }

        $this->callback = $callback;
    }

    public function call()
    {
        return is_callable($this->callback) ? call_user_func($this->callback) : $this->defaultCallback();
    }
}