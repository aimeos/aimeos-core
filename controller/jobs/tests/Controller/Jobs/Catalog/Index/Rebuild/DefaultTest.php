<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_Jobs_Catalog_Index_Rebuild_DefaultTest extends MW_Unittest_Testcase
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
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();

		$this->_object = new Controller_Jobs_Catalog_Index_Rebuild_Default( $context, $arcavias );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = null;
	}


	public function testGetName()
	{
		$this->assertEquals( 'Catalog index rebuild', $this->_object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Rebuilds the catalog index for searching products';
		$this->assertEquals( $text, $this->_object->getDescription() );
	}


	public function testRun()
	{
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();


		$name = 'ControllerJobsCatalogIndexRebuildDefaultRun';
		$context->getConfig()->set( 'classes/catalog/manager/name', $name );


		$catalogManagerStub = $this->getMockBuilder( 'MShop_Catalog_Manager_Default' )
			->setMethods( array( 'getSubManager' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$catalogIndexManagerStub = $this->getMockBuilder( 'MShop_Catalog_Manager_Index_Default' )
			->setMethods( array( 'rebuildIndex' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		MShop_Catalog_Manager_Factory::injectManager( 'MShop_Catalog_Manager_' . $name, $catalogManagerStub );


		$catalogManagerStub->expects( $this->once() )->method( 'getSubManager' )
			->will( $this->returnValue( $catalogIndexManagerStub ) );

		$catalogIndexManagerStub->expects( $this->once() )->method( 'rebuildIndex' );


		$object = new Controller_Jobs_Catalog_Index_Rebuild_Default( $context, $arcavias );
		$object->run();
	}
}
