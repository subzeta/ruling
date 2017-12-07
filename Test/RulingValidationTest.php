<?php

namespace subzeta\Ruling\Test;

use subzeta\Ruling\Exception\InvalidCallbackException;
use subzeta\Ruling\Exception\InvalidContextException;
use subzeta\Ruling\Exception\InvalidRuleException;
use subzeta\Ruling\Ruling;

class RulingValidationTest extends \PHPUnit_Framework_TestCase
{
    /** @var Ruling */
    private $ruling;

    public function setUp()
    {
        $this->ruling = new Ruling();
    }

    /**
     * @dataProvider invalidContexts
     * @param mixed $context
     * @test
     */
    public function itShouldReturnAnInvalidContextExceptionIfContextIsNotValid($context)
    {
        $this->setExpectedException(
            InvalidContextException::class,
            'Context must be an array with string keys and values.'
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
            InvalidRuleException::class,
            'Rule must be a string or an array of strings.'
        );

        $this->ruling
            ->given(['hahaha' => 1])
            ->when($rule)
            ->then(function(){return null;})
            ->execute();
    }

    /**
     * @test
     */
    public function itShouldReturnAnInvalidRuleExceptionIfRuleIsValid()
    {
        $this->setExpectedExceptionRegExp(
            InvalidRuleException::class,
            '/^(Rules aren\'t semantically valid)(.*)$/'
        );

        $this->ruling
            ->given(['hahaha' => 1])
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
            InvalidCallbackException::class,
            'Success callback must be callable.'
        );

        $this->ruling
            ->given(['heyyo' => 1])
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
            InvalidCallbackException::class,
            'Fail callback must be callable.'
        );

        $this->ruling
            ->given(['heyyo' => true])
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
            [[':']],
            [['thisIsAValueWithAnIntKey']],
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
