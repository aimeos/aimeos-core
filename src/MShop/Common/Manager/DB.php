<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2023
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager;


/**
 * Method trait for managers
 *
 * @package MShop
 * @subpackage Common
 */
trait DB
{
	private ?\Aimeos\Base\Criteria\Iface $search;
	private ?string $resourceName = null;
	private array $cachedStmts = [];
	private string $subpath;
	private string $domain;


	/**
	 * Returns the context object.
	 *
	 * @return \Aimeos\MShop\ContextIface Context object
	 */
	abstract protected function context() : \Aimeos\MShop\ContextIface;


	/**
	 * Creates the criteria attribute items from the list of entries
	 *
	 * @param array $list Associative array of code as key and array with properties as values
	 * @return \Aimeos\Base\Criteria\Attribute\Standard[] List of criteria attribute items
	 */
	abstract protected function createAttributes( array $list ) : array;


	/**
	 * Returns the attribute helper functions for searching defined by the manager.
	 *
	 * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes List of search attribute items
	 * @return array Associative array of attribute code and helper function
	 */
	abstract protected function getSearchFunctions( array $attributes ) : array;


	/**
	 * Returns the attribute translations for searching defined by the manager.
	 *
	 * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes List of search attribute items
	 * @return array Associative array of attribute code and internal attribute code
	 */
	abstract protected function getSearchTranslations( array $attributes ) : array;


	/**
	 * Returns the attribute types for searching defined by the manager.
	 *
	 * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes List of search attribute items
	 * @return array Associative array of attribute code and internal attribute type
	 */
	abstract protected function getSearchTypes( array $attributes ) : array;


	/**
	 * Returns the outmost decorator of the decorator stack
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Outmost decorator object
	 */
	abstract protected function object() : \Aimeos\MShop\Common\Manager\Iface;


	/**
	 * Returns the site expression for the given name
	 *
	 * @param string $name Name of the site condition
	 * @param int $sitelevel Site level constant from \Aimeos\MShop\Locale\Manager\Base
	 * @return \Aimeos\Base\Criteria\Expression\Iface Site search condition
	 */
	abstract protected function siteCondition( string $name, int $sitelevel ) : \Aimeos\Base\Criteria\Expression\Iface;


	/**
	 * Returns the site ID that should be used based on the site level
	 *
	 * @param string $siteId Site ID to check
	 * @param int $sitelevel Site level to check against
	 * @return string Site ID that should be use based on the site level
	 * @since 2022.04
	 */
	abstract protected function siteId( string $siteId, int $sitelevel ) : string;


	/**
	 * Returns the type of the mananger as separate parts
	 *
	 * @return string[] List of manager part names
	 */
	abstract public function type() : array;


	/**
	 * Adds additional column names to SQL statement
	 *
	 * @param string[] $columns List of column names
	 * @param string $sql Insert or update SQL statement
	 * @param bool $mode True for insert, false for update statement
	 * @return string Modified insert or update SQL statement
	 */
	protected function addSqlColumns( array $columns, string $sql, bool $mode = true ) : string
	{
		$names = $values = '';

		if( $mode )
		{
			foreach( $columns as $name )
			{
				// @todo: Remove in 2025.01
				$parts = explode( '.', $name );
				$name = trim( end( $parts ), '"' );

				$names .= '"' . $name . '", '; $values .= '?, ';
			}
		}
		else
		{
			foreach( $columns as $name )
			{
				// @todo: Remove in 2025.01
				$parts = explode( '.', $name );
				$name = trim( end( $parts ), '"' );

				$names .= '"' . $name . '" = ?, ';
			}
		}

		return str_replace( [':names', ':values'], [$names, $values], $sql );
	}


