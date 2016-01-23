<?php

namespace subzeta\Ruling\Test;

use subzeta\Ruling\Ruling;

class RulingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Ruling
     */
    private $ruling;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->ruling = new Ruling();
    }

    /**
     * @dataProvider invalidContexts
     * @param mixed $context
     * @test
     */
    public function itShouldReturnAnInvalidContextExceptionIfContextIsNotAnArray($context)
    {
        $this->setExpectedException(
            'subzeta\Ruling\Exception\InvalidContextException',
            'Context must be an array with string keys and not null/blank values.'
        );

        $this->ruling
            ->given($context)
            ->when('blablabla')
            ->then(function(){return null;})
            ->execute();
    }

    /**
     * @dataProvider invalidRules
     * @param $rule
     * @test
     */
    public function itShouldReturnAnInvalidRuleExceptionIfRuleIsNotAStringOrAnArrayOfStrings($rule)
    {
        $this->setExpectedException(
            'subzeta\Ruling\Exception\InvalidRuleException',
            'Rule must be a string or an array of strings.'
        );

        $this->ruling
            ->given(['hahaha'])
            ->when($rule)
            ->then(function(){return null;})
            ->execute();
    }

    /**
     * @test
     */
    public function itShouldReturnAnInvalidRuleExceptionIfRuleIsNotProvidedButItIsNotValid()
    {
        $this->setExpectedException(
            'subzeta\Ruling\Exception\InvalidRuleException',
            'Rule format is not semantically valid.'
        );

        $this->ruling
            ->given(['hahaha'])
            ->when('1 < 3 ;')
            ->then(function(){return null;})
            ->execute();
    }

    /**
     * @test
     */
    public function itShouldReturnAnInvalidCallbackExceptionIfSuccessCallbackIsProvidedButNotCallable()
    {
        $this->setExpectedException(
            'subzeta\Ruling\Exception\InvalidCallbackException',
            'Success callback must be callable.'
        );

        $this->ruling
            ->given(['haha'])
            ->when('1 == 2')
            ->then('morcilla')
            ->execute();
    }

    /**
     * @test
     */
    public function itShouldReturnAnInvalidCallbackExceptionIfFailCallbackIsProvidedAndIsNotCallable()
    {
        $this->setExpectedException(
            'subzeta\Ruling\Exception\InvalidCallbackException',
            'Fail callback must be callable.'
        );

        $this->ruling
            ->given(['haha'])
            ->when('2 > 3')
            ->then(function(){return null;})
            ->otherwise('morcilla')
            ->execute();
    }

    /**
     * @test
     */
    public function itShouldReturnAnStringWhenRuleAssertsAndSuccessCallbackReturnsAnString()
    {
        $this->assertEquals(
            'It works!',
            $this->ruling
                ->given(['something' => 10])
                ->when(':something is greater than 5 and :something is less than 15')
                ->then(function(){return 'It works!';})
                ->execute()
        );
    }

    /**
     * @test
     */
    public function isShouldReturnTrueWhenMultipleRulesAssertsAndSuccessCallbackReturnsTrue()
    {
        $this->assertTrue(
            $this->ruling
                ->given(['something' => 20])
                ->when([
                    ':something is greater than 5 and :something is less than 25',
                    ':something is equal to 20'
                ])
                ->then(function(){return true;})
                ->execute()
        );
    }

    /**
     * @test
     */
    public function isShouldReturnAnStringWhenRuleDoesNotAssertsAndFailCallbackReturnsAnString()
    {
        $this->assertFalse(
            $this->ruling
                ->given(['something' => 20])
                ->when(':something is greater than 5 and :something is less than 15')
                ->then(function(){return 'It works!';})
                ->execute()
        );
    }

    /**
     * @test
     */
    public function isShouldReturnAnStringWhenThe2ndRuleDoesNotAssertButTheFirstOneYesAndFailCallbackReturnsAnString()
    {
        $this->assertEquals(
            'It fails!',
            $this->ruling
                ->given(['something' => 20])
                ->when([
                    ':something is greater than 5 and :something is less than 25',
                    ':something is greater than 21'
                ])
                ->then(function(){return 'It works!';})
                ->otherwise(function(){return 'It fails!';})
                ->execute()
        );
    }

    /**
     * @test
     */
    public function itShouldReturnTrueIfEvaluationIsSuccessfulAndNoThenCallbackIsProvided()
    {
        $this->assertTrue(
            $this->ruling
                ->given(['haha' => 4])
                ->when(':haha is greater than 3')
                ->execute()
        );
    }

    /**
     * @test
     */
    public function itShouldReturnFalseIfEvaluationIsNegativeAndNoOtherwiseCallbackIsProvided()
    {
        $this->assertFalse(
            $this->ruling
                ->given(['haha' => 2])
                ->when(':haha is greater than 3')
                ->then(function(){return null;})
                ->execute()
        );
    }

    /**
     * @return array
     */
    public function invalidContexts()
    {
        return [
            [1],
            [3.2],
            [function(){return 'This is callable';}],
            [null],
            [''],
            [['']],
            [[null]],
            [['' => '']],
            [[0 => '']],
            [['' => 0]],
            [[null => '']],
            [['' => null]],
            [['' => null]],
        ];
    }

    /**
     * @return array
     */
    public function invalidRules()
    {
        return [
            [1],
            [3.2],
            [function(){return 'This is callable';}],
            [null],
            [''],
            [[1, 2, 3]],
            [[1.1]],
            [[function(){return 'This is callable';}]],
            [['', '']],
            [[null, '']],
            [['this rule has the rule in the key' => '']],
            [['the first rule is ok but the second it\'s not', 1]],
        ];
    }
}
