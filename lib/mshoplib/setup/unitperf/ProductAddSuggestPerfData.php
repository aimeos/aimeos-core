<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds suggestion performance records to products.
 */
class ProductAddSuggestPerfData extends \Aimeos\MW\Setup\Task\Base
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
		return array( 'ProductAddBasePerfData', 'ProductAddSelectPerfData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'CatalogRebuildPerfIndex' );
	}


	/**
	 * Insert product data.
	 */
	public function migrate()
	{
		$this->msg( 'Adding product suggestion performance data', 0 );


		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->getContext() );
		$productListManager = $productManager->getSubManager( 'lists' );
		$productListTypeManager = $productListManager->getSubManager( 'type' );

		$expr = [];
		$search = $productListTypeManager->createSearch();
		$expr[] = $search->compare( '==', 'product.lists.type.code', 'suggestion' );
		$expr[] = $search->compare( '==', 'product.lists.type.domain', 'product' );
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $productListTypeManager->searchItems( $search );

		if( ( $listTypeItem = reset( $types ) ) === false ) {
			throw new \RuntimeException( 'Product list type item not found' );
		}

		$search = $productManager->createSearch();
		$search->setSortations( array( $search->sort( '+', 'product.id' ) ) );
		$search->setSlice( 0, 1000 );

		$refsearch = $productManager->createSearch();
		$refsearch->setSortations( array( $refsearch->sort( '+', 'product.id' ) ) );
		$refsearch->setSlice( 1, 1000 );

		$listItem = $productListManager->createItem();
		$listItem->setTypeId( $listTypeItem->getId() );
		$listItem->setDomain( 'product' );


		$start = 0;

		do
		{
			$num = 0;

			$result = $productManager->searchItems( $search );
			$refresult = $productManager->searchItems( $refsearch );

			$this->txBegin();

			foreach( $result as $id => $product )
			{
				$pos = 0;
				$length = ( $num % 4 ) + 3;

				foreach( array_slice( $refresult, $num, $length, true ) as $refid => $refproduct )
				{
					$listItem->setId( null );
					$listItem->setParentId( $id );
					$listItem->setRefId( $refid );
					$listItem->setPosition( $pos++ );

					$productListManager->saveItem( $listItem, false );
				}

				$num++;
			}

			$this->txCommit();

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start, 1000 );
			$refsearch->setSlice( $start + 1, 1000 );
		}
		while( $count == $search->getSliceSize() );


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
