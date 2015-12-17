<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\Controller\ExtJS\Tag;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$this->object = new \Aimeos\Controller\ExtJS\Tag\Standard( \TestHelperExtjs::getContext() );
	}


	protected function tearDown()
	{
		$this->object = null;
	}


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'tag.languageid' => 'de' ) ) ) ),
			'sort' => 'tag.label',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 6, $result['total'] );
		$this->assertEquals( 'Cappuccino', $result['items'][0]->{'tag.label'} );
	}


	public function testSaveDeleteItem()
	{
		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'tag.type.code' => 'taste' ) ) ) )
		);

		$typeCtrl = new \Aimeos\Controller\ExtJS\Tag\Type\Standard( \TestHelperExtjs::getContext() );
		$types = $typeCtrl->searchItems( $searchParams );
		$this->assertEquals( 1, count( $types['items'] ) );


		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'tag.typeid' => $types['items'][0]->{'tag.type.id'},
				'tag.languageid' => 'de',
				'tag.label' => 'unittest',
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'tag.label' => 'unittest' ) ) ) )
		);

		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'tag.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'tag.id'} );
		$this->assertEquals( $saved['items']->{'tag.id'}, $searched['items'][0]->{'tag.id'} );
		$this->assertEquals( $saved['items']->{'tag.typeid'}, $searched['items'][0]->{'tag.typeid'} );
		$this->assertEquals( $saved['items']->{'tag.languageid'}, $searched['items'][0]->{'tag.languageid'} );
		$this->assertEquals( $saved['items']->{'tag.label'}, $searched['items'][0]->{'tag.label'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
