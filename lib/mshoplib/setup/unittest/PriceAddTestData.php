<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds price test data.
 */
class MW_Setup_Task_PriceAddTestData extends MW_Setup_Task_Abstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'LocaleAddTestData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'CatalogRebuildTestIndex' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process();
	}


	/**
	 * Adds price test data.
	 */
	protected function _process()
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $this->_additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->_msg( 'Adding price test data', 0 );
		$this->_additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'price.php';

		if( ( $testdata = include( $path ) ) == false ){
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for price domain', $path ) );
		}

		$this->_addPriceData( $testdata );

		$this->_status( 'done' );
	}


	/**
	 * Adds the price test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws MW_Setup_Exception If a required ID is not available
	 */
	private function _addPriceData( array $testdata )
	{
		$priceManager = MShop_Price_Manager_Factory::createManager( $this->_additional, 'Default' );
		$priceTypeManager = $priceManager->getSubManager( 'type', 'Default' );

		$ptypeIds = array();
		$ptype = $priceTypeManager->createItem();

		$this->_conn->begin();

		foreach( $testdata['price/type'] as $key => $dataset )
		{
			$ptype->setId( null );
			$ptype->setCode( $dataset['code'] );
			$ptype->setDomain( $dataset['domain'] );
			$ptype->setLabel( $dataset['label'] );
			$ptype->setStatus( $dataset['status'] );

			$priceTypeManager->saveItem( $ptype );
			$ptypeIds[ $key ] = $ptype->getId();
		}

		$price = $priceManager->createItem();
		foreach( $testdata['price'] as $key => $dataset )
		{
			if ( !isset( $ptypeIds[ $dataset['typeid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No price type ID found for "%1$s"', $dataset['typeid'] ) );
			}

			$price->setId( null );
			$price->setCurrencyId( $dataset['currencyid'] );
			$price->setTypeId( $ptypeIds[ $dataset['typeid'] ] );
			$price->setDomain( $dataset['domain'] );
			$price->setLabel( $dataset['label'] );
			$price->setQuantity( $dataset['quantity'] );
			$price->setValue( $dataset['value'] );
			$price->setCosts( $dataset['shipping'] );
			$price->setRebate( $dataset['rebate'] );
			$price->setTaxRate( $dataset['taxrate'] );
			$price->setStatus( $dataset['status'] );

			$priceManager->saveItem( $price, false );
		}

		$this->_conn->commit();
	}
}