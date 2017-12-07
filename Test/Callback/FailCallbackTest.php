<?php

namespace subzeta\Ruling\Test\Callback;

use subzeta\Ruling\Callback\FailCallback;

class FailCallbackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function itShouldReturnAsItsDefaultCallback()
    {
        $this->assertSame(false, (new FailCallback())->call());
    }
}