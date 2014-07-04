<?php

require_once('Book.php');

class BookTest extends Nextt\UnitTestCase {

	/**
	 * @var Book $_modBook
	 */
	private $_modBook = null;
	
	function setUp() {
		/* Setup Routine */
		$this->_modBook = new Book();
	}
		
	function fetchBooksTest() {
		
		$books = $this->_modBook->fetchBooks();
		
		$this->assertThat( count($books),array(array('equal',1, '图书个数为3')) ,'测试图书元素' );
		$this->assertThat( !$books,array(array('not_empty','值不能为空')) ,'测试图书元素' );
		$this->assertNotNull( !$books,'图书表中数据为空' );

	}
	
	function tearDown() {
		/* Tear Down Routine */
		$this->_modBook = null;
	}

}
