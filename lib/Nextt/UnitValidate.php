<?php
/**
 *
 * Validation of value from origin input
 *
 * @author	Owen Wang(lesorb@gmail.com)
 * @date	2013-10-30
 */

namespace Nextt;

class UnitValidate {
	
	private static $_instance = null ;
	
    const SKIP_ON_FAILED = 'skip_on_failed';
	
    const SKIP_OTHERS    = 'skip_others';
   
    const PASSED         = true;
   
    const FAILED         = false;
   
    const CHECK_ALL      = true;
	
	/**
     * @var array
     */
    protected $_locale;
    
    public function __construct() {
		$this->_locale = localeconv();
	}
    
	/**
	 * @return Next_Unit_Validate
	 */
	public static function getInstance() {
		if (! (self::$_instance instanceof UnitValidate) )
			self::$_instance = new UnitValidate();
		return self::$_instance;
	}
	
	public function not_null_string($value) {
		return !empty($value) && is_string($value);
	}	
	public function not_null_array($value) {
		return !empty($value) && is_array($value);
	}	

	/*
	 * validate($value, 'max', 5)) <==> validateByArgs('max', array($value, 5));
	 * 
	 * validate($value, 'between', 1, 5) <==> validateByArgs('between', array($value, 1,5));
	 * 
	 * validate($value, 'custom_callback', $args) <==> validateByArgs('custom_callback', array($value, $args));
	 */
	public function validate($value, $validation){
		$args = func_get_args();
		unset($args[1]);
        $result = $this->validateByArgs($validation, $args);
        return (bool)$result;
	}
	
	public function validateByArgs($validation, array $args){
		//if (empty($validation)) return null ;
		$method = null ;
		if ($this->not_null_string($validation)){
			if (method_exists($this, $validation))
				$method = array(& $this, $validation);
			elseif (strpos($validation, '::') && is_callable($validation))
				$method = explode('::', $validation);
			elseif (is_callable($validation))
				$method = $validation ;
		}		
		elseif ($this->not_null_array($validation) && is_callable($validation)){
			$method = $validation ;
		}
		return $method ? call_user_func_array($method, $args): null;
	}
	
	/**
     *
     * Methods for a value of applying a set of validation rules, and returns the final result
     * This set of validation rules as long as there is a validation failure, will return false
     *
     * example：
     * validateBatch($value, array(
     *         array('is_int'),
     *         array('between', 2, 6),
     * ));
     *
     *
     * @param mixed $value
     * @param array $validations
     * @param boolean $check_all
     * @param mixed $failed
     *
     * @return boolean
     */
	public function validateBatch($value, array $validations, $check_all = false, & $failed = null){
		$result = true;$failed = array();
		foreach ($validations as $validation){
			$rule = $validation[0]; // eg. is_int
			$validation[0] = $value; 
			$ret = $this->validateByArgs($rule,$validation);
			
            if ($ret === self::SKIP_OTHERS) {
                return $result;
            }

            if ($ret === self::SKIP_ON_FAILED) {
                $check_all = false;
                continue;
            }

            if ($ret)
				continue;
			
            $failed[] = $rule;
            $result = $result && $ret;

            if (!$result && !$check_all) {
				return false;
			}
		}
		return (bool)$result;
	}
	
	/**
     * if equal empty(null)
     */
    public function skip_empty($value) {
        return (strlen($value) == 0) ? self::SKIP_OTHERS : true;
    }

    /**
     * if nulll then skip ...
     */
    public function skip_null($value) {
        return (is_null($value)) ? self::SKIP_OTHERS : true;
    }

    /**
     * 
     */
    public function skip_on_failed() {
        return self::SKIP_ON_FAILED;
    }
	
	
	/**
     * use regexgual
     */
	public function regex($value, $regxp) {
        return preg_match($regxp, $value) > 0;
    }
    
    /**
     * equal 
     */
    public function equal($value, $test) {
        return $value == $test && strlen($value) == strlen($test);
    }

    /**
     * not equal
     */
    public function not_equal($value, $test) {
        return $value != $test || strlen($value) != strlen($test);
    }

    /**
     * full equal
     */
    public function same($value, $test) {
        return $value === $test;
    }

    /**
     * not full equal
     */
    public function not_same($value, $test) {
        return $value !== $test;
    }

    /**
     * check length of character
     */
    public function strlen($value, $len) {
        return strlen($value) == (int)$len;
    }

    /**
     * min length
     */
    public function min_length($value, $len) {
        return strlen($value) >= $len;
    }

    /**
     * max length
     */
    public function max_length($value, $len) {
        return strlen($value) <= $len;
    }

    /**
     * min value
     */
    public function min($value, $min) {
        return $value >= $min;
    }

    /**
     * max value
     */
    public function max($value, $max) {
        return $value <= $max;
    }

    /**
     * between
     *
     * @param mixed $value
     * @param int|float $min
     * @param int|float $max
     * @param boolean $inclusive
     *
     * @return boolean
     */
    public function between($value, $min, $max, $inclusive = true) {
        if ($inclusive) {
            return $value >= $min && $value <= $max;
        } else {
            return $value > $min && $value < $max;
        }
    }

    /**
     * >
     */
    public function greater_than($value, $test) {
        return $value > $test;
    }

    /**
     * >=
     */
    public function greater_or_equal($value, $test) {
        return $value >= $test;
    }

    /**
     * <
     */
    public function less_than($value, $test) {
        return $value < $test;
    }

    /**
     * <=
     */
    public function less_or_equal($value, $test) {
        return $value <= $test;
    }

    /**
     * not null
     */
    public function not_null($value) {
        return !is_null($value);
    }

