<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2024
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
		return [
			'Product', 'Index', 'Attribute', 'Media', 'Price', 'Stock', 'Text',
			'MShopSetLocale', 'MShopAddTypeDataDefault', 'MShopAddCodeDataDefault',
			'DemoAddTypeData', 'DemoAddCatalogData', 'DemoAddSupplierData'
		];
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
		$context = $this->context();
		$value = $context->config()->get( 'setup/default/demo', '' );

		if( $value === '' ) {
			return;
		}


		$this->info( 'Processing product demo data', 'vv' );

		$items = $this->removeItems();
		$this->removeStockItems( $items );
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
		$manager = \Aimeos\MShop::create( $context, 'index' );

		foreach( $data as $idx => $entry )
		{
			$item = $manager->create()->fromArray( $entry );

			$this->addRefItems( $item, $entry, $idx );
			$this->addPropertyItems( $item, $entry['property'] ?? [] );

			$manager->save( $item );
			$manager->rate( $item->getId(), $entry['rating'] ?? 0, $entry['ratings'] ?? 0 );

			$this->addStockItems( $item->getId(), $entry['stock'] ?? [] );
		}
	}


	/**
	 * Adds the properties from the given entry data.
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface $item Product item
	 * @param array $entries List of stock entries
	 * @return \Aimeos\MShop\Product\Item\Iface $item Updated product item
	 */
	protected function addPropertyItems( \Aimeos\MShop\Product\Item\Iface $item, array $entries ) : \Aimeos\MShop\Product\Item\Iface
	{
		$manager = \Aimeos\MShop::create( $this->context(), 'product/property' );

		foreach( $entries as $values )
		{
			$propItem = $manager->create()->fromArray( $values );
			$item->addPropertyItem( $propItem );
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
		$manager->begin();

		foreach( $data as $entry )
		{
			$item = $manager->create()->fromArray( $entry )->setProductId( $productId );
			$manager->save( $item, false );
		}

		$manager->commit();
	}


	/**
	 * Deletes the demo product items
	 */
	protected function removeItems() : \Aimeos\Map
	{
		$context = $this->context();
		$domains = ['media', 'price', 'text'];

		$manager = \Aimeos\MShop::create( $context, 'product' );
		$manager->begin();

		$filter = $manager->filter()->add( 'product.code', '=~', 'demo-' )->slice( 0, 0x7fffffff );
		$items = $manager->search( $filter, $domains );

		$this->removeRefItems( $items, $domains );
		$manager->delete( $items );
		$manager->commit();

		return $items;
	}


	/**
	 * Deletes the demo attribute items
	 */
	protected function removeAttributeItems()
	{
		$manager = \Aimeos\MShop::create( $this->context(), 'attribute' );
		$filter = $manager->filter()->add( 'attribute.label', '=~', 'Demo:' );
		$manager->delete( $manager->search( $filter ) );
	}


	/**
	 * Deletes the demo stock items
	 */
	protected function removeStockItems( \Aimeos\Map $products )
	{
		$manager = \Aimeos\MShop::create( $this->context(), 'stock' );
		$filter = $manager->filter()->add( ['stock.productid' => $products] )->slice( 0, 0x7fffffff );
		$manager->delete( $manager->search( $filter ) );
	}
}
