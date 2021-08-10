<?php
/*
 * This file is part of phpunit-xpath-assertions.
 *
 * (c) Thomas Weinert <thomas@weinert.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\PHPUnit\Xpath\Constraint;

use PHPUnit\Xpath\Constraint\XpathMatch;
use Tests\PHPUnit\Xpath\TestCase;

class XpathMatchTest extends TestCase
{
    /**
     * @dataProvider provideMatchingExpressions
     */
    public function testXpathMatchExpectTrue(string $expression, array $namespaces = []): void
    {
        $constraint = new XpathMatch($expression, $namespaces);
        $this->assertTrue($constraint->evaluate($this->getXMLDocument(), '', true));
    }

    public function provideMatchingExpressions(): array
    {
        return [
           ['/root'],
           ['//child'],
           ['//test:child', ['test' => 'urn:dummy']],
           ['//child = "One"'],
           ['string(//child)'],
           ['count(//test:child)', ['test' => 'urn:dummy']],
       ];
    }
    /**
     * @dataProvider provideNonMatchingExpressions
     */
    public function testXpathMatchExpectFalse(string $expression, array $namespaces = []): void
    {
        $constraint = new XpathMatch($expression, $namespaces);
        $this->assertFalse($constraint->evaluate($this->getXMLDocument(), '', true));
    }

    public function provideNonMatchingExpressions(): array
    {
        return [
            ['/child'],
            ['//non-existing'],
            ['//test:child', ['test' => 'urn:non-existing']],
            ['//child = "NON-EXISTING"'],
            ['string(//non-existing)'],
            ['count(//test:child)', ['test' => 'urn:non-existing']],
        ];
    }
}
