<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Locale_Manager_Default.
 */
class MShop_Catalog_Manager_FactoryTest extends MW_Unittest_Testcase
{

	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite = new PHPUnit_Framework_TestSuite('MShop_Catalog_Manager_FactoryTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}

	/**
	 * testCreateManager().
	 */

	public function testCreateManager()
	{
		$target = 'MShop_Common_Manager_Interface';

		$manager = MShop_Catalog_Manager_Factory::createManager( TestHelper::getContext() );
		$this->assertInstanceOf( $target, $manager );

		$manager = MShop_Catalog_Manager_Factory::createManager( TestHelper::getContext(), 'Default' );
		$this->assertInstanceOf( $target, $manager );
	}

	public function testCreateManagerInvalidName()
	{
		$this->setExpectedException('MShop_Catalog_Exception');
		MShop_Catalog_Manager_Factory::createManager(TestHelper::getContext(), '%^');
	}


	public function testCreateManagerNotExisting()
	{
		$this->setExpectedException('MShop_Exception');
		MShop_Catalog_Manager_Factory::createManager(TestHelper::getContext(), 'notexist');
	}
}