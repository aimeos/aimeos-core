<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Controller_Jobs_Catalog_Index_Rebuild_FactoryTest extends PHPUnit_Framework_TestCase
{
	public function testCreateController()
	{
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();

		$obj = Controller_Jobs_Catalog_Index_Rebuild_Factory::createController( $context, $arcavias );
		$this->assertInstanceOf( 'Controller_Jobs_Interface', $obj);
	}


	public function testFactoryExceptionWrongName()
	{
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();

		$this->setExpectedException( 'Controller_Jobs_Exception' );
		Controller_Jobs_Catalog_Index_Rebuild_Factory::createController( $context, $arcavias, 'Wrong$$$Name' );
	}


	public function testFactoryExceptionWrongClass()
	{
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();

		$this->setExpectedException( 'Controller_Jobs_Exception' );
		Controller_Jobs_Catalog_Index_Rebuild_Factory::createController( $context, $arcavias, 'WrongClass' );
	}


	public function testFactoryExceptionWrongInterface()
	{
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();

		$this->setExpectedException( 'Controller_Jobs_Exception' );
		Controller_Jobs_Catalog_Index_Rebuild_Factory::createController( $context, $arcavias, 'Factory' );
	}

}
