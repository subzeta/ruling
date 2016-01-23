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
            if (empty($key) || empty($value) || !is_string($key)) {
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
                $context[':' . $key] = $value;
                unset($context[$key]);
            }
        }

        return $context;
    }
}