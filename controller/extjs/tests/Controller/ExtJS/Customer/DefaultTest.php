<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class Controller_ExtJS_Customer_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$this->object = new Controller_ExtJS_Customer_Default( TestHelper::getContext() );
	}


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '~=' => (object) array( 'customer.label' => 'unitCustomer001' ) ) ) ),
			'sort' => 'customer.label',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( 'unitCustomer001', $result['items'][0]->{'customer.label'} );
	}


	public function testSaveDeleteItem()
	{
		$saveParam = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'customer.label' => 'controller test customer',
				'customer.code' => 'extjs test customer',
				'customer.birthday' => '2000-01-01',
				'customer.password' => 'testpass',
				'customer.dateverified' => '2015-01-01',
				'customer.status' => 1,
				'customer.salutation' => 'mrs',
				'customer.company' => 'Aimeos',
				'customer.vatid' => 'DE123456789',
				'customer.title' => 'Dr.',
				'customer.firstname' => 'Aimeos',
				'customer.lastname' => 'e-commerce',
				'customer.address1' => 'teststreet',
				'customer.address2' => '1',
				'customer.address3' => '2. floor',
				'customer.postal' => '12345',
				'customer.city' => 'test city',
				'customer.state' => 'NY',
				'customer.languageid' => 'en',
				'customer.countryid' => 'US',
				'customer.telephone' => '1234567890',
				'customer.email' => 'example@aimeos.org',
				'customer.telefax' => '0987654321',
				'customer.website' => 'https://aimeos.org',
			),
		);

		$searchParams = (object) array( 'site' => 'unittest', 'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'customer.label' => 'controller test customer' ) ) ) ) );

		$saved = $this->object->saveItems( $saveParam );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'customer.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'customer.id'} );
		$this->assertEquals( $saved['items']->{'customer.id'}, $searched['items'][0]->{'customer.id'} );
		$this->assertEquals( $saved['items']->{'customer.code'}, $searched['items'][0]->{'customer.code'} );
		$this->assertEquals( $saved['items']->{'customer.label'}, $searched['items'][0]->{'customer.label'} );
		$this->assertEquals( $saved['items']->{'customer.birthday'}, $searched['items'][0]->{'customer.birthday'} );
		$this->assertEquals( $saved['items']->{'customer.password'}, $searched['items'][0]->{'customer.password'} );
		$this->assertEquals( $saved['items']->{'customer.dateverified'}, $searched['items'][0]->{'customer.dateverified'} );
		$this->assertEquals( $saved['items']->{'customer.status'}, $searched['items'][0]->{'customer.status'} );
		$this->assertEquals( $saved['items']->{'customer.salutation'}, $searched['items'][0]->{'customer.salutation'} );
		$this->assertEquals( $saved['items']->{'customer.company'}, $searched['items'][0]->{'customer.company'} );
		$this->assertEquals( $saved['items']->{'customer.vatid'}, $searched['items'][0]->{'customer.vatid'} );
		$this->assertEquals( $saved['items']->{'customer.title'}, $searched['items'][0]->{'customer.title'} );
		$this->assertEquals( $saved['items']->{'customer.firstname'}, $searched['items'][0]->{'customer.firstname'} );
		$this->assertEquals( $saved['items']->{'customer.lastname'}, $searched['items'][0]->{'customer.lastname'} );
		$this->assertEquals( $saved['items']->{'customer.address1'}, $searched['items'][0]->{'customer.address1'} );
		$this->assertEquals( $saved['items']->{'customer.address2'}, $searched['items'][0]->{'customer.address2'} );
		$this->assertEquals( $saved['items']->{'customer.address3'}, $searched['items'][0]->{'customer.address3'} );
		$this->assertEquals( $saved['items']->{'customer.postal'}, $searched['items'][0]->{'customer.postal'} );
		$this->assertEquals( $saved['items']->{'customer.city'}, $searched['items'][0]->{'customer.city'} );
		$this->assertEquals( $saved['items']->{'customer.state'}, $searched['items'][0]->{'customer.state'} );
		$this->assertEquals( $saved['items']->{'customer.languageid'}, $searched['items'][0]->{'customer.languageid'} );
		$this->assertEquals( $saved['items']->{'customer.countryid'}, $searched['items'][0]->{'customer.countryid'} );
		$this->assertEquals( $saved['items']->{'customer.telephone'}, $searched['items'][0]->{'customer.telephone'} );
		$this->assertEquals( $saved['items']->{'customer.email'}, $searched['items'][0]->{'customer.email'} );
		$this->assertEquals( $saved['items']->{'customer.telefax'}, $searched['items'][0]->{'customer.telefax'} );
		$this->assertEquals( $saved['items']->{'customer.website'}, $searched['items'][0]->{'customer.website'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
