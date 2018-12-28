<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds demo records to product tables.
 */
class DemoAddProductData extends \Aimeos\MW\Setup\Task\MShopAddDataAbstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopAddTypeDataDefault', 'MShopAddCodeDataDefault' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'DemoRebuildIndex' );
	}


	/**
	 * Insert product data.
	 */
	public function migrate()
	{
		$this->msg( 'Processing product demo data', 0 );

		$context = $this->getContext();
		$value = $context->getConfig()->get( 'setup/default/demo', '' );

		if( $value === '' )
		{
			$this->status( 'OK' );
			return;
		}


		$productCodes = [];
		$domains = ['media', 'price', 'text'];
		$manager = \Aimeos\MShop\Factory::createManager( $context, 'product' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '=~', 'product.code', 'demo-' ) );
		$products = $manager->searchItems( $search, $domains );

		foreach( $domains as $domain )
		{
			$rmIds = [];

			foreach( $products as $item ) {
				$rmIds = array_merge( $rmIds, array_keys( $item->getRefItems( $domain, null, null, false ) ) );
			}

			\Aimeos\MShop\Factory::createManager( $context, $domain )->deleteItems( $rmIds );
		}

		$manager->deleteItems( array_keys( $products ) );
		$this->removeAttributeItems();
		$this->removeStockItems();


		if( $value === '1' )
		{
			$this->addDemoData();
			$this->status( 'added' );
		}
		else
		{
			$this->status( 'removed' );
		}
	}


	/**
	 * Adds the demo data to the database.
	 *
	 * @throws \Aimeos\MShop\Exception If the file isn't found
	 */
	protected function addDemoData()
	{
		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'demo-product.php';

		if( ( $data = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for product domain', $path ) );
		}

		$context = $this->getContext();
		$manager = \Aimeos\MShop\Factory::createManager( $context, 'product' );

		foreach( $data as $entry )
		{
			$item = $manager->createItem( $entry['product.type'], 'product' );
			$item->fromArray( $entry );

			$this->addRefItems( $item, $entry );
			$this->addPropertyItems( $item, $entry );

			$manager->saveItem( $item );

			if( isset( $entry['stock'] ) ) {
				$this->addStockItems( $entry['product.code'], $entry['stock'] );
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
			$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product/property' );

			foreach( (array) $entry['property'] as $values )
			{
				$propItem = $manager->createItem( $values['product.property.type'], 'product' );
				$propItem->fromArray( $values );

				$item->addPropertyItem( $propItem );
			}
		}

		return $item;
	}


	/**
	 * Adds the referenced items from the given entry data.
	 *
	 * @param \Aimeos\MShop\Common\Item\ListRef\Iface $item Item with list items
	 * @param array $entry Associative list of data with stock, attribute, media, price, text and product sections
	 * @return \Aimeos\MShop\Common\Item\ListRef\Iface $item Updated item
	 */
	protected function addRefItems( \Aimeos\MShop\Common\Item\ListRef\Iface $item, array $entry )
	{
		$context = $this->getContext();
		$domain = $item->getResourceType();
		$listManager = \Aimeos\MShop\Factory::createManager( $context, $domain . '/lists' );

		if( isset( $entry['attribute'] ) )
		{
			$manager = \Aimeos\MShop\Factory::createManager( $context, 'attribute' );

			foreach( $entry['attribute'] as $data )
			{
				$listItem = $listManager->createItem( $data[$domain . '.lists.type'], 'attribute' );
				$listItem->fromArray( $data );

				$refItem = $manager->createItem( $data['attribute.type'], $domain );
				$refItem->fromArray( $data );

				try
				{
					$manager = \Aimeos\MShop\Factory::createManager( $context, 'attribute' );
					$refItem = $manager->findItem( $data['attribute.code'], [], $domain, $data['attribute.type'] );
				}
				catch( \Aimeos\MShop\Exception $e ) {}

				$refItem = $this->addRefItems( $refItem, $data );

				$item->addListItem( 'attribute', $listItem, $refItem );
			}
		}

		foreach( ['media', 'price', 'text'] as $refDomain )
		{
			if( isset( $entry[$refDomain] ) )
			{
				$manager = \Aimeos\MShop\Factory::createManager( $context, $refDomain );

				foreach( $entry[$refDomain] as $data )
				{
					$listItem = $listManager->createItem( $data[$domain . '.lists.type'], $refDomain );
					$listItem->fromArray( $data );

					$refItem = $manager->createItem( $data[$refDomain . '.type'], $domain );
					$refItem->fromArray( $data );

					$item->addListItem( $refDomain, $listItem, $refItem );
				}
			}
		}

		if( isset( $entry['product'] ) )
		{
			$manager = \Aimeos\MShop\Factory::createManager( $context, 'product' );

			foreach( $entry['product'] as $data )
			{
				$listItem = $listManager->createItem( $data['product.lists.type'], 'product' );
				$listItem->fromArray( $data );
				$listItem->setRefId( $manager->findItem( $data['product.code'] )->getId() );

				$item->addListItem( 'product', $listItem );
			}
		}

		return $item;
	}


	/**
	 * Adds stock levels to the given product in the database.
	 *
	 * @param string $productcode Code of the product item where the stock levels should be associated to
	 * @param array $data Two dimensional associative list of product stock data
	 */
	protected function addStockItems( $productcode, array $data )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'stock/type' );

		$types = [];
		foreach( $manager->searchItems( $manager->createSearch() ) as $id => $item ) {
			$types[$item->getCode()] = $id;
		}

		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'stock' );

		foreach( $data as $entry )
		{
			$item = $manager->createItem( $entry['stock.type'], 'product' );
			$item->setProductCode( $productcode )->fromArray( $entry );

			$manager->saveItem( $item, false );
		}
	}


	/**
	 * Deletes the demo attribute items
	 */
	protected function removeAttributeItems()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'attribute' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '=~', 'attribute.label', 'Demo:' ) );

		$manager->deleteItems( array_keys( $manager->searchItems( $search ) ) );
	}


	/**
	 * Deletes the demo stock items
	 */
	protected function removeStockItems()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'stock' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '=~', 'stock.productcode', 'demo-' ) );

		$manager->deleteItems( array_keys( $manager->searchItems( $search ) ) );
	}
}
