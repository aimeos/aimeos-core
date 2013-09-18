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


		$name = 'ControllerJobsCatalogIndexDefaultRun';
		$context->getConfig()->set( 'classes/catalog/manager/name', $name );
		$context->getConfig()->set( 'classes/locale/manager/name', $name );


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


		$localeManagerStub = $this->getMockBuilder( 'MShop_Locale_Manager_Default' )
			->setMethods( array( 'bootstrap', 'getSubManager' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$localeSiteManagerStub = $this->getMockBuilder( 'MShop_Locale_Manager_Site_Default' )
			->setMethods( array( 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		MShop_Locale_Manager_Factory::injectManager( 'MShop_Locale_Manager_' . $name, $localeManagerStub );


		$localeManagerStub->expects( $this->once() )->method( 'getSubManager' )
			->will( $this->returnValue( $localeSiteManagerStub ) );

		$localeItem = $localeManagerStub->createItem();
		$localeItem->setId( -1 );

		$localeManagerStub->expects( $this->once() )->method( 'bootstrap' )
			->will( $this->returnValue( $localeItem ) );

		$siteItem = $localeSiteManagerStub->createItem();
		$siteItem->setId( -1 );

		$localeSiteManagerStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->onConsecutiveCalls( array( $siteItem ), array() ) );


		$object = new Controller_Jobs_Catalog_Index_Rebuild_Default( $context, $arcavias );
		$object->run();
	}


	public function testRunException()
	{
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();


		$name = 'ControllerJobsCatalogIndexDefaultRun';
		$context->getConfig()->set( 'classes/locale/manager/name', $name );


		$localeManagerStub = $this->getMockBuilder( 'MShop_Locale_Manager_Default' )
			->setMethods( array( 'bootstrap', 'getSubManager' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$localeSiteManagerStub = $this->getMockBuilder( 'MShop_Locale_Manager_Site_Default' )
			->setMethods( array( 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		MShop_Locale_Manager_Factory::injectManager( 'MShop_Locale_Manager_' . $name, $localeManagerStub );


		$localeManagerStub->expects( $this->once() )->method( 'getSubManager' )
			->will( $this->returnValue( $localeSiteManagerStub ) );

		$localeManagerStub->expects( $this->once() )->method( 'bootstrap' )
			->will( $this->throwException( new MShop_Catalog_Exception( 'Test exception' ) ) );

		$siteItem = $localeSiteManagerStub->createItem();
		$siteItem->setCode( 'catalog-index-test' );
		$siteItem->setId( -1 );

		$localeSiteManagerStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->onConsecutiveCalls( array( $siteItem ), array() ) );


		$object = new Controller_Jobs_Catalog_Index_Rebuild_Default( $context, $arcavias );
		$object->run();
	}
}
