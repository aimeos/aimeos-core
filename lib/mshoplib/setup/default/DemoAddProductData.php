<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds demo records to product tables.
 */
class MW_Setup_Task_DemoAddProductData extends MW_Setup_Task_MShopAddDataAbstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopAddTypeDataDefault', 'MShopAddWarehouseDataDefault' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
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

		$context =  $this->_getContext();
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


		if( $context->getConfig()->get( 'setup/default/demo', false ) == true )
		{
			$ds = DIRECTORY_SEPARATOR;
			$path = __DIR__ . $ds . 'data' . $ds . 'demo-product.php';

			if( ( $data = include( $path ) ) == false ) {
				throw new MShop_Exception( sprintf( 'No file "%1$s" found for product domain', $path ) );
			}


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


				if( isset( $entry['stock'] ) ) {
					$this->_addProductStock( $item->getId(), $entry['stock'] );
				}

				if( isset( $entry['attribute'] ) ) {
					$this->_addAttributes( $item->getId(), $entry['attribute'], 'product' );
				}

				if( isset( $entry['media'] ) ) {
					$this->_addMedia( $item->getId(), $entry['media'], 'product' );
				}

				if( isset( $entry['price'] ) ) {
					$this->_addPrices( $item->getId(), $entry['price'], 'product' );
				}

				if( isset( $entry['text'] ) ) {
					$this->_addTexts( $item->getId(), $entry['text'], 'product' );
				}

				if( isset( $entry['product'] ) ) {
					$this->_addProducts( $item->getId(), $entry['product'], 'product' );
				}
			}

			$this->_status( 'added' );
		}
		else
		{
			$this->_status( 'removed' );
		}
	}
}
