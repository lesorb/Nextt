<?php
/**
 * 
 *
 * @package	lib/Nextt
 * @author	Owen Wang(lesorb@gmail.com)
 * @date:	2013-10-30
 */

namespace Nextt;

class UnitCore {

	/**
	 *
	 * @var array
	 */
	private static $_object = array();
	
	/**
	 *
	 * @var array
	 */
	private static $_classesPath = array();
	
	/**
	 * 
	 * @param string	$className
	 * @param array		$initParams
	 * @return mixed
	 */
	static function getInstance($className,array $initParams=null) {
		
		if (!is_string($className))
			throw new Exception(sprintf('Type mismatch. $className expected is string, actual is "%s".',gettype($className)));
		
		$regkey = "__next::runtime#{$className}__"; 
		if (isset(self::$_objects[$regkey]) )
			return self::$_objects[$regkey];

		try {
			$_reflect = new \ReflectionClass($className);
		
			if ($initParams)
				$obj = $_reflect->newInstanceArgs($initParams);
			else
				$obj = $_reflect->newInstance();

			self::$_objects[$regkey] = $obj;

		}catch(\ReflectionException $ex){
			throw $ex;
			return null;
		}

        return $obj ;
	}
	
	/**
	 * print variable value ...
	 *
	 * @param mixed $vars
	 * @param string $label
	 * @param boolean $return
	 * @return string | null
	 */
	public static function dump($vars, $label = '', $return = false) {		
	    if (ini_get('html_errors')) {
	        $content = "<pre>\n";
	        if ($label != '') {
	            $content .= "<strong>{$label} :</strong>\n";
	        }
	        $content .= htmlspecialchars(print_r($vars, true));
	        $content .= "\n</pre>\n";
	    } else {
	        $content = $label . " :\n" . print_r($vars, true);
	    }

	    if ($return) { 
			return $content; 
		}
	    
	    echo $content;

	    return ;
	}

	public static function simp( $vars, $label = '',$endSeparator = '' ) {

		echo $label.' : '.$endSeparator.$endSeparator;
		foreach( $vars as $var ) {
			echo '-class : ' . $var['class'] .$endSeparator;
			if(isset($var['methods'])) {
				foreach( $var['methods'] as $methodIndex=>$methodItem ) {
					echo '--methods : '.$methodIndex .$endSeparator;
					echo '--total : '.$methodItem['total'] .$endSeparator;
					echo '--success : '.$methodItem['success'] .$endSeparator;
					//echo 'methods : '.$methodItem['asserts'];
					if(is_array($methodItem['asserts'])) {
						foreach( $methodItem['asserts'] as $_index=>$_item ) {
							echo '---code->'.$_item['code'].$endSeparator;
							echo '---failedRule->'.$_item['failedRule'].$endSeparator;
							echo '---failedMessage->'.$_item['failedMessage'].$endSeparator;
						}
					}
				}
			}
			echo '-time : ' . (isset($var['time']) ? $var['time'] : 0) .$endSeparator;
			echo '-total success : ' . (isset($var['success']) ? $var['success'] : '') .$endSeparator.$endSeparator;
		}

		return ;
	}
	
	private static function __booleanValue( $successVal = 0 ) {
		return $successVal === 1 ? 'true' : 'false';
	}

	/**
     * The string or array format and returns an array of formatted
     *
     * @code php
     * $input = 'item1, item2, item3';
     * // $output
     * // $output = array(
     * //   'item1',
     * //   'item2',
     * //   'item3',
     * // );
     *
     * $input = 'item1|item2|item3';
     * $output = UnitCore::normalize($input, '|');
     * @endcode
     *
     * @param array|string $input
     * @param string $delimiter
     *
     * @return array
     */
	public static function normalize($input, $delimiter = ',') {
        if (!is_array($input)) {
            $input = explode($delimiter, $input);
        }
        $input = array_map('trim', $input);
        return array_filter($input, 'strlen');
    }
	
    /**
     * @param object $ref1
     * @param object $ref2
     */
    public static function compare_obj($ref1,$ref2){
    	if (is_object($ref1) && is_object($ref2))
    		return $ref1 === $ref2 ;
    	return false ;
    }
    
	/**
	 * callback is valid
	 * 
	 * 	1. "class::method" static
	 *  2. array(object $obj,"method_name")
	 *  3. "function_name"
	 * @param mixed $callback
	 * @return bool
	 */
	public static function is_callback($callback){
		return is_callable($callback);
	}
	
