<?php

namespace PHPUnit\Xpath\Helper;

use InvalidArgumentException;

use function debug_backtrace;
use function gettype;
use function sprintf;

class InvalidArgumentHelper
{
    public static function factory($argument, $type, $value = null): InvalidArgumentException
    {
        $stack = debug_backtrace();
        
        return new InvalidArgumentException(sprintf('Argument #%d%sof %s::%s() must be a %s', $argument, $value !== null ? ' (' . gettype($value) . '#' . $value . ')' : ' (No Value) ', $stack[1]['class'], $stack[1]['function'], $type));
    }
}
