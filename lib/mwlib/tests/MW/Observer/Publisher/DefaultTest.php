<?php

/**
 * Test class for MW_Session_CMSLite.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Observer_Publisher_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->object = new MW_Observer_Publisher_Test();
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

		$this->object->addListener($l, 'test');
	}

	public function testRemoveListener()
	{
		$l = new MW_Observer_Listener_Test();

		$this->object->addListener($l, 'test');
		$this->object->removeListener($l, 'test');
	}

	public function testclearListeners()
	{
		$this->object->clearListenersPublic();
	}

	public function testnotifyListeners()
	{
		$l = new MW_Observer_Listener_Test();
		$this->object->addListener($l, 'test');
		$this->object->addListener($l, 'testagain');

		$this->object->notifyListenersPublic('test', 'warn');
		$this->object->notifyListenersPublic('testagain', 'warn');
	}
}


class MW_Observer_Publisher_Test extends MW_Observer_Publisher_Base
{
	/**
	 * @param string $action
	 * @param string $value
	 */
	public function notifyListenersPublic($action, $value = null)
	{
		$this->notifyListeners($action, $value);
	}

	public function clearListenersPublic()
	{
		$this->clearListeners();
	}
}
