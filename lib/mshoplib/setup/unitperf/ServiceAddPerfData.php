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


		$this->init();

		$manager = \Aimeos\MShop\Factory::createManager( $this->additional, 'service' );
		$manager->begin();

		for( $i = 0; $i < self::NUM_SERVICES; $i++ )
		{
			$code = 'perf-pay-' . str_pad( $i, 3, '0', STR_PAD_LEFT );

			$item = $manager->createItem()
				->setTypeId( $this->getTypeId( 'service/type', 'service', 'payment' ) )
				->setLabel( 'Payment service ' . $code )
				->setProvider( 'PrePay' )
				->setCode( $code )
				->setStatus( 1 );

			$manager->saveItem( $item );
		}


		for( $i = 0; $i < self::NUM_SERVICES; $i++ )
		{
			$code = 'perf-ship-' . str_pad( $i, 3, '0', STR_PAD_LEFT );

			$item = $manager->createItem()
				->setTypeId( $this->getTypeId( 'service/type', 'service', 'delivery' ) )
				->setLabel( 'Delivery service ' . $code )
				->setProvider( 'Manual' )
				->setCode( $code )
				->setStatus( 1 );

			$manager->saveItem( $item );
		}

		$manager->commit();


		$this->status( 'done' );
	}


	protected function getTypeId( $path, $domain, $code )
	{
		if( !isset( $this->typeIds[$path][$domain][$code] ) ) {
			throw new \RuntimeException( sprintf( 'No "%1$s" ID for "%2$s" and "%3$s" available', $path, $domain, $code ) );
		}

		return $this->typeIds[$path][$domain][$code];
	}


	protected function init()
	{
		foreach( ['service/type'] as $path )
		{
			$manager = \Aimeos\MShop\Factory::createManager( $this->additional, $path );
			$search = $manager->createSearch()->setSlice( 0, 0x7fffffff );

			foreach( $manager->searchItems( $search ) as $id => $item ) {
				$this->typeIds[$path][$item->getDomain()][$item->getCode()] = $id;
			}
		}
	}
}
