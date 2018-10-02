<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
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
	 * Creates a new empty item instance
	 *
	 * @param string|null Type the item should be created with
	 * @param string|null Domain of the type the item should be created with
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Product\Item\Iface New product item object
	 */
	public function createItem( $type = null, $domain = null, array $values = [] )
	{
		return \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product' )->createItem( $type, $domain, $values );
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
	 * @return \Aimeos\MShop\Common\Item\Iface Saved item
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Iface $item, $fetch = true )
	{
		return $this->rebuildIndex( array( $item->getId() => $item ) );
	}


	/**
	 * Adds or updates a list of items
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[] $items List of items whose data should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\Iface[] Saved items
	 */
	public function saveItems( array $items, $fetch = true )
	{
		return $this->rebuildIndex( $items );
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
	 * Returns the string replacements for the SQL statements
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search critera object
	 * @param array $attributes Associative list of search keys and objects implementing the \Aimeos\MW\Criteria\Attribute\Iface
	 * @param array $plugins Associative list of item keys and plugin objects implementing \Aimeos\MW\Criteria\Plugin\Iface
	 * @param array $joins Associative list of SQL joins
	 * @param array Array of keys, find and replace arrays
	 */
	protected function getSQLReplacements( \Aimeos\MW\Criteria\Iface $search, array $attributes, array $plugins, array $joins )
	{
		$types = $this->getSearchTypes( $attributes );
		$funcs = $this->getSearchFunctions( $attributes );
		$translations = $this->getSearchTranslations( $attributes );

		$keys = [];
		$find = array( ':joins', ':cond', ':start', ':size' );
		$replace = array(
			implode( "\n", array_unique( $joins ) ),
			$search->getConditionSource( $types, $translations, $plugins, $funcs ),
			$search->getSliceStart(),
			$search->getSliceSize(),
		);

		if( count( $search->getSortations() ) > 0 )
		{
			$names = $search->translate( $search->getSortations() );
			$cols = $search->translate( $search->getSortations(), $translations );

			$list = $aliases = [];
			foreach( $cols as $idx => $col )
			{
				$list[] = 'MIN(' . $col . ') AS "s' . $idx . '"';
				$aliases[ $names[$idx] ] = '"s' . $idx . '"';
			}

			$keys[] = 'orderby';
			$find[] = ':order';
			$replace[] = $search->getSortationSource( $types, $aliases, $funcs );

			$keys[] = 'mincols';
			$find[] = ':mincols';
			$replace[] = implode( ', ', $list );
		}

		return [$keys, $find, $replace];
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
			$required = array( 'product' );

			/** mshop/index/manager/sitemode
			 * Mode how items from levels below or above in the site tree are handled
			 *
			 * By default, only items from the current site are fetched from the
			 * storage. If the ai-sites extension is installed, you can create a
			 * tree of sites. Then, this setting allows you to define for the
			 * whole index domain if items from parent sites are inherited,
			 * sites from child sites are aggregated or both.
			 *
			 * Available constants for the site mode are:
			 * * 0 = only items from the current site
			 * * 1 = inherit items from parent sites
			 * * 2 = aggregate items from child sites
			 * * 3 = inherit and aggregate items at the same time
			 *
			 * You also need to set the mode in the locale manager
			 * (mshop/locale/manager/standard/sitelevel) to one of the constants.
			 * If you set it to the same value, it will work as described but you
			 * can also use different modes. For example, if inheritance and
			 * aggregation is configured the locale manager but only inheritance
			 * in the domain manager because aggregating items makes no sense in
			 * this domain, then items wil be only inherited. Thus, you have full
			 * control over inheritance and aggregation in each domain.
			 *
			 * @param integer Constant from Aimeos\MShop\Locale\Manager\Base class
			 * @category Developer
			 * @since 2018.01
			 * @see mshop/locale/manager/standard/sitelevel
			 */
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
			$level = $context->getConfig()->get( 'mshop/index/manager/sitemode', $level );

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