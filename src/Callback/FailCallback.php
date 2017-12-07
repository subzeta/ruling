<?php

namespace subzeta\Ruling\Callback;

class FailCallback extends BaseCallback
{
    protected function defaultCallback()
    {
        return false;
    }
}