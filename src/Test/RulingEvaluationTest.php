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
     * @test
     */
    public function itShouldReturnACorrectInterpretation()
    {
        $this->assertSame(
            ['(true === false && 1 < 2) || 3 <= 4'],
            $this->ruling
                ->given(['a' => true, 'b' => 2, 'c' => 4])
                ->when('(:a same as false and 1 < :b) or 3 is less or equal to 4')
                ->interpret()
        );
    }

    /**
     * @dataProvider getData
     * @param array $context
     * @param string|string[] $rules
     * @param bool $expectation
     * @test
     */
    public function itShouldReturnTheExpectedCallback($context, $rules, $expectation)
    {
        $this->assertSame($expectation, $this->ruling->given($context)->when($rules)->execute());
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
                true
            ],
            [
                ['something' => 2.3],
                ':something is greater than 1.5 and :something is less than 3.2',
                true
            ],
            [
                ['something' => 'fideuà'],
                ':something is equal to "fideuá" and :something isn\'t "croissant"',
                false
            ],
            [
                ['something' => 'fideuà'],
                ':something is equal to "fideuà" and :something is not equal to "croissant"',
                true
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
                true
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
                true
            ],
            [
                ['something' => 'fricandó'],
                [':something is not equal to "fricandó"', ':something is equal to "fideuà"'],
                false
            ],
            [
                ['something' => 3],
                [':something is equal to 3', ':something is equal to 4'],
                false
            ],
            [
                ['something' => 'fricandó'],
                [':something is not equal to "fricandó"', ':something is equal to "fricandó"'],
                false
            ],
            [
                ['something' => 8],
                [':something is less or equal to 10', ':something is greater than 6'],
                true
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
                true
            ],
            [
                ['something' => 'tortilla de patatas'],
                '(:something is equal to "tortilla de patatas" and :something is equal to "antananaribo") or :something is equal to "madalenas"',
                false
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
                false
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
                false
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
                true
            ],
            [
                ['something' => ['a', 'b', 'c']],
                '\'a\' in :something',
                true
            ],
            [
                ['something' => ['1', '2', '3']],
                '\'1\' in :something',
                true
            ],
            [
                ['something' => ['1', '2', '3']],
                '1 in :something',
                false
            ],
            [
                ['something' => [1, 2, 3]],
                '\'1\' in :something',
                false
            ],
            [
                ['something' => [1, 2, 3]],
                '"1" in :something',
                false
            ],
            [
                ['something' => [1, 2, 3]],
                '1 in :something',
                true
            ],
            [
                ['something' => [1.13, 2, 3]],
                '1.13 in :something',
                true
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
                false
            ],
            [
                ['logged' => function(){return true;}, 'name' => function(){return 'foo';}],
                ':logged is true and :name is "foo"',
                true
            ],
        ];
    }

    public function stricts()
    {
        return [
            [
                ['pretty' => 1, 'likes_acdc' => function(){return true;}],
                ':pretty same as 1 and :likes_acdc is true',
                true
            ],
            [
                ['pretty' => false, 'likes_acdc' => function(){return true;}],
                ':pretty not same as true or :likes_acdc same as true',
                true
            ],
            [
                ['pretty' => true, 'likes_acdc' => function(){return false;}],
                ':pretty same as true and :likes_acdc same as false',
                true
            ],
            [
                ['pretty' => '1'],
                ':pretty same as "1"',
                true
            ],
            [
                ['pretty' => 1],
                ':pretty same as 1',
                true
            ],
        ];
    }

    public function notStricts()
    {
        return [
            [
                ['pretty' => 1, 'likes_acdc' => function(){return true;}],
                ':pretty is true and :likes_acdc is not true',
                false
            ],
            [
                ['logged' => function(){return 'true';}, 'name' => function(){return 'foo';}],
                ':logged is "true" and :name is "foo"',
                true
            ],
            [
                ['logged' => function(){return 1;}, 'name' => function(){return 'foo';}],
                ':logged is true and :name is "foo"',
                true
            ],
            [
                ['pretty' => 0, 'likes_acdc' => function(){return true;}],
                ':pretty is not true and :likes_acdc is not true',
                false
            ],
            [
                ['logged' => function(){return 'false';}, 'name' => function(){return 'foo';}],
                ':logged isn\'t "true" and :name is "foo"',
                true
            ],
        ];
    }

    public function expectations()
    {
        return [
            [
                ['something' => null],
                ':something is null',
                true
            ],
            [
                ['something' => 'null'],
                ':something is not null',
                true
            ],
            [
                ['something' => true],
                ':something is true',
                true
            ],
            [
                ['something' => false],
                ':something is false',
                true
            ],
            [
                ['something' => 'true'],
                ':something is "true"',
                true
            ],
            [
                ['something' => 'false'],
                ':something is "false"',
                true
            ],
            [
                ['something' => 1],
                ':something is true',
                true
            ],
            [
                ['something' => 0],
                ':something is false',
                true
            ],
            [
                ['something' => 1.1],
                ':something is less than 1.2',
                true
            ],
            [
                ['something' => 1.3],
                ':something is greater or equal to 1.2',
                true
            ],
            [
                ['something' => 'The Rolling Stones'],
                ':something is "The Rolling Stones"',
                true
            ],
            [
                ['something' => 'The Rolling Stones'],
                ':something is \'The Rolling Stones\'',
                true
            ],
            [
                ['something' => 'The Rolling Stones'],
                ":something is 'The Rolling Stones'",
                true
            ],
            [
                ['something' => 'The Cardigans'],
                ':something is not "The Cure"',
                true
            ],
            [
                ['something' => 'The Cardigans'],
                ':something is not \'The Cure\'',
                true
            ],
            [
                ['something' => 'The Cardigans'],
                ":something is not 'The Cure'",
                true
            ],
            [
                ['something' => 'Pink Floyd'],
                ':something isn"t "Deep Purple"',
                true
            ],
            [
                ['something' => 'Pink Floyd'],
                ':something isn\'t \'Deep Purple\'',
                true
            ],
            [
                ['something' => 'Pink Floyd'],
                ":something isn't 'Deep Purple'",
                true
            ],
            [
                ['number' => 'Pink Floyd'],
                ":number in ['Deep Purple','Pink Floyd']",
                true
            ],
            [
                ['number' => 3],
                ":number contained in [1,2,3,4]",
                true
            ],
            [
                ['number' => '34'],
                ":number contained in [1,2,'34',3,4]",
                true
            ],
            [
                ['numbers' => [1, 2, 3]],
                "3 in :numbers",
                true
            ],
            [
                ['strings' => ['the', 'rolling', 'stones']],
                "'rolling' in :strings",
                true
            ],
            [
                ['strings' => ['the', 'rolling', 'stones']],
                "'potato' in :strings",
                false
            ]
        ];
    }
}