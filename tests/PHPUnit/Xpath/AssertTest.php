<?php
/*
 * This file is part of phpunit-xpath-assertions.
 *
 * (c) Thomas Weinert <thomas@weinert.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\PHPUnit\Xpath;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Xpath\Assert;

class AssertTest extends TestCase
{
    use Assert;

    public function testAssertXpathMatchSuccess(): void
    {
        self::assertXpathMatch('//child', $this->getXMLDocument());
    }

    public function testAssertXpathMatchFailure(): void
    {
        $this->expectException(AssertionFailedError::class);
        
        if (method_exists($this, 'expectExceptionMessageRegExp')) {
            $this->expectExceptionMessageRegExp(
                '(Failed asserting that DOMDocument Object .* matches expression: //non-existing\\.)'
            );
        } else {
            $this->expectExceptionMessageMatches(
                '(Failed asserting that DOMDocument Object .* matches expression: //non-existing\\.)'
            );
        }
        self::assertXpathMatch('//non-existing', $this->getXMLDocument());
    }

    public function testAssertXpathMatchAgainstJsonExpectingSuccess(): void
    {
        self::assertXpathMatch('//child', ['child' => 'Text']);
    }

    public function testAssertXpathCountSuccess(): void
    {
        self::assertXpathCount(1, '//child', $this->getXMLDocument());
    }

    public function testAssertXpathCountFailure(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage(
            'Failed asserting that actual node count 0 matches expected count 1.'
        );
        self::assertXpathCount(1, '//non-existing', $this->getXMLDocument());
    }

    public function testAssertXpathEqualsSuccess(): void
    {
        $expected = $this->getXMLDocument()->documentElement->firstChild;
        self::assertXpathEquals($expected, '//child', $this->getXMLDocument());
    }

    public function testAssertXpathEqualsWithNamespaceSuccess(): void
    {
        $expected = $this->getXMLDocument()->documentElement->childNodes;
        self::assertXpathEquals(
            $expected,
            '//child|//x:child',
            $this->getXMLDocument(),
            ['x' => 'urn:dummy']
        );
    }

    public function testAssertXpathEqualsWithStringExpectingSuccess(): void
    {
        self::assertXpathEquals('One', 'string(//child)', $this->getXMLDocument());
    }

    public function testAssertXpathEqualsFailure(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage(
            'Failed asserting that two DOM structures are equal.'
        );
        $expected = $this->getXMLDocument()->documentElement->lastChild;
        self::assertXpathEquals($expected, '//child', $this->getXMLDocument());
    }
}
