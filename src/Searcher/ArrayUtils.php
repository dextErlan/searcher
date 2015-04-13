<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 13.04.15
 * Time: 15:55
 */

namespace Searcher;


abstract class ArrayUtils
{
    /**
     * @param array $array
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($array, $key, $default = null)
    {
        if(!is_array($array)){
            return $default;
        }
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }
        return $default;
    }
}