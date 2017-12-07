<?php

namespace subzeta\Ruling\Exception;

class InvalidCallbackException extends \Exception
{
    protected $message = 'Invalid or uncallable callback.';
}