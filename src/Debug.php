<?php

namespace Kiss;

/**
 * Class Debug
 */
abstract class Debug
{
    protected const LABEL_STYLE   = 'color: #9E9E9E';
    protected const KEYWORD_STYLE = 'color: #64B5F6 ; font-weight: bold';
    protected const NUMBER_STYLE  = 'color: #64B5F6';
    protected const FLOAT_STYLE   = 'color: #64B5F6';
    protected const STRING_STYLE  = 'color: #4CAF50';
    protected const COMMENT_STYLE = 'color: #607D8B ; font-style: italic';
    protected const UNKNWON_STYLE = 'color: #F44336 ; font-style: italic';

    /**
     * @param $var
     * @param string|null $label
     * @param bool|null $open
     */
    public static function dump(&$var, string $label = null, bool $open = null)
    {
        if (PHP_SAPI === 'cli')
        {
            var_dump($var);
            return;
        }

        echo '<div dir="ltr" style="font-family: monospace">';
        static::print($var, $label, $open);
        echo '</div>';
    }

    /**
     * @param $var
     * @param string|null $label
     * @param bool $open (true = open all) (false = close all) (null = auto, only first open)
     * @param int $depth (0 = open first) (1 = close all) (-127 = open the first 128)
     *
     * @todo STRING STYLE white-space: nowrap ; overflow: hidden ; text-overflow: ellipsis (think about it)
     * @todo Catch float INF number is_infinite
     */
    protected static function print(&$var, string $label = null, bool $open = null, $depth = 0)
    {
        $label_html = $label === null ? '' : '<span style="'.static::LABEL_STYLE.'">'.$label.': </span>';

        switch (gettype($var))
        {
            // NULL
            case 'NULL':
                echo '<div>'.$label_html.'<span style="'.static::KEYWORD_STYLE.'">null</span></div>';
                break;

            // Scalar bool
            case 'boolean':
                echo '<div>'.$label_html.'<span style="'.static::KEYWORD_STYLE.'">'.($var===true?'true':'false').'</span></div>';
                break;

            // Scalar int
            case 'integer':
                echo '<div>'.$label_html.'<span style="'.static::NUMBER_STYLE.'">'.$var.'</span></div>';
                break;

            // Scalar float (double)
            case 'double':
                echo '<div>'.$label_html.'<span style="'.static::FLOAT_STYLE.'">'.(is_nan($var) ? 'NaN' : (strpos($var, '.') !== false ? $var : $var.'.0')).'</span></div>';
                break;

            // Scalar string
            case 'string':
                echo '<div>';
                echo $label_html.'<span style="'.static::STRING_STYLE.'">"'.(mb_strlen($var) <= 62 ? htmlspecialchars($var) : substr(htmlspecialchars($var), 0, 62).'...').'"</span>';
                echo ' <span style="'.static::COMMENT_STYLE.'">(length='.mb_strlen($var).')</span>';
                echo '</div>';
                break;

            // Compound array
            case 'array':
                if (isset($var['__dump_recursion']) OR array_key_exists('__dump_recursion', $var))
                {
                    echo '<div>'.$label_html.'<span style="'.static::COMMENT_STYLE.'">[ *RECURSION* ]</span></div>';
                }
                else if (empty($var))
                {
                    echo '<div>'.$label_html.'<span style="'.static::COMMENT_STYLE.'">array</span></div>';
                }
                else
                {
                    echo $open === false ? '<details style="margin-left: -1em;">' : '<details open>'; // open details also for negative numbers
                    echo '<summary>'.$label_html.'<span style="'.static::COMMENT_STYLE.'">array (size='.count($var).')</span></summary>';
                    echo '<div style="margin-left: 1em; padding-left: 30px; border-left: 1px dotted rgba(158, 158, 158, 0.25)">';

                    $var['__dump_recursion'] = true;
                    foreach ($var as $key => &$value)
                    {
                        if ($key !== '__dump_recursion')
                        {
                            static::print($value, $key, $open===true?:false, $depth+1);
                        }
                    }
                    unset($var['__dump_recursion']);

                    echo '</div>';
                    echo '</details>';
                }
                break;

            // Compound object
            case 'object':
                static $__object_recursion = [];
                if (isset($__object_recursion[$hash = 'u'.spl_object_hash($var)]) OR array_key_exists($hash, $__object_recursion))
                {
                    echo '<div>'.$label_html.'<span style="'.static::COMMENT_STYLE.'">{ *RECURSION* }</span></div>';
                }
                else if (empty(($reflection = new \ReflectionObject($var))->getProperties()))
                {
                    echo '<div>'.$label_html.'<span style="'.static::COMMENT_STYLE.'">object ('.$reflection->getName().')</span></div>';
                }
                else
                {
                    echo $open === false ? '<details style="margin-left: -1em">' : '<details open>'; // open details also for negative numbers
                    echo '<summary>'.$label_html.'<span style="'.static::COMMENT_STYLE.'">object ('.$reflection->getName().')</span></summary>';
                    echo '<div style="margin-left: 2px; padding-left: 30px; border-left: 1px dotted rgba(158, 158, 158, 0.25)">';

                    $__object_recursion[$hash] = true;
                    foreach ($reflection->getProperties() as $property)
                    {
                        $property->setAccessible(true);
                        $value = $property->getValue($var);
                        static::print($value, $property->getName(), $open===true?:false, $depth+1);
                    }
                    unset($__object_recursion[$hash]);

                    echo '</div>';
                    echo '</details>';
                }
                break;

            // Resource
            case 'resource':
                echo '<div>'.$label_html.'<span style="'.static::COMMENT_STYLE.'">resource ('.get_resource_type($var).')</span></div>';
                break;

            // Unknown
            default:
                echo '<div>'.$label_html.'<span style="'.static::UNKNWON_STYLE.'">'.gettype($var).'</span></div>';
                break;
        }
    }
}
