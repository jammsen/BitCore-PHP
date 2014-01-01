<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class VarHelper implements IVar {

    public static function __tcall($option, $var, $options = array()) {
        return filter_var($var, $option, $options);
    }

	
	public static function isBase64($var) {
 		return preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s); 
	}

    public static function isInt($var, $min = null, $max = null, $options = array()) {
        if ($min != null)
            $options['options']['min_range'] = $min;
        if ($max != null)
            $options['options']['max_range'] = $max;

        return self::__tcall(FILTER_VALIDATE_INT, $var, $options);
    }

    public static function isBool($var, $options = array()) {
        return self::__tcall(FILTER_VALIDATE_BOOLEAN, $var, $options);
    }

    public static function isFloat($var, $sep = null, $options = array()) {
        if ($sep != null)
            $options['options'] = array("decimal" => $sep);

        return self::__tcall(FILTER_VALIDATE_FLOAT, $var, $options);
    }

    public static function isURL($var, $flags = null) {
        return self::__tcall(FILTER_VALIDATE_URL, $var, $flags);
    }

    public static function isIP($var, $flags = null) {
        return self::__tcall(FILTER_VALIDATE_IP, $var, $flags);
    }

    public static function isEmail($var) {
        return self::__tcall(FILTER_VALIDATE_EMAIL, $var);
    }

    public static function getInt($var) {
        return self::__tcall(FILTER_SANITIZE_NUMBER_INT, $var);
    }

    public static function getFloat($var, $flags = null) {
        return self::__tcall(FILTER_SANITIZE_NUMBER_FLOAT, $var, $flags);
    }
    public static function getString($var, $flags = null) {
        return self::__tcall(FILTER_SANITIZE_STRING, $var, $flags);
    }

    
    
    public static function getURL($var) {
        return self::__tcall(FILTER_SANITIZE_URL, $var);
    }

    public static function getEmail($var) {
        return self::__tcall(FILTER_SANITIZE_EMAIL, $var);
    }

    public static function getBlank($var, $filter, $option = null) {
        return self::__tcall($filter, $var, $option);
    }

    public static function valCallback($var, $callback) {
        return self::__tcall(FILTER_CALLBACK, $var, array('options' => $callback));
    }

    public static function valRegEx($var, $regex, $options = array()) {
        $options['options'] = array("regexp" => $regex);
        return self::__tcall(FILTER_VALIDATE_REGEXP, $var, $options);
    }

}