<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds performance records to product table.
 */
class ProductAddBasePerfData extends \Aimeos\MW\Setup\Task\Base
{
	private $count = 9000;


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
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'CatalogRebuildPerfIndex' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		$this->process();
	}


	/**
	 * Insert product data.
	 */
	protected function process()
	{
		$this->msg( 'Adding product base performance data', 0 );

		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->getContext() );
		$productTypeItem = $this->getTypeItem( 'product/type', 'product', 'default' );

		$this->txBegin();

		$productItem = $productManager->createItem();
		$productItem->setTypeId( $productTypeItem->getId() );
		$productItem->setStatus( 1 );
		$productItem->setSupplierCode( 'My brand' );
		$productItem->setDateStart( '1970-01-01 00:00:00' );

		for( $i = 0; $i < $this->count; $i++ )
		{
			$code = 'perf-' . str_pad( $i, 5, '0', STR_PAD_LEFT );

			$productItem->setId( null );
			$productItem->setCode( $code );
			$productItem->setLabel( $code );
			$productManager->saveItem( $productItem, false );
		}

		$this->txCommit();

		$this->status( 'done' );
	}


	protected function getContext()
	{
		return $this->additional;
	}


	/**
	 * @param string $domain
	 * @param string $code
	 */
	protected function getProductListItem( $domain, $code )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product/lists/type' );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', 'product.lists.type.code', $code ),
			$search->compare( '==', 'product.lists.type.domain', $domain ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$types = $manager->searchItems( $search );

		if( ( $listTypeItem = reset( $types ) ) === false ) {
			throw new \Exception( 'Product list type item not found' );
		}


		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product/lists' );

		$listItem = $manager->createItem();
		$listItem->setTypeId( $listTypeItem->getId() );
		$listItem->setDomain( $domain );

		return $listItem;
	}


	/**
	 * Returns the attribute type item specified by the code.
	 *
	 * @param string $prefix Domain prefix for the manager, e.g. "media/type"
	 * @param string $domain Domain of the type item
	 * @param string $code Code of the type item
	 * @return \Aimeos\MShop\Common\Item\Type\Iface Type item
	 * @throws \Exception If no item is found
	 */
	protected function getTypeItem( $prefix, $domain, $code )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), $prefix );
		$prefix = str_replace( '/', '.', $prefix );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', $prefix . '.domain', $domain ),
			$search->compare( '==', $prefix . '.code', $code ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new \Exception( sprintf( 'No type item for "%1$s/%2$s" in "%3$s" found', $domain, $code, $prefix ) );
		}

		return $item;
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
