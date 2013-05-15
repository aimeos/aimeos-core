<?php

/**
 * Test class for MW_Session_CMSLite.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Observer_Listener_DefaultTest extends MW_Unittest_Testcase
{
	/**
	 * @var    MW_Observer_Listener
	 * @access protected
	 */
	private $_object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_object = new MW_Observer_Listener_Test;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
	}

	public function testRegister()
	{
		$p = new MW_Observer_Publisher_Test();

		$this->_object->register($p);
	}

	public function testUpdate()
	{
		$p = new MW_Observer_Publisher_Test();

		$this->_object->update($p, 'test');
	}
}



class MW_Observer_Listener_Test implements MW_Observer_Listener_Interface
{
	public function register( MW_Observer_Publisher_Interface $p )
	{
	}

	public function update( MW_Observer_Publisher_Interface $p, $action, $value = null )
	{
		if ($action == 'test') {
			return false;
		}
	}
}