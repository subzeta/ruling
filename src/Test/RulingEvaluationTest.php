<?php

namespace subzeta\Ruling\Test;

use subzeta\Ruling\Ruling;

class RulingEvaluationTest extends \PHPUnit_Framework_TestCase
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
     * @test
     */
    public function shouldReturnTrueWhenRuleAssertsAndSuccessCallbackIsNotSet()
    {
        $this->assertTrue(
            $this->ruling
                ->given(['something' => 10])
                ->when(':something is greater than 5 and :something is less than 15')
                ->execute()
        );
    }

    /**
     * @test
     */
    public function shouldReturnAnStringWhenRuleDoesNotAssertAndFailCallbackIsNotSet()
    {
        $this->assertFalse(
            $this->ruling
                ->given(['something' => 20])
                ->when(':something is greater than 5 and :something is less than 15')
                ->execute()
        );
    }

    /**
     * @dataProvider getData
     * @param array $context
     * @param string|string[] $rules
     * @param callable $successCallback
     * @param callable $failCallback
     * @test
     */
    public function itShouldReturnTheExpectedCallback($context, $rules, $successCallback = null, $failCallback = null)
    {
        $this->assertSame(
            $successCallback ? $successCallback() : $failCallback(),
            $this->ruling
                ->given($context)
                ->when($rules)
                ->then($successCallback)
                ->otherwise($failCallback)
                ->execute()
        );
    }

    /**
     * @return array
     */
    public function getData()
    {
        return array_merge(
            $this->baseCheckUps(),
            $this->multipleContext(),
            $this->multipleRule(),
            $this->parenthesis(),
            $this->encodings(),
            $this->caseSensitivity(),
            $this->operators()
        );
    }

    /**
     * @return array
     */
    public function baseCheckUps()
    {
        return [
            [
                ['something' => 10],
                ':something is greater than 5 and :something is less than 15',
                function () {return 'It works!';}
            ],
            [
                ['something' => 2.3],
                ':something is greater than 1.5 and :something is less than 3.2',
                function () {return true;}
            ],
            [
                ['something' => 'fideuà'],
                ':something is equal to "fideuá" and :something is not equal to "croissant"',
                null,
                function () {return false;}
            ],
            [
                ['something' => 'fideuà'],
                ':something is equal to "fideuà" and :something is not equal to "croissant"',
                function () {return true;}
            ],
        ];
    }

    /**
     * @return array
     */
    public function multipleContext()
    {
        return [
            [
                ['something' => 10, 'somehow' => 'Joe'],
                ':something is greater than 5 and :something is less than 15 and :somehow is equal to "Joe"',
                function () {return 'It works!';}
            ],
        ];
    }

    /**
     * @return array
     */
    public function multipleRule()
    {
        return [
            [
                ['something' => 'fricandó'],
                [
                    ':something is equal to "fricandó"',
                    ':something is not equal to "fideuà"'
                ],
                function () {return true;}
            ],
        ];
    }

    /**
     * @return array
     */
    public function parenthesis()
    {
        return [
            [
                ['something' => 'fideuà'],
                '(:something is equal to "fideuà" and :something is not equal to "croissant") or :something is equal to "fideuà"',
                function () {return true;}
            ],
            [
                ['something' => 'tortilla de patatas'],
                '(:something is equal to "tortilla de patatas" and :something is equal to "antananaribo") or :something is equal to "madalenas"',
                null,
                function () {return false;}
            ],
        ];
    }

    /**
     * @return array
     */
    public function encodings()
    {
        return [
            [
                ['something' => 'fideuà'],
                ':something is equal to "fideuá" and :something is not equal to "croissant"',
                null,
                function(){return false;}
            ],
        ];
    }

    /**
     * @return array
     */
    public function caseSensitivity()
    {
        return [
            [
                ['something' => 'fideua'],
                ':something is equal to "FIDEUA" and :something is not equal to "croissant"',
                null,
                function(){return false;}
            ]
        ];
    }

    /**
     * @return array
     */
    public function operators()
    {
        return [
            [
                ['something' => 'gazpacho'],
                ':something is "gazpacho" and :something is not "salmorejo"',
                function(){return true;},
            ]
        ];
    }
}