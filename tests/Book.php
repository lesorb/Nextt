<?php
/**
 *
 * @author
 * @date:	2013-10-30
 */

require_once('Mysql.php');

class Book {
	/**
	 * @var Kenxu_Mysql
	 */
	private $_db = null;
	
	function __construct(){
		$this->_db = new Mysql();
		$this->_db->connect('localhost','root','111111','test','utf8');
	}
	
	function fetchBooks(){
		$books = $this->_db->fetch_all('select * from books') ;
		return $books ;
	}
	
}
