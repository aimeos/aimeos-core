<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
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
		return ['MShopSetLocale'];
	}


	/**
	 * Adds price test data.
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->additional );

		$this->msg( 'Adding price test data', 0 );
		$this->additional->setEditor( 'core:lib/mshoplib' );

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
		$priceManager = \Aimeos\MShop\Price\Manager\Factory::create( $this->additional, 'Standard' );
		$priceTypeManager = $priceManager->getSubManager( 'type', 'Standard' );
		$propTypeManager = $priceManager->getSubManager( 'property', 'Standard' )->getSubManager( 'type', 'Standard' );

		$priceManager->begin();

		$ptype = $priceTypeManager->createItem();
		foreach( $testdata['price/type'] as $key => $dataset )
		{
			$ptype->setId( null );
			$ptype->setCode( $dataset['code'] );
			$ptype->setDomain( $dataset['domain'] );
			$ptype->setLabel( $dataset['label'] );
			$ptype->setStatus( $dataset['status'] );

			$priceTypeManager->saveItem( $ptype );
		}

		$propTypeItem = $propTypeManager->createItem();
		foreach( $testdata['price/property/type'] as $key => $dataset )
		{
			$propTypeItem->setId( null );
			$propTypeItem->setCode( $dataset['code'] );
			$propTypeItem->setDomain( $dataset['domain'] );
			$propTypeItem->setLabel( $dataset['label'] );
			$propTypeItem->setStatus( $dataset['status'] );

			$propTypeManager->saveItem( $propTypeItem );
		}

		$price = $priceManager->createItem();
		foreach( $testdata['price'] as $key => $dataset )
		{
			$price->setId( null );
			$price->setCurrencyId( $dataset['currencyid'] );
			$price->setType( $dataset['type'] );
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

		$priceManager->commit();
	}
}
