<?php
/**
 * Modules of test framework
 *
 * @author	Owen Wang(lesorb@gmail.com)
 * @date:
 */
namespace Nextt;

class UnitFramework {

	private static $_runner = null ;
	
	/**
	 * 
	 * @var array
	 */
	private static $_config = null ;
	
	/**
	 * 
	 * @var array
	 */
	private static $_resultset = null ;
	
	/**
	 * Initialization
	 */
	public static function init(array $config) {
		self::$_runner = new UnitRunner();

		$must = array('classes') ;
		foreach ($must as $opt) {
			if (!isset($config[$opt]))
				throw new Exception(
					sprintf("%s::%s(array) Parameter setting error,must set [%s]",__FILE__,__METHOD__,implode(',',$must))
				);
		}
		self::$_config = $config ;
		self::$_resultset = array();
	}
	
	/**
	 * run test example
	 */
	public static function run( $config = array() ) {
		
		if(isset($config['result']) && $config['result']['init'])
			self::init( $config );

		//self::$_config['classes'] = array_unique(self::$_config['classes']);
		foreach (self::$_config['classes'] as $testcaseClass) {
			
			try {

				$testcase = UnitCore::getInstance($testcaseClass);
				
			} catch (\Exception $e) {
				
				self::$_resultset[$testcaseClass] = array(
					'class' => $testcaseClass,
					'message' => $e->getMessage()
				);
				continue;
			}
			
			if ($testcase instanceof UnitTestCase) {
				self::$_resultset[$testcaseClass] = self::$_runner->execute($testcase);
			} else {
				self::$_resultset[$testcaseClass] = array(
					'class' => $testcaseClass,
					'message' => $testcaseClass . ' is not a NexttTestCase instance',
				);
			}
			
		}

		// restore error handler ...
		//restore_error_handler();
		if(isset(self::$_config['result']) && self::$_config['result']['report'])
			self::report();
	}

	/**
	 * print test report
	 */
	public static function report() {

		$reportFormat = self::$_config['result']['reportFormat'];
		$reportSep = self::$_config['result']['reportSep'];
		$version = self::_getVersion();

		if($reportFormat === 'dump') {
			header('Content-Type: text/html; charset=utf-8');
			UnitCore::dump(self::$_resultset,$version . ' testing result');
		}elseif($reportFormat === 'simp') {
			header('Content-Type: text/html; charset=utf-8');
			UnitCore::simp(self::$_resultset,$version . ' testing result',$reportSep);
		}else{
		
		}
	}
	
	protected static function _getVersion() {
		return 'Nextt 0.11';
	}
}
