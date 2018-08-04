<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds service performance records
 */
class ServiceAddPerfData extends \Aimeos\MW\Setup\Task\Base
{
	const NUM_SERVICES = 100;

	private $typeIds = [];


	public function __construct( \Aimeos\MW\Setup\DBSchema\Iface $schema, \Aimeos\MW\DB\Connection\Iface $conn, $additional = null )
	{
		\Aimeos\MW\Common\Base::checkClass( '\\Aimeos\\MShop\\Context\\Item\\Iface', $additional );

		parent::__construct( $schema, $conn, $additional );
	}


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return ['MShopAddTypeDataUnitperf', 'LocaleAddPerfData'];
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
		$this->msg( 'Adding service performance data', 0 );


		$manager = \Aimeos\MShop\Factory::createManager( $this->additional, 'service' );
		$manager->begin();

		$payItem = $manager->createItem( 'payment', 'service' );
		$shipItem = $manager->createItem( 'delivery', 'service' );

		for( $i = 0; $i < self::NUM_SERVICES; $i++ )
		{
			$code = 'perf-pay-' . str_pad( $i, 3, '0', STR_PAD_LEFT );

			$item = (clone $payItem)
				->setLabel( 'Payment service ' . $code )
				->setProvider( 'PrePay' )
				->setCode( $code )
				->setStatus( 1 );

			$manager->saveItem( $item );
		}


		for( $i = 0; $i < self::NUM_SERVICES; $i++ )
		{
			$code = 'perf-ship-' . str_pad( $i, 3, '0', STR_PAD_LEFT );

			$item = (clone $shipItem)
				->setLabel( 'Delivery service ' . $code )
				->setProvider( 'Manual' )
				->setCode( $code )
				->setStatus( 1 );

			$manager->saveItem( $item );
		}

		$manager->commit();


		$this->status( 'done' );
	}
}
