<?php

/**
 * Test class for MW_Container_Factory.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Container_FactoryTest extends PHPUnit_Framework_TestCase
{
	public function testFactory()
	{
		$object = MW_Container_Factory::getContainer( 'tempfile', 'Zip', 'CSV', array() );
		$this->assertInstanceOf( 'MW_Container_Iface', $object );
	}

	public function testFactoryFail()
	{
		$this->setExpectedException('MW_Container_Exception');
		MW_Container_Factory::getContainer( 'tempfile', 'notDefined', 'invalid' );
	}
}
