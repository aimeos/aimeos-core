<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_ExtJS_Order_Base_Address_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Order_Base_Address_DefaultTest' );
		$result = PHPUnit_TextUI_TestRunner::run( $suite );
	}


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_object = new Controller_ExtJS_Order_Base_Address_Default( TestHelper::getContext() );
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
		MShop_Factory::clear();
	}


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array(
				'&&' => array(
					0 => array( '==' => (object) array( 'order.base.address.lastname' => 'Unittest' ) ),
					1 => array( '==' => (object) array( 'order.base.address.editor' => 'core:unittest' ) ),
				)
			),
			'sort' => 'order.base.address.type',
			'dir' => 'DESC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 4, $result['total'] );
		$this->assertEquals( 'Unittest', $result['items'][0]->{'order.base.address.lastname'} );
	}


	public function testSaveDeleteItem()
	{
		$ctx = TestHelper::getContext();

		$localeManager = MShop_Locale_Manager_Factory::createManager( $ctx );
		$siteManager = $localeManager->getSubManager( 'site' );


		$search = $siteManager->createSearch();
		$search->setConditions( $search->compare( '==', 'locale.site.code', 'unittest' ) );
		$sites = $siteManager->searchItems( $search );

		if ( ( $siteItem = reset( $sites ) ) === false ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Site item for code "%1$s" not found', $site ) );
		}

		$localeItem = $localeManager->createItem();
		$localeItem->setCurrencyId( 'EUR' );
		$localeItem->setLanguageId( 'en' );
		$localeItem->setSiteId( $siteItem->getId() );


		$manager = MShop_Order_Manager_Factory::createManager( $ctx );
		$baseManager = $manager->getSubManager( 'base' );

		$baseItem = $baseManager->createItem();
		$baseItem->setCustomerId( 'unituser' );
		$baseItem->setComment( 'FoooBar' );
		$baseItem->setLocale( $localeItem );

		$baseManager->saveItem( $baseItem );


		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'order.base.address.id' => null,
				'order.base.address.baseid' => $baseItem->getId(),
				'order.base.address.addressid' => '-1',
				'order.base.address.type' => 'payment',
				'order.base.address.company' => 'MusterMax',
				'order.base.address.salutation' => MShop_Common_Item_Address_Abstract::SALUTATION_MR,
				'order.base.address.title' => 'Herr',
				'order.base.address.firstname' => 'Max',
				'order.base.address.lastname' => 'Mustermann',
				'order.base.address.address1' => 'Addresse 1',
				'order.base.address.address2' => 'Addresse 2',
				'order.base.address.address3' => 'Addresse 3',
				'order.base.address.postal' => '22222',
				'order.base.address.city' => 'Hamburg',
				'order.base.address.state' => 'Hamburg',
				'order.base.address.countryid' => 'en',
				'order.base.address.languageid' => 'en',
				'order.base.address.telephone' => '0815-4711',
				'order.base.address.email' => 'ich@du.de',
				'order.base.address.telefax' => '0815-4712',
				'order.base.address.website' => 'www.metaways.de'
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array(
				'&&' => array(
					0 => array( '==' => (object) array( 'order.base.address.type' => 'payment' ) ),
					1 => array( '==' => (object) array( 'order.base.address.baseid' => $baseItem->getId() ) )
				)
			)
		);

		$savedAddress = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );

		$deleteParamsAddr = (object) array( 'site' => 'unittest', 'items' => $savedAddress['items']->{'order.base.address.id'} );
		$this->_object->deleteItems( $deleteParamsAddr );

		$baseManager->deleteItem( $baseItem->getId() );


		$this->assertInternalType( 'object', $savedAddress['items'] );
		$this->assertNotNull( $savedAddress['items']->{'order.base.address.id'} );
		$this->assertEquals( $savedAddress['items']->{'order.base.address.id'}, $searched['items'][0]->{'order.base.address.id'} );
		$this->assertEquals( $savedAddress['items']->{'order.base.address.baseid'}, $searched['items'][0]->{'order.base.address.baseid'} );
		$this->assertEquals( $savedAddress['items']->{'order.base.address.addressid'}, $searched['items'][0]->{'order.base.address.addressid'} );
		$this->assertEquals( $savedAddress['items']->{'order.base.address.type'}, $searched['items'][0]->{'order.base.address.type'} );
		$this->assertEquals( $savedAddress['items']->{'order.base.address.company'}, $searched['items'][0]->{'order.base.address.company'} );
		$this->assertEquals( $savedAddress['items']->{'order.base.address.salutation'}, $searched['items'][0]->{'order.base.address.salutation'} );
		$this->assertEquals( $savedAddress['items']->{'order.base.address.title'}, $searched['items'][0]->{'order.base.address.title'} );
		$this->assertEquals( $savedAddress['items']->{'order.base.address.firstname'}, $searched['items'][0]->{'order.base.address.firstname'} );
		$this->assertEquals( $savedAddress['items']->{'order.base.address.lastname'}, $searched['items'][0]->{'order.base.address.lastname'} );
		$this->assertEquals( $savedAddress['items']->{'order.base.address.address1'}, $searched['items'][0]->{'order.base.address.address1'} );
		$this->assertEquals( $savedAddress['items']->{'order.base.address.address2'}, $searched['items'][0]->{'order.base.address.address2'} );
		$this->assertEquals( $savedAddress['items']->{'order.base.address.address3'}, $searched['items'][0]->{'order.base.address.address3'} );
		$this->assertEquals( $savedAddress['items']->{'order.base.address.postal'}, $searched['items'][0]->{'order.base.address.postal'} );
		$this->assertEquals( $savedAddress['items']->{'order.base.address.city'}, $searched['items'][0]->{'order.base.address.city'} );
		$this->assertEquals( $savedAddress['items']->{'order.base.address.state'}, $searched['items'][0]->{'order.base.address.state'} );
		$this->assertEquals( $savedAddress['items']->{'order.base.address.countryid'}, $searched['items'][0]->{'order.base.address.countryid'} );
		$this->assertEquals( $savedAddress['items']->{'order.base.address.languageid'}, $searched['items'][0]->{'order.base.address.languageid'} );
		$this->assertEquals( $savedAddress['items']->{'order.base.address.telephone'}, $searched['items'][0]->{'order.base.address.telephone'} );
		$this->assertEquals( $savedAddress['items']->{'order.base.address.email'}, $searched['items'][0]->{'order.base.address.email'} );
		$this->assertEquals( $savedAddress['items']->{'order.base.address.telefax'}, $searched['items'][0]->{'order.base.address.telefax'} );
		$this->assertEquals( $savedAddress['items']->{'order.base.address.website'}, $searched['items'][0]->{'order.base.address.website'} );
		$this->assertEquals( 1, count( $searched['items'] ) );

		$searched = $this->_object->searchItems( $searchParams );
		$this->assertEquals( 0, $searched['total'] );
		$this->assertTrue( $searched['success'] );
	}
}

