<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: FactoryTest.php 14602 2011-12-27 15:27:08Z gwussow $
 */


class MShop_Product_Manager_FactoryTest extends MW_Unittest_Testcase
{

	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Product_Manager_FactoryTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}

	public function testCreateManager()
	{
		$target = 'MShop_Common_Manager_Interface';
		$manager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$this->assertInstanceOf( $target, $manager );

		$manager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext(), 'Default' );
		$this->assertInstanceOf( $target, $manager );
	}

	public function testCreateManagerInvalidName()
	{
		$this->setExpectedException('MShop_Product_Exception');
		$target = 'MShop_Common_Manager_Interface';
		$manager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext(), '#$%' );

		$this->assertInstanceOf( $target, $manager );
	}
}