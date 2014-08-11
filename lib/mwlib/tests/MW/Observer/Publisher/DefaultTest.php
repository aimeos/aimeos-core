<?php

/**
 * Test class for MW_Session_CMSLite.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Observer_Publisher_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_object = new MW_Observer_Publisher_Test();
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


	public function testaddListener()
	{
		$l = new MW_Observer_Listener_Test();

		$this->_object->addListener($l, 'test');
	}

	public function testRemoveListener()
	{
		$l = new MW_Observer_Listener_Test();

		$this->_object->addListener($l, 'test');
		$this->_object->removeListener($l, 'test');
	}

	public function testclearListeners()
	{
		$this->_object->clearListeners();
	}

	public function testnotifyListeners()
	{
		$l = new MW_Observer_Listener_Test();
		$this->_object->addListener($l, 'test');
		$this->_object->addListener($l, 'testagain');

		$this->_object->notifyListeners('test', 'warn');
		$this->_object->notifyListeners('testagain', 'warn');
	}
}


class MW_Observer_Publisher_Test extends MW_Observer_Publisher_Abstract
{
	public function notifyListeners($action, $value = null)
	{
		$this->_notifyListeners($action, $value);
	}

	public function clearListeners()
	{
		$this->_clearListeners();
	}
}
