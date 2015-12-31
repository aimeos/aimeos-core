<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds demo records to product tables.
 */
class MW_Setup_Task_DemoAddProductData extends MW_Setup_Task_MShopAddDataAbstract
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
	protected function _mysql()
	{
		$this->_process();
	}


	/**
	 * Insert product data.
	 */
	protected function _process()
	{
		$this->_msg( 'Processing product demo data', 0 );

		$context = $this->_getContext();
		$value = $context->getConfig()->get( 'setup/default/demo', '' );

		if( $value === '' )
		{
			$this->_status( 'OK' );
			return;
		}


		$manager = MShop_Factory::createManager( $context, 'product' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '=~', 'product.code', 'demo-' ) );
		$products = $manager->searchItems( $search );

		foreach( $products as $item )
		{
			$this->_removeItems( $item->getId(), 'product/list', 'product', 'attribute' );
			$this->_removeItems( $item->getId(), 'product/list', 'product', 'media' );
			$this->_removeItems( $item->getId(), 'product/list', 'product', 'price' );
			$this->_removeItems( $item->getId(), 'product/list', 'product', 'text' );
			$this->_removeListItems( $item->getId(), 'product/list', 'product' );
		}

		$manager->deleteItems( array_keys( $products ) );


		if( $value === '1' )
		{
			$this->_addDemoData();
			$this->_status( 'added' );
		}
		else
		{
			$this->_status( 'removed' );
		}
	}


	/**
	 * Adds the demo data to the database.
	 *
	 * @throws MShop_Exception If the file isn't found
	 */
	protected function _addDemoData()
	{
		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'demo-product.php';

		if( ( $data = include( $path ) ) == false ) {
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for product domain', $path ) );
		}

		$context = $this->_getContext();
		$manager = MShop_Factory::createManager( $context, 'product' );

		foreach( $data as $entry )
		{
			$item = $manager->createItem();
			$item->setTypeId( $this->_getTypeId( 'product/type', 'product', $entry['type'] ) );
			$item->setCode( $entry['code'] );
			$item->setLabel( $entry['label'] );
			$item->setSupplierCode( $entry['supplier'] );
			$item->setDateStart( $entry['start'] );
			$item->setDateEnd( $entry['end'] );
			$item->setStatus( $entry['status'] );

			$manager->saveItem( $item );

			$this->_addPropertyItems( $entry, $item->getId() );
			$this->_addRefItems( $entry, $item->getId() );
		}
	}


	/**
	 * Adds the properties from the given entry data.
	 *
	 * @param array $entry Associative list of data with stock, attribute, media, price, text and product sections
	 * @param string $id Parent ID for inserting the items
	 */
	protected function _addPropertyItems( array $entry, $id )
	{
		if( isset( $entry['property'] ) )
		{
			$context = $this->_getContext();
			$manager = MShop_Factory::createManager( $context, 'product/property' );

			foreach( (array) $entry['property'] as $values )
			{
				$item = $manager->createItem();
				$item->setParentId( $id );
				$item->setLanguageId( $values['languageid'] );
				$item->setTypeId( $this->_getTypeId( 'product/property/type', 'product/property', $values['type'] ) );
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
	protected function _addRefItems( array $entry, $id )
	{
		if( isset( $entry['stock'] ) ) {
			$this->_addProductStock( $id, $entry['stock'] );
		}

		if( isset( $entry['attribute'] ) ) {
			$this->_addAttributes( $id, $entry['attribute'], 'product' );
		}

		if( isset( $entry['media'] ) ) {
			$this->_addMedia( $id, $entry['media'], 'product' );
		}

		if( isset( $entry['price'] ) ) {
			$this->_addPrices( $id, $entry['price'], 'product' );
		}

		if( isset( $entry['text'] ) ) {
			$this->_addTexts( $id, $entry['text'], 'product' );
		}

		if( isset( $entry['product'] ) ) {
			$this->_addProducts( $id, $entry['product'], 'product' );
		}
	}
}
