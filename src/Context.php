<?php

namespace subzeta\Ruling;

class Context
{
    /**
     * @param array
     */
    private $context;

    /**
     * @param array $context
     */
    public function __construct($context)
    {
        $this->context = $this->build($context);
    }

    /**
     * @return array
     */
    public function get()
    {
        return $this->context;
    }

    /**
     * @return bool
     */
    public function valid()
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

    /**
     * @param array $context
     * @return array
     */
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

    /**
     * @param mixed $value
     * @return mixed
     */
    private function processValue($value)
    {
        if (!is_bool($value) && empty($value)) {
            return 'null';
        }

        $value = is_callable($value) ? $value() : $value;

        if (is_string($value)) {
            $value = '"'.$value.'"';
        } elseif (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }

        return $value;
    }
}