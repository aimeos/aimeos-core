<?php

/**
 * Test class for MW_Tree_Factory.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Tree_FactoryTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_dbm;
	private $_config;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_object = MW_Tree_Factory::createManager('LDAP', array(), null );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_object );
	}

	public function testFactory()
	{
		$this->assertInstanceOf('MW_Tree_Manager_Interface', $this->_object);
	}

	public function testFactoryFail()
	{
		$this->setExpectedException('MW_Tree_Exception');
		$this->_object = MW_Tree_Factory::createManager('notDefined', array(), null );
	}
}
