<?php
/**
 * 测试用例 基类,测试用例测试方法定义规范: 
 * 	1. 以 Test 结尾
	2. 测试方法不能保护参数
	3. 测试方法不能声明成static
 *
 * @author	Owen Wang(lesorb@gmail.com)
 * @date:	2013-10-30
 */
 namespace Nextt;

abstract class UnitTestCase extends UnitAssert {
	
	protected function setUp(){
		/* Setup Routine */
	}
	
	protected function tearDown(){
		/* Tear Down Routine */
	}

	public function getMock() {
		// to do ...
	}

	// to do ...
}
