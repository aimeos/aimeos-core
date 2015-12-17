<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\Controller\ExtJS\Tag\Type;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$this->object = new \Aimeos\Controller\ExtJS\Tag\Type\Standard( \TestHelperExtjs::getContext() );
	}


	protected function tearDown()
	{
		$this->object = null;
	}


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '~=' => (object) array( 'tag.type.code' => '' ) ) ) ),
			'sort' => 'tag.type.code',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 2, $result['total'] );
		$this->assertEquals( 'sort', $result['items'][0]->{'tag.type.code'} );
	}


	public function testSaveDeleteItem()
	{
		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'tag.type.code' => 'test',
				'tag.type.label' => 'testLabel',
				'tag.type.domain' => 'product/tag',
				'tag.type.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'tag.type.code' => 'test' ) ) ) )
		);

		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$params = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'tag.type.id'} );
		$this->object->deleteItems( $params );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'tag.type.id'} );
		$this->assertEquals( $saved['items']->{'tag.type.id'}, $searched['items'][0]->{'tag.type.id'} );
		$this->assertEquals( $saved['items']->{'tag.type.code'}, $searched['items'][0]->{'tag.type.code'} );
		$this->assertEquals( $saved['items']->{'tag.type.domain'}, $searched['items'][0]->{'tag.type.domain'} );
		$this->assertEquals( $saved['items']->{'tag.type.label'}, $searched['items'][0]->{'tag.type.label'} );
		$this->assertEquals( $saved['items']->{'tag.type.status'}, $searched['items'][0]->{'tag.type.status'} );

		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
