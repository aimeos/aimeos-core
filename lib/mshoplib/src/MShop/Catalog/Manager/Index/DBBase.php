<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Catalog
 */


/**
 * Base class for all database based catalog index managers
 *
 * @package MShop
 * @subpackage Catalog
 */
abstract class MShop_Catalog_Manager_Index_DBBase
	extends MShop_Common_Manager_Abstract
	implements MShop_Catalog_Manager_Index_Interface
{
	/**
	 * Initializes the manager object
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );

		$this->_setResourceName( 'db-product' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param integer[] $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		foreach( $this->_getSubManagers() as $submanager ) {
			$submanager->cleanup( $siteids );
		}
	}


	/**
	 * Creates new product item
	 *
	 * @return MShop_Product_Item_Interface Product item object
	 */
	public function createItem()
	{
		return MShop_Factory::createManager( $this->_getContext(), 'product' )->createItem();
	}


	/**
	 * Creates a search object and optionally sets its base criteria
	 *
	 * @param boolean $default True to add the default criteria
	 * @return MW_Common_Criteria_Interface Criteria object
	 */
	public function createSearch( $default = false )
	{
		return MShop_Factory::createManager( $this->_getContext(), 'product' )->createSearch( $default );
	}


	/**
	 * Returns the product item for the given ID
	 *
	 * @param integer $id Id of item
	 * @return MShop_Product_Item_Interface Product item object
	 */
	public function getItem( $id, array $ref = array() )
	{
		return MShop_Factory::createManager( $this->_getContext(), 'product' )->getItem( $id, $ref );
	}


	/**
	 * Returns a list of attribute objects describing the available criteria for searching
	 *
	 * @param boolean $withsub True to return attributes of sub-managers too
	 * @return array List of items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		return MShop_Factory::createManager( $this->_getContext(), 'product' )->getSearchAttributes( $withsub );
	}


	/**
	 * Rebuilds the customer catalog index
	 *
	 * @param MShop_Common_Item_Interface[] $items Associative list of product IDs and items implementing MShop_Product_Item_Interface
	 */
	public function rebuildIndex( array $items = array() )
	{
		foreach( $this->_getSubManagers() as $submanager ) {
			$submanager->rebuildIndex( $items );
		}
	}


	/**
	 * Stores a new item into the index
	 *
	 * @param MShop_Common_Item_Interface $item Product item
	 * @param boolean $fetch True if the new ID should be set in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$this->rebuildIndex( array( $item->getId() => $item ) );
	}


	/**
	 * Removes all entries not touched after the given timestamp
	 *
	 * @param string $timestamp Timestamp in ISO format (YYYY-MM-DD HH:mm:ss)
	 * @param string $path Configuration path to the SQL statement to execute
	 */
	protected function _doCleanupIndex( $timestamp, $path )
	{
		$context = $this->_getContext();
		$siteid = $context->getLocale()->getSiteId();


		$this->begin();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$stmt = $this->_getCachedStatement( $conn, $path );

			$stmt->bind( 1, $timestamp ); // ctime
			$stmt->bind( 2, $siteid, MW_DB_Statement_Abstract::PARAM_INT );

			$stmt->execute()->finish();

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			$this->rollback();
			throw $e;
		}

		$this->commit();

		foreach( $this->_getSubManagers() as $submanager ) {
			$submanager->cleanupIndex( $timestamp );
		}
	}


	/**
	 * Removes several items from the catalog index
	 *
	 * @param array $ids List of product IDs
	 * @param string $path Configuration path to the SQL statement to execute
	 */
	protected function _doDeleteItems( array $ids, $path )
	{
		if( empty( $ids ) ) { return; }

		foreach( $this->_getSubManagers() as $submanager ) {
			$submanager->deleteItems( $ids );
		}

		$sql = $this->_getContext()->getConfig()->get( $path, $path );

		$this->_deleteItems( $ids, $sql, true, 'prodid' );
	}


	/**
	 * Optimizes the catalog customer index if necessary
	 *
	 * @param string $path Configuration path to the SQL statements to execute
	 */
	protected function _doOptimize( $path )
	{
		$context = $this->_getContext();
		$config = $context->getConfig();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			foreach( (array) $config->get( $path, array() ) as $sql ) {
				$conn->create( $sql )->execute()->finish();
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		foreach( $this->_getSubManagers() as $submanager ) {
			$submanager->optimize();
		}
	}


	/**
	 * Searches for items matching the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search criteria
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @param integer &$total Total number of items matched by the given criteria
	 * @param string $cfgPathSearch Configuration path to the search SQL statement
	 * @param string $cfgPathCount Configuration path to the count SQL statement
	 * @return array List of items implementing MShop_Product_Item_Interface with ids as keys
	 */
	protected function _doSearchItems( MW_Common_Criteria_Interface $search,
		array $ref, &$total, $cfgPathSearch, $cfgPathCount )
	{
		$list = $ids = array();
		$context = $this->_getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$level = MShop_Locale_Manager_Abstract::SITE_ALL;
			$required = array( 'product' );

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false ) {
				$ids[] = $row['id'];
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		$manager = MShop_Factory::createManager( $context, 'product' );
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
	abstract protected function _getSubManagers();
}