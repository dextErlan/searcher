<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 16.04.15
 * Time: 17:01
 */

namespace Searcher;


class StringUtils {

    public static function toLower($string){
        return mb_strtolower($string);
    }
}