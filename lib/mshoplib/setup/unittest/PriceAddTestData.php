<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds price test data.
 */
class PriceAddTestData extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'CatalogRebuildTestIndex' );
	}


	/**
	 * Adds price test data.
	 */
	public function migrate()
	{
		$iface = '\\Aimeos\\MShop\\Context\\Item\\Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding price test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'price.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for price domain', $path ) );
		}

		$this->addPriceData( $testdata );

		$this->status( 'done' );
	}


	/**
	 * Adds the price test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws \Aimeos\MW\Setup\Exception If a required ID is not available
	 */
	private function addPriceData( array $testdata )
	{
		$priceManager = \Aimeos\MShop\Price\Manager\Factory::createManager( $this->additional, 'Standard' );
		$priceTypeManager = $priceManager->getSubManager( 'type', 'Standard' );

		$ptypeIds = [];
		$ptype = $priceTypeManager->createItem();

		$this->conn->begin();

		foreach( $testdata['price/type'] as $key => $dataset )
		{
			$ptype->setId( null );
			$ptype->setCode( $dataset['code'] );
			$ptype->setDomain( $dataset['domain'] );
			$ptype->setLabel( $dataset['label'] );
			$ptype->setStatus( $dataset['status'] );

			$priceTypeManager->saveItem( $ptype );
			$ptypeIds[$key] = $ptype->getId();
		}

		$price = $priceManager->createItem();
		foreach( $testdata['price'] as $key => $dataset )
		{
			if( !isset( $ptypeIds[$dataset['typeid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No price type ID found for "%1$s"', $dataset['typeid'] ) );
			}

			$price->setId( null );
			$price->setCurrencyId( $dataset['currencyid'] );
			$price->setTypeId( $ptypeIds[$dataset['typeid']] );
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

		$this->conn->commit();
	}
}