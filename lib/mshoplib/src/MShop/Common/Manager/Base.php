<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
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
abstract class Base extends \Aimeos\MW\Common\Manager\Base
{
	use \Aimeos\MShop\Common\Manager\Sub\Traits;


	private $context;
	private $object;
	private $resourceName;
	private $stmts = [];


	/**
	 * Initialization of class.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		$this->context = $context;
	}


	/**
	 * Catch unknown methods
	 *
	 * @param string $name Name of the method
	 * @param array $param List of method parameter
	 * @throws \Aimeos\MShop\Common\Manager\Exception If method call failed
	 */
	public function __call( $name, array $param )
	{
		throw new \Aimeos\MShop\Exception( sprintf( 'Unable to call method "%1$s"', $name ) );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param string[] $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function cleanup( array $siteids )
	{
		return $this;
	}


	/**
	 * Creates a search critera object
	 *
	 * @param boolean $default Add default criteria (optional)
	 * @return \Aimeos\MW\Criteria\Iface New search criteria object
	 */
	public function createSearch( $default = false )
	{
		$db = $this->getResourceName();
		$config = $this->context->getConfig();
		$dbm = $this->context->getDatabaseManager();

		if( ( $adapter = $config->get( 'resource/' . $db . '/adapter' ) ) === null ) {
			$adapter = $config->get( 'resource/db/adapter' );
		}

		$conn = $dbm->acquire( $db );

		switch( $adapter )
		{
			case 'pgsql':
				$search = new \Aimeos\MW\Criteria\PgSQL( $conn ); break;
			default:
				$search = new \Aimeos\MW\Criteria\SQL( $conn ); break;
		}

		$dbm->release( $conn, $db );

		return $search;
	}


	/**
	 * Deletes an item from storage.
	 *
	 * @param string $itemId Unique ID of the item in the storage
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function deleteItem( $itemId )
	{
		return $this->getObject()->deleteItems( [$itemId] );
	}


	/**
	 * Starts a database transaction on the connection identified by the given name
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function begin()
	{
		return $this->beginTransation( $this->getResourceName() );
	}


	/**
	 * Commits the running database transaction on the connection identified by the given name
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function commit()
	{
		return $this->commitTransaction( $this->getResourceName() );
	}


	/**
	 * Rolls back the running database transaction on the connection identified by the given name
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function rollback()
	{
		return $this->rollbackTransaction( $this->getResourceName() );
	}


	/**
	 * Adds or updates a list of item objects.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[] $items List of item object whose data should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\Iface[] Saved item objects
	 */
	public function saveItems( array $items, $fetch = true )
	{
		foreach( $items as $id => $item ) {
			$items[$id] = $this->getObject()->saveItem( $item, $fetch );
		}

		return $items;
	}


	/**
	 * Injects the reference of the outmost object
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $object Reference to the outmost manager or decorator
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function setObject( \Aimeos\MShop\Common\Manager\Iface $object )
	{
		$this->object = $object;
		return $this;
	}


	/**
	 * Counts the number products that are available for the values of the given key.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria
	 * @param string $key Search key for aggregating the key column
	 * @param string $cfgPath Configuration key for the SQL statement
	 * @param string[] $required List of domain/sub-domain names like "catalog.index" that must be additionally joined
	 * @param string|null $value Search key for aggregating the value column
	 * @return integer[] List of ID values as key and the number of counted products as value
	 * @todo 2018.01 Reorder Parameter list
	 */
	protected function aggregateBase( \Aimeos\MW\Criteria\Iface $search, $key, $cfgPath, $required = [], $value = null )
	{
		$list = [];
		$context = $this->getContext();

		$dbname = $this->getResourceName();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$search = clone $search;
			$attrList = $this->getObject()->getSearchAttributes();

			if( $value === null && ( $value = key( $attrList ) ) === null ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'No search keys available' ) );
			}

			if( !isset( $attrList[$key] ) ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Unknown search key "%1$s"', $key ) );
			}

