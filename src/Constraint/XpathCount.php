<?php
/*
 * This file is part of phpunit-xpath-assertions.
 *
 * (c) Thomas Weinert <thomas@weinert.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Xpath\Constraint;

use function sprintf;

/**
 * Constraint that asserts that the result of an Xpath
 * expression is a node list with the specific count of nodes.
 * The Xpath expression and namespaces are passed in the constructor.
 */
class XpathCount extends Xpath
{
    private $_expectedCount;
    private $_actualCount = 0;

    public function __construct(int $expectedCount, string $expression, array $namespaces = [])
    {
        parent::__construct($expression, $namespaces);
        
        $this->_expectedCount = $expectedCount;
    }

    /**
     * @param mixed $other Value or object to evaluate.
     *
     * @return bool
     */
    protected function matches($other): bool
    {
        $actual             = $this->evaluateXpathAgainst($other);
        $this->_actualCount = $actual->length;

        return $this->_actualCount === $this->_expectedCount;
    }

    protected function failureDescription($other): string
    {
        return sprintf(
            'actual node count %d matches expected count %d',
            $this->_actualCount,
            $this->_expectedCount
        );
    }

    public function toString(): string
    {
        return sprintf(
            'count matches %d',
            $this->_expectedCount
        );
    }
}
