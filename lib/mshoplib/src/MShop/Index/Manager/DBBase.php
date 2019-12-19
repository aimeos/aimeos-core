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
	implements \Aimeos\MShop\Product\Manager\Iface
{
	private $manager;


	/**
	 * Initializes the manager object
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );

		$this->setResourceName( 'db-product' );
		$this->manager = \Aimeos\MShop::create( $this->getContext(), 'product' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param string[] $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Index\Manager\Iface Manager object for chaining method calls
	 */
	public function clear( array $siteids )
	{
		foreach( $this->getSubManagers() as $submanager ) {
			$submanager->clear( $siteids );
		}

		return $this;
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Product\Item\Iface New product item object
	 */
	public function createItem( array $values = [] )
	{
		return $this->manager->createItem( $values );
	}


	/**
	 * Creates a search object and optionally sets its base criteria
	 *
	 * @param boolean $default True to add the default criteria
	 * @return \Aimeos\MW\Criteria\Iface Criteria object
	 */
	public function createSearch( $default = false )
	{
		return $this->manager->createSearch( $default );
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Index\Manager\Iface Manager object for chaining method calls
	 */
	public function deleteItems( array $itemIds )
	{
		if( empty( $itemIds ) ) { return; }

		foreach( $this->getSubManagers() as $submanager ) {
			$submanager->deleteItems( $itemIds );
		}

		return $this;
	}


	/**
	 * Returns the item specified by its code and domain/type if necessary
	 *
	 * @param string $code Code of the item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param string|null $domain Domain of the item if necessary to identify the item uniquely
	 * @param string|null $type Type code of the item if necessary to identify the item uniquely
	 * @param boolean $default True to add default criteria
	 * @return \Aimeos\MShop\Common\Item\Iface Item object
	 */
	public function findItem( $code, array $ref = [], $domain = 'product', $type = null, $default = false )
	{
		return $this->manager->findItem( $code, $ref, $domain, $type, $default );
	}


	/**
	 * Returns the product item for the given ID
	 *
	 * @param string $id Id of item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MShop\Product\Item\Iface Product item object
	 */
	public function getItem( $id, array $ref = [], $default = false )
	{
		return $this->manager->getItem( $id, $ref, $default );
	}


	/**
	 * Returns a list of attribute objects describing the available criteria for searching
	 *
	 * @param boolean $withsub True to return attributes of sub-managers too
	 * @return array List of items implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		return $this->manager->getSearchAttributes( $withsub );
	}


	/**
	 * Rebuilds the customer index
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface[] $items Associative list of product IDs and items values
	 * @return \Aimeos\MShop\Index\Manager\Iface Manager object for chaining method calls
	 */
	public function rebuild( array $items = [] )
	{
		foreach( $this->getSubManagers() as $submanager ) {
			$submanager->rebuild( $items );
		}

		return $this;
	}


	/**
	 * Stores a new item into the index
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface $item Product item
	 * @param boolean $fetch True if the new ID should be set in the item
	 * @return \Aimeos\MShop\Product\Item\Iface Saved item
	 */
	public function saveItem( \Aimeos\MShop\Product\Item\Iface $item, $fetch = true )
	{
		return $this->manager->saveItem( $item, $fetch );
	}


	/**
	 * Adds or updates a list of items
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface[] $items List of items whose data should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Product\Item\Iface[] Saved items
	 */
	public function saveItems( array $items, $fetch = true )
	{
		$list = [];

		foreach( $this->manager->saveItems( $items, true ) as $item ) {
			$list[$item->getId()] = $item;
		}

		return $list;
	}


	/**
	 * Removes all entries not touched after the given timestamp
	 *
	 * @param string $timestamp Timestamp in ISO format (YYYY-MM-DD HH:mm:ss)
	 * @param string $path Configuration path to the SQL statement to execute
	 * @return \Aimeos\MShop\Index\Manager\Iface Manager object for chaining method calls
	 */
	protected function cleanupBase( $timestamp, $path )
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
			$stmt->bind( 2, $siteid );

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
			$submanager->cleanup( $timestamp );
		}

		return $this;
	}


	/**
	 * Removes several items from the index
	 *
	 * @param string[] $ids List of product IDs
	 * @param string $path Configuration path to the SQL statement to execute
	 * @param boolean $siteidcheck If siteid should be used in the statement
	 * @param string $name Name of the ID column
	 * @return \Aimeos\MShop\Index\Manager\Iface Manager object for chaining method calls
	 */
	protected function deleteItemsBase( array $ids, $path, $siteidcheck = true, $name = 'prodid' )
	{
		if( empty( $ids ) ) { return; }

		foreach( $this->getSubManagers() as $submanager ) {
			$submanager->deleteItems( $ids );
		}

		return parent::deleteItemsBase( $ids, $path, $siteidcheck, $name );
	}


	/**
	 * Returns the product manager instance
	 *
	 * @return \Aimeos\MShop\Product\Manager\Iface Product manager object
	 */
	protected function getManager()
	{
		return $this->manager;
	}


	/**
	 * Returns the string replacements for the SQL statements
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search critera object
	 * @param \Aimeos\MW\Criteria\Attribute\Iface[] $attributes Associative list of search keys and criteria attribute items
	 * @param \Aimeos\MW\Criteria\Plugin\Iface[] $plugins Associative list of item keys and criteria plugin objects
	 * @param string[] $joins Associative list of SQL joins
	 * @param \Aimeos\MW\Criteria\Attribute\Iface[] $columns Additional columns to retrieve values from
	 * @return array Array of keys, find and replace arrays
	 */
	protected function getSQLReplacements( \Aimeos\MW\Criteria\Iface $search, array $attributes, array $plugins, array $joins, array $columns = [] )
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
				$aliases[$names[$idx]] = '"s' . $idx . '"';
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
	 * @return \Aimeos\MShop\Index\Manager\Iface Manager object for chaining method calls
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

		return $this;
	}


	/**
	 * Searches for items matching the given criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param integer &$total Total number of items matched by the given criteria
	 * @param string $cfgPathSearch Configuration path to the search SQL statement
	 * @param string $cfgPathCount Configuration path to the count SQL statement
	 * @return \Aimeos\MShop\Product\Item\Iface[] List of product items
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

		$manager = \Aimeos\MShop::create( $context, 'product' );
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
