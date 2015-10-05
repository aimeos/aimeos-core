<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
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
		return array( 'MShopAddTypeDataDefault', 'MShopAddWarehouseDataDefault' );
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
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		$this->process();
	}


	/**
	 * Insert product data.
	 */
	protected function process()
	{
		$this->msg( 'Processing product demo data', 0 );

		$context = $this->getContext();
		$manager = \Aimeos\MShop\Factory::createManager( $context, 'product' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '=~', 'product.code', 'demo-' ) );
		$products = $manager->searchItems( $search );


		foreach( $products as $item )
		{
			$this->removeItems( $item->getId(), 'product/lists', 'product', 'attribute' );
			$this->removeItems( $item->getId(), 'product/lists', 'product', 'media' );
			$this->removeItems( $item->getId(), 'product/lists', 'product', 'price' );
			$this->removeItems( $item->getId(), 'product/lists', 'product', 'text' );
			$this->removeListItems( $item->getId(), 'product/lists', 'product' );
		}

		$manager->deleteItems( array_keys( $products ) );


		if( $context->getConfig()->get( 'setup/default/demo', false ) == true )
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
			$item = $manager->createItem();
			$item->setTypeId( $this->getTypeId( 'product/type', 'product', $entry['type'] ) );
			$item->setCode( $entry['code'] );
			$item->setLabel( $entry['label'] );
			$item->setSupplierCode( $entry['supplier'] );
			$item->setDateStart( $entry['start'] );
			$item->setDateEnd( $entry['end'] );
			$item->setStatus( $entry['status'] );

			$manager->saveItem( $item );

			$this->addPropertyItems( $entry, $item->getId() );
			$this->addRefItems( $entry, $item->getId() );
		}
	}


	/**
	 * Adds the properties from the given entry data.
	 *
	 * @param array $entry Associative list of data with stock, attribute, media, price, text and product sections
	 * @param string $id Parent ID for inserting the items
	 */
	protected function addPropertyItems( array $entry, $id )
	{
		if( isset( $entry['property'] ) )
		{
			$context = $this->getContext();
			$manager = \Aimeos\MShop\Factory::createManager( $context, 'product/property' );

			foreach( (array) $entry['property'] as $values )
			{
				$item = $manager->createItem();
				$item->setParentId( $id );
				$item->setLanguageId( $values['languageid'] );
				$item->setTypeId( $this->getTypeId( 'product/property/type', 'product/property', $values['type'] ) );
				$item->setLanguageId( $values['languageid'] );
				$item->setValue( $values['value'] );

				$manager->saveItem( $item, false );
			}
		}
	}


	/**
	 * Adds the referenced items from the given entry data.
	 *
	 * @param array $entry Associative list of data with stock, attribute, media, price, text and product sections
	 * @param string $id Parent ID for inserting the items
	 */
	protected function addRefItems( array $entry, $id )
	{
		if( isset( $entry['stock'] ) ) {
			$this->addProductStock( $id, $entry['stock'] );
		}

		if( isset( $entry['attribute'] ) ) {
			$this->addAttributes( $id, $entry['attribute'], 'product' );
		}

		if( isset( $entry['media'] ) ) {
			$this->addMedia( $id, $entry['media'], 'product' );
		}

		if( isset( $entry['price'] ) ) {
			$this->addPrices( $id, $entry['price'], 'product' );
		}

		if( isset( $entry['text'] ) ) {
			$this->addTexts( $id, $entry['text'], 'product' );
		}

		if( isset( $entry['product'] ) ) {
			$this->addProducts( $id, $entry['product'], 'product' );
		}
	}
}
