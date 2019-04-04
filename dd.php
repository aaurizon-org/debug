<?php

if (!function_exists('dd'))
{
    /**
     * @param $expression
     * @param array ...$expressions
     */
    function dd(&$expression, &...$expressions)
    {
        while (ob_end_clean());

        \Kiss\Dumper::dump($expression);

        foreach ($expressions as &$expression)
            \Kiss\Dumper::dump($expression);

        exit;
    }
}
