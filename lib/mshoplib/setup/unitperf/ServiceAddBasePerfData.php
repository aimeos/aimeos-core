<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds performance records to product table.
 */
class ServiceAddBasePerfData extends \Aimeos\MW\Setup\Task\Base
{
	public function __construct( \Aimeos\MW\Setup\DBSchema\Iface $schema, \Aimeos\MW\DB\Connection\Iface $conn, $additional = null )
	{
		$iface = '\\Aimeos\\MShop\\Context\\Item\\Iface';
		if( !( $additional instanceof $iface ) ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		parent::__construct( $schema, $conn, $additional );
	}


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopAddTypeDataUnitperf' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return [];
	}


	/**
	 * Insert product data.
	 */
	public function migrate()
	{
		$this->msg( 'Adding service base performance data', 0 );


		$manager = \Aimeos\MShop\Service\Manager\Factory::createManager( $this->getContext() );
		$typeManager = $manager->getSubManager( 'type' );

		$search = $typeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'service.type.domain', 'service' ),
			$search->compare( '==', 'service.type.code', 'payment' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $typeManager->searchItems( $search );

		if( ( $typeItem = reset( $types ) ) === false ) {
			throw new \RuntimeException( 'Service type item "payment" not found' );
		}

		$item = $manager->createItem();
		$item->setTypeId( $typeItem->getId() );
		$item->setProvider( 'PrePay' );
		$item->setStatus( 1 );

		$this->txBegin();

		for( $i = 0; $i < 100; $i++ )
		{
			$code = 'perf-' . str_pad( $i, 3, '0', STR_PAD_LEFT );

			$item->setId( null );
			$item->setCode( $code );
			$item->setLabel( 'Payment service ' . $code );
			$item->setPosition( $i );

			$manager->saveItem( $item, false );
		}

		$this->txCommit();


		$search = $typeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'service.type.domain', 'service' ),
			$search->compare( '==', 'service.type.code', 'delivery' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $typeManager->searchItems( $search );

		if( ( $typeItem = reset( $types ) ) === false ) {
			throw new \RuntimeException( 'Service type item "delivery" not found' );
		}

		$item = $manager->createItem();
		$item->setTypeId( $typeItem->getId() );
		$item->setProvider( 'Manual' );
		$item->setStatus( 1 );

		$this->txBegin();

		for( $i = 0; $i < 100; $i++ )
		{
			$code = 'perf-' . str_pad( $i, 3, '0', STR_PAD_LEFT );

			$item->setId( null );
			$item->setCode( $code );
			$item->setLabel( 'Delivery service ' . $code );
			$item->setPosition( $i );

			$manager->saveItem( $item, false );
		}

		$this->txCommit();


		$this->status( 'done' );
	}


	protected function getContext()
	{
		return $this->additional;
	}


	protected function txBegin()
	{
		$dbm = $this->additional->getDatabaseManager();

		$conn = $dbm->acquire();
		$conn->begin();
		$dbm->release( $conn );
	}


	protected function txCommit()
	{
		$dbm = $this->additional->getDatabaseManager();

		$conn = $dbm->acquire();
		$conn->commit();
		$dbm->release( $conn );
	}
}
