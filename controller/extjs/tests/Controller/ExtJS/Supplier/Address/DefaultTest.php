<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class Controller_ExtJS_Supplier_Address_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;


	protected function setUp()
	{
		$this->_object = new Controller_ExtJS_Supplier_Address_Default( TestHelper::getContext() );
	}


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '~=' => (object) array( 'supplier.address.company' => 'unitcompany' ) ) ) ),
			'sort' => 'supplier.address.company',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( 'unitcompany', $result['items'][0]->{'supplier.address.company'} );
	}


	public function testSaveDeleteItem()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'supplier.code' => 'unitCode003' ) ) ) ),
			'start' => 0,
			'limit' => 1,
		);
		$manager = Controller_ExtJS_Supplier_Factory::createController( TestHelper::getContext() );
		$resultType = $manager->searchItems( $params );

		$saveParam = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'supplier.address.refid' => $resultType['items'][0]->{'supplier.id'},
				'supplier.address.salutation' => 'mrs',
				'supplier.address.company' => 'Aimeos',
				'supplier.address.vatid' => 'DE123456789',
				'supplier.address.title' => 'Dr.',
				'supplier.address.firstname' => 'Aimeos',
				'supplier.address.lastname' => 'e-commerce',
				'supplier.address.address1' => 'teststreet',
				'supplier.address.address2' => '1',
				'supplier.address.address3' => '2. floor',
				'supplier.address.postal' => '12345',
				'supplier.address.city' => 'test city',
				'supplier.address.state' => 'NY',
				'supplier.address.languageid' => 'en',
				'supplier.address.countryid' => 'US',
				'supplier.address.telephone' => '1234567890',
				'supplier.address.email' => 'example@aimeos.org',
				'supplier.address.telefax' => '0987654321',
				'supplier.address.website' => 'https://aimeos.org',
			),
		);

		$searchParams = (object) array( 'site' => 'unittest', 'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'supplier.address.vatid' => 'DE123456789' ) ) ) ) );

		$saved = $this->_object->saveItems( $saveParam );
		$searched = $this->_object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'supplier.address.id'} );
		$this->_object->deleteItems( $deleteParams );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'supplier.address.id'} );
		$this->assertEquals( $saved['items']->{'supplier.address.id'}, $searched['items'][0]->{'supplier.address.id'} );
		$this->assertEquals( $saved['items']->{'supplier.address.refid'}, $searched['items'][0]->{'supplier.address.refid'} );
		$this->assertEquals( $saved['items']->{'supplier.address.salutation'}, $searched['items'][0]->{'supplier.address.salutation'} );
		$this->assertEquals( $saved['items']->{'supplier.address.company'}, $searched['items'][0]->{'supplier.address.company'} );
		$this->assertEquals( $saved['items']->{'supplier.address.vatid'}, $searched['items'][0]->{'supplier.address.vatid'} );
		$this->assertEquals( $saved['items']->{'supplier.address.title'}, $searched['items'][0]->{'supplier.address.title'} );
		$this->assertEquals( $saved['items']->{'supplier.address.firstname'}, $searched['items'][0]->{'supplier.address.firstname'} );
		$this->assertEquals( $saved['items']->{'supplier.address.lastname'}, $searched['items'][0]->{'supplier.address.lastname'} );
		$this->assertEquals( $saved['items']->{'supplier.address.address1'}, $searched['items'][0]->{'supplier.address.address1'} );
		$this->assertEquals( $saved['items']->{'supplier.address.address2'}, $searched['items'][0]->{'supplier.address.address2'} );
		$this->assertEquals( $saved['items']->{'supplier.address.address3'}, $searched['items'][0]->{'supplier.address.address3'} );
		$this->assertEquals( $saved['items']->{'supplier.address.postal'}, $searched['items'][0]->{'supplier.address.postal'} );
		$this->assertEquals( $saved['items']->{'supplier.address.city'}, $searched['items'][0]->{'supplier.address.city'} );
		$this->assertEquals( $saved['items']->{'supplier.address.state'}, $searched['items'][0]->{'supplier.address.state'} );
		$this->assertEquals( $saved['items']->{'supplier.address.languageid'}, $searched['items'][0]->{'supplier.address.languageid'} );
		$this->assertEquals( $saved['items']->{'supplier.address.countryid'}, $searched['items'][0]->{'supplier.address.countryid'} );
		$this->assertEquals( $saved['items']->{'supplier.address.telephone'}, $searched['items'][0]->{'supplier.address.telephone'} );
		$this->assertEquals( $saved['items']->{'supplier.address.email'}, $searched['items'][0]->{'supplier.address.email'} );
		$this->assertEquals( $saved['items']->{'supplier.address.telefax'}, $searched['items'][0]->{'supplier.address.telefax'} );
		$this->assertEquals( $saved['items']->{'supplier.address.website'}, $searched['items'][0]->{'supplier.address.website'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