	/**
	 * covert to string
	 * @param $callback
	 */
	public static function callbackToString($callback) {
		// fun | Class::func
		if (is_string($callback)) return $callback ;
		// & $obj , func
		else if (is_array($callback)){
			return get_class(array_shift($callback)) . '::' . array_shift($callback) ;
		}
		throw new Exception('$callback must a string | array(Object $obj,string $method)');
	}
	
	/**
	 * 
	 * @param callback $callback
	 * @param array $input
	 */
	public static function array_map($callback=null,array $input) {
//		$callback = self::is_callback($callback) ? $callback : null ;
		foreach ($input as $k=>$ele) {
			if (is_object($ele)) {
				unset($input[$k]);
			} elseif (is_array($ele)) { 
				$input[$k] = array_map($callback,$ele);
			} else {
				$input[$k] = call_user_func_array($callback,array($ele));
			}
		}
		return $input;
	}
	
	/**
	 * Generating a unique Key
	 * return like: XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX 
	 * style unique id, (8 letters)-(4 letters)-(4 letters)-(4 letters)-(12 letters)
	 * 
	 * @return string
	 */
	public static function newGuid(){
		$s = strtoupper(md5(uniqid(rand(),true))); 
	    return substr($s,0,8) . '-' . substr($s,8,4) . '-' . 
	        substr($s,12,4). '-' . substr($s,16,4). '-' . substr($s,20);
	}
	
	/**
	 * 
	 * @return float
	 */
	public static function getPhpVersion(){
		return substr(PHP_VERSION, 0, 6);
	}
	
	/**
	 * @return string
	 */
	public static function getIP(){
		// if getenv results in something, proxy detected
		if (getenv('HTTP_X_FORWARDED_FOR'))
			return getenv('HTTP_X_FORWARDED_FOR') ;
		else // otherwise no proxy detected
			return getenv('REMOTE_ADDR');
	}
	
	/**
	 * generater character by random
	 * 
	 * @param int $length
	 * @param bool $repeated
	 * @return string
	 */
	public static function passGenerator ($length = 8,$repeated=false){
		$password = "";//start with  blank password
		//possible characters
		$key = "0123456789abcdefghijklmnopqrstuvwxyz_-"; //you can change this!
		if (!$repeated&&$length>strlen($key)){
			//watch out you don't allow repeated values and the lenght of the key is less than length of the password you want
			return "ERROR: Password not generated, requested password length: $length, lenght of the key: ".strlen($key);
		}
		else{//random characters to $password until $length is reached
			$i = 0; 
			while ($i < $length) { 	
				$char = substr($key, mt_rand(0, strlen($key)-1), 1); //pick a random character from the key
			
				if (!strstr($password, $char)||$repeated) {
					//we don't want this character if it's already in the password or if we allow repeated if repeated == true
					$password .= $char;
					$i++;
				}
			
			}
			return $password;//return the generated pass
		} 
	  
	}
	
	private static $_objects = array();
	
	/*
		register_shutdown_function是指在执行完所有PHP语句后再调用函数，不要理解成客户端关闭流浏览器页面时调用函数

		可以这样理解调用条件：
      1、当页面被用户强制停止时
      2、当程序代码运行超时时
      3、当ＰＨＰ代码执行完成时
	*/
	const HALT_CALLBACK_KEY = '__next::runtime#halt_callback__' ;
	static function __halt_cleanup(){
		$halt_callback_list = isset(self::$_objects[self::HALT_CALLBACK_KEY]) ? self::$_objects[self::HALT_CALLBACK_KEY] : null ;
		while(!empty($halt_callback_list)){
			list($callback,$params) = array_pop($halt_callback_list);
			call_user_func_array($callback,$params);
		}
	}
	static function register_halt_callback($callback,$params=null){
		if (self::is_callback($callback)) {		
			self::$_objects[self::HALT_CALLBACK_KEY][] = 
			    array($callback ,is_array($params)?$params:null);
		}
	}
	
}


/*
register_shutdown_function( 'halt_cleanup' );
function halt_cleanup() {
	NexttCore::__halt_cleanup();
}
*/

register_shutdown_function(array('Nextt\UnitCore', '__halt_cleanup'));