			if( $value !== null && !isset( $attrList[$value] ) ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Unknown search key "%1$s"', $value ) );
			}

			/** @todo Required to get the joins, but there should be a better way */
			$expr = array(
				$search->getConditions(),
				$search->compare( '!=', $key, null ),
				$search->compare( '!=', $value, null ),
			);
			$search->setConditions( $search->combine( '&&', $expr ) );

			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
			$total = null;

			$sql = $this->getSqlConfig( $cfgPath );
			$sql = str_replace( ':key', $attrList[$key]->getInternalCode(), $sql );
			$sql = str_replace( ':val', $attrList[$value]->getInternalCode(), $sql );

			$results = $this->searchItemsBase( $conn, $search, $sql, '', $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false ) {
				$list[$row['key']] = $row['count'];
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $list;
	}


	/**
	 * Returns the newly created ID for the last record which was inserted.
	 *
	 * @param \Aimeos\MW\DB\Connection\Iface $conn Database connection used to insert the new record
	 * @param string $cfgpath Configuration path to the SQL statement for retrieving the new ID of the last inserted record
	 * @return string ID of the last record that was inserted by using the given connection
	 * @throws \Aimeos\MShop\Common\Exception if there's no ID of the last record available
	 */
	protected function newId( \Aimeos\MW\DB\Connection\Iface $conn, $cfgpath )
	{
		$result = $conn->create( $this->getSqlConfig( $cfgpath ) )->execute();

		if( ( $row = $result->fetch( \Aimeos\MW\DB\Result\Base::FETCH_NUM ) ) === false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'ID of last inserted database record not available' ) );
		}
		$result->finish();

		return $row[0];
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param string[] $siteids List of IDs for sites whose entries should be deleted
	 * @param string $cfgpath Configuration key to the cleanup statement
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	protected function cleanupBase( array $siteids, $cfgpath )
	{
		$dbm = $this->context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$sql = $this->getSqlConfig( $cfgpath );
			$sql = str_replace( ':cond', '1=1', $sql );

			$stmt = $conn->create( $sql );

			foreach( $siteids as $siteid )
			{
				$stmt->bind( 1, $siteid, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$stmt->execute()->finish();
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $this;
	}


	/**
	 * Sets the base criteria "status".
	 * (setConditions overwrites the base criteria)
	 *
	 * @param string $domain Name of the domain/sub-domain like "product" or "product.list"
	 * @return \Aimeos\MW\Criteria\Iface Search critery object
	 */
	protected function createSearchBase( $domain )
	{
		$object = $this->createSearch();
		$object->setConditions( $object->compare( '==', $domain . '.status', 1 ) );

		return $object;
	}


	/**
	 * Returns the context object.
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface Context object
	 */
	protected function getContext()
	{
		return $this->context;
	}


	/**
	 * Returns the outmost decorator of the decorator stack
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Outmost decorator object
	 */
	protected function getObject()
	{
		if( $this->object !== null ) {
			return $this->object;
		}

		return $this;
	}


	/**
	 * Returns the search attribute objects used for searching.
	 *
	 * @param array $list Associative list of search keys and the lists of search definitions
	 * @param string $path Configuration path to the sub-domains for fetching the search definitions
	 * @param string[] $default List of sub-domains if no others are configured
	 * @param boolean $withsub True to include search definitions of sub-domains, false if not
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] Associative list of search keys and criteria attribute items as values
	 * @since 2014.09
	 */
	protected function getSearchAttributesBase( array $list, $path, array $default, $withsub )
	{
		$attr = [];

		foreach( $list as $key => $fields ) {
			$attr[$key] = new \Aimeos\MW\Criteria\Attribute\Standard( $fields );
		}

		if( $withsub === true )
		{
			$domains = $this->context->getConfig()->get( $path, $default );

			foreach( $domains as $domain ) {
				$attr += $this->getObject()->getSubManager( $domain )->getSearchAttributes( true );
			}
		}

		return $attr;
	}


	/**
	 * Returns the search results for the given SQL statement.
	 *
	 * @param \Aimeos\MW\DB\Connection\Iface $conn Database connection
	 * @param string $sql SQL statement
	 * @return \Aimeos\MW\DB\Result\Iface Search result object
	 */
	protected function getSearchResults( \Aimeos\MW\DB\Connection\Iface $conn, $sql )
	{
		$time = microtime( true );

		$stmt = $conn->create( $sql );
		$result = $stmt->execute();

		$msg = [
			'time' => ( microtime( true ) - $time ) * 1000,
			'class' => get_class( $this ),
			'stmt' => (string) $stmt,
		];
		$this->context->getLogger()->log( $msg, \Aimeos\MW\Logger\Base::DEBUG, 'core/sql' );

		return $result;
	}


	/**
	 * Returns the site IDs for the given site level constant.
	 *
	 * @param integer $sitelevel Site level constant from \Aimeos\MShop\Locale\Manager\Base
	 * @return string[] List of site IDs
	 */
	private function getSiteIds( $sitelevel )
	{
		$locale = $this->context->getLocale();
		$siteIds = array( $locale->getSiteId() );

		if( $sitelevel & \Aimeos\MShop\Locale\Manager\Base::SITE_PATH ) {
			$siteIds = array_merge( $siteIds, $locale->getSitePath() );
		}

		if( $sitelevel & \Aimeos\MShop\Locale\Manager\Base::SITE_SUBTREE ) {
			$siteIds = array_merge( $siteIds, $locale->getSiteSubTree() );
		}

		$siteIds = array_unique( $siteIds );

		return $siteIds;
	}


	/**
	 * Returns the SQL statement for the given config path
	 *
	 * If available, the database specific SQL statement is returned, otherwise
	 * the ANSI SQL statement. The database type is determined via the resource
	 * adapter.
	 *
	 * @param string $path Configuration path to the SQL statement
	 * @return string ANSI or database specific SQL statement
	 */
	protected function getSqlConfig( $path )
	{
		$config = $this->getContext()->getConfig();
		$adapter = $config->get( 'resource/' . $this->getResourceName() . '/adapter' );

		return $config->get( $path . '/' . $adapter, $config->get( $path . '/ansi', $path ) );
	}


	/**
	 * Returns the item for the given search key/value pairs.
	 *
	 * @param array $pairs Search key/value pairs for the item
	 * @param string[] $ref List of domains whose items should be fetched too
	 * @param boolean $default True to add default criteria
	 * @return \Aimeos\MShop\Common\Item\Iface Requested item
	 * @throws \Aimeos\MShop\Exception if no item with the given ID found
	 */
	protected function findItemBase( array $pairs, array $ref, $default  )
	{
		$expr = [];
		$criteria = $this->getObject()->createSearch( $default )->setSlice( 0, 1 );

		foreach( $pairs as $key => $value )
		{
			if( $value === null ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Required value for "%1$s" is missing', $key ) );
			}
			$expr[] = $criteria->compare( '==', $key, $value );
		}

		$criteria->setConditions( $criteria->combine( '&&', $expr ) );
		$items = $this->getObject()->searchItems( $criteria, $ref );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No item found for conditions: %1$s', print_r( $pairs, true ) ) );
		}

		return $item;
	}


	/**
	 * Returns the cached statement for the given key or creates a new prepared statement.
	 * If no SQL string is given, the key is used to retrieve the SQL string from the configuration.
	 *
	 * @param \Aimeos\MW\DB\Connection\Iface $conn Database connection
	 * @param string $cfgkey Unique key for the SQL
	 * @param string|null $sql SQL string if it shouldn't be retrieved from the configuration
	 * @return \Aimeos\MW\DB\Statement\Iface Database statement object
	 */
	protected function getCachedStatement( \Aimeos\MW\DB\Connection\Iface $conn, $cfgkey, $sql = null )
	{
		if( !isset( $this->stmts['stmt'][$cfgkey] ) || !isset( $this->stmts['conn'][$cfgkey] )
				|| $conn !== $this->stmts['conn'][$cfgkey]
		) {
			if( $sql === null ) {
				$sql = $this->getSqlConfig( $cfgkey );
			}

			$this->stmts['stmt'][$cfgkey] = $conn->create( $sql );
			$this->stmts['conn'][$cfgkey] = $conn;
		}

		return $this->stmts['stmt'][$cfgkey];
	}


	/**
	 * Returns the item for the given search key and ID.
	 *
	 * @param string $key Search key for the requested ID
	 * @param string $id Unique ID to search for
	 * @param string[] $ref List of domains whose items should be fetched too
	 * @param boolean $default True to add default criteria
	 * @return \Aimeos\MShop\Common\Item\Iface Requested item
	 * @throws \Aimeos\MShop\Exception if no item with the given ID found
	 */
	protected function getItemBase( $key, $id, array $ref, $default )
	{
		$criteria = $this->getObject()->createSearch( $default )->setSlice( 0, 1 );
		$expr = [
			$criteria->compare( '==', $key, $id ),
			$criteria->getConditions()
		];
		$criteria->setConditions( $criteria->combine( '&&', $expr ) );
		$items = $this->getObject()->searchItems( $criteria, $ref );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Item with ID "%2$s" in "%1$s" not found', $key, $id ) );
		}

		return $item;
	}


	/**
	 * Returns the SQL strings for joining dependent tables.
	 *
	 * @param \Aimeos\MW\Criteria\Attribute\Iface[] $attributes List of criteria attribute items
	 * @param string $prefix Search key prefix
	 * @return array List of JOIN SQL strings
	 */
	private function getJoins( array $attributes, $prefix )
	{
		$iface = \Aimeos\MW\Criteria\Attribute\Iface::class;
		$sep = $this->getKeySeparator();
		$name = $prefix . $sep . 'id';

		if( isset( $attributes[$name] ) && $attributes[$name] instanceof $iface ) {
			return $attributes[$name]->getInternalDeps();
		}
		else if( isset( $attributes['id'] ) && $attributes['id'] instanceof $iface ) {
			return $attributes['id']->getInternalDeps();
		}

		return [];
	}


	/**
	 * Returns the available manager types
	 *
	 * @param string $type Main manager type
	 * @param string $path Configuration path to the sub-domains
	 * @param string[] $default List of sub-domains if no others are configured
	 * @param boolean $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	protected function getResourceTypeBase( $type, $path, array $default, $withsub )
	{
		$list = array( $type );

		foreach( $this->context->getConfig()->get( $path, $default ) as $domain ) {
			$list = array_merge( $list, $this->getObject()->getSubManager( $domain )->getResourceType( $withsub ) );
		}

		return $list;
	}


	/**
	 * Returns the name of the resource or of the default resource.
	 *
	 * @return string Name of the resource
	 */
	protected function getResourceName()
	{
		if( $this->resourceName === null ) {
			$this->resourceName = $this->context->getConfig()->get( 'resource/default', 'db' );
		}

		return $this->resourceName;
	}


	/**
	 * Sets the name of the database resource that should be used.
	 *
	 * @param string $name Name of the resource
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	protected function setResourceName( $name )
	{
		$config = $this->context->getConfig();

		if( $config->get( 'resource/' . $name ) === null ) {
			$this->resourceName = $config->get( 'resource/default', 'db' );
		} else {
			$this->resourceName = $name;
		}

		return $this;
	}


	/**
	 * Replaces ":site" marker in a search config item array.
	 *
	 * @param array &$searchAttr Single search config definition including the "internalcode" key
	 * @param string $column Name (including alias) of the column containing the site ID in the storage
	 * @param string|string[] $value Site ID or list of site IDs
	 * @param string $marker Marker to replace
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	protected function replaceSiteMarker( &$searchAttr, $column, $value, $marker = ':site' )
	{
		$types = array( 'siteid' => \Aimeos\MW\DB\Statement\Base::PARAM_INT );
		$translations = array( 'siteid' => $column );
		$conn = new \Aimeos\MW\DB\Connection\None();

		$search = new \Aimeos\MW\Criteria\SQL( $conn );

		$expr = $search->compare( '==', 'siteid', ( is_array( $value ) ? array_unique( $value ) : $value ) );
		$string = $expr->toSource( $types, $translations );

		$searchAttr['internalcode'] = str_replace( $marker, $string, $searchAttr['internalcode'] );

		return $this;
	}


	/**
	 * Returns the site coditions for the search request
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $keys Sorted list of criteria keys
	 * @param \Aimeos\MW\Criteria\Attribute\Iface[] $attributes Associative list of search keys and criteria attribute items as values
	 * @param string[] $siteIds List of site IDs that should be used for searching
	 * @return \Aimeos\MW\Criteria\Expression\Iface[] List of search conditions
	 * @since 2015.01
	 */
	protected function getSearchSiteConditions( \Aimeos\MW\Criteria\Iface $search, array $keys, array $attributes, array $siteIds )
	{
		/** mshop/common/manager/sitecheck
		 * Enables or disables using the site IDs in search queries
		 *
		 * For market places, products of different shop owners managing their
		 * own sites should be shown in the frontend. By default, only the items
		 * from the current site are displayed. Setting this option to false
		 * disables the restriction to the current site and shows all products
		 * from all sites. This does also apply to all other records from
		 * different domains than "product".
		 *
		 * This option is most effective if it's only set for the shop frontend,
		 * so the shop owners will only see and manager their own products in
		 * the administration interface.
		 *
		 * @param boolean True to resrict items to the current site, false to show item form all sites
		 * @since 2016.10
		 * @category Developer
		 */
		if( $this->context->getConfig()->get( 'mshop/common/manager/sitecheck', true ) == false ) {
			return [];
		}

		$cond = [];
		$sep = $this->getKeySeparator();

		foreach( $keys as $key )
		{
			$name = $key . $sep . 'siteid';

			if( isset( $attributes[$name] ) ) {
				$cond[] = $search->compare( '==', $name, $siteIds );
			}
		}

		return $cond;
	}


	/**
	 * Returns the string replacements for the SQL statements
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search critera object
	 * @param \Aimeos\MW\Criteria\Attribute\Iface[] $attributes Associative list of search keys and criteria attribute items as values
	 * @param \Aimeos\MW\Criteria\Plugin\Iface[] $plugins Associative list of search keys and criteria plugin items as values
	 * @param string[] $joins Associative list of SQL joins
	 * @return array Array of keys, find and replace arrays
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
			$keys[] = 'orderby';
			$find[] = ':order';
			$replace[] = $search->getSortationSource( $types, $translations, $funcs );

			$keys[] = 'columns';
			$find[] = ':columns';
			$replace[] = implode( ', ', $search->translate( $search->getSortations(), $translations ) );
		}

		return [$keys, $find, $replace];
	}


	/**
	 * Returns the search result of the statement combined with the given criteria.
	 *
	 * @param \Aimeos\MW\DB\Connection\Iface $conn Database connection
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string $cfgPathSearch Path to SQL statement in configuration for searching
	 * @param string $cfgPathCount Path to SQL statement in configuration for counting
	 * @param string[] $required Additional search keys to add conditions for even if no conditions are available
	 * @param integer|null $total Contains the number of all records matching the criteria if not null
	 * @param integer $sitelevel Constant from \Aimeos\MShop\Locale\Manager\Base for defining which site IDs should be used for searching
	 * @param \Aimeos\MW\Criteria\Plugin\Iface[] $plugins Associative list of search keys and criteria plugin items as values
	 * @return \Aimeos\MW\DB\Result\Iface SQL result object for accessing the found records
	 * @throws \Aimeos\MShop\Exception if no number of all matching records is available
	 */
	protected function searchItemsBase( \Aimeos\MW\DB\Connection\Iface $conn, \Aimeos\MW\Criteria\Iface $search,
		$cfgPathSearch, $cfgPathCount, array $required, &$total = null,
		$sitelevel = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL, array $plugins = [] )
	{
		$joins = [];
		$conditions = $search->getConditions();
		$siteIds = $this->getSiteIds( $sitelevel );
		$attributes = $this->getObject()->getSearchAttributes();
		$keys = $this->getCriteriaKeyList( $search, $required );

		$basekey = array_shift( $required );

		foreach( $keys as $key )
		{
			if( $key !== $basekey ) {
				$joins = array_merge( $joins, $this->getJoins( $attributes, $key ) );
			}
		}

		$cond = $this->getSearchSiteConditions( $search, $keys, $attributes, $siteIds );

		if( $conditions !== null ) {
			$cond[] = $conditions;
		}

		$search = clone $search;
		$search->setConditions( $search->combine( '&&', $cond ) );

		list( $keys, $find, $replace ) = $this->getSQLReplacements( $search, $attributes, $plugins, $joins );

		if( $total !== null )
		{
			$sql = new \Aimeos\MW\Template\SQL( $this->getSqlConfig( $cfgPathCount ) );
			$sql->replace( $find, $replace )->enable( $keys );

			$result = $this->getSearchResults( $conn, $sql->str() );
			$row = $result->fetch();
			$result->finish();

			if( $row === false ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Total results value not found' ) );
			}

			$total = (int) $row['count'];
		}


		$sql = new \Aimeos\MW\Template\SQL( $this->getSqlConfig( $cfgPathSearch ) );
		$sql->replace( $find, $replace )->enable( $keys );

		return $this->getSearchResults( $conn, $sql->str() );
	}


	/**
	 * Deletes items specified by its IDs.
	 *
	 * @param string[] $ids List of IDs
	 * @param string $cfgpath Configuration path to the SQL statement
	 * @param boolean $siteidcheck If siteid should be used in the statement
	 * @param string $name Name of the ID column
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	protected function deleteItemsBase( array $ids, $cfgpath, $siteidcheck = true, $name = 'id' )
	{
		if( empty( $ids ) ) { return; }

		$context = $this->getContext();
		$dbname = $this->getResourceName();

		$search = $this->getObject()->createSearch();
		$search->setConditions( $search->compare( '==', $name, $ids ) );

		$types = array( $name => \Aimeos\MW\DB\Statement\Base::PARAM_STR );
		$translations = array( $name => '"' . $name . '"' );

		$cond = $search->getConditionSource( $types, $translations );
		$sql = str_replace( ':cond', $cond, $this->getSqlConfig( $cfgpath ) );

		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$stmt = $conn->create( $sql );

			if( $siteidcheck ) {
				$stmt->bind( 1, $context->getLocale()->getSiteId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			}

			$stmt->execute()->finish();

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $this;
	}


	/**
	 * Starts a database transaction on the connection identified by the given name.
	 *
	 * @param string $dbname Name of the database settings in the resource configuration
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	protected function beginTransation( $dbname = 'db' )
	{
		$dbm = $this->context->getDatabaseManager();

		$conn = $dbm->acquire( $dbname );
		$conn->begin();
		$dbm->release( $conn, $dbname );

		return $this;
	}


	/**
	 * Commits the running database transaction on the connection identified by the given name.
	 *
	 * @param string $dbname Name of the database settings in the resource configuration
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	protected function commitTransaction( $dbname = 'db' )
	{
		$dbm = $this->context->getDatabaseManager();

		$conn = $dbm->acquire( $dbname );
		$conn->commit();
		$dbm->release( $conn, $dbname );

		return $this;
	}


	/**
	 * Rolls back the running database transaction on the connection identified by the given name.
	 *
	 * @param string $dbname Name of the database settings in the resource configuration
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	protected function rollbackTransaction( $dbname = 'db' )
	{
		$dbm = $this->context->getDatabaseManager();

		$conn = $dbm->acquire( $dbname );
		$conn->rollback();
		$dbm->release( $conn, $dbname );

		return $this;
	}
}