    /**
     *
     */
    public function not_empty($value,$skipZeroString=false) {
    	if ($skipZeroString && $value === '0') return true ;
    	return !empty($value);
    }

    /**
     *
     */
    public function is_type($value, $type) {
        return gettype($value) == $type;
    }

    /**
     *
     */
    public function is_alnum($value) {
		return ctype_alnum($value);
	}

    /**
     *
     */
    public function is_alpha($value) {
		return ctype_alpha($value);
	}

    /**
     *
     */
    public function is_alnumu($value) {
		return preg_match('/[^a-zA-Z0-9_]/', $value) == 0;
	}
    
    public function is_chinese($value) {
		return preg_match( "/^[\x80-\xff]+/",$value,$match) && ($match[0] == $value);
	}

    /**
     * 
     */
    public function is_cntrl($value) {
		return ctype_cntrl($value);
	}

    /**
     * 
     */
    public function is_digits($value) {
		return ctype_digit($value);
	}

    /**
     * If the character is visible
     */
    public function is_graph($value) {
		return ctype_graph($value);
	}

    /**
     * Whether it is all lowercase
     */
    public function is_lower($value) {
		return ctype_lower($value);
	}

    /**
     * Whether the printable character
     */
    public function is_print($value) {
		return ctype_print($value);
	}

    /**
     * Whether punctuation
     */
    public function is_punct($value) {
		return ctype_punct($value);
	}

    /**
     * Whether it is a blank character
     */
    public function is_whitespace($value) {
		return ctype_space($value);
	}

    /**
     * Whether it is all uppercase
     */
    public function is_upper($value) {
		return ctype_upper($value);
	}

    /**
     * Whether it is a hexadecimal number
     */
    public function is_xdigits($value) {
		return ctype_xdigit($value);
	}

    /**
     * Whether it is an ASCII character
     */
    public function is_ascii($value) {
		return preg_match('/[^\x20-\x7f]/', $value) == 0;
	}
	
	/**
	* Whether it is the octal value
	*/
    public function is_octal($value) {
		return preg_match('/0[0-7]+/', $value);
	}

    /**
     * Whether it is a binary value
     */
    public function is_binary($value) {
		return preg_match('/[01]+/', $value);
	}

    /**
     * Whether it is whether the e-mail address
     */
    public function is_email($value) {
		return preg_match('/^[a-z0-9]+[._\-\+]*@([a-z0-9]+[-a-z0-9]*\.)+[a-z0-9]+$/i', $value);
	}

    /**
     * Whether it is the date( yyyy/mm/dd yyyy-mm-dd )
     */
    public function is_date($value) {
        if (strpos($value, '-') !== false) {
        	$p = '-';
        } elseif (strpos($value, '/') !== false) { 
        	$p = '\/';
        } else { 
        	return false;
		}

        if (preg_match('/^\d{4}' . $p . '\d{1,2}' . $p . '\d{1,2}$/', $value)) {
            $arr = explode($p, $value);
            if (count($arr) < 3) { 
				return false;
			}

            list($year, $month, $day) = $arr;

            return checkdate($month, $day, $year);

        } else {
            return false;
		}
    }

    /**
     * Is It Time (hh:mm:ss)
     */
    public function is_time($value) {
        $parts = explode(':', $value);
		$count = count($parts);
        
		if ($count != 2 || $count != 3) {
			return false;
		}
        
		if ($count == 2) {
			$parts[2] = '00';
		}

        $test = strtotime($parts[0] . ':' . $parts[1] . ':' . $parts[2]);
        if ($test === - 1 || $test === false || date('H:i:s') != $value) {
            return false;
		}

        return true;
    }

    /**
     * Whether it is a date + time
     */
    public function is_datetime($value) {
        $test = strtotime($value);
        if ($test === false || $test === - 1) {
            return false;
		}
        return true;
    }

    /**
     * Is an integer
     */
    public function is_int($value) {
        $value = str_replace($this->_locale['decimal_point'], '.', $value);
        $value = str_replace($this->_locale['thousands_sep'], '', $value);

        return strval(intval($value)) == $value;
    }
	
    /**
     * Whether it is a float
     */
    public function is_float($value) {
        $value = str_replace($this->_locale['decimal_point'], '.', $value);
        $value = str_replace($this->_locale['thousands_sep'], '', $value);
		
        return strval(floatval($value)) == $value ;
    }

    /**
     * Whether it is an IPv4 address (format: a.b.c.h)
     */
    public function is_ipv4($value) {
		$test = ip2long($value);
		return $test !== - 1 && $test !== false;
	}

    /**
     * Whether it is the Internet domain name名
     */
    public function is_domain($value) {
		return preg_match('/[a-z0-9\.]+/i', $value);
	}
    
    /**
     * Verify that the value is not being injection attacks
     */
	public function notHackerDefense($value){
		$notAllowedExp = array(	
			'/<[^>]*script.*\"?[^>]*>/','/<[^>]*style.*\"?[^>]*>/',
			'/<[^>]*object.*\"?[^>]*>/','/<[^>]*iframe.*\"?[^>]*>/',
			'/<[^>]*applet.*\"?[^>]*>/','/<[^>]*window.*\"?[^>]*>/',
			'/<[^>]*docuemnt.*\"?[^>]*>/','/<[^>]*cookie.*\"?[^>]*>/',
			'/<[^>]*meta.*\"?[^>]*>/','/<[^>]*alert.*\"?[^>]*>/',
			'/<[^>]*form.*\"?[^>]*>/','/<[^>]*php.*\"?[^>]*>/','/<[^>]*img.*\"?[^>]*>/'
		);
		foreach ($notAllowedExp as $exp) {
			if ( preg_match($exp, $value) ) {
				return false;
			}
		}
		return true;
	}
}
