<?php
/**
 * Modifier_excerpt
 * returns entire string up to but not including
 * string defined in const 'marker' below
 *  
 * @author  Clay Harmon
 *   
 */
class Modifier_excerpt extends Modifier
{
    const marker = '[->]';
    const more_string = '';

    public function index($value, $parameters=array()) {
        
        if(strpos($value,self::marker)) //test for existence of marker in string and just return original if false
        {	
	        $value = substr($value,0,strpos($value, self::marker)).self::more_string;
	        
	        return $value;
    	}
    	else return $value;
    }
}
