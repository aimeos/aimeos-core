<?php

namespace Aimeos\Controller\Common\Product\Import\Csv\Processor\Property;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $context;
	private $endpoint;


	protected function setUp()
	{
		\Aimeos\MShop\Factory::setCache( true );

		$this->context = \TestHelperCntl::getContext();
		$this->endpoint = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Done( $this->context, array() );
	}


	protected function tearDown()
	{
		\Aimeos\MShop\Factory::setCache( false );
		\Aimeos\MShop\Factory::clear();
	}


	public function testProcess()
	{
		$mapping = array(
			0 => 'product.property.type',
			1 => 'product.property.value',
			2 => 'product.property.languageid',
			3 => 'product.property.type',
			4 => 'product.property.value',
		);

		$data = array(
			0 => 'package-weight',
			1 => '3.00',
			2 => 'de',
			3 => 'package-width',
			4 => '50',
		);

		$product = $this->create( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Property\Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );
		$items = $this->getProperties( $product->getId() );
		$this->delete( $product );


		$pos = 0;
		$expected = array(
			array( 'package-weight', '3.00', 'de' ),
			array( 'package-width', '50', null ),
		);

		$this->assertEquals( 2, count( $items ) );

		foreach( $items as $item )
		{
			$this->assertEquals( $expected[$pos][0], $item->getType() );
			$this->assertEquals( $expected[$pos][1], $item->getValue() );
			$this->assertEquals( $expected[$pos][2], $item->getLanguageId() );
			$pos++;
		}
	}


	public function testProcessUpdate()
	{
		$mapping = array(
			0 => 'product.property.type',
			1 => 'product.property.value',
		);

		$data = array(
			0 => 'package-weight',
			1 => '3.00',
		);

		$dataUpdate = array(
			0 => 'package-height',
			1 => '10',
		);

		$product = $this->create( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Property\Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );

		$result = $object->process( $product, $dataUpdate );

		$product = $this->get( 'job_csv_test' );
		$items = $this->getProperties( $product->getId() );
		$this->delete( $product );


		$item = reset( $items );

		$this->assertEquals( 1, count( $items ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Product\\Item\\Property\\Iface', $item );

		$this->assertEquals( 'package-height', $item->getType() );
		$this->assertEquals( '10', $item->getValue() );
	}


	public function testProcessDelete()
	{
		$mapping = array(
			0 => 'product.property.type',
			1 => 'product.property.value',
		);

		$data = array(
			0 => 'package-weight',
			1 => '3.00',
		);

		$product = $this->create( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Property\Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Property\Standard( $this->context, array(), $this->endpoint );
		$result = $object->process( $product, array() );

		$product = $this->get( 'job_csv_test' );
		$items = $this->getProperties( $product->getId() );
		$this->delete( $product );


		$this->assertEquals( 0, count( $items ) );
	}


	public function testProcessEmpty()
	{
		$mapping = array(
			0 => 'product.property.type',
			1 => 'product.property.value',
			2 => 'product.property.type',
			3 => 'product.property.value',
		);

		$data = array(
			0 => '',
			1 => '',
			2 => 'package-weight',
			3 => '3.00',
		);

		$product = $this->create( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Property\Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );
		$items = $this->getProperties( $product->getId() );
		$this->delete( $product );


		$this->assertEquals( 1, count( $items ) );
	}


	protected function create( $code )
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->context );
		$typeManager = $manager->getSubManager( 'type' );

		$typeSearch = $typeManager->createSearch();
		$typeSearch->setConditions( $typeSearch->compare( '==', 'product.type.code', 'default' ) );
		$typeResult = $typeManager->searchItems( $typeSearch );

		if( ( $typeItem = reset( $typeResult ) ) === false ) {
			throw new \Exception( 'No product type "default" found' );
		}

		$item = $manager->createItem();
		$item->setTypeid( $typeItem->getId() );
		$item->setCode( $code );

		$manager->saveItem( $item );

		return $item;
	}


	protected function delete( \Aimeos\MShop\Product\Item\Iface $product )
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->context );
		$listManager = $manager->getSubManager( 'lists' );

		foreach( $product->getListItems('attribute') as $listItem ) {
			$listManager->deleteItem( $listItem->getId() );
		}

		$manager->deleteItem( $product->getId() );
	}


	protected function get( $code )
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->context );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );

		$result = $manager->searchItems( $search, array('attribute') );

		if( ( $item = reset( $result ) ) === false ) {
			throw new \Exception( sprintf( 'No product item for code "%1$s"', $code ) );
		}

		return $item;
	}


	protected function getProperties( $prodid )
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->context )->getSubManager( 'property' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.property.parentid', $prodid ) );
		$search->setSortations( array( $search->sort( '+', 'product.property.type.code' ) ) );

		return $manager->searchItems( $search );
	}
}