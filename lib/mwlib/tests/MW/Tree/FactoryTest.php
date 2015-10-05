<?php

namespace Aimeos\MW\Tree;


/**
 * Test class for \Aimeos\MW\Tree\Factory.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
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
		$this->object = \Aimeos\MW\Tree\Factory::createManager('LDAP', array(), null );
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
		$this->assertInstanceOf('\\Aimeos\\MW\\Tree\\Manager\\Iface', $this->object);
	}

	public function testFactoryFail()
	{
		$this->setExpectedException('\\Aimeos\\MW\\Tree\\Exception');
		$this->object = \Aimeos\MW\Tree\Factory::createManager('notDefined', array(), null );
	}
}
