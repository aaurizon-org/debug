<?php

if (!function_exists('dump'))
{
    /**
     * @param $expression
     * @param array ...$expressions
     */
    function dump(&$expression, &...$expressions)
    {
        \Kiss\Debug::dump($expression);

        foreach ($expressions as &$expression)
            \Kiss\Debug::dump($expression);
    }
}
