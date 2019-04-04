<?php

if (!function_exists('dump'))
{
    /**
     * @param $expression
     * @param array ...$expressions
     */
    function dump(&$expression, &...$expressions)
    {
        \Kiss\Dumper::dump($expression);

        foreach ($expressions as &$expression)
            \Kiss\Dumper::dump($expression);
    }
}

