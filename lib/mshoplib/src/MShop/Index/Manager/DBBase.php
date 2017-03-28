<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Index
 */


namespace Aimeos\MShop\Index\Manager;


/**
 * Base class for all database based index managers
 *
 * @package MShop
 * @subpackage Index
 */
abstract class DBBase
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Index\Manager\Iface
{
	/**
	 * Initializes the manager object
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );

		$this->setResourceName( 'db-product' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param integer[] $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		foreach( $this->getSubManagers() as $submanager ) {
			$submanager->cleanup( $siteids );
		}
	}


	/**
	 * Creates new product item
	 *
	 * @return \Aimeos\MShop\Product\Item\Iface Product item object
	 */
	public function createItem()
	{
		return \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product' )->createItem();
	}


	/**
	 * Creates a search object and optionally sets its base criteria
	 *
	 * @param boolean $default True to add the default criteria
	 * @return \Aimeos\MW\Criteria\Iface Criteria object
	 */
	public function createSearch( $default = false )
	{
		return \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product' )->createSearch( $default );
	}


	/**
	 * Returns the product item for the given ID
	 *
	 * @param integer $id Id of item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MShop\Product\Item\Iface Product item object
	 */
	public function getItem( $id, array $ref = [], $default = false )
	{
		return \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product' )->getItem( $id, $ref, $default );
	}


	/**
	 * Returns a list of attribute objects describing the available criteria for searching
	 *
	 * @param boolean $withsub True to return attributes of sub-managers too
	 * @return array List of items implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		return \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product' )->getSearchAttributes( $withsub );
	}


	/**
	 * Rebuilds the customer index
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[] $items Associative list of product IDs and items implementing \Aimeos\MShop\Product\Item\Iface
	 */
	public function rebuildIndex( array $items = [] )
	{
		foreach( $this->getSubManagers() as $submanager ) {
			$submanager->rebuildIndex( $items );
		}
	}


	/**
	 * Stores a new item into the index
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Product item
	 * @param boolean $fetch True if the new ID should be set in the item
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Iface $item, $fetch = true )
	{
		$this->rebuildIndex( array( $item->getId() => $item ) );
	}


	/**
	 * Removes all entries not touched after the given timestamp
	 *
	 * @param string $timestamp Timestamp in ISO format (YYYY-MM-DD HH:mm:ss)
	 * @param string $path Configuration path to the SQL statement to execute
	 */
	protected function cleanupIndexBase( $timestamp, $path )
	{
		$context = $this->getContext();
		$siteid = $context->getLocale()->getSiteId();


		$this->begin();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$stmt = $this->getCachedStatement( $conn, $path );

			$stmt->bind( 1, $timestamp ); // ctime
			$stmt->bind( 2, $siteid, \Aimeos\MW\DB\Statement\Base::PARAM_INT );

			$stmt->execute()->finish();

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			$this->rollback();
			throw $e;
		}

		$this->commit();

		foreach( $this->getSubManagers() as $submanager ) {
			$submanager->cleanupIndex( $timestamp );
		}
	}


	/**
	 * Removes several items from the index
	 *
	 * @param array $ids List of product IDs
	 * @param string $path Configuration path to the SQL statement to execute
	 * @param boolean $siteidcheck If siteid should be used in the statement
	 * @param string $name Name of the ID column
	 */
	protected function deleteItemsBase( array $ids, $path, $siteidcheck = true, $name = 'prodid' )
	{
		if( empty( $ids ) ) { return; }

		foreach( $this->getSubManagers() as $submanager ) {
			$submanager->deleteItems( $ids );
		}

		parent::deleteItemsBase( $ids, $path, $siteidcheck, $name );
	}


	/**
	 * Optimizes the catalog customer index if necessary
	 *
	 * @param string $path Configuration path to the SQL statements to execute
	 */
	protected function optimizeBase( $path )
	{
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			foreach( (array) $this->getSqlConfig( $path ) as $sql ) {
				$conn->create( $sql )->execute()->finish();
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		foreach( $this->getSubManagers() as $submanager ) {
			$submanager->optimize();
		}
	}


	/**
	 * Searches for items matching the given criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @param integer &$total Total number of items matched by the given criteria
	 * @param string $cfgPathSearch Configuration path to the search SQL statement
	 * @param string $cfgPathCount Configuration path to the count SQL statement
	 * @return array List of items implementing \Aimeos\MShop\Product\Item\Iface with ids as keys
	 */
	protected function searchItemsIndexBase( \Aimeos\MW\Criteria\Iface $search,
		array $ref, &$total, $cfgPathSearch, $cfgPathCount )
	{
		$list = $ids = [];
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
			$required = array( 'product' );

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false ) {
				$ids[] = $row['id'];
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		$manager = \Aimeos\MShop\Factory::createManager( $context, 'product' );
		$prodSearch = $manager->createSearch();
		$prodSearch->setConditions( $prodSearch->compare( '==', 'product.id', $ids ) );
		$prodSearch->setSlice( 0, $search->getSliceSize() );
		$items = $manager->searchItems( $prodSearch, $ref );

		foreach( $ids as $id )
		{
			if( isset( $items[$id] ) ) {
				$list[$id] = $items[$id];
			}
		}

		return $list;
	}


	/**
	 * Returns the sub-manager instances available for the manager
	 *
	 * @return array Associative list of the sub-domain as key and the manager object as value
	 */
	abstract protected function getSubManagers();
}