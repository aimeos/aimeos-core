<?php

/**
 * Test class for MW_Tree_Factory.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Tree_FactoryTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $dbm;
	private $config;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->object = MW_Tree_Factory::createManager('LDAP', array(), null );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->object );
	}

	public function testFactory()
	{
		$this->assertInstanceOf('MW_Tree_Manager_Iface', $this->object);
	}

	public function testFactoryFail()
	{
		$this->setExpectedException('MW_Tree_Exception');
		$this->object = MW_Tree_Factory::createManager('notDefined', array(), null );
	}
}
