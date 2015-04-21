<?php

namespace Searcher;


class StringUtils {

    public static function toLower($string){
        return mb_strtolower($string);
    }
}