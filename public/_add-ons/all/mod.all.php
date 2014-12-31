<?php
/**
 * Modifier_all
 * filter to strip out specific string from content 
 * used in conjunction with excerpt filter 
 * const 'marker' should be the same as in mod.excerpt.php
 * @author  Clay Harmon
 * 
 */
class Modifier_all extends Modifier
{
    const marker = "[->]";


    public function index($string, $parameters=array()) {
        
        $string = str_replace(self::marker,"",$string);
        
        return $string;
    }
}