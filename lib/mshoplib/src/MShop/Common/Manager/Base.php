<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager;

use \Aimeos\MShop\Locale\Manager\Base as Locale;


/**
 * Provides common methods required by most of the manager classes.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class Base
	extends \Aimeos\MW\Common\Manager\Base
	implements \Aimeos\MW\Macro\Iface
{
	use \Aimeos\MShop\Common\Manager\Sub\Traits;
	use \Aimeos\MW\Macro\Traits;


	private $context;
	private $object;
	private $resourceName;
	private $stmts = [];
	private $search;


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
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this;
	}


	/**
	 * Creates a search critera object
	 *
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @param bool $site TRUE for adding site criteria to limit items by the site of related items
	 * @return \Aimeos\MW\Criteria\Iface New search criteria object
	 */
	public function filter( ?bool $default = false, bool $site = false ) : \Aimeos\MW\Criteria\Iface
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
	 * Adds or updates an item object or a list of them.
	 *
	 * @param \Aimeos\Map|\Aimeos\MShop\Common\Item\Iface[]|\Aimeos\MShop\Common\Item\Iface $items Item or list of items whose data should be saved
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\Map|\Aimeos\MShop\Common\Item\Iface Saved item or items
	 */
	public function save( $items, bool $fetch = true )
	{
		if( is_iterable( $items ) )
		{
			foreach( $items as $id => $item ) {
				$items[$id] = $this->getObject()->saveItem( $item, $fetch );
			}
			return map( $items );
		}

		return $this->getObject()->saveItem( $items, $fetch );
	}


	/**
	 * Searches for all items matching the given critera.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $filter Criteria object with conditions, sortations, etc.
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Common\Item\Iface with ids as keys
	 */
	public function search( \Aimeos\MW\Criteria\Iface $filter, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		return $this->getObject()->search( $filter, $ref, $total );
	}


	/**
	 * Starts a database transaction on the connection identified by the given name
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function begin() : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->beginTransation( $this->getResourceName() );
	}


	/**
	 * Commits the running database transaction on the connection identified by the given name
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function commit() : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->commitTransaction( $this->getResourceName() );
	}


	/**
	 * Rolls back the running database transaction on the connection identified by the given name
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function rollback() : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->rollbackTransaction( $this->getResourceName() );
	}


	/**
	 * Returns the additional column/search definitions
	 *
	 * @return array Associative list of column names as keys and items implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getSaveAttributes() : array
	{
		return [];
	}


	/**
	 * Injects the reference of the outmost object
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $object Reference to the outmost manager or decorator
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function setObject( \Aimeos\MShop\Common\Manager\Iface $object ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$this->object = $object;
		return $this;
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
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria
	 * @param array|string $keys Search key or list of keys for aggregation
	 * @param string $cfgPath Configuration key for the SQL statement
	 * @param string[] $required List of domain/sub-domain names like "catalog.index" that must be additionally joined
	 * @param string|null $value Search key for aggregating the value column
	 * @param string|null $type Type of aggregation, e.g.  "sum", "min", "max" or NULL for "count"
	 * @return \Aimeos\Map List of ID values as key and the number of counted products as value
	 * @todo 2018.01 Reorder Parameter list
	 */
	protected function aggregateBase( \Aimeos\MW\Criteria\Iface $search, $keys, string $cfgPath,
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
		$limit = $this->context->getConfig()->get( 'mshop/common/manager/aggregate/limit', 10000 );
		$keys = (array) $keys;

		if( !count( $keys ) )
		{
			$msg = $this->getContext()->translate( 'mshop', 'At least one key is required for aggregation' );
			throw new \Aimeos\MShop\Exception( $msg );
		}

		$dbname = $this->getResourceName();
		$dbm = $this->getContext()->getDatabaseManager();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$total = null;
			$cols = $map = [];
			$search = clone $search;
			$search->slice( $search->getOffset(), min( $search->getLimit(), $limit ) );

			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
			$attrList = $this->getObject()->getSearchAttributes();

			if( $value === null && ( $value = key( $attrList ) ) === null )
			{
				$msg = $this->getContext()->translate( 'mshop', 'No search keys available' );
				throw new \Aimeos\MShop\Exception( $msg );
			}

			if( ( $pos = strpos( $valkey = $value, '(' ) ) !== false ) {
				$value = substr( $value, 0, $pos );
			}

			if( !isset( $attrList[$value] ) )
			{
				$msg = $this->getContext()->translate( 'mshop', 'Unknown search key "%1$s"' );
				throw new \Aimeos\MShop\Exception( $msg );
			}

			foreach( $keys as $string )
			{
				if( !isset( $attrList[$string] ) )
				{
					$msg = $this->getContext()->translate( 'mshop', 'Unknown search key "%1$s"' );
					throw new \Aimeos\MShop\Exception( $msg );
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
			$sql = str_replace( ':type', $type ?: 'count', $sql );

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

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return map( $map );
	}


	/**
	 * Returns the newly created ID for the last record which was inserted.
	 *
	 * @param \Aimeos\MW\DB\Connection\Iface $conn Database connection used to insert the new record
	 * @param string $cfgpath Configuration path to the SQL statement for retrieving the new ID of the last inserted record
	 * @return string ID of the last record that was inserted by using the given connection
	 * @throws \Aimeos\MShop\Exception if there's no ID of the last record available
	 */
	protected function newId( \Aimeos\MW\DB\Connection\Iface $conn, string $cfgpath ) : string
	{
		$result = $conn->create( $this->getSqlConfig( $cfgpath ) )->execute();

		if( ( $row = $result->fetch( \Aimeos\MW\DB\Result\Base::FETCH_NUM ) ) === false )
		{
			$msg = $this->getContext()->translate( 'mshop', 'ID of last inserted database record not available' );
			throw new \Aimeos\MShop\Exception( $msg );
		}
		$result->finish();

		return $row[0];
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
				$stmt->bind( 1, $siteid );
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
	 * Creates the criteria attribute items from the list of entries
	 *
	 * @param array $list Associative array of code as key and array with properties as values
	 * @return \Aimeos\MW\Criteria\Attribute\Standard[] List of criteria attribute items
	 */
	protected function createAttributes( array $list ) : array
	{
		$attr = [];

		foreach( $list as $key => $fields ) {
			$attr[$key] = new \Aimeos\MW\Criteria\Attribute\Standard( $fields );
		}

		return $attr;
	}


	/**
	 * Sets the base criteria "status".
	 * (setConditions overwrites the base criteria)
	 *
	 * @param string $domain Name of the domain/sub-domain like "product" or "product.list"
	 * @param bool|null $default TRUE for status=1, NULL for status>0, FALSE for no restriction
	 * @return \Aimeos\MW\Criteria\Iface Search critery object
	 */
	protected function filterBase( string $domain, ?bool $default = true ) : \Aimeos\MW\Criteria\Iface
	{
		$filter = self::filter();

		if( $default !== false ) {
			$filter->add( [$domain . '.status' => 1], $default ? '==' : '>=' );
		}

		return $filter;
	}


	/**
	 * Returns the context object.
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface Context object
	 */
	protected function getContext() : \Aimeos\MShop\Context\Item\Iface
	{
		return $this->context;
	}


	/**
	 * Returns the outmost decorator of the decorator stack
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Outmost decorator object
	 */
	protected function getObject() : \Aimeos\MShop\Common\Manager\Iface
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
	 * @param bool $withsub True to include search definitions of sub-domains, false if not
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] Associative list of search keys and criteria attribute items as values
	 * @since 2014.09
	 */
	protected function getSearchAttributesBase( array $list, string $path, array $default, bool $withsub ) : array
	{
		$attr = $this->createAttributes( $list );

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
	protected function getSearchResults( \Aimeos\MW\DB\Connection\Iface $conn, string $sql ) : \Aimeos\MW\DB\Result\Iface
	{
		$time = microtime( true );

		$stmt = $conn->create( $sql );
		$result = $stmt->execute();

		$level = \Aimeos\MW\Logger\Base::DEBUG;
		$time = ( microtime( true ) - $time ) * 1000;
		$msg = 'Time: ' . $time . "ms\n"
			. 'Class: ' . get_class( $this ) . "\n"
			. str_replace( ["\t", "\n\n"], ['', "\n"], trim( (string) $stmt ) );

		if( $time > 1000.0 )
		{
			$level = \Aimeos\MW\Logger\Base::NOTICE;
			$msg .= "\n" . ( new \Exception() )->getTraceAsString();
		}

		$this->context->getLogger()->log( $msg, $level, 'core/sql' );

		return $result;
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
		$config = $this->getContext()->getConfig();
		$adapter = $config->get( 'resource/' . $this->getResourceName() . '/adapter' );

		return $config->get( $path . '/' . $adapter, $config->get( $path . '/ansi', $path ) );
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
		$criteria = $this->getObject()->filter( $default )->slice( 0, 1 );

		foreach( $pairs as $key => $value )
		{
			if( $value === null )
			{
				$msg = $this->getContext()->translate( 'mshop', 'Required value for "%1$s" is missing' );
				throw new \Aimeos\MShop\Exception( $msg );
			}
			$expr[] = $criteria->compare( '==', $key, $value );
		}

		$criteria->setConditions( $criteria->and( $expr ) );

		if( ( $item = $this->getObject()->search( $criteria, $ref )->first() ) ) {
			return $item;
		}

		$msg = $this->getContext()->translate( 'mshop', 'No item found for conditions: %1$s' );
		throw new \Aimeos\MShop\Exception( sprintf( $msg, print_r( $pairs, true ) ) );
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
	protected function getCachedStatement( \Aimeos\MW\DB\Connection\Iface $conn, string $cfgkey,
		string $sql = null ) : \Aimeos\MW\DB\Statement\Iface
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
		$criteria = $this->getObject()->filter( $default )->slice( 0, 1 );
		$expr = [
			$criteria->compare( '==', $key, $id ),
			$criteria->getConditions()
		];
		$criteria->setConditions( $criteria->and( $expr ) );

		if( ( $item = $this->getObject()->search( $criteria, $ref )->first() ) ) {
			return $item;
		}

		$msg = $this->getContext()->translate( 'mshop', 'Item with ID "%2$s" in "%1$s" not found' );
		throw new \Aimeos\MShop\Exception( sprintf( $msg, $key, $id ) );
	}


	/**
	 * Returns the SQL strings for joining dependent tables.
	 *
	 * @param \Aimeos\MW\Criteria\Attribute\Iface[] $attributes List of criteria attribute items
	 * @param string $prefix Search key prefix
	 * @return array List of JOIN SQL strings
	 */
	private function getJoins( array $attributes, string $prefix ) : array
	{
		$iface = \Aimeos\MW\Criteria\Attribute\Iface::class;
		$sep = $this->getKeySeparator();
		$name = $prefix . $sep . 'id';

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
	protected function getResourceName() : string
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
	protected function setResourceName( string $name ) : \Aimeos\MShop\Common\Manager\Iface
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
	 * Returns a search object singleton
	 *
	 * @return \Aimeos\MW\Criteria\Iface Search object
	 */
	protected function getSearch() : \Aimeos\MW\Criteria\Iface
	{
		if( !isset( $this->search ) ) {
			$this->search = $this->filter();
		}

		return $this->search;
	}


	/**
	 * Replaces the given marker with an expression
	 *
	 * @param string $column Name (including alias) of the column
	 * @param mixed $value Value used in the expression
	 * @param string $op Operator used in the expression
	 * @param int $type Type constant from \Aimeos\MW\DB\Statement\Base class
	 * @return string Created expression
	 */
	protected function toExpression( string $column, $value, string $op = '==',
		int $type = \Aimeos\MW\DB\Statement\Base::PARAM_STR ) : string
	{
		$types = ['marker' => $type];
		$translations = ['marker' => $column];
		$value = ( is_array( $value ) ? array_unique( $value ) : $value );

		return $this->getSearch()->compare( $op, 'marker', $value )->toSource( $types, $translations );
	}


	/**
	 * Returns the site expression for the given name
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string $name Name of the site condition
	 * @param int $sitelevel Site level constant from \Aimeos\MShop\Locale\Manager\Base
	 * @return \Aimeos\MW\Criteria\Expression\Iface Site search condition
	 * @since 2020.01
	 */
	protected function getSiteCondition( \Aimeos\MW\Criteria\Iface $search, string $name,
		int $sitelevel ) : \Aimeos\MW\Criteria\Expression\Iface
	{
		$sites = $this->context->getLocale()->getSites();
		$cond = [$search->compare( '==', $name, '' )];

		if( isset( $sites[Locale::SITE_PATH] ) && $sitelevel & Locale::SITE_PATH ) {
			$cond[] = $search->compare( '==', $name, $sites[Locale::SITE_PATH] );
		} elseif( isset( $sites[Locale::SITE_ONE] ) ) {
			$cond[] = $search->compare( '==', $name, $sites[Locale::SITE_ONE] );
		}

		if( isset( $sites[Locale::SITE_SUBTREE] ) && $sitelevel & Locale::SITE_SUBTREE ) {
			$cond[] = $search->compare( '=~', $name, $sites[Locale::SITE_SUBTREE] );
		}

		return $search->or( $cond );
	}


	/**
	 * Returns the site coditions for the search request
	 *
	 * @param string[] $keys Sorted list of criteria keys
	 * @param \Aimeos\MW\Criteria\Attribute\Iface[] $attributes Associative list of search keys and criteria attribute items as values
	 * @param int $sitelevel Site level constant from \Aimeos\MShop\Locale\Manager\Base
	 * @return \Aimeos\MW\Criteria\Expression\Iface[] List of search conditions
	 * @since 2015.01
	 */
	protected function getSiteConditions( array $keys, array $attributes, int $sitelevel ) : array
	{
		$list = [];
		$sep = $this->getKeySeparator();

		foreach( $keys as $key )
		{
			$name = $key . $sep . 'siteid';

			if( isset( $attributes[$name] ) ) {
				$list[] = $this->getSiteCondition( $this->getSearch(), $name, $sitelevel );
			}
		}

		return $list;
	}


	/**
	 * Returns the site expression for the given name
	 *
	 * @param string $name SQL name for the site condition
	 * @param int $sitelevel Site level constant from \Aimeos\MShop\Locale\Manager\Base
	 * @return string Site search condition
	 * @since 2020.01
	 */
	protected function getSiteString( string $name, int $sitelevel ) : string
	{
		$translation = ['marker' => $name];
		$types = ['marker' => \Aimeos\MW\DB\Statement\Base::PARAM_STR];

		return $this->getSiteCondition( $this->getSearch(), 'marker', $sitelevel )->toSource( $types, $translation );
	}


	/**
	 * Returns the string replacements for the SQL statements
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search critera object
	 * @param \Aimeos\MW\Criteria\Attribute\Iface[] $attributes Associative list of search keys and criteria attribute items as values
	 * @param \Aimeos\MW\Criteria\Plugin\Iface[] $plugins Associative list of search keys and criteria plugin items as values
	 * @param string[] $joins Associative list of SQL joins
	 * @param \Aimeos\MW\Criteria\Attribute\Iface[] $columns Additional columns to retrieve values from
	 * @return array Array of keys, find and replace arrays
	 */
	protected function getSQLReplacements( \Aimeos\MW\Criteria\Iface $search, array $attributes, array $plugins,
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
	 * Returns the search result of the statement combined with the given criteria.
	 *
	 * @param \Aimeos\MW\DB\Connection\Iface $conn Database connection
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string $cfgPathSearch Path to SQL statement in configuration for searching
	 * @param string $cfgPathCount Path to SQL statement in configuration for counting
	 * @param string[] $required Additional search keys to add conditions for even if no conditions are available
	 * @param int|null $total Contains the number of all records matching the criteria if not null
	 * @param int $sitelevel Constant from \Aimeos\MShop\Locale\Manager\Base for defining which site IDs should be used for searching
	 * @param \Aimeos\MW\Criteria\Plugin\Iface[] $plugins Associative list of search keys and criteria plugin items as values
	 * @return \Aimeos\MW\DB\Result\Iface SQL result object for accessing the found records
	 * @throws \Aimeos\MShop\Exception if no number of all matching records is available
	 */
	protected function searchItemsBase( \Aimeos\MW\DB\Connection\Iface $conn, \Aimeos\MW\Criteria\Iface $search,
		string $cfgPathSearch, string $cfgPathCount, array $required, int &$total = null,
		int $sitelevel = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL, array $plugins = [] ) : \Aimeos\MW\DB\Result\Iface
	{
		$joins = [];
		$conditions = $search->getConditions();
		$columns = $this->getObject()->getSaveAttributes();
		$attributes = $this->getObject()->getSearchAttributes();
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
				$msg = $this->getContext()->translate( 'mshop', 'Total results value not found' );
				throw new \Aimeos\MShop\Exception( $msg );
			}

			$total = (int) $row['count'];
		}

		return $this->getSearchResults( $conn, str_replace( $find, $replace, $this->getSqlConfig( $cfgPathSearch ) ) );
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
		if( is_map( $items ) ) { $items = $items->toArray(); }
		if( !is_array( $items ) ) { $items = [$items]; }
		if( empty( $items ) ) { return $this; }

		$context = $this->getContext();
		$dbname = $this->getResourceName();

		$search = $this->getObject()->filter();
		$search->setConditions( $search->compare( '==', $name, $items ) );

		$types = array( $name => \Aimeos\MW\DB\Statement\Base::PARAM_STR );
		$translations = array( $name => '"' . $name . '"' );

		$cond = $search->getConditionSource( $types, $translations );
		$sql = str_replace( ':cond', $cond, $this->getSqlConfig( $cfgpath ) );

		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$stmt = $conn->create( $sql );

			if( $siteid ) {
				$stmt->bind( 1, $context->getLocale()->getSiteId() );
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
	protected function beginTransation( string $dbname = 'db' ) : \Aimeos\MShop\Common\Manager\Iface
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
	protected function commitTransaction( string $dbname = 'db' ) : \Aimeos\MShop\Common\Manager\Iface
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
	protected function rollbackTransaction( string $dbname = 'db' ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$dbm = $this->context->getDatabaseManager();

		$conn = $dbm->acquire( $dbname );
		$conn->rollback();
		$dbm->release( $conn, $dbname );

		return $this;
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
}
