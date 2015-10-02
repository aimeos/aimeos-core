<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class Controller_ExtJS_Customer_Address_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$this->object = new Controller_ExtJS_Customer_Address_Default( TestHelper::getContext() );
	}


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '~=' => (object) array( 'customer.address.company' => 'unitcompany' ) ) ) ),
			'sort' => 'customer.address.company',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( 'unitcompany', $result['items'][0]->{'customer.address.company'} );
	}


	public function testSaveDeleteItem()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'customer.code' => 'UTC003' ) ) ) ),
			'start' => 0,
			'limit' => 1,
		);
		$manager = Controller_ExtJS_Customer_Factory::createController( TestHelper::getContext() );
		$resultType = $manager->searchItems( $params );

		$saveParam = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'customer.address.refid' => $resultType['items'][0]->{'customer.id'},
				'customer.address.salutation' => 'mrs',
				'customer.address.company' => 'Aimeos',
				'customer.address.vatid' => 'DE123456789',
				'customer.address.title' => 'Dr.',
				'customer.address.firstname' => 'Aimeos',
				'customer.address.lastname' => 'e-commerce',
				'customer.address.address1' => 'teststreet',
				'customer.address.address2' => '1',
				'customer.address.address3' => '2. floor',
				'customer.address.postal' => '12345',
				'customer.address.city' => 'test city',
				'customer.address.state' => 'NY',
				'customer.address.languageid' => 'en',
				'customer.address.countryid' => 'US',
				'customer.address.telephone' => '1234567890',
				'customer.address.email' => 'example@aimeos.org',
				'customer.address.telefax' => '0987654321',
				'customer.address.website' => 'https://aimeos.org',
			),
		);

		$searchParams = (object) array( 'site' => 'unittest', 'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'customer.address.vatid' => 'DE123456789' ) ) ) ) );

		$saved = $this->object->saveItems( $saveParam );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'customer.address.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'customer.address.id'} );
		$this->assertEquals( $saved['items']->{'customer.address.id'}, $searched['items'][0]->{'customer.address.id'} );
		$this->assertEquals( $saved['items']->{'customer.address.refid'}, $searched['items'][0]->{'customer.address.refid'} );
		$this->assertEquals( $saved['items']->{'customer.address.salutation'}, $searched['items'][0]->{'customer.address.salutation'} );
		$this->assertEquals( $saved['items']->{'customer.address.company'}, $searched['items'][0]->{'customer.address.company'} );
		$this->assertEquals( $saved['items']->{'customer.address.vatid'}, $searched['items'][0]->{'customer.address.vatid'} );
		$this->assertEquals( $saved['items']->{'customer.address.title'}, $searched['items'][0]->{'customer.address.title'} );
		$this->assertEquals( $saved['items']->{'customer.address.firstname'}, $searched['items'][0]->{'customer.address.firstname'} );
		$this->assertEquals( $saved['items']->{'customer.address.lastname'}, $searched['items'][0]->{'customer.address.lastname'} );
		$this->assertEquals( $saved['items']->{'customer.address.address1'}, $searched['items'][0]->{'customer.address.address1'} );
		$this->assertEquals( $saved['items']->{'customer.address.address2'}, $searched['items'][0]->{'customer.address.address2'} );
		$this->assertEquals( $saved['items']->{'customer.address.address3'}, $searched['items'][0]->{'customer.address.address3'} );
		$this->assertEquals( $saved['items']->{'customer.address.postal'}, $searched['items'][0]->{'customer.address.postal'} );
		$this->assertEquals( $saved['items']->{'customer.address.city'}, $searched['items'][0]->{'customer.address.city'} );
		$this->assertEquals( $saved['items']->{'customer.address.state'}, $searched['items'][0]->{'customer.address.state'} );
		$this->assertEquals( $saved['items']->{'customer.address.languageid'}, $searched['items'][0]->{'customer.address.languageid'} );
		$this->assertEquals( $saved['items']->{'customer.address.countryid'}, $searched['items'][0]->{'customer.address.countryid'} );
		$this->assertEquals( $saved['items']->{'customer.address.telephone'}, $searched['items'][0]->{'customer.address.telephone'} );
		$this->assertEquals( $saved['items']->{'customer.address.email'}, $searched['items'][0]->{'customer.address.email'} );
		$this->assertEquals( $saved['items']->{'customer.address.telefax'}, $searched['items'][0]->{'customer.address.telefax'} );
		$this->assertEquals( $saved['items']->{'customer.address.website'}, $searched['items'][0]->{'customer.address.website'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
