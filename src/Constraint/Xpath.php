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
use DOMXPath;
use InvalidArgumentException;
use JsonSerializable;
use PHPUnit\Framework\Constraint\Constraint as PHPUnitConstraint;
use PHPUnit\Xpath\Import\JsonToXml;
use PHPUnit\Xpath\Helper\InvalidArgumentHelper;
use stdClass;
use function is_array;

/**
 * Constraint superclass for constraints that uses Xpath expressions
 *
 * The Xpath expression and namespaces are passed in the constructor.
 */
abstract class Xpath extends PHPUnitConstraint
{
    /**
     * @var string
     */
    protected $_expression;

    /**
     * @var array
     */
    private $_namespaces;

    public function __construct(string $expression, array $namespaces = [])
    {
        $this->_expression = $expression;
        $this->_namespaces = $namespaces;
    }

    /**
     * Evaluate the xpath expression on the given context and
     * return the result.
     *
     * @param mixed $context
     *
     * @return DOMNodeList|bool|string|float
     */
    protected function evaluateXpathAgainst($context)
    {
        if ($context instanceof DOMNode) {
            $document = $context instanceof DOMDocument ? $context : $context->ownerDocument;
        } else {
            $importer = new JsonToXml($context);
            $document = $importer->getDocument();
            $context  = $document->documentElement;
        }

        $xpath = new DOMXPath($document);
    
        foreach ($this->_namespaces as $prefix => $namespaceURI) {
            $xpath->registerNamespace($prefix, $namespaceURI);
        }

        return $xpath->evaluate($this->_expression, $context, false);
    }

    /**
     * @param mixed $context
     * @param int   $argument
     *
     * @throws InvalidArgumentException
     */
    public static function isValidContext($context, int $argument): void
    {
        if (
        !(
            $context instanceof DOMNode ||
            is_array($context) ||
            $context instanceof stdClass ||
            $context instanceof JsonSerializable
        )
        ) {
            throw InvalidArgumentHelper::factory(
                $argument,
                '\\DOMNode, array, \\stdClass or \\JsonSerializable'
            );
        }
    }
}
