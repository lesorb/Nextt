<?php
/**
 * UnitAssertionException extends RuntimeException
 *
 * @author	Owen Wang(lesorb@gmail.com)
 * @date:	2013-10-30
 */
namespace Nextt;

class UnitAssertionException extends \RuntimeException {
	/**
	 * Assertion failure triggered rules
	 * @var $_failedRule string
	 */
	private $_failedRule = null;
	
	/**
	 * Assertion failed error feedback information
	 * @var $_failedRule string
	 */
	private $_failedMessage = null;
	
	/**
	 * @param string $failedRule
	 * @param string $failedMessage
	 * @param string $description
	 */
	function __construct($failedRule,$failedMessage=null,$description) {
		parent::__construct($description);
		$this->_failedRule = $failedRule;
		$this->_failedMessage = $failedMessage;
	}
	
	/**
	 * Return assertion failure triggered rules
	 * @params none
	 * @return string
	 */
	function getAssertRule() {
		return $this->_failedRule;
	}
	
	/**
	 * Return assertion failed error feedback information
	 * @params none
	 * @return string
	 */
	function getAssertMessage() {
		return $this->_failedMessage;
	}
}
