<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Xpath\Assert;

require_once __DIR__.'/../vendor/autoload.php';

class MyProjectExampleTest extends TestCase
{
    use Assert;

    public function testChildElementExistsInDocument()
    {
        $document = new DOMDocument();
        $document->loadXML('<root><child>TEXT</child></root>');

        self::assertXpathMatch('//child', $document);
    }

    public function testCompareChildElementFromDocument()
    {
        $document = new DOMDocument();
        $document->loadXML('<root><child>TEXT</child></root>');

        self::assertXpathEquals('<child>TEXT</child>', '//child', $document);
    }
}