	/**
	 * Counts the number products that are available for the values of the given key.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $search Search criteria
	 * @param array|string $keys Search key or list of keys for aggregation
	 * @param string $cfgPath Configuration key for the SQL statement
	 * @param string[] $required List of domain/sub-domain names like "catalog.index" that must be additionally joined
	 * @param string|null $value Search key for aggregating the value column
	 * @param string|null $type Type of aggregation, e.g.  "sum", "min", "max" or NULL for "count"
	 * @return \Aimeos\Map List of ID values as key and the number of counted products as value
	 * @todo 2018.01 Reorder Parameter list
	 */
	protected function aggregateBase( \Aimeos\Base\Criteria\Iface $search, $keys, string $cfgPath,
		array $required = [], ?string $value = null, ?string $type = null ) : \Aimeos\Map
	{
		/** mshop/common/manager/aggregate/limit
		 * Limits the number of records that are used when aggregating items
		 *
		 * As counting huge amount of records (several 10 000 records) takes a long time,
		 * the limit can cut down response times so the counts are available more quickly
		 * in the front-end and the server load is reduced.
		 *
		 * Using a low limit can lead to incorrect numbers if the amount of found items
		 * is very high. Approximate item counts are normally not a problem but it can
		 * lead to the situation that visitors see that no items are available despite
		 * the fact that there would be at least one.
		 *
		 * @param integer Number of records
		 * @since 2021.04
		 */
		$limit = $this->context()->config()->get( 'mshop/common/manager/aggregate/limit', 10000 );

		if( empty( $keys ) )
		{
			$msg = $this->context()->translate( 'mshop', 'At least one key is required for aggregation' );
			throw new \Aimeos\MShop\Exception( $msg );
		}

		$attrMap = array_column( array_filter( $this->object()->getSearchAttributes(), function( $item ) {
			return $item->isPublic() || strncmp( $item->getCode(), 'agg:', 4 ) === 0;
		} ), null, 'code' );

		if( $value === null && ( $value = key( $attrMap ) ) === null )
		{
			$msg = $this->context()->translate( 'mshop', 'No search keys available' );
			throw new \Aimeos\MShop\Exception( $msg );
		}

		if( ( $pos = strpos( $valkey = $value, '(' ) ) !== false ) {
			$value = substr( $value, 0, $pos ) . '()'; // remove parameters from search function
		}

		if( !isset( $attrMap[$value] ) )
		{
			$msg = $this->context()->translate( 'mshop', 'Unknown search key "%1$s"' );
			throw new \Aimeos\MShop\Exception( sprintf( $msg, $value ) );
		}

		$keys = (array) $keys;
		$acols = $cols = $expr = [];
		$search = (clone $search)->slice( $search->getOffset(), min( $search->getLimit(), $limit ) );

		foreach( $keys as $string )
		{
			if( ( $attrItem = $attrMap[$string] ?? null ) === null )
			{
				$msg = $this->context()->translate( 'mshop', 'Unknown search key "%1$s"' );
				throw new \Aimeos\MShop\Exception( sprintf( $msg, $string ) );
			}

			if( strpos( $attrItem->getInternalCode(), '"' ) === false ) {
				$prefixed = $this->alias( $attrItem->getCode() ) . '."' . $attrItem->getInternalCode() . '"';
			} else { // @todo: Remove in 2025.01
				$prefixed = $attrItem->getInternalCode();
			}

			$acols[] = $prefixed . ' AS "' . $string . '"';
			$cols[] = $prefixed;

			$expr[] = $search->compare( '!=', $string, null ); // required for the joins
		}

		$expr[] = $search->compare( '!=', $valkey, null );
		$search->add( $search->and( $expr ) );

		$val = $attrMap[$value]->getInternalCode();

		if( strpos( $val, '"' ) === false ) {
			$val = $this->alias( $attrMap[$value]->getCode() ) . '."' . $val . '"';
		}

		$sql = $this->getSqlConfig( $cfgPath );
		$sql = str_replace( ':cols', join( ', ', $cols ), $sql );
		$sql = str_replace( ':acols', join( ', ', $acols ), $sql );
		$sql = str_replace( ':keys', '"' . join( '", "', $keys ) . '"', $sql );
		$sql = str_replace( ':type', in_array( $type, ['avg', 'count', 'max', 'min', 'sum'] ) ? $type : 'count', $sql );
		$sql = str_replace( ':val', $val, $sql );

		return $this->aggregateResult( $search, $sql, $required );
	}


	/**
	 * Returns the aggregated values for the given SQL string and filter.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $filter Filter object
	 * @param string $sql SQL statement
	 * @param string[] $required List of domain/sub-domain names like "catalog.index" that must be additionally joined
	 * @return \Aimeos\Map (Nested) list of aggregated values as key and the number of counted products as value
	 */
	protected function aggregateResult( \Aimeos\Base\Criteria\Iface $filter, string $sql, array $required ) : \Aimeos\Map
	{
		$map = [];
		$total = null;
		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
		$conn = $this->context()->db( $this->getResourceName() );
		$results = $this->searchItemsBase( $conn, $filter, $sql, '', $required, $total, $level );

		while( $row = $results->fetch() )
		{
			$row = $this->transform( $row );

			$temp = &$map;
			$last = array_pop( $row );

			foreach( $row as $val ) {
				$temp[$val] = $temp[$val] ?? [];
				$temp = &$temp[$val];
			}
			$temp = $last;
		}

		return map( $map );
	}


	/**
	 * Returns the table alias name.
	 *
	 * @param string|null $attrcode Search attribute code
	 * @return string Table alias name
	 */
	protected function alias( ?string $attrcode = null ) : string
	{
		if( $attrcode )
		{
			$parts = array_slice( explode( '.', $attrcode ), 0, -1 ) ?: $this->type();
			$str = 'm' . substr( (string) array_shift( $parts ), 0, 3 );
		}
		else
		{
			$parts = $this->type();
			$str = 'm' . substr( (string) array_shift( $parts ), 0, 3 );
		}

		foreach( $parts as $part ) {
			$str .= substr( $part, 0, 2 );
		}

		return $str;
	}


	/**
	 * Adds aliases for the columns
	 *
	 * @param array $map Associative list of search keys as keys and internal column names as values
	 * @return array Associative list of search keys as keys and aliased column names as values
	 */
	protected function aliasTranslations( array $map ) : array
	{
		foreach( $map as $key => $value )
		{
			if( strpos( $value, '"' ) === false ) {
				$map[$key] = $this->alias( $key ) . '."' . $value . '"';
			}
		}

		return $map;
	}


