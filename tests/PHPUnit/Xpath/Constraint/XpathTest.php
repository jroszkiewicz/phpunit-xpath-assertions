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

use DOMNodeList;
use PHPUnit\Xpath\Constraint\Xpath;
use Tests\PHPUnit\Xpath\TestCase;

class XpathTest extends TestCase
{
    public function testEvaluateXpathAgainstWithDocument(): void
    {
        $constraint = new Xpath_TestProxy(
            '//child'
        );
        $actual = $constraint->evaluateXpathAgainst($this->getXmlDocument());
        $this->assertInstanceOf(DOMNodeList::class, $actual);
    }

    public function testEvaluateXpathAgainstWithNode(): void
    {
        $constraint = new Xpath_TestProxy(
            '//child'
        );
        $actual = $constraint->evaluateXpathAgainst($this->getXmlDocument()->documentElement);
        $this->assertInstanceOf(DOMNodeList::class, $actual);
    }

    public function testEvaluateXpathAgainstWithNodeAndNamespace(): void
    {
        $constraint = new Xpath_TestProxy(
            '//d:child', ['d' => 'urn:dummy']
        );
        $actual = $constraint->evaluateXpathAgainst($this->getXmlDocument()->documentElement);
        $this->assertInstanceOf(DOMNodeList::class, $actual);
        $this->assertCount(2, $actual);
    }
}

class Xpath_TestProxy extends Xpath
{
    public function evaluateXpathAgainst($context)
    {
        return parent::evaluateXpathAgainst($context);
    }
    
    public function matches($other): bool
    {
    }
    
    public function toString(): string
    {
        return '';
    }
}
