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

}