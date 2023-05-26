<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
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


	private \Aimeos\MShop\ContextIface $context;
	private ?\Aimeos\Base\Criteria\Iface $search;
	private array $stmts = [];


	/**
	 * Initialization of class.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 */
	public function __construct( \Aimeos\MShop\ContextIface $context )
	{
		$this->context = $context;
	}


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
			foreach( $columns as $name ) {
				$names .= '"' . $name . '", '; $values .= '?, ';
			}
		}
		else
		{
			foreach( $columns as $name ) {
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
		array $required = [], string $value = null, string $type = null ) : \Aimeos\Map
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
		$limit = $this->context->config()->get( 'mshop/common/manager/aggregate/limit', 10000 );
		$keys = (array) $keys;

		if( !count( $keys ) )
		{
			$msg = $this->context()->translate( 'mshop', 'At least one key is required for aggregation' );
			throw new \Aimeos\MShop\Exception( $msg );
		}

		$conn = $this->context->db( $this->getResourceName() );

		$total = null;
		$cols = $map = [];
		$search = clone $search;
		$search->slice( $search->getOffset(), min( $search->getLimit(), $limit ) );

		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
		$attrList = array_filter( $this->object()->getSearchAttributes(), function( $item ) {
			return $item->isPublic() || strncmp( $item->getCode(), 'agg:', 4 ) === 0;
		} );

		if( $value === null && ( $value = key( $attrList ) ) === null )
		{
			$msg = $this->context()->translate( 'mshop', 'No search keys available' );
			throw new \Aimeos\MShop\Exception( $msg );
		}

		if( ( $pos = strpos( $valkey = $value, '(' ) ) !== false ) {
			$value = substr( $value, 0, $pos );
		}

		if( !isset( $attrList[$value] ) )
		{
			$msg = $this->context()->translate( 'mshop', 'Unknown search key "%1$s"' );
			throw new \Aimeos\MShop\Exception( sprintf( $msg, $value ) );
		}

		foreach( $keys as $string )
		{
			if( !isset( $attrList[$string] ) )
			{
				$msg = $this->context()->translate( 'mshop', 'Unknown search key "%1$s"' );
				throw new \Aimeos\MShop\Exception( sprintf( $msg, $string ) );
			}

			$cols[] = $attrList[$string]->getInternalCode();
			$acols[] = $attrList[$string]->getInternalCode() . ' AS "' . $string . '"';

			/** @todo Required to get the joins, but there should be a better way */
			$search->add( [$string => null], '!=' );
		}
		$search->add( [$valkey => null], '!=' );

		$sql = $this->getSqlConfig( $cfgPath );
		$sql = str_replace( ':cols', join( ', ', $cols ), $sql );
		$sql = str_replace( ':acols', join( ', ', $acols ), $sql );
		$sql = str_replace( ':keys', '"' . join( '", "', $keys ) . '"', $sql );
		$sql = str_replace( ':val', $attrList[$value]->getInternalCode(), $sql );
		$sql = str_replace( ':type', in_array( $type, ['avg', 'count', 'max', 'min', 'sum'] ) ? $type : 'count', $sql );

		$results = $this->searchItemsBase( $conn, $search, $sql, '', $required, $total, $level );

		while( ( $row = $results->fetch() ) !== null )
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

		$conn = $this->context->db( $this->getResourceName() );

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
	 * Returns the context object.
	 *
	 * @return \Aimeos\MShop\ContextIface Context object
	 */
	protected function context() : \Aimeos\MShop\ContextIface
	{
		return $this->context;
	}


	/**
	 * Creates the criteria attribute items from the list of entries
	 *
	 * @param array $list Associative array of code as key and array with properties as values
	 * @return \Aimeos\Base\Criteria\Attribute\Standard[] List of criteria attribute items
	 */
	protected function createAttributes( array $list ) : array
	{
		$attr = [];

		foreach( $list as $key => $fields ) {
			$attr[$key] = new \Aimeos\Base\Criteria\Attribute\Standard( $fields );
		}

		return $attr;
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
			$domains = $this->context->config()->get( $path, $default );

			foreach( $domains as $domain ) {
				$attr += $this->object()->getSubManager( $domain )->getSearchAttributes( true );
			}
		}

		return $attr;
	}


	/**
	 * Returns the attribute helper functions for searching defined by the manager.
	 *
	 * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes List of search attribute items
	 * @return array Associative array of attribute code and helper function
	 */
	protected function getSearchFunctions( array $attributes ) : array
	{
		$list = [];
		$iface = \Aimeos\Base\Criteria\Attribute\Iface::class;

		foreach( $attributes as $key => $item )
		{
			if( $item instanceof $iface ) {
				$list[$item->getCode()] = $item->getFunction();
			} else if( isset( $item['code'] ) ) {
				$list[$item['code']] = $item['function'] ?? null;
			} else {
				throw new \Aimeos\MShop\Exception( sprintf( 'Invalid attribute at position "%1$d"', $key ) );
			}
		}

		return $list;
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

		$this->context->logger()->log( $msg, $level, 'core/sql' );

		return $result;
	}


	/**
	 * Returns the attribute translations for searching defined by the manager.
	 *
	 * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes List of search attribute items
	 * @return array Associative array of attribute code and internal attribute code
	 */
	protected function getSearchTranslations( array $attributes ) : array
	{
		$translations = [];
		$iface = \Aimeos\Base\Criteria\Attribute\Iface::class;

		foreach( $attributes as $key => $item )
		{
			if( $item instanceof $iface ) {
				$translations[$item->getCode()] = $item->getInternalCode();
			} else if( isset( $item['code'] ) ) {
				$translations[$item['code']] = $item['internalcode'];
			} else {
				throw new \Aimeos\MShop\Exception( sprintf( 'Invalid attribute at position "%1$d"', $key ) );
			}
		}

		return $translations;
	}


	/**
	 * Returns the attribute types for searching defined by the manager.
	 *
	 * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes List of search attribute items
	 * @return array Associative array of attribute code and internal attribute type
	 */
	protected function getSearchTypes( array $attributes ) : array
	{
		$types = [];
		$iface = \Aimeos\Base\Criteria\Attribute\Iface::class;

		foreach( $attributes as $key => $item )
		{
			if( $item instanceof $iface ) {
				$types[$item->getCode()] = $item->getInternalType();
			} else if( isset( $item['code'] ) ) {
				$types[$item['code']] = $item['internaltype'];
			} else {
				throw new \Aimeos\MShop\Exception( sprintf( 'Invalid attribute at position "%1$d"', $key ) );
			}
		}

		return $types;
	}


	/**
	 * Returns the SQL statement for the given config path
	 *
	 * If available, the database specific SQL statement is returned, otherwise
	 * the ANSI SQL statement. The database type is determined via the resource
	 * adapter.
	 *
	 * @param string $path Configuration path to the SQL statement
	 * @return array|string ANSI or database specific SQL statement
	 */
	protected function getSqlConfig( string $path )
	{
		$config = $this->context()->config();
		$adapter = $config->get( 'resource/' . $this->getResourceName() . '/adapter' );

		return $config->get( $path . '/' . $adapter, $config->get( $path . '/ansi', $path ) );
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
		$filter = self::filter();

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
		string $sql = null ) : \Aimeos\Base\DB\Statement\Iface
	{
		if( !isset( $this->stmts['stmt'][$cfgkey] )
			|| !isset( $this->stmts['conn'][$cfgkey] )
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
	private function getJoins( array $attributes, string $prefix ) : array
	{
		$iface = \Aimeos\Base\Criteria\Attribute\Iface::class;
		$name = $prefix . '.id';

		if( isset( $attributes[$prefix] ) && $attributes[$prefix] instanceof $iface ) {
			return $attributes[$prefix]->getInternalDeps();
		}
		elseif( isset( $attributes[$name] ) && $attributes[$name] instanceof $iface ) {
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
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	protected function getResourceTypeBase( string $type, string $path, array $default, bool $withsub ) : array
	{
		$list = [$type];

		if( $withsub )
		{
			foreach( $this->context->config()->get( $path, $default ) as $domain ) {
				$list = array_merge( $list, $this->object()->getSubManager( $domain )->getResourceType( $withsub ) );
			}
		}

		return $list;
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

		foreach( $keys as $key )
		{
			$name = $key . '.siteid';

			if( isset( $attributes[$name] ) ) {
				$list[] = $this->siteCondition( $name, $sitelevel );
			}
		}

		return $list;
	}


	/**
	 * Returns the string replacements for the SQL statements
	 *
	 * @param \Aimeos\Base\Criteria\Iface $search Search critera object
	 * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes Associative list of search keys and criteria attribute items as values
	 * @param \Aimeos\Base\Criteria\Plugin\Iface[] $plugins Associative list of search keys and criteria plugin items as values
	 * @param string[] $joins Associative list of SQL joins
	 * @param \Aimeos\Base\Criteria\Attribute\Iface[] $columns Additional columns to retrieve values from
	 * @return array Array of keys, find and replace arrays
	 */
	protected function getSQLReplacements( \Aimeos\Base\Criteria\Iface $search, array $attributes, array $plugins,
		array $joins, array $columns = [] ) : array
	{
		$types = $this->getSearchTypes( $attributes );
		$funcs = $this->getSearchFunctions( $attributes );
		$translations = $this->getSearchTranslations( $attributes );

		$colstring = '';
		foreach( $columns as $name => $entry ) {
			$colstring .= $entry->getInternalCode() . ', ';
		}

		$find = array( ':columns', ':joins', ':cond', ':start', ':size' );
		$replace = array(
			$colstring,
			implode( "\n", array_unique( $joins ) ),
			$search->getConditionSource( $types, $translations, $plugins, $funcs ),
			$search->getOffset(),
			$search->getLimit(),
		);

		if( empty( $search->getSortations() ) && ( $attribute = reset( $attributes ) ) !== false ) {
			$search = ( clone $search )->setSortations( [$search->sort( '+', $attribute->getCode() )] );
		}

		$find[] = ':order';
		$replace[] = $search->getSortationSource( $types, $translations, $funcs );

		$find[] = ':group';
		$replace[] = implode( ', ', $search->translate( $search->getSortations(), $translations, $funcs ) ) . ', ';

		return [$find, $replace];
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
		$result = $conn->create( $this->getSqlConfig( $cfgpath ) )->execute();

		if( ( $row = $result->fetch( \Aimeos\Base\DB\Result\Base::FETCH_NUM ) ) === false )
		{
			$msg = $this->context()->translate( 'mshop', 'ID of last inserted database record not available' );
			throw new \Aimeos\MShop\Exception( $msg );
		}
		$result->finish();

		return $row[0];
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
		string $cfgPathSearch, string $cfgPathCount, array $required, int &$total = null,
		int $sitelevel = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL, array $plugins = [] ) : \Aimeos\Base\DB\Result\Iface
	{
		$joins = [];
		$conditions = $search->getConditions();
		$columns = $this->object()->getSaveAttributes();
		$attributes = $this->object()->getSearchAttributes();
		$keys = $this->getCriteriaKeyList( $search, $required );

		$basekey = array_shift( $required );

		foreach( $keys as $key )
		{
			if( $key !== $basekey ) {
				$joins = array_merge( $joins, $this->getJoins( $attributes, $key ) );
			}
		}

		$joins = array_unique( $joins );
		$cond = $this->getSiteConditions( $keys, $attributes, $sitelevel );

		if( $conditions !== null ) {
			$cond[] = $conditions;
		}

		$search = clone $search;
		$search->setConditions( $search->and( $cond ) );

		list( $find, $replace ) = $this->getSQLReplacements( $search, $attributes, $plugins, $joins, $columns );

		if( $total !== null )
		{
			$sql = str_replace( $find, $replace, $this->getSqlConfig( $cfgPathCount ) );
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

		return $this->getSearchResults( $conn, str_replace( $find, $replace, $this->getSqlConfig( $cfgPathSearch ) ) );
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
	private function getCriteriaKeys( array $prefix, \Aimeos\Base\Criteria\Expression\Iface $expr = null ) : array
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
