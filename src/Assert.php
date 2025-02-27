<?php
/*
 * This file is part of phpunit-xpath-assertions.
 *
 * (c) Thomas Weinert <thomas@weinert.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Xpath;

use ArrayAccess;
use DOMNode;
use JsonSerializable;
use PHPUnit\Framework\Exception;
use PHPUnit\Xpath\Constraint\Xpath;
use PHPUnit\Xpath\Constraint\XpathCount;
use PHPUnit\Xpath\Constraint\XpathEquals;
use PHPUnit\Xpath\Constraint\XpathMatch;
use PHPUnit\Xpath\Helper\InvalidArgumentHelper;
use stdClass;
use function is_array;
use function is_int;
use function is_string;

/**
 * Trait that with Xpath based assertions
 */
trait Assert
{
    /**
     * Asserts that DOM node matches a specified Xpath expression.
     *
     * @param string                                  $expression
     * @param DOMNode|array|stdClass|JsonSerializable $context
     * @param array|ArrayAccess                       $namespaces
     * @param string                                  $message
     *
     * @throws Exception
     */
    public static function assertXpathMatch(string $expression, $context, $namespaces = [], $message = '')
    {
        Xpath::isValidContext($context, 2);
        
        if (!(is_array($namespaces) || $namespaces instanceof ArrayAccess)) {
            throw InvalidArgumentHelper::factory(
                3,
                'array or \\ArrayAccess'
            );
        }

        $constraint = new XpathMatch($expression, $namespaces);
        static::assertThat($context, $constraint, $message);
    }

    /**
     * Asserts that DOM node matches a specified Xpath expression.
     *
     * @param int|string                              $expected
     * @param string                                  $expression
     * @param DOMNode|array|stdClass|JsonSerializable $context
     * @param array|ArrayAccess                       $namespaces
     * @param string                                  $message
     *
     * @throws Exception
     */
    public static function assertXpathCount($expected, string $expression, $context, $namespaces = [], $message = '')
    {
        if (!(is_int($expected) || is_string($expected))) {
            throw InvalidArgumentHelper::factory(
                1,
                'integer or string'
            );
        }
        
        Xpath::isValidContext($context, 3);

        if (!(is_array($namespaces) || $namespaces instanceof ArrayAccess)) {
            throw InvalidArgumentHelper::factory(
                4,
                'array or \\ArrayAccess'
            );
        }

        $constraint = new XpathCount($expected, $expression, $namespaces);
        static::assertThat($context, $constraint, $message);
    }

    /**
     * Asserts that DOM nodes returned by an Xpath expresion are equal to the expected
     *
     * @param mixed                                   $expected
     * @param string                                  $expression
     * @param DOMNode|array|stdClass|JsonSerializable $context
     * @param array|ArrayAccess                       $namespaces
     * @param string                                  $message
     *
     * @throws Exception
     */
    public static function assertXpathEquals($expected, string $expression, $context, $namespaces = [], $message = '')
    {
        Xpath::isValidContext($context, 3);
        
        if (!(is_array($namespaces) || $namespaces instanceof ArrayAccess)) {
            throw InvalidArgumentHelper::factory(
                4,
                'array or ArrayAccess'
            );
        }

        $constraint = new XpathEquals($expected, $expression, $namespaces);
        static::assertThat($context, $constraint, $message);
    }
}
