<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds performance records to stock table.
 */
class StockAddPerfData extends \Aimeos\MW\Setup\Task\Base
{
	private $typeId;


	/**
	 * Initializes the task object.
	 *
	 * @param \Aimeos\MW\Setup\DBSchema\Iface $schema Database schema object
	 * @param \Aimeos\MW\DB\Connection\Iface $conn Database connection
	 * @param mixed $additional Additionally provided information for the setup tasks if required
	 * @param array $paths List of paths of the setup tasks ordered by dependencies
	 */
	public function __construct( \Aimeos\MW\Setup\DBSchema\Iface $schema, \Aimeos\MW\DB\Connection\Iface $conn,
		$additional = null, array $paths = [] )
	{
		\Aimeos\MW\Common\Base::checkClass( '\\Aimeos\\MShop\\Context\\Item\\Iface', $this->additional );

		parent::__construct( $schema, $conn, $additional, $paths );
	}


	public function __clone()
	{
		$this->additional = clone $this->additional;
		$this->additional->__sleep();
	}


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return ['MShopAddTypeDataUnitperf', 'CatalogAddPerfData'];
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return ['IndexRebuildPerf'];
	}


	/**
	 * Insert stock data.
	 */
	public function migrate()
	{
		$this->msg( 'Adding stock performance data', 0 );


		$fcn = function( \Aimeos\MW\Setup\Task\Iface $self, $max, $idx ) {

			\Aimeos\MShop\Factory::clear();
			$self->addStock( $max, $idx );
		};


		$typeManager = \Aimeos\MShop\Factory::createManager( $this->additional, 'stock/type' );
		$this->typeId = $typeManager->findItem( 'default', [], 'product' )->getId();
		$process = $this->additional->getProcess();

		for( $i = 0; $i < 4; $i++ ) {
			$process->start( $fcn, [$this, 4, $i] );
		}

		$process->wait();


		$this->status( 'done' );
	}


	public function addStock( $max, $idx )
	{
		$productManager = \Aimeos\MShop\Factory::createManager( $this->additional, 'product' );
		$stockManager = \Aimeos\MShop\Factory::createManager( $this->additional, 'stock' );

		$search = $productManager->createSearch();
		$search->setSortations( [$search->sort( '+', 'product.id' )] );

		$item = $stockManager->createItem()->setTypeId( $this->typeId );
		$stocklevels = [null, 100, 80, 60, 40, 20, 10, 5, 2, 0];
		$start = 10000 * $idx;

		do
		{
			$search->setSlice( $start, 10000 );
			$result = $productManager->searchItems( $search );

			$stockManager->begin();

			foreach( $result as $product )
			{
				$item->setId( null );
				$item->setProductCode( $product->getCode() );
				$item->setStockLevel( current( $stocklevels ) );

				$stockManager->saveItem( $item );

				if( next( $stocklevels ) === false ) {
					reset( $stocklevels );
				}
			}

			$stockManager->commit();

			$count = count( $result );
			$start += $count * $max;
		}
		while( $count == $search->getSliceSize() );
	}
}
