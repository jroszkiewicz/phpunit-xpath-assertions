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

use DOMDocument;
use DOMNode;
use DOMNodeList;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\IsFalse;
use PHPUnit\Framework\Constraint\IsTrue;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Xpath\Import\JsonToXml;
use SebastianBergmann\Comparator\ComparisonFailure;
use Traversable;
use function is_array;
use function is_bool;
use function is_scalar;
use function is_string;
use function trim;

/**
 * Constraint that asserts that the result of an Xpath
 * expression is equal to a provided document.
 *
 * The Xpath expression and namespaces are passed in the constructor.
 */
class XpathEquals extends Xpath
{
    private $_value;

    public function __construct($value, string $expression, array $namespaces = [])
    {
        parent::__construct($expression, $namespaces);
        
        $this->_value = $value;
    }

    /**
     * @param mixed  $other        Value or object to evaluate.
     * @param string $description
     * @param bool   $returnResult
     *
     * @throws ExpectationFailedException
     */
    public function evaluate($other, string $description = '', bool $returnResult = false): ?bool
    {
        $actual = $this->evaluateXpathAgainst($other);
        
        try {
            if (is_scalar($actual)) {
                if (is_bool($actual)) {
                    $constraint = $this->_value ? new IsTrue() : new IsFalse();
                } else {
                    $constraint = new IsEqual($this->_value);
                }

                return $constraint->evaluate($actual, $description, $returnResult);
            }
            
            if (is_string($this->_value)) {
                $this->_value = $this->loadXmlFragment($this->_value);
            } elseif (!$this->isNodeOrNodeList($this->_value)) {
                $importer     = new JsonToXml($this->_value);
                $this->_value = $importer->getDocument()->documentElement->childNodes;
            }
            
            $expectedAsString = $this->nodesToText($this->_value);
            $actualAsString   = $this->nodesToText($actual);

            if ($expectedAsString !== $actualAsString) {
                throw new ComparisonFailure(
                    $this->_value,
                    $actual,
                    $expectedAsString,
                    $actualAsString,
                    false,
                    "Failed asserting that two DOM structures are equal.\n"
                );
            }

            return true;
        } catch (ComparisonFailure $f) {
            if ($returnResult) {
                return false;
            }

            throw new ExpectationFailedException(
                trim($description . "\n" . $f->getMessage()),
                $f
            );
        }
    }

    private function isNodeOrNodeList($value): bool
    {
        return
            ($value instanceof DOMNodeList || $value instanceof DOMNode) ||
            (is_array($value) && isset($value[0]) && $value[0] instanceof DOMNode);
    }

    private function nodesToText($nodes)
    {
        $fragmentString = '';
        
        if ($nodes instanceof DOMNode) {
            $fragmentString = $nodes->C14N();
        } elseif (is_iterable($nodes)) {
            $fragmentString = '';
            
            foreach ($nodes as $node) {
                $fragmentString .= $node->C14N();
            }
        }
        
        $document               = new DOMDocument();
        $document->formatOutput = true;
        $document->normalizeDocument();
        $fragment = $document->createDocumentFragment();
        $fragment->appendXML($fragmentString);

        return $document->saveXML($fragment);
    }

    private function loadXmlFragment($xmlString): DOMDocument
    {
        $document = new DOMDocument();
        $fragment = $document->createDocumentFragment();
        $fragment->appendXML($xmlString);
        $document->appendChild($fragment);

        return $document;
    }

    public function toString(): string
    {
        return 'is equal to nodes matched by: ' . $this->_expression;
    }
}
