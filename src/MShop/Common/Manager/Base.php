<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager;


/**
 * Provides common methods required by most of the manager classes.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class Base implements \Aimeos\Macro\Iface
{
	use \Aimeos\Macro\Macroable;
	use Sub\Traits;
	use Methods;
	use Site;
	use DB;


	private \Aimeos\MShop\ContextIface $context;


	/**
	 * Initialization of class.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 */
	public function __construct( \Aimeos\MShop\ContextIface $context )
	{
		$this->context = $context;
		$domain = $this->domain();

		$this->setResourceName( $context->config()->get( 'mshop/' . $domain . '/manager/resource', 'db-' . $domain ) );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		foreach( $this->context()->config()->get( $this->getConfigKey( 'submanagers' ), [] ) as $domain ) {
			$this->object()->getSubManager( $domain )->clear( $siteids );
		}

		return $this->clearBase( $siteids, $this->getConfigKey( 'delete', 'mshop/common/manager/delete' ) );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Common\Item\Iface New attribute item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$prefix = $this->prefix();
		$values[$prefix . 'siteid'] = $values[$prefix . 'siteid'] ?? $this->context()->locale()->getSiteId();

		return new \Aimeos\MShop\Common\Item\Base( $prefix, $values );
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function delete( $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->deleteItemsBase( $itemIds, $this->getConfigKey( 'delete', 'mshop/common/manager/delete' ) );
	}


	/**
	 * Creates a search critera object
	 *
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @param bool $site TRUE for adding site criteria to limit items by the site of related items
	 * @return \Aimeos\Base\Criteria\Iface New search criteria object
	 */
	public function filter( ?bool $default = false, bool $site = false ) : \Aimeos\Base\Criteria\Iface
	{
		return $this->filterBase( $this->domain() );
	}


	/**
	 * Returns the attributes item specified by its ID.
	 *
	 * @param string $id Unique ID of the attribute item in the storage
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Common\Item\Iface Returns the attribute item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function get( string $id, array $ref = [], ?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->getItemBase( $this->prefix() . 'id', $id, $ref, $default );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 * @deprecated 2025.01 Use type() instead
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		return $this->getResourceTypeBase( join( '/', $this->type() ), $this->getConfigKey( 'submanagers' ), [], $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		$prefix = $this->prefix();

		$attr = array_replace( $this->createAttributes( [
			$prefix . 'id' => [
				'internalcode' => 'id',
				'label' => 'ID',
				'type' => 'int',
				'public' => false,
			],
			$prefix . 'siteid' => [
				'internalcode' => 'siteid',
				'label' => 'Site ID',
				'public' => false,
			],
			$prefix . 'ctime' => [
				'internalcode' => 'ctime',
				'label' => 'Create date/time',
				'type' => 'datetime',
				'public' => false,
			],
			$prefix . 'mtime' => [
				'internalcode' => 'mtime',
				'label' => 'Modification date/time',
				'type' => 'datetime',
				'public' => false,
			],
			$prefix . 'editor' => [
				'internalcode' => 'editor',
				'label' => 'Editor',
				'public' => false,
			],
		] ), $this->getSaveAttributes() );

		if( $withsub )
		{
			$domains = $this->context()->config()->get( $this->getConfigKey( 'submanagers' ), [] );

			foreach( $domains as $domain ) {
				$attr += $this->object()->getSubManager( $domain )->getSearchAttributes( true );
			}
		}

		return $attr;
	}


	/**
	 * Returns a new manager for attribute extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g Type, List's etc.
	 */
	public function getSubManager( string $manager, ?string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$type = $this->type();
		$manager = trim( join( '/', array_slice( $type, 1 ) ) . '/' . $manager, '/' );

		return $this->getSubManagerBase( current( $type ), $manager, $name );
	}


	/**
	 * Iterates over all matched items and returns the found ones
	 *
	 * @param \Aimeos\MShop\Common\Cursor\Iface $cursor Cursor object with filter, domains and cursor
	 * @param string[] $ref List of domains whose items should be fetched too
	 * @return \Aimeos\Map|null List of items implementing \Aimeos\MShop\Common\Item\Iface with ids as keys
	 */
	public function iterate( \Aimeos\MShop\Common\Cursor\Iface $cursor, array $ref = [] ) : ?\Aimeos\Map
	{
		if( $cursor->value() === '' ) {
			return null;
		}

		if( ( $first = current( $this->getSearchAttributes() ) ) === false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No search configuration available for "%1$s"', get_class( $this ) ) );
		}

		$filter = $cursor->filter()->add( $first->getCode(), '>', (int) $cursor->value() )->order( $first->getCode() );
		$items = $this->search( $filter, $ref );
		$cursor->setValue( $items->lastKey() ?: '' );

		return !$items->isEmpty() ? $items : null;
	}


	/**
	 * Adds or updates an item object or a list of them.
	 *
	 * @param \Aimeos\Map|\Aimeos\MShop\Common\Item\Iface[]|\Aimeos\MShop\Common\Item\Iface $items Item or list of items whose data should be saved
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\Map|\Aimeos\MShop\Common\Item\Iface Saved item or items
	 */
	public function save( $items, bool $fetch = true )
	{
		foreach( map( $items ) as $item )
		{
			if( method_exists( $this, 'saveItem' ) ) {
				$this->saveItem( $item, $fetch );
			} else {
				$this->saveBase( $item, $fetch );
			}
		}

		return is_array( $items ) ? map( $items ) : $items;
	}


	/**
	 * Searches for all items matching the given critera.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $filter Criteria object with conditions, sortations, etc.
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Common\Item\Iface with ids as keys
	 */
	public function search( \Aimeos\Base\Criteria\Iface $filter, array $ref = [], ?int &$total = null ) : \Aimeos\Map
	{
		/** mshop/common/manager/search/mysql
		 * Retrieves the records matched by the given criteria in the database
		 *
		 * @see mshop/common/manager/search/ansi
		 */

		/** mshop/common/manager/search/ansi
		 * Retrieves the records matched by the given criteria in the database
		 *
		 * Fetches the records matched by the given criteria from the
		 * database. The records must be from one of the sites that are
		 * configured via the context item. If the current site is part of
		 * a tree of sites, the SELECT statement can retrieve all records
		 * from the current site and the complete sub-tree of sites.
		 *
		 * As the records can normally be limited by criteria from sub-managers,
		 * their tables must be joined in the SQL context. This is done by
		 * using the "internaldeps" property from the definition of the ID
		 * column of the sub-managers. These internal dependencies specify
		 * the JOIN between the tables and the used columns for joining. The
		 * ":joins" placeholder is then replaced by the JOIN strings from
		 * the sub-managers.
		 *
		 * To limit the records matched, conditions can be added to the given
		 * criteria object. It can contain comparisons like column names that
		 * must match specific values which can be combined by AND, OR or NOT
		 * operators. The resulting string of SQL conditions replaces the
		 * ":cond" placeholder before the statement is sent to the database
		 * server.
		 *
		 * If the records that are retrieved should be ordered by one or more
		 * columns, the generated string of column / sort direction pairs
		 * replaces the ":order" placeholder. Columns of
		 * sub-managers can also be used for ordering the result set but then
		 * no index can be used.
		 *
		 * The number of returned records can be limited and can start at any
		 * number between the begining and the end of the result set. For that
		 * the ":size" and ":start" placeholders are replaced by the
		 * corresponding values from the criteria object. The default values
		 * are 0 for the start and 100 for the size value.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for searching items
		 * @since 2023.10
		 * @see mshop/common/manager/insert/ansi
		 * @see mshop/common/manager/update/ansi
		 * @see mshop/common/manager/newid/ansi
		 * @see mshop/common/manager/delete/ansi
		 * @see mshop/common/manager/count/ansi
		 */
		$cfgPathSearch = $this->getConfigKey( 'search', 'mshop/common/manager/search' );

		/** mshop/common/manager/count/mysql
		 * Counts the number of records matched by the given criteria in the database
		 *
		 * @see mshop/common/manager/count/ansi
		 */

		/** mshop/common/manager/count/ansi
		 * Counts the number of records matched by the given criteria in the database
		 *
		 * Counts all records matched by the given criteria from the
		 * database. The records must be from one of the sites that are
		 * configured via the context item. If the current site is part of
		 * a tree of sites, the statement can count all records from the
		 * current site and the complete sub-tree of sites.
		 *
		 * As the records can normally be limited by criteria from sub-managers,
		 * their tables must be joined in the SQL context. This is done by
		 * using the "internaldeps" property from the definition of the ID
		 * column of the sub-managers. These internal dependencies specify
		 * the JOIN between the tables and the used columns for joining. The
		 * ":joins" placeholder is then replaced by the JOIN strings from
		 * the sub-managers.
		 *
		 * To limit the records matched, conditions can be added to the given
		 * criteria object. It can contain comparisons like column names that
		 * must match specific values which can be combined by AND, OR or NOT
		 * operators. The resulting string of SQL conditions replaces the
		 * ":cond" placeholder before the statement is sent to the database
		 * server.
		 *
		 * Both, the strings for ":joins" and for ":cond" are the same as for
		 * the "search" SQL statement.
		 *
		 * Contrary to the "search" statement, it doesn't return any records
		 * but instead the number of records that have been found. As counting
		 * thousands of records can be a long running task, the maximum number
		 * of counted records is limited for performance reasons.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for counting items
		 * @since 2023.10
		 * @see mshop/common/manager/insert/ansi
		 * @see mshop/common/manager/update/ansi
		 * @see mshop/common/manager/newid/ansi
		 * @see mshop/common/manager/delete/ansi
		 * @see mshop/common/manager/search/ansi
		 */
		$cfgPathCount = $this->getConfigKey( 'count', 'mshop/common/manager/count' );

		$level = $this->getSiteMode();
		$plugins = $this->searchPlugins();
		$required = [$this->getSearchKey()];
		$conn = $this->context()->db( $this->getResourceName() );

		$attrs = array_filter( $this->getSearchAttributes( false ), fn( $attr ) => $attr->getType() === 'json' );
		$attrs = array_column( $attrs, null, 'code' );

		$results = $this->searchItemsBase( $conn, $filter, $cfgPathSearch, $cfgPathCount, $required, $total, $level, $plugins );
		$prefix = $this->prefix();
		$map = $items = [];

		try
		{
			while( $row = $results->fetch() )
			{
				foreach( $attrs as $code => $attr ) {
					$row[$code] = json_decode( $row[$code] ?? '{}', true );
				}

				$map[$row[$prefix . 'id']] = $row;
			}
		}
		catch( \Exception $e )
		{
			$results->finish();
			throw $e;
		}

		foreach( $this->object()->searchRefs( $map, $ref ) as $id => $row )
		{
			if( $item = $this->applyFilter( $this->create( $row ) ) ) {
				$items[$id] = $item;
			}
		}

		return map( $items );
	}


	/**
	 * Starts a database transaction on the connection identified by the given name
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function begin() : \Aimeos\MShop\Common\Manager\Iface
	{
		$this->context->db( $this->getResourceName() )->begin();
		return $this;
	}


	/**
	 * Commits the running database transaction on the connection identified by the given name
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function commit() : \Aimeos\MShop\Common\Manager\Iface
	{
		$this->context->db( $this->getResourceName() )->commit();
		return $this;
	}


	/**
	 * Rolls back the running database transaction on the connection identified by the given name
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function rollback() : \Aimeos\MShop\Common\Manager\Iface
	{
		$this->context->db( $this->getResourceName() )->rollback();
		return $this;
	}


	/**
	 * Returns the context object.
	 *
	 * @return \Aimeos\MShop\ContextIface Context object
	 */
	protected function context() : \Aimeos\MShop\ContextIface
	{
		return $this->context;
	}


	/**
	 * Returns the domain of the manager
	 *
	 * @return string Domain of the manager
	 */
	protected function domain() : string
	{
		return current( $this->type() ) ?: '';
	}


	/**
	 * Returns the site mode constant for inheritance/aggregation
	 *
	 * @return int Site mode constant (default: SITE_ALL for inheritance and aggregation)
	 */
	protected function getSiteMode() : int
	{
		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
		return $this->context()->config()->get( $this->getConfigKey( 'sitemode', 'mshop/common/manager/sitemode' ), $level );
	}


	/**
	 * Returns the search plugins for transforming the search criteria
	 *
	 * @return \Aimeos\MW\Criteria\Plugin\Iface[] List of search plugins
	 */
	protected function searchPlugins() : array
	{
		return [];
	}
}
