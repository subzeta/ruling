<?php

namespace subzeta\Ruling\Test;

use subzeta\Ruling\Ruling;

class RulingValidationTest extends \PHPUnit_Framework_TestCase
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
