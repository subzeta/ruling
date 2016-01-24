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
            $this->simpleContextAndSimpleRule(),
            $this->multipleContext(),
            $this->multipleRule(),
            $this->parenthesis(),
            $this->encodings(),
            $this->caseSensitivity(),
            $this->operators(),
            $this->callableContextValue(),
            $this->stricts(),
            $this->notStricts(),
            $this->expectations()
        );
    }

    /**
     * @return array
     */
    public function simpleContextAndSimpleRule()
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
                ':something is equal to "fideuá" and :something isn\'t "croissant"',
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
                [':something is equal to "fricandó"', ':something is not equal to "fideuà"'],
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

    /**
     * @return array
     */
    public function callableContextValue()
    {
        return [
            [
                ['purchase' => function(){return 'gazpacho';}, 'price' => function(){return 40;}],
                ':purchase is "gazpacho" and :price is greater than 50',
                null,
                function(){return false;}
            ],
            [
                ['logged' => function(){return true;}, 'name' => function(){return 'foo';}],
                ':logged is true and :name is "foo"',
                function(){return 'It\'s him!';}
            ],
        ];
    }

    public function stricts()
    {
        return [
            [
                ['pretty' => 1, 'likes_acdc' => function(){return true;}],
                ':pretty same as 1 and :likes_acdc is true',
                function(){return true;}
            ],
            [
                ['pretty' => false, 'likes_acdc' => function(){return true;}],
                ':pretty not same as true or :likes_acdc same as true',
                function(){return true;}
            ],
            [
                ['pretty' => true, 'likes_acdc' => function(){return false;}],
                ':pretty same as true and :likes_acdc same as false',
                function(){return true;}
            ],
            [
                ['pretty' => '1'],
                ':pretty same as "1"',
                function(){return true;}
            ],
            [
                ['pretty' => 1],
                ':pretty same as 1',
                function(){return true;}
            ],
        ];
    }

    public function notStricts()
    {
        return [
            [
                ['pretty' => 1, 'likes_acdc' => function(){return true;}],
                ':pretty is true and :likes_acdc is not true',
                null,
                function(){return 'Shook me all night long is a masterpice honey.';}
            ],
            [
                ['logged' => function(){return 'true';}, 'name' => function(){return 'foo';}],
                ':logged is "true" and :name is "foo"',
                function(){return 'It\'s him!';}
            ],
            [
                ['logged' => function(){return 1;}, 'name' => function(){return 'foo';}],
                ':logged is true and :name is "foo"',
                function(){return 'It\'s him!';}
            ],
            [
                ['pretty' => 0, 'likes_acdc' => function(){return true;}],
                ':pretty is not true and :likes_acdc is not true',
                null,
                function(){return 'Shook me all night long is a masterpice honey.';}
            ],
            [
                ['logged' => function(){return 'false';}, 'name' => function(){return 'foo';}],
                ':logged isn\'t "true" and :name is "foo"',
                function(){return 'It\'s him!';}
            ],
        ];
    }

    public function expectations()
    {
        return [
            [
                ['something' => null],
                ':something is null',
                function(){return 'Yep!';}
            ],
            [
                ['something' => 'null'],
                ':something is not null',
                function(){return 'Yep!';}
            ],
            [
                ['something' => true],
                ':something is true',
                function(){return 'Yep!';}
            ],
            [
                ['something' => false],
                ':something is false',
                function(){return 'Yep!';}
            ],
            [
                ['something' => 'true'],
                ':something is "true"',
                function(){return 'Yep!';}
            ],
            [
                ['something' => 'false'],
                ':something is "false"',
                function(){return 'Yep!';}
            ],
            [
                ['something' => 1],
                ':something is true',
                function(){return 'Yep!';}
            ],
            [
                ['something' => 0],
                ':something is false',
                function(){return 'Yep!';}
            ],
            [
                ['something' => 1.1],
                ':something is less than 1.2',
                function(){return 'Yep!';}
            ],
            [
                ['something' => 1.3],
                ':something is greater or equal to 1.2',
                function(){return 'Yep!';}
            ],
            [
                ['something' => 'The Rolling Stones'],
                ':something is "The Rolling Stones"',
                function(){return 'Yep!';}
            ],
            [
                ['something' => 'The Rolling Stones'],
                ':something is \'The Rolling Stones\'',
                function(){return 'Yep!';}
            ],
            [
                ['something' => 'The Rolling Stones'],
                ":something is 'The Rolling Stones'",
                function(){return 'Yep!';}
            ],
            [
                ['something' => 'The Cardigans'],
                ':something is not "The Cure"',
                function(){return 'Yep!';}
            ],
            [
                ['something' => 'The Cardigans'],
                ':something is not \'The Cure\'',
                function(){return 'Yep!';}
            ],
            [
                ['something' => 'The Cardigans'],
                ":something is not 'The Cure'",
                function(){return 'Yep!';}
            ],
            [
                ['something' => 'Pink Floyd'],
                ':something isn"t "Deep Purple"',
                function(){return 'Yep!';}
            ],
            [
                ['something' => 'Pink Floyd'],
                ':something isn\'t \'Deep Purple\'',
                function(){return 'Yep!';}
            ],
            [
                ['something' => 'Pink Floyd'],
                ":something isn't 'Deep Purple'",
                function(){return 'Yep!';}
            ],
        ];
    }
}