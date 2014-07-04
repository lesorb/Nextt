<?php
/**
 *
 * @author	Owen Wang(lesorb@gmail.com)
 * @date:	2013-10-30
 */

namespace Nextt;

class UnitRunner {
	
	/**
	 * @var
	 */
	private $_testCase = null;
	
	/**
	 * 
	 * @var ReflectionClass $_reflect
	 */
	private $_reflect = null;
	
	/**
	 * 
	 * @var array
	 */
	private $_result = null;
	
	public function execute(UnitTestCase $testCase){
		
		$this->_testCase = $testCase;
		
		$this->_reflect = new \ReflectionClass($testCase);
		
		$this->_result = array(
			'class' => $this->_reflect->getName(),
			'code' =>  $this->_reflect->getFileName()
		);
		
		$start = UnitAssert::getCount();
	
		try {
			
			$startTime = $this->__getMicrotime();

			$this->_testCase->setUp();
			
			$testMethods = $this->_fetchTestMethods($this->_testCase);
			
			if (!empty($testMethods)){
				
				$this->_result['failed'] = 0;
				
				$this->_result['methods'] = array();
				
				foreach ($testMethods as $testMethod) {
					$this->_evaluate($testMethod);
					
					$this->_result['failed'] += $this->_result['methods'][$testMethod]['failed'];
				}
				
			}
			
			$this->_testCase->tearDown();
			
			$stop = UnitAssert::getCount();
			
			$total = $stop - $start;
			
			$this->_result['total'] = $total;
			$this->_result['time'] = ($this->__getMicrotime() - $startTime) . '(s)';
			$this->_result['success'] = $total - $this->_result['failed'];
			
			
		} catch (Exception $ex){			
			$this->_result['bug'] = $ex->getMessage();
			$this->_result['bug_trace'] = $ex->getTraceAsString();			
		}
		
		return $this->_result;
	}
	
	private function __getMicrotime() {
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}

	/**
	 * excute
	 * @param string $testMethod
	 */
	private function _evaluate($testMethod) {
		// Get the current test method execution times before running the assertion
		$start = UnitAssert::getCount();
		
		$this->_testCase->{$testMethod}();
		
		// Get the current test method execution is complete assertion of execution times
		$stop = UnitAssert::getCount();
		
		$total = $stop - $start ;
		
		// Acquisition method corresponding error message when an assertion fails collection
		$assertFaileds = UnitAssert::getAssertionFailedTrace($this->_result['class'],$testMethod) ;
		$this->_result['methods'][$testMethod] = array(
			'total' => $total ,
			'success' => $total - count($assertFaileds) ,
			'failed' => count($assertFaileds) ,
			'asserts' => $assertFaileds
		) ; 
	}
	
	/**
	 *
	 * Gets a collection of test object Test Methods
	 * 
	 * @param NexttTestCase $testCase
	 * @param bool			$noCaseSensitive
	 * @return array
	 */
	private function _fetchTestMethods( UnitTestCase $testCase ,$noCaseSensitive = true ) {
		// PHP5 start
		$methods = get_class_methods($testCase); 
		// array_map('strtolower', $methods);
		$testMethods = array();
		
		$identifier = $noCaseSensitive ? '/Test$/i' : '/Test$/' ;
		
		foreach ($methods as $method) {
			if (preg_match($identifier,$method) && is_callable(array($testCase,$method))){
				$testMethods[] = $method ;
			}
		}
		return $testMethods ;
	}
	
}
