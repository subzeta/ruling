<?php

namespace subzeta\Ruling\Test\Callback;

use subzeta\Ruling\Callback\BaseCallback;
use subzeta\Ruling\Callback\SuccessCallback;
use subzeta\Ruling\Exception\InvalidCallbackException;

class BaseCallbackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function itShouldThrowAnExpectedExceptionForAnInvalidDefaultCall()
    {
        $this->setExpectedException(InvalidCallbackException::class, 'Invalid default callback.');

        $this->invalidDefaultCallback();
    }

    /**
     * @test
     */
    public function itShouldThrowAnWhenSettingAnUncallableCall()
    {
        $this->setExpectedException(InvalidCallbackException::class, 'Callback must be callable.');

        new SuccessCallback('this is an invalid function');
    }

    /**
     * @test
     */
    public function itShouldWorkWhenUsingTheDefaultCallback()
    {
        $this->assertSame(true, $this->validCallback()->call());
    }

    /**
     * @test
     */
    public function itShouldWorkWhenSettingAnACorrectCallback()
    {
        $callback = new SuccessCallback(function() {
            return 'this is a valid callback';
        });

        $this->assertSame('this is a valid callback', $callback->call());
    }

    private function invalidDefaultCallback()
    {
        return new class extends BaseCallback {
            public function defaultCallback()
            {
            }
        };
    }

    private function validCallback()
    {
        return new class extends BaseCallback {
            public function defaultCallback()
            {
                return true;
            }
        };
    }
}