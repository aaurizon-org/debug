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

        \Kiss\Debug::dump($expression);

        foreach ($expressions as &$expression)
            \Kiss\Debug::dump($expression);

        exit;
    }
}
