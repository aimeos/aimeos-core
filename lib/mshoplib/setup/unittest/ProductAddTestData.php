<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds product test data
 */
class ProductAddTestData extends \Aimeos\MW\Setup\Task\Base
{

	/**
	 * Returns the list of task names which this task depends on
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return ['MShopSetLocale', 'AttributeAddTestData', 'MediaAddTestData', 'PriceAddTestData', 'TagAddTestData', 'TextAddTestData'];
	}


	/**
	 * Returns the list of task names which depends on this task
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return ['CatalogRebuildTestIndex'];
	}


	/**
	 * Adds product test data
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->additional );

		$this->msg( 'Adding product test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$config = $this->additional->getConfig();
		$name = $config->get( 'mshop/product/manager/name' );

		\Aimeos\MShop\Factory::clear();
		$config->set( 'mshop/product/manager/name', 'Standard' );

		$this->createData( $this->getData() );

		$config->set( 'mshop/product/manager/name', $name );
		\Aimeos\MShop\Factory::clear();

		$this->status( 'done' );
	}


	/**
	 * Creates the test data
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	protected function createData( array $testdata )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->additional, 'product' );
		$manager->begin();

		$domains = ['attribute', 'media', 'price', 'product', 'tag', 'text'];

		$this->addTypeItems( $testdata, ['product/type', 'product/lists/type', 'product/property/type'] );
		$refItems = $this->getRefItems( ['attribute', 'media', 'price', 'tag', 'text'] );
		$items = [];

		foreach( $testdata['product'] as $key => $entry )
		{
			$item = $manager->createItem( $entry['product.type'], 'product' );
			$item->fromArray( $entry );

			$refItems['product/' . $item->getCode()] = $item;

			$item = $this->addPropertyData( $item, $entry );
			$items[] = $this->addListData( $item, $entry, $refItems, $domains );
		}

		$manager->saveItems( $items );

		$manager->commit();
	}


	/**
	 * Creates the type test data
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @param array $domains List of domain names
	 */
	protected function addTypeItems( array $testdata, array $domains )
	{
		foreach( $domains as $domain )
		{
			$manager = \Aimeos\MShop\Factory::createManager( $this->additional, $domain );

			foreach( $testdata[$domain] as $key => $entry )
			{
				$item = $manager->createItem();
				$item->fromArray( $entry );

				$manager->saveItem( $item );
			}
		}
	}


	/**
	 * Adds the list test data
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface $item Product item
	 * @param array $entry Associative list of key/list pairs
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 * @param array $domains List of domain names
	 * @return \Aimeos\MShop\Product\Item\Iface Modified product item
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	protected function addListData( \Aimeos\MShop\Product\Item\Iface $item, array $entry, array $refItems, array $domains )
	{
		foreach( $domains as $domain )
		{
			if( isset( $entry[$domain] ) )
			{
				$manager = \Aimeos\MShop\Factory::createManager( $this->additional, 'product/lists' );

				foreach( (array) $entry[$domain] as $data )
				{
					$listItem = $manager->createItem( $data['product.lists.type'], $domain );
					$listItem->fromArray( $data );

					$refItem = ( isset( $refItems[$data['product.lists.refid']] ) ? $refItems[$data['product.lists.refid']] : null );
					$item->addListItem( $domain, $listItem, $refItem );
				}
			}
		}

		return $item;
	}


	/**
	 * Adds the property test data
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface $item Product item
	 * @param array $entry Associative list of key/list pairs
	 * @return \Aimeos\MShop\Product\Item\Iface Modified product item
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	protected function addPropertyData( \Aimeos\MShop\Product\Item\Iface $item, array $entry )
	{
		if( isset( $entry['product/property'] ) )
		{
			$manager = \Aimeos\MShop\Factory::createManager( $this->additional, 'product/property' );

			foreach( (array) $entry['product/property'] as $data )
			{
				$propItem = $manager->createItem( $data['product.property.type'], 'product' );
				$propItem->fromArray( $data );

				$item->addPropertyItem( $propItem );
			}
		}

		return $item;
	}


	/**
	 * Returns the test data
	 *
	 * @return array Multi-dimensional associative array
	 */
	protected function getData()
	{
		$path = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'product.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for product domain', $path ) );
		}

		return $testdata;
	}


	/**
	 * Returns the items from the given domains
	 *
	 * @param array $domains List of domain names
	 * @return array Associative list of key/ID pairs
	 */
	protected function getRefItems( array $domains )
	{
		$list = [];

		foreach( $domains as $domain )
		{
			$manager = \Aimeos\MShop\Factory::createManager( $this->additional, $domain );
			$search = $manager->createSearch()->setSlice( 0, 0x7fffffff );

			foreach( $manager->searchItems( $search ) as $item ) {
				$list[$domain . '/' . $item->getLabel()] = $item;
			}
		}

		return $list;
	}
}