	/**
	 * Binds additional values to the statement before execution.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Item object
	 * @param \Aimeos\Base\DB\Statement\Iface $stmt Database statement object
	 * @param int $idx Current bind index
	 * @return \Aimeos\Base\DB\Statement\Iface Database statement object with bound values
	 */
	protected function bind( \Aimeos\MShop\Common\Item\Iface $item, \Aimeos\Base\DB\Statement\Iface $stmt, int &$idx ) : \Aimeos\Base\DB\Statement\Iface
	{
		return $stmt;
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @param string $cfgpath Configuration key to the cleanup statement
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	protected function clearBase( iterable $siteids, string $cfgpath ) : \Aimeos\MShop\Common\Manager\Iface
	{
		if( empty( $siteids ) ) {
			return $this;
		}

		$conn = $this->context()->db( $this->getResourceName() );

		$sql = $this->getSqlConfig( $cfgpath );
		$sql = str_replace( ':cond', '1=1', $sql );

		$stmt = $conn->create( $sql );

		foreach( $siteids as $siteid )
		{
			$stmt->bind( 1, $siteid );
			$stmt->execute()->finish();
		}

		return $this;
	}


	/**
	 * Deletes items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface|\Aimeos\Map|array|string $items List of item objects or IDs of the items
	 * @param string $cfgpath Configuration path to the SQL statement
	 * @param bool $siteid If siteid should be used in the statement
	 * @param string $name Name of the ID column
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	protected function deleteItemsBase( $items, string $cfgpath, bool $siteid = true,
		string $name = 'id' ) : \Aimeos\MShop\Common\Manager\Iface
	{
		if( map( $items )->isEmpty() ) {
			return $this;
		}

		$search = $this->object()->filter();
		$search->setConditions( $search->compare( '==', $name, $items ) );

		$types = array( $name => \Aimeos\Base\DB\Statement\Base::PARAM_STR );
		$translations = array( $name => '"' . $name . '"' );

		$cond = $search->getConditionSource( $types, $translations );
		$sql = str_replace( ':cond', $cond, $this->getSqlConfig( $cfgpath ) );

		$context = $this->context();
		$conn = $context->db( $this->getResourceName() );

		$stmt = $conn->create( $sql );

		if( $siteid ) {
			$stmt->bind( 1, $context->locale()->getSiteId() . '%' );
		}

		$stmt->execute()->finish();

		return $this;
	}


	/**
	 * Sets the base criteria "status".
	 * (setConditions overwrites the base criteria)
	 *
	 * @param string $domain Name of the domain/sub-domain like "product" or "product.list"
	 * @param bool|null $default TRUE for status=1, NULL for status>0, FALSE for no restriction
	 * @return \Aimeos\Base\Criteria\Iface Search critery object
	 */
	protected function filterBase( string $domain, ?bool $default = false ) : \Aimeos\Base\Criteria\Iface
	{
		$context = $this->context();
		$db = $this->getResourceName();
		$conn = $context->db( $db );
		$config = $context->config();

		if( ( $adapter = $config->get( 'resource/' . $db . '/adapter' ) ) === null ) {
			$adapter = $config->get( 'resource/db/adapter' );
		}

		switch( $adapter )
		{
			case 'pgsql':
				$filter = new \Aimeos\Base\Criteria\PgSQL( $conn ); break;
			default:
				$filter = new \Aimeos\Base\Criteria\SQL( $conn ); break;
		}

		if( $default !== false ) {
			$filter->add( $domain . '.status', $default ? '==' : '>=', 1 );
		}

		return $filter;
	}


	/**
	 * Returns the item for the given search key/value pairs.
	 *
	 * @param array $pairs Search key/value pairs for the item
	 * @param string[] $ref List of domains whose items should be fetched too
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Common\Item\Iface Requested item
	 * @throws \Aimeos\MShop\Exception if no item with the given ID found
	 */
	protected function findBase( array $pairs, array $ref, ?bool $default ) : \Aimeos\MShop\Common\Item\Iface
	{
		$expr = [];
		$criteria = $this->object()->filter( $default )->slice( 0, 1 );

		foreach( $pairs as $key => $value )
		{
			if( $value === null )
			{
				$msg = $this->context()->translate( 'mshop', 'Required value for "%1$s" is missing' );
				throw new \Aimeos\MShop\Exception( sprintf( $msg, $key ) );
			}
			$expr[] = $criteria->compare( '==', $key, $value );
		}

		$criteria->setConditions( $criteria->and( $expr ) );

		if( ( $item = $this->object()->search( $criteria, $ref )->first() ) ) {
			return $item;
		}

		$msg = $this->context()->translate( 'mshop', 'No item found for conditions: %1$s' );
		throw new \Aimeos\MShop\Exception( sprintf( $msg, print_r( $pairs, true ) ), 404 );
	}


	/**
	 * Returns the cached statement for the given key or creates a new prepared statement.
	 * If no SQL string is given, the key is used to retrieve the SQL string from the configuration.
	 *
	 * @param \Aimeos\Base\DB\Connection\Iface $conn Database connection
	 * @param string $cfgkey Unique key for the SQL
	 * @param string|null $sql SQL string if it shouldn't be retrieved from the configuration
	 * @return \Aimeos\Base\DB\Statement\Iface Database statement object
	 */
	protected function getCachedStatement( \Aimeos\Base\DB\Connection\Iface $conn, string $cfgkey,
		?string $sql = null ) : \Aimeos\Base\DB\Statement\Iface
	{
		if( !isset( $this->cachedStmts['stmt'][$cfgkey] )
			|| !isset( $this->cachedStmts['conn'][$cfgkey] )
			|| $conn !== $this->cachedStmts['conn'][$cfgkey]
		) {
			if( $sql === null ) {
				$sql = $this->getSqlConfig( $cfgkey );
			}

			$this->cachedStmts['stmt'][$cfgkey] = $conn->create( $sql );
			$this->cachedStmts['conn'][$cfgkey] = $conn;
		}

		return $this->cachedStmts['stmt'][$cfgkey];
	}


	/**
	 * Returns the full configuration key for the passed last part
	 *
	 * @param string $name Configuration last part
	 * @param string $default Default configuration key
	 * @return string Full configuration key
	 */
	protected function getConfigKey( string $name, string $default = '' ) : string
	{
		$type = $this->type();
		array_splice( $type, 1, 0, ['manager'] );
		$key = 'mshop/' . join( '/', $type ) . '/' . $name;

		if( $this->context()->config()->get( $key ) ) {
			return $key;
		}

		return $default;
	}


	/**
	 * Returns a sorted list of required criteria keys.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $criteria Search criteria object
	 * @param string[] $required List of prefixes of required search conditions
	 * @return string[] Sorted list of criteria keys
	 */
	protected function getCriteriaKeyList( \Aimeos\Base\Criteria\Iface $criteria, array $required ) : array
	{
		$keys = array_merge( $required, $this->getCriteriaKeys( $required, $criteria->getConditions() ) );

		foreach( $criteria->getSortations() as $sortation ) {
			$keys = array_merge( $keys, $this->getCriteriaKeys( $required, $sortation ) );
		}

		$keys = array_unique( array_merge( $required, $keys ) );
		sort( $keys );

		return $keys;
	}


	/**
	 * Returns the item for the given search key and ID.
	 *
	 * @param string $key Search key for the requested ID
	 * @param string $id Unique ID to search for
	 * @param string[] $ref List of domains whose items should be fetched too
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Common\Item\Iface Requested item
	 * @throws \Aimeos\MShop\Exception if no item with the given ID found
	 */
	protected function getItemBase( string $key, string $id, array $ref, ?bool $default ) : \Aimeos\MShop\Common\Item\Iface
	{
		$criteria = $this->object()->filter( $default )->add( [$key => $id] )->slice( 0, 1 );

		if( ( $item = $this->object()->search( $criteria, $ref )->first() ) ) {
			return $item;
		}

		$msg = $this->context()->translate( 'mshop', 'Item with ID "%2$s" in "%1$s" not found' );
		throw new \Aimeos\MShop\Exception( sprintf( $msg, $key, $id ), 404 );
	}


	/**
	 * Returns the SQL strings for joining dependent tables.
	 *
	 * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes List of criteria attribute items
	 * @param string $prefix Search key prefix
	 * @return array List of JOIN SQL strings
	 */
	protected function getJoins( array $attributes, string $prefix ) : array
	{
		$iface = \Aimeos\Base\Criteria\Attribute\Iface::class;
		$name = $prefix . '.id';

		if( isset( $attributes[$prefix] ) && $attributes[$prefix] instanceof $iface ) {
			return $attributes[$prefix]->getInternalDeps();
		} elseif( isset( $attributes[$name] ) && $attributes[$name] instanceof $iface ) {
			return $attributes[$name]->getInternalDeps();
		} elseif( isset( $attributes['id'] ) && $attributes['id'] instanceof $iface ) {
			return $attributes['id']->getInternalDeps();
		}

		return [];
	}


	/**
	 * Returns the required SQL joins for the critera.
	 *
	 * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes List of criteria attribute items
	 * @param string $prefix Search key prefix
	 * @return array|null List of JOIN SQL strings
	 */
	protected function getRequiredJoins( array $attributes, array $keys, ?string $basekey = null ) : array
	{
		$joins = [];

		foreach( $keys as $key )
		{
			if( $key !== $basekey ) {
				$joins = array_merge( $joins, $this->getJoins( $attributes, $key ) );
			}
		}

		return array_unique( $joins );
	}


	/**
	 * Returns the name of the resource.
	 *
	 * @return string Name of the resource, e.g. "db-product"
	 */
	protected function getResourceName() : string
	{
		if( $this->resourceName === null ) {
			$this->setResourceName( 'db-' . current( $this->type() ) );
		}

		return $this->resourceName;
	}


	/**
	 * Returns the available manager types
	 *
	 * @param string $type Main manager type
	 * @param string $path Configuration path to the sub-domains
	 * @param string[] $default List of sub-domains if no others are configured
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	protected function getResourceTypeBase( string $type, string $path, array $default, bool $withsub ) : array
	{
		$list = [$type];

		if( $withsub )
		{
			foreach( $this->context()->config()->get( $path, $default ) as $domain ) {
				$list = array_merge( $list, $this->object()->getSubManager( $domain )->getResourceType( $withsub ) );
			}
		}

		return $list;
	}


	/**
	 * Returns the search attribute objects used for searching.
	 *
	 * @param array $list Associative list of search keys and the lists of search definitions
	 * @param string $path Configuration path to the sub-domains for fetching the search definitions
	 * @param string[] $default List of sub-domains if no others are configured
	 * @param bool $withsub True to include search definitions of sub-domains, false if not
	 * @return \Aimeos\Base\Criteria\Attribute\Iface[] Associative list of search keys and criteria attribute items as values
	 * @since 2014.09
	 */
	protected function getSearchAttributesBase( array $list, string $path, array $default, bool $withsub ) : array
	{
		$attr = $this->createAttributes( $list );

		if( $withsub === true )
		{
			$config = $this->context()->config();
			$domains = $config->get( $path, $default );

			foreach( $domains as $domain )
			{
				$name = $config->get( substr( $path, 0, strrpos( $path, '/' ) ) . '/' . $domain . '/name' );
				$attr += $this->object()->getSubManager( $domain, $name )->getSearchAttributes( true );
			}
		}

		return $attr;
	}


	/**
	 * Returns the item search key for the passed name
	 *
	 * @param string $name Search key name
	 * @return string Item prefix e.g. "product.lists.type.id"
	 */
	protected function getSearchKey( string $name = '' ) : string
	{
		return join( '.', $this->type() ) . ( $name ? '.' . $name : '' );
	}


	/**
	 * Returns the search results for the given SQL statement.
	 *
	 * @param \Aimeos\Base\DB\Connection\Iface $conn Database connection
	 * @param string $sql SQL statement
	 * @return \Aimeos\Base\DB\Result\Iface Search result object
	 */
	protected function getSearchResults( \Aimeos\Base\DB\Connection\Iface $conn, string $sql ) : \Aimeos\Base\DB\Result\Iface
	{
		$time = microtime( true );

		$stmt = $conn->create( $sql );
		$result = $stmt->execute();

		$level = \Aimeos\Base\Logger\Iface::DEBUG;
		$time = ( microtime( true ) - $time ) * 1000;
		$msg = 'Time: ' . $time . "ms\n"
			. 'Class: ' . get_class( $this ) . "\n"
			. str_replace( ["\t", "\n\n"], ['', "\n"], trim( (string) $stmt ) );

		if( $time > 1000.0 )
		{
			$level = \Aimeos\Base\Logger\Iface::NOTICE;
			$msg .= "\n" . ( new \Exception() )->getTraceAsString();
		}

		$this->context()->logger()->log( $msg, $level, 'core/sql' );

		return $result;
	}


	/**
	 * Returns the site coditions for the search request
	 *
	 * @param string[] $keys Sorted list of criteria keys
	 * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes Associative list of search keys and criteria attribute items as values
	 * @param int $sitelevel Site level constant from \Aimeos\MShop\Locale\Manager\Base
	 * @return \Aimeos\Base\Criteria\Expression\Iface[] List of search conditions
	 * @since 2015.01
	 */
	protected function getSiteConditions( array $keys, array $attributes, int $sitelevel ) : array
	{
		$list = [];
		$entries = array_column( $attributes, null, 'code' );

		foreach( $keys as $key )
		{
			$name = $key . '.siteid';

			if( isset( $entries[$name] ) ) {
				$list[] = $this->siteCondition( $name, $sitelevel );
			} elseif( isset( $entries['siteid'] ) ) {
				$list[] = $this->siteCondition( 'siteid', $sitelevel );
			}
		}

		return $list;
	}


	/**
	 * Returns the SQL statement for the given config path
	 *
	 * If available, the database specific SQL statement is returned, otherwise
	 * the ANSI SQL statement. The database type is determined via the resource
	 * adapter.
	 *
	 * @param string $sql Configuration path to the SQL statement
	 * @param array $replace Associative list of keys with strings to replace by their values
	 * @return array|string ANSI or database specific SQL statement
	 */
	protected function getSqlConfig( string $sql, array $replace = [] )
	{
		if( preg_match( '#^[a-z0-9\-]+(/[a-z0-9\-]+)*$#', $sql ) === 1 )
		{
			$config = $this->context()->config();
			$adapter = $config->get( 'resource/' . $this->getResourceName() . '/adapter' );

			if( ( $str = $config->get( $sql . '/' . $adapter, $config->get( $sql . '/ansi' ) ) ) === null )
			{
				$parts = explode( '/', $sql );
				$cpath = 'mshop/common/manager/' . end( $parts );
				$str = $config->get( $cpath . '/' . $adapter, $config->get( $cpath . '/ansi', $sql ) );
			}

			$sql = $str;
		}

		foreach( $replace as $key => $value ) {
			$sql = str_replace( $key, $value, $sql );
		}

		return str_replace( [':alias', ':table'], [$this->alias(), $this->table()], $sql );
	}


	/**
	 * Returns a search object singleton
	 *
	 * @return \Aimeos\Base\Criteria\Iface Search object
	 */
	protected function getSearch() : \Aimeos\Base\Criteria\Iface
	{
		if( !isset( $this->search ) ) {
			$this->search = $this->filter();
		}

		return $this->search;
	}


	/**
	 * Returns the string replacements for the SQL statements
	 *
	 * @param \Aimeos\Base\Criteria\Iface $search Search critera object
	 * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes Associative list of search keys and criteria attribute items as values
	 * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes Associative list of search keys and criteria attribute items as values for the base table
	 * @param \Aimeos\Base\Criteria\Plugin\Iface[] $plugins Associative list of search keys and criteria plugin items as values
	 * @param string[] $joins Associative list of SQL joins
	 * @param \Aimeos\Base\Criteria\Attribute\Iface[] $columns Additional columns to retrieve values from
	 * @return array Array of keys, find and replace arrays
	 */
	protected function getSQLReplacements( \Aimeos\Base\Criteria\Iface $search, array $attributes, array $attronly, array $plugins, array $joins ) : array
	{
		$types = $this->getSearchTypes( $attributes );
		$funcs = $this->getSearchFunctions( $attributes );
		$trans = $this->getSearchTranslations( $attributes );
		$trans = $this->aliasTranslations( $trans );

		if( empty( $search->getSortations() ) && ( $attribute = reset( $attronly ) ) !== false ) {
			$search = ( clone $search )->setSortations( [$search->sort( '+', $attribute->getCode() )] );
		}
		$sorts = $search->translate( $search->getSortations(), $trans, $funcs );

		$cols = $group = [];
		foreach( $attronly as $name => $entry )
		{
			if( str_contains( $name, ':' ) || empty( $entry->getInternalCode() ) ) {
				continue;
			}

			$icode = $entry->getInternalCode();

			if( !str_contains( $icode, '"' ) )
			{
				$alias = $this->alias( $entry->getCode() );
				$icode = $alias . '."' . $icode . '"';
			}

			$cols[] = $icode . ' AS "' . $entry->getCode() . '"';
			$group[] = $icode;
		}

		return [
			':columns' => join( ', ', $cols ),
			':joins' => join( "\n", array_unique( $joins ) ),
			':group' => join( ', ', array_unique( array_merge( $group, $sorts ) ) ),
			':cond' => $search->getConditionSource( $types, $trans, $plugins, $funcs ),
			':order' => $search->getSortationSource( $types, $trans, $funcs ),
			':start' => $search->getOffset(),
			':size' => $search->getLimit(),
		];
	}


	/**
	 * Returns the available sub-manager names
	 *
	 * @return array Sub-manager names, e.g. ['lists', 'property', 'type']
	 */
	protected function getSubManagers() : array
	{
		return $this->context()->config()->get( $this->getConfigKey( 'submanagers' ), [] );
	}


	/**
	 * Returns the name of the used table
	 *
	 * @return string Table name e.g. "mshop_product_property_type"
	 */
	protected function table() : string
	{
		/** @todo 2025.10 Remove any only use table() */
		if( method_exists( $this, 'getTable' ) ) {
			return $this->getTable();
		}

		return 'mshop_' . join( '_', $this->type() );
	}


	/**
	 * Checks if the item is modified
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Item object
	 * @return bool True if the item is modified, false if not
	 */
	protected function isModified( \Aimeos\MShop\Common\Item\Iface $item ) : bool
	{
		return $item->isModified();
	}


	/**
	 * Returns the newly created ID for the last record which was inserted.
	 *
	 * @param \Aimeos\Base\DB\Connection\Iface $conn Database connection used to insert the new record
	 * @param string $cfgpath Configuration path to the SQL statement for retrieving the new ID of the last inserted record
	 * @return string ID of the last record that was inserted by using the given connection
	 * @throws \Aimeos\MShop\Exception if there's no ID of the last record available
	 */
	protected function newId( \Aimeos\Base\DB\Connection\Iface $conn, string $cfgpath ) : string
	{
		$sql = $this->getSqlConfig( $cfgpath );

		$result = $conn->create( $sql )->execute();

		if( ( $row = $result->fetch( \Aimeos\Base\DB\Result\Base::FETCH_NUM ) ) === false )
		{
			$msg = $this->context()->translate( 'mshop', 'ID of last inserted database record not available' );
			throw new \Aimeos\MShop\Exception( $msg );
		}
		$result->finish();

		return $row[0];
	}


	/**
	 * Saves an attribute item to the storage.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Item object
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\Iface $item Updated item including the generated ID
	 */
	protected function saveBase( \Aimeos\MShop\Common\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Common\Item\Iface
	{
		if( !$this->isModified( $item ) ) {
			return $this->object()->saveRefs( $item );
		}

		$context = $this->context();
		$conn = $context->db( $this->getResourceName() );

		$id = $item->getId();
		$columns = array_column( $this->object()->getSaveAttributes(), null, 'internalcode' );

		if( $id === null )
		{
			/** mshop/common/manager/insert/mysql
			 * Inserts a new record into the database table
			 *
			 * @see mshop/common/manager/insert/ansi
			 */

			/** mshop/common/manager/insert/ansi
			 * Inserts a new record into the database table
			 *
			 * Items with no ID yet (i.e. the ID is NULL) will be created in
			 * the database and the newly created ID retrieved afterwards
			 * using the "newid" SQL statement.
			 *
			 * The SQL statement must be a string suitable for being used as
			 * prepared statement. It must include question marks for binding
			 * the values from the item to the statement before they are
			 * sent to the database server. The number of question marks must
			 * be the same as the number of columns listed in the INSERT
			 * statement. The order of the columns must correspond to the
			 * order in the save() method, so the correct values are
			 * bound to the columns.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for inserting records
			 * @since 2023.10
			 * @see mshop/common/manager/update/ansi
			 * @see mshop/common/manager/newid/ansi
			 * @see mshop/common/manager/delete/ansi
			 * @see mshop/common/manager/search/ansi
			 * @see mshop/common/manager/count/ansi
			 */
			$path = $this->getConfigKey( 'insert', 'mshop/common/manager/insert' );
			$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ) );
		}
		else
		{
			/** mshop/common/manager/update/mysql
			 * Updates an existing record in the database
			 *
			 * @see mshop/common/manager/update/ansi
			 */

			/** mshop/common/manager/update/ansi
			 * Updates an existing record in the database
			 *
			 * Items which already have an ID (i.e. the ID is not NULL) will
			 * be updated in the database.
			 *
			 * The SQL statement must be a string suitable for being used as
			 * prepared statement. It must include question marks for binding
			 * the values from the item to the statement before they are
			 * sent to the database server. The order of the columns must
			 * correspond to the order in the save() method, so the
			 * correct values are bound to the columns.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for updating records
			 * @since 2023.10
			 * @see mshop/common/manager/insert/ansi
			 * @see mshop/common/manager/newid/ansi
			 * @see mshop/common/manager/delete/ansi
			 * @see mshop/common/manager/search/ansi
			 * @see mshop/common/manager/count/ansi
			 */
			$path = $this->getConfigKey( 'update', 'mshop/common/manager/update' );
			$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ), false );
		}

		$idx = 1;
		$values = $item->toArray( true );
		$stmt = $this->getCachedStatement( $conn, $path, $sql );

		foreach( $columns as $entry )
		{
			$value = $values[$entry->getCode()] ?? null;
			$value = $entry->getType() === 'json' ? json_encode( $value, JSON_FORCE_OBJECT ) : $value;
			$stmt->bind( $idx++, $value, \Aimeos\Base\Criteria\SQL::type( $entry->getType() ) );
		}

		$stmt = $this->bind( $item, $stmt, $idx );

		$stmt->bind( $idx++, $context->datetime() ); // mtime
		$stmt->bind( $idx++, $context->editor() );

		if( $id !== null ) {
			$stmt->bind( $idx++, $context->locale()->getSiteId() . '%' );
			$stmt->bind( $idx++, $id, \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		} else {
			$stmt->bind( $idx++, $this->siteId( $item->getSiteId(), \Aimeos\MShop\Locale\Manager\Base::SITE_SUBTREE ) );
			$stmt->bind( $idx++, $item->getTimeCreated() ?: $context->datetime() ); // ctime
		}

		$stmt->execute()->finish();

		if( $id === null )
		{
			/** mshop/common/manager/newid/mysql
			 * Retrieves the ID generated by the database when inserting a new record
			 *
			 * @see mshop/common/manager/newid/ansi
			 */

			/** mshop/common/manager/newid/ansi
			 * Retrieves the ID generated by the database when inserting a new record
			 *
			 * As soon as a new record is inserted into the database table,
			 * the database server generates a new and unique identifier for
			 * that record. This ID can be used for retrieving, updating and
			 * deleting that specific record from the table again.
			 *
			 * For MySQL:
			 *  SELECT LAST_INSERT_ID()
			 * For PostgreSQL:
			 *  SELECT currval('seq_matt_id')
			 * For SQL Server:
			 *  SELECT SCOPE_IDENTITY()
			 * For Oracle:
			 *  SELECT "seq_matt_id".CURRVAL FROM DUAL
			 *
			 * There's no way to retrive the new ID by a SQL statements that
			 * fits for most database servers as they implement their own
			 * specific way.
			 *
			 * @param string SQL statement for retrieving the last inserted record ID
			 * @since 2023.10
			 * @see mshop/common/manager/insert/ansi
			 * @see mshop/common/manager/update/ansi
			 * @see mshop/common/manager/delete/ansi
			 * @see mshop/common/manager/search/ansi
			 * @see mshop/common/manager/count/ansi
			 */
			$id = $this->newId( $conn, $this->getConfigKey( 'newid', 'mshop/common/manager/newid' ) );
		}

		return $this->object()->saveRefs( $item->setId( $id ) );
	}


	/**
	 * Returns the search result of the statement combined with the given criteria.
	 *
	 * @param \Aimeos\Base\DB\Connection\Iface $conn Database connection
	 * @param \Aimeos\Base\Criteria\Iface $search Search criteria object
	 * @param string $cfgPathSearch Path to SQL statement in configuration for searching
	 * @param string $cfgPathCount Path to SQL statement in configuration for counting
	 * @param string[] $required Additional search keys to add conditions for even if no conditions are available
	 * @param int|null $total Contains the number of all records matching the criteria if not null
	 * @param int $sitelevel Constant from \Aimeos\MShop\Locale\Manager\Base for defining which site IDs should be used for searching
	 * @param \Aimeos\Base\Criteria\Plugin\Iface[] $plugins Associative list of search keys and criteria plugin items as values
	 * @return \Aimeos\Base\DB\Result\Iface SQL result object for accessing the found records
	 * @throws \Aimeos\MShop\Exception if no number of all matching records is available
	 */
	protected function searchItemsBase( \Aimeos\Base\DB\Connection\Iface $conn, \Aimeos\Base\Criteria\Iface $search,
		string $cfgPathSearch, string $cfgPathCount, array $required, ?int &$total = null,
		int $sitelevel = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL, array $plugins = [] ) : \Aimeos\Base\DB\Result\Iface
	{
		$attributes = $this->object()->getSearchAttributes();
		$keys = $this->getCriteriaKeyList( $search, $required );
		$joins = $this->getRequiredJoins( $attributes, $keys, array_shift( $required ) );

		if( !empty( $cond = $this->getSiteConditions( $keys, $attributes, $sitelevel ) ) ) {
			$search = ( clone $search )->add( $search->and( $cond ) );
		}

		$attronly = $this->object()->getSearchAttributes( false );
		$replace = $this->getSQLReplacements( $search, $attributes, $attronly, $plugins, $joins );

		if( $total !== null )
		{
			$sql = $this->getSqlConfig( $cfgPathCount, $replace );
			$result = $this->getSearchResults( $conn, $sql );
			$row = $result->fetch();
			$result->finish();

			if( $row === null )
			{
				$msg = $this->context()->translate( 'mshop', 'Total results value not found' );
				throw new \Aimeos\MShop\Exception( $msg );
			}

			$total = (int) $row['count'];
		}

		return $this->getSearchResults( $conn, $this->getSqlConfig( $cfgPathSearch, $replace ) );
	}


	/**
	 * Sets the name of the database resource that should be used.
	 *
	 * @param string $name Name of the resource
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	protected function setResourceName( string $name ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$config = $this->context()->config();

		if( $config->get( 'resource/' . $name ) === null ) {
			$this->resourceName = $config->get( 'resource/default', 'db' );
		} else {
			$this->resourceName = $name;
		}

		return $this;
	}


	/**
	 * Replaces the given marker with an expression
	 *
	 * @param string $column Name (including alias) of the column
	 * @param mixed $value Value used in the expression
	 * @param string $op Operator used in the expression
	 * @param int $type Type constant from \Aimeos\Base\DB\Statement\Base class
	 * @return string Created expression
	 */
	protected function toExpression( string $column, $value, string $op = '==',
		int $type = \Aimeos\Base\DB\Statement\Base::PARAM_STR ) : string
	{
		$types = ['marker' => $type];
		$translations = ['marker' => $column];
		$value = ( is_array( $value ) ? array_unique( $value ) : $value );

		return $this->getSearch()->compare( $op, 'marker', $value )->toSource( $types, $translations );
	}


	/**
	 * Transforms the application specific values to Aimeos standard values.
	 *
	 * @param array $values Associative list of key/value pairs from the storage
	 * @return array Associative list of key/value pairs with standard Aimeos values
	 */
	protected function transform( array $values ) : array
	{
		return $values;
	}


	/**
	 * Cuts the last part separated by a dot repeatedly and returns the list of resulting string.
	 *
	 * @param string[] $prefix Required base prefixes of the search keys
	 * @param string $string String containing parts separated by dots
	 * @return array List of resulting strings
	 */
	private function cutNameTail( array $prefix, string $string ) : array
	{
		$result = [];
		$noprefix = true;
		$strlen = strlen( $string );

		foreach( $prefix as $key )
		{
			$len = strlen( $key );

			if( strncmp( $string, $key, $len ) === 0 )
			{
				if( $strlen > $len && ( $pos = strrpos( $string, '.' ) ) !== false )
				{
					$result[] = $string = substr( $string, 0, $pos );
					$result = array_merge( $result, $this->cutNameTail( $prefix, $string ) );
					$noprefix = false;
				}

				break;
			}
		}

		if( $noprefix )
		{
			if( ( $pos = strrpos( $string, ':' ) ) !== false ) {
				$result[] = substr( $string, 0, $pos );
				$result[] = $string;
			} elseif( ( $pos = strrpos( $string, '.' ) ) !== false ) {
				$result[] = substr( $string, 0, $pos );
			} else {
				$result[] = $string;
			}
		}

		return $result;
	}


	/**
	 * Returns a list of unique criteria names shortend by the last element after the ''
	 *
	 * @param string[] $prefix Required base prefixes of the search keys
	 * @param \Aimeos\Base\Criteria\Expression\Iface|null $expr Criteria object
	 * @return array List of shortend criteria names
	 */
	private function getCriteriaKeys( array $prefix, ?\Aimeos\Base\Criteria\Expression\Iface $expr = null ) : array
	{
		if( $expr === null ) { return []; }

		$result = [];

		foreach( $this->getCriteriaNames( $expr ) as $item )
		{
			if( strncmp( $item, 'sort:', 5 ) === 0 ) {
				$item = substr( $item, 5 );
			}

			if( ( $pos = strpos( $item, '(' ) ) !== false ) {
				$item = substr( $item, 0, $pos );
			}

			$result = array_merge( $result, $this->cutNameTail( $prefix, $item ) );
		}

		return $result;
	}


	/**
	 * Returns a list of criteria names from a expression and its sub-expressions.
	 *
	 * @param \Aimeos\Base\Criteria\Expression\Iface Criteria object
	 * @return array List of criteria names
	 */
	private function getCriteriaNames( \Aimeos\Base\Criteria\Expression\Iface $expr ) : array
	{
		if( $expr instanceof \Aimeos\Base\Criteria\Expression\Compare\Iface ) {
			return array( $expr->getName() );
		}

		if( $expr instanceof \Aimeos\Base\Criteria\Expression\Combine\Iface )
		{
			$list = [];
			foreach( $expr->getExpressions() as $item ) {
				$list = array_merge( $list, $this->getCriteriaNames( $item ) );
			}
			return $list;
		}

		if( $expr instanceof \Aimeos\Base\Criteria\Expression\Sort\Iface ) {
			return array( $expr->getName() );
		}

		return [];
	}
}
