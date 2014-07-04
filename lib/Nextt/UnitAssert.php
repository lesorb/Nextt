<?php
/**
 * A set of assert methods.
 *
 * @package	lib/Nextt
 * @author	Owen Wang(lesorb@gmail.com)
 * @date:	2013-10-30
 */

namespace Nextt;

abstract class UnitAssert {
	
	/**
	 * @var integer
	 */
	private static $__count = 0;
	
	/**
	 *
	 * Error message when an assertion fails the storage location in the following format
	 * 	array(
	 * 		':class' => array(
	 * 			':method' => :msg
	 * 		)
	 * 	)
	 * @var array
	 */
	protected static $_assertionFailedTrace = array();
	
	/**
	 * Press the test case class / method name to get an error message when an assertion fails collection
	 * 
	 * @param	string		$class
	 * @param	string		$method
	 * @return	array|null
	 */
	public static function getAssertionFailedTrace( $class,$method=null ) {
		if (!empty($class) && is_string($class) && isset(self::$_assertionFailedTrace[$class])) {
			$classTrace = self::$_assertionFailedTrace[$class];
			if (empty($method)) 
				return $classTrace;
			if (is_string($method) && isset($classTrace[$method]))
				return $classTrace[$method];
		}
		return null;
	}
	
	/**
	 * Test value with a set of rules, each rule is the first element of the callback function, the successful return true, otherwise false
	 * 
	 * @param mixed $value
	 * @param array $rules 
	 * @param string $description
	 * @return boolean
	 */
	public static function assertThat( $value,array $rules=null,$description=null ) {
		self::$__count ++;
		
		$validator = UnitValidate::getInstance();
		/* @var $validator Kenxu_Unit_Validate */
		if (!$validator->not_null_array($rules)) 
			return true;
		
		$errors = array(); // $fld -> errorInfo 
		foreach ($rules as $index=>$rule)
			// $rule => array(rule, validationParams, errorInfo)
			$errors[UnitCore::callbackToString($rule[0])] = array_pop($rules[$index]);
		
		$failed = null;
		
		if ($validator->validateBatch($value,$rules,false,$failed)) 
			return true;
		
		try {
			$failedRule = UnitCore::callbackToString(array_pop($failed));
			throw new UnitAssertionException($failedRule,$errors[$failedRule],$description);
		} catch (UnitAssertionException $ex) {
			self::_fail($ex);
		}
		return false;
	}
	
	public static function assertNotNull($value,$description=null){
		UnitAssert::assertThat( $value,array(array('not_empty','Value can not be null')) ,'测试{...}元素' );
	}
	
	/**
	 * Exception thrown when an assertion fails information capture
	 * @param RuntimeException $ex
	 */
	protected static function _fail(UnitAssertionException $ex) {
		$traces = $ex->getTrace();
		
		$testMethodTrace = $traces[1];
		
		$testcaseClass = $testMethodTrace['class'];
		$testMethod = $testMethodTrace['function'];
		
		//init
		if (!isset(self::$_assertionFailedTrace[$testcaseClass]))
			self::$_assertionFailedTrace[$testcaseClass] = array();
		if (!isset(self::$_assertionFailedTrace[$testcaseClass][$testMethod]))
			self::$_assertionFailedTrace[$testcaseClass][$testMethod] = array();
		
		//0 is asserted at the code to call the information
		$assertFailedTrace = $traces[0];	
		
		//Calling code string
		$argsType = array_map('gettype',$assertFailedTrace['args']);
		$code = sprintf("{$assertFailedTrace['class']}{$assertFailedTrace['type']}{$assertFailedTrace['function']}(%s)",
			implode(',',$argsType));		
		
		//opearter info of $assertTrace
		//Asserted purpose
		//Calling code
		//Lines of code
		//Failure rules
		//Failure message
		self::$_assertionFailedTrace[$testcaseClass][$testMethod][] = array(
			'assertDestination' => $ex->getMessage(),
			'code' => $code,
			'line' => $assertFailedTrace['line'],
			'failedRule' => $ex->getAssertRule(),
			'failedMessage' => $ex->getAssertMessage()
		);
		
		unset($ex);
	}
	
	/**
	 * Return the current assertion count.
	 *
	 * @return integer
	 */
	public static function getCount() {
		return self::$__count;
	}

}
