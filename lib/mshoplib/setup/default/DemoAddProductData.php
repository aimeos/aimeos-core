<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds demo records to product tables.
 */
class DemoAddProductData extends MShopAddDataAbstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['MShopAddTypeDataDefault', 'MShopAddCodeDataDefault', 'DemoAddTypeData'];
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function before() : array
	{
		return ['DemoRebuildIndex'];
	}


	/**
	 * Insert product data.
	 */
	public function up()
	{
		$this->info( 'Processing product demo data', 'v' );

		$context = $this->context();
		$value = $context->getConfig()->get( 'setup/default/demo', '' );

		if( $value === '' ) {
			return;
		}


		$domains = ['media', 'price', 'text'];
		$manager = \Aimeos\MShop::create( $context, 'product' );

		$search = $manager->filter();
		$search->setConditions( $search->compare( '=~', 'product.code', 'demo-' ) );
		$products = $manager->search( $search, $domains );

		foreach( $domains as $domain )
		{
			$rmIds = map();

			foreach( $products as $item ) {
				$rmIds = $rmIds->merge( $item->getRefItems( $domain, null, null, false )->keys() );
			}

			\Aimeos\MShop::create( $context, $domain )->delete( $rmIds->toArray() );
		}

		$manager->delete( $products->toArray() );
		$this->removeStockItems( $products->keys() );
		$this->removeAttributeItems();


		if( $value === '1' ) {
			$this->addDemoData();
		}
	}


	/**
	 * Adds the demo data to the database.
	 *
	 * @throws \RuntimeException If the file isn't found
	 */
	protected function addDemoData()
	{
		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'demo-product.php';

		if( ( $data = include( $path ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No file "%1$s" found for product domain', $path ) );
		}

		$context = $this->context();
		$manager = \Aimeos\MShop::create( $context, 'product' );

		foreach( $data as $entry )
		{
			$item = $manager->create()->fromArray( $entry );

			$this->addRefItems( $item, $entry );
			$this->addPropertyItems( $item, $entry );

			$manager->save( $item );

			if( isset( $entry['stock'] ) ) {
				$this->addStockItems( $item->getId(), $entry['stock'] );
			}
		}
	}


	/**
	 * Adds the properties from the given entry data.
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface $item Product item
	 * @param array $entry Associative list of data with stock, attribute, media, price, text and product sections
	 * @return \Aimeos\MShop\Product\Item\Iface $item Updated product item
	 */
	protected function addPropertyItems( \Aimeos\MShop\Product\Item\Iface $item, array $entry )
	{
		if( isset( $entry['property'] ) )
		{
			$manager = \Aimeos\MShop::create( $this->context(), 'product/property' );

			foreach( (array) $entry['property'] as $values )
			{
				$propItem = $manager->create()->fromArray( $values );
				$item->addPropertyItem( $propItem );
			}
		}

		return $item;
	}


	/**
	 * Adds the referenced items from the given entry data.
	 *
	 * @param \Aimeos\MShop\Common\Item\ListsRef\Iface $item Item with list items
	 * @param array $entry Associative list of data with stock, attribute, media, price, text and product sections
	 * @return \Aimeos\MShop\Common\Item\ListsRef\Iface $item Updated item
	 */
	protected function addRefItems( \Aimeos\MShop\Common\Item\ListsRef\Iface $item, array $entry )
	{
		$context = $this->context();
		$domain = $item->getResourceType();
		$listManager = \Aimeos\MShop::create( $context, $domain . '/lists' );

		if( isset( $entry['attribute'] ) )
		{
			$manager = \Aimeos\MShop::create( $context, 'attribute' );

			foreach( $entry['attribute'] as $data )
			{
				$listItem = $listManager->create()->fromArray( $data );
				$refItem = $manager->create()->fromArray( $data );

				try
				{
					$manager = \Aimeos\MShop::create( $context, 'attribute' );
					$refItem = $manager->find( $refItem->getCode(), [], $domain, $refItem->getType() );
				}
				catch( \RuntimeException $e ) { ; } // attribute doesn't exist yet

				$refItem = $this->addRefItems( $refItem, $data );
				$item->addListItem( 'attribute', $listItem, $refItem );
			}
		}

		foreach( ['media', 'price', 'text'] as $refDomain )
		{
			if( isset( $entry[$refDomain] ) )
			{
				$manager = \Aimeos\MShop::create( $context, $refDomain );

				foreach( $entry[$refDomain] as $data )
				{
					$listItem = $listManager->create()->fromArray( $data );
					$refItem = $manager->create()->fromArray( $data );

					if( isset( $data['property'] ) )
					{
						foreach( (array) $data['property'] as $property )
						{
							$propItem = $manager->createPropertyItem()->fromArray( $property );
							$refItem->addPropertyItem( $propItem );
						}
					}

					$item->addListItem( $refDomain, $listItem, $refItem );
				}
			}
		}

		if( isset( $entry['product'] ) )
		{
			$manager = \Aimeos\MShop::create( $context, 'product' );

			foreach( $entry['product'] as $data )
			{
				$listItem = $listManager->create()->fromArray( $data );
				$listItem->setRefId( $manager->find( $data['product.code'] )->getId() );

				$item->addListItem( 'product', $listItem );
			}
		}

		return $item;
	}


	/**
	 * Adds stock levels to the given product in the database.
	 *
	 * @param string $productId ID of the product item where the stock levels should be associated to
	 * @param array $data Two dimensional associative list of product stock data
	 */
	protected function addStockItems( $productId, array $data )
	{
		$manager = \Aimeos\MShop::create( $this->context(), 'stock' );

		foreach( $data as $entry )
		{
			$item = $manager->create()->fromArray( $entry )->setProductId( $productId );
			$manager->save( $item, false );
		}
	}


	/**
	 * Deletes the demo attribute items
	 */
	protected function removeAttributeItems()
	{
		$manager = \Aimeos\MShop::create( $this->context(), 'attribute' );

		$search = $manager->filter();
		$search->setConditions( $search->compare( '=~', 'attribute.label', 'Demo:' ) );

		$manager->delete( $manager->search( $search ) );
	}


	/**
	 * Deletes the demo stock items
	 */
	protected function removeStockItems( \Aimeos\Map $prodIds )
	{
		$manager = \Aimeos\MShop::create( $this->context(), 'stock' );

		$filter = $manager->filter()->add( ['stock.productid' => $prodIds] )->slice( 0, $prodIds->count() );

		$manager->delete( $manager->search( $filter ) );
	}
}
