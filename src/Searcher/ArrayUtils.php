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
        if (!is_array($array)) {
            return $default;
        }
        if (isset($array[$key])) {
            return $array[$key];
        }

        return self::getValueFromPath($array, $key, $default);
    }

    /**
     * @param array $array
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getArray($array, $key, $default = array())
    {
        $data = self::get($array, $key, $default);
        if (is_array($data)) {
            return $data;
        }

        return $default;
    }

    /**
     * @param array $array
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getScalar($array, $key, $default = "")
    {
        $data = self::get($array, $key, $default);
        if (is_scalar($data)) {
            return $data;
        }

        return $default;
    }

    /**
     * @param string $aPath
     * @return array
     */
    protected static function toPathKeys($aPath)
    {
        $parts = explode('.', $aPath);

        return $parts;
    }

    /**
     * @param array $array
     * @param string $aPath
     * @param null $default
     * @return mixed
     */
    protected static function getValueFromPath(array $array, $aPath, $default = null)
    {
        $pathKeys = self::toPathKeys($aPath);

        foreach ($pathKeys as $pathKey) {

            if (!isset($array[$pathKey])) {
                return $default;
            }
            $array = $array[$pathKey];
        }

        return $array;
    }

    /**
     * Test whether an array is a list
     *
     * A list is a collection of values assigned to continuous integer keys
     * starting at 0 and ending at count() - 1.
     *
     * For example:
     * <code>
     * $list = array('a', 'b', 'c', 'd');
     * $list = array(
     *     0 => 'foo',
     *     1 => 'bar',
     *     2 => array('foo' => 'baz'),
     * );
     * </code>
     *
     * @param  mixed $value
     * @param  bool  $allowEmpty    Is an empty list a valid list?
     * @return bool
     */
    public static function isList($value, $allowEmpty = false)
    {
        if (!is_array($value)) {
            return false;
        }
        if (!$value) {
            return $allowEmpty;
        }
        return (array_values($value) === $value);
    }
}