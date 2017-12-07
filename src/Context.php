<?php

namespace subzeta\Ruling;

class Context
{
    private $context = [];

    public function __construct($context)
    {
        $this->context = $this->build($context);
    }

    public function get()
    {
        return $this->context;
    }

    public function valid(): bool
    {
        if (empty($this->get()) || !is_array($this->get())) {
            return false;
        }

        foreach ($this->get() as $key => $value) {
            if (empty($key) || !preg_match('/^\:[a-zA-Z\_]+$/', $key)) {
                return false;
            }
        }

        return true;
    }

    private function build($context)
    {
        if (is_array($context)) {
            foreach ($context as $key => $value) {
                $context[':' . $key] = $this->processValue($value);
                unset($context[$key]);
            }
        }

        return $context;
    }

    private function processValue($value)
    {
        if (!is_bool($value) && empty($value)) {
            return 'null';
        }

        $value = is_callable($value) ? $value() : $value;

        if (is_array($value)) {
            $value = '['.implode(',', array_map(function($e) {return is_string($e) ? '"'.$e.'"' : $e;}, $value)).']';
        } elseif (is_string($value)) {
            $value = '"'.$value.'"';
        } elseif (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }

        return $value;
    }
}