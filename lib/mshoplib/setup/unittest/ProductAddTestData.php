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
		$iface = '\\Aimeos\\MShop\\Context\\Item\\Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding product test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$path = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'product.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for product domain', $path ) );
		}

		$this->createData( $testdata );

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
		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->additional, 'Standard' );
		$manager->begin();

		$domains = ['attribute', 'media', 'price', 'product', 'tag', 'text'];
		$typeIds = $this->getTypeIds( $testdata, ['product/type', 'product/lists/type', 'product/property/type'] );

		$refItems = $this->getRefItems( ['attribute', 'media', 'price', 'tag', 'text'] );

		foreach( $testdata['product'] as $key => $entry )
		{
			if( !isset( $typeIds['product/type'][$entry['product.typeid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No product type ID found for "%1$s"', $entry['product.typeid'] ) );
			}

			$entry['product.typeid'] = $typeIds['product/type'][$entry['product.typeid']];
			$item = $manager->createItem();
			$item->fromArray( $entry );

			$refItems['product/' . $item->getCode()] = $item;

			$item = $this->addPropertyData( $item, $entry, $typeIds );
			$item = $this->addListData( $item, $entry, $typeIds, $refItems, $domains );

			$manager->saveItem( $item );
		}

		$manager->commit();
	}


	/**
	 * Adds the list test data
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface $item Product item
	 * @param array $entry Associative list of key/list pairs
	 * @param array $typeIds Associative list of type/key/ID triple
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 * @param array $domains List of domain names
	 * @return \Aimeos\MShop\Product\Item\Iface Modified product item
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	protected function addListData( \Aimeos\MShop\Product\Item\Iface $item, array $entry, array $typeIds, array $refItems, array $domains )
	{
		foreach( $domains as $domain )
		{
			if( isset( $entry[$domain] ) )
			{
				$manager = \Aimeos\MShop\Factory::createManager( $this->additional, 'product/lists' );

				foreach( (array) $entry[$domain] as $data )
				{
					if( !isset( $typeIds['product/lists/type'][$data['product.lists.typeid']] ) )
					{
						$msg = sprintf( 'No product list type ID found for "%1$s"', $data['product.lists.typeid'] );
						throw new \Aimeos\MW\Setup\Exception( $msg );
					}

					$data['product.lists.typeid'] = $typeIds['product/lists/type'][$data['product.lists.typeid']];
					$listItem = $manager->createItem();
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
	 * @param array $typeIds Associative list of type/key/ID triples
	 * @return \Aimeos\MShop\Product\Item\Iface Modified product item
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	protected function addPropertyData( \Aimeos\MShop\Product\Item\Iface $item, array $entry, array $typeIds )
	{
		if( isset( $entry['product/property'] ) )
		{
			$manager = \Aimeos\MShop\Factory::createManager( $this->additional, 'product/property' );

			foreach( (array) $entry['product/property'] as $data )
			{
				if( !isset( $typeIds['product/property/type'][$data['product.property.typeid']] ) )
				{
					$msg = sprintf( 'No product property type ID found for "%1$s"', $data['product.property.typeid'] );
					throw new \Aimeos\MW\Setup\Exception( $msg );
				}

				$data['product.property.typeid'] = $typeIds['product/property/type'][$data['product.property.typeid']];
				$propItem = $manager->createItem();
				$propItem->fromArray( $data );

				$item->addPropertyItem( $propItem );
			}
		}

		return $item;
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


	/**
	 * Creates the type test data and returns their IDs
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @param array $domains List of domain names
	 * @return array Associative list of type/key/ID triples
	 */
	protected function getTypeIds( array $testdata, array $domains )
	{
		$typeIds = [];

		foreach( $domains as $domain )
		{
			$manager = \Aimeos\MShop\Factory::createManager( $this->additional, $domain );

			foreach( $testdata[$domain] as $key => $entry )
			{
				$item = $manager->createItem();
				$item->fromArray( $entry );

				$typeIds[$domain][$key] = $manager->saveItem( $item )->getId();
			}
		}

		return $typeIds;
	}
}