<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
abstract class Base
	extends \Aimeos\MW\Common\Manager\Base
	implements \Aimeos\MShop\Common\Manager\Iface
{
	private $context;
	private $resourceName;
	private $stmts = [];
	private $subManagers = [];


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
	 * @param array $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
	}


	/**
	 * Creates a search object.
	 *
	 * @param boolean $default Add default criteria; Optional
	 * @return \Aimeos\MW\Criteria\Iface
	 */
	public function createSearch( $default = false )
	{
		$dbm = $this->context->getDatabaseManager();
		$db = $this->getResourceName();

		$conn = $dbm->acquire( $db );
		$search = new \Aimeos\MW\Criteria\SQL( $conn );
		$dbm->release( $conn, $db );

		return $search;
	}


	/**
	 * Deletes an item from storage.
	 *
	 * @param integer $itemId Unique ID of the item in the storage
	 */
	public function deleteItem( $itemId )
	{
		$this->deleteItems( array( $itemId ) );
	}


	/**
	 * Starts a database transaction on the connection identified by the given name.
	 */
	public function begin()
	{
		$this->beginTransation( $this->getResourceName() );
	}


	/**
	 * Commits the running database transaction on the connection identified by the given name.
	 */
	public function commit()
	{
		$this->commitTransaction( $this->getResourceName() );
	}


	/**
	 * Rolls back the running database transaction on the connection identified by the given name.
	 */
	public function rollback()
	{
		$this->rollbackTransaction( $this->getResourceName() );
	}


	/**
	 * Counts the number products that are available for the values of the given key.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria
	 * @param string $key Search key (usually the ID) to aggregate products for
	 * @param string $cfgPath Configuration key for the SQL statement
	 * @param string[] $required List of domain/sub-domain names like "catalog.index" that must be additionally joined
	 * @return array List of ID values as key and the number of counted products as value
	 */
	protected function aggregateBase( \Aimeos\MW\Criteria\Iface $search, $key, $cfgPath, $required = [] )
	{
		$list = [];
		$context = $this->getContext();

		$dbname = $this->getResourceName();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$search = clone $search;
			$attrList = $this->getSearchAttributes();

			if( !isset( $attrList[$key] ) ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Unknown search key "%1$s"', $key ) );
			}

			/** @todo Required to get the joins for the index managers, but there should be a better way */
			$expr = array( $search->getConditions(), $search->compare( '!=', $key, null ) );
			$search->setConditions( $search->combine( '&&', $expr ) );

			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
			$total = null;

			$sql = str_replace( ':key', $attrList[$key]->getInternalCode(), $this->getSqlConfig( $cfgPath ) );
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
	 * @param array $siteids List of IDs for sites whose entries should be deleted
	 * @param string $cfgpath Configuration key to the cleanup statement
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
		$dbm = $this->context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		$object = new \Aimeos\MW\Criteria\SQL( $conn );
		$object->setConditions( $object->compare( '==', $domain . '.status', 1 ) );

		$dbm->release( $conn, $dbname );

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
	 * Returns the search attribute objects used for searching.
	 *
	 * @param array $list Associative list of search keys and the lists of search definitions
	 * @param string $path Configuration path to the sub-domains for fetching the search definitions
	 * @param array $default List of sub-domains if no others are configured
	 * @param boolean $withsub True to include search definitions of sub-domains, false if not
	 * @return array Associative list of search keys and objects implementing the \Aimeos\MW\Criteria\Attribute\Iface
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
				$attr += $this->getSubManager( $domain )->getSearchAttributes( true );
			}
		}

		return $attr;
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
	 * Returns a new manager the given extension name.
	 *
	 * @param string $domain Name of the domain (product, text, media, etc.)
	 * @param string $manager Name of the sub manager type in lower case (can contain a path like base/product)
	 * @param string|null $name Name of the implementation, will be from configuration (or Standard) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions
	 */
	protected function getSubManagerBase( $domain, $manager, $name )
	{
		$domain = strtolower( $domain );
		$manager = strtolower( $manager );
		$key = $domain . $manager . $name;

		if( !isset( $this->subManagers[$key] ) )
		{
			if( empty( $domain ) || ctype_alnum( $domain ) === false ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Invalid characters in domain name "%1$s"', $domain ) );
			}

			if( preg_match( '/^[a-z0-9\/]+$/', $manager ) !== 1 ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Invalid characters in manager name "%1$s"', $manager ) );
			}

			if( $name === null ) {
				$path = 'mshop/' . $domain . '/manager/' . $manager . '/name';
				$name = $this->context->getConfig()->get( $path, 'Standard' );
			}

			if( empty( $name ) || ctype_alnum( $name ) === false ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Invalid characters in manager name "%1$s"', $name ) );
			}

			$domainname = ucfirst( $domain );
			$subnames = $this->createSubNames( $manager );

			$classname = '\\Aimeos\\MShop\\' . $domainname . '\\Manager\\' . $subnames . '\\' . $name;
			$interface = '\\Aimeos\\MShop\\' . $domainname . '\\Manager\\' . $subnames . '\\Iface';

			if( class_exists( $classname ) === false ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
			}

			$subManager = new $classname( $this->context );

			if( ( $subManager instanceof $interface ) === false ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $interface ) );
			}

			$this->subManagers[$key] = $this->addManagerDecorators( $subManager, $manager, $domain );
		}

		return $this->subManagers[$key];
	}


	/**
	 * Adds the decorators to the manager object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context instance with necessary objects
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager object
	 * @param array $decorators List of decorator names that should be wrapped around the manager object
	 * @param string $classprefix Decorator class prefix, e.g. "\Aimeos\MShop\Product\Manager\Decorator\"
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object
	 */
	protected function addDecorators( \Aimeos\MShop\Context\Item\Iface $context,
		\Aimeos\MShop\Common\Manager\Iface $manager, array $decorators, $classprefix )
	{
		$iface = '\\Aimeos\\MShop\\Common\\Manager\\Decorator\\Iface';

		foreach( $decorators as $name )
		{
			if( ctype_alnum( $name ) === false ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Invalid characters in class name "%1$s"', $name ) );
			}

			$classname = $classprefix . $name;

			if( class_exists( $classname ) === false ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
			}

			$manager = new $classname( $manager, $context );

			if( !( $manager instanceof $iface ) ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $iface ) );
			}
		}

		return $manager;
	}


	/**
	 * Adds the configured decorators to the given manager object.
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager object
	 * @param string $managerpath Manager sub-names separated by slashes, e.g. "list/type"
	 * @param string $domain Domain name in lower case, e.g. "product"
	 */
	protected function addManagerDecorators( \Aimeos\MShop\Common\Manager\Iface $manager, $managerpath, $domain )
	{
		$config = $this->context->getConfig();

		$decorators = $config->get( 'mshop/common/manager/decorators/default', [] );
		$excludes = $config->get( 'mshop/' . $domain . '/manager/' . $managerpath . '/decorators/excludes', [] );

		foreach( $decorators as $key => $name )
		{
			if( in_array( $name, $excludes ) ) {
				unset( $decorators[$key] );
			}
		}

		$classprefix = '\\Aimeos\\MShop\\Common\\Manager\\Decorator\\';
		$manager = $this->addDecorators( $this->context, $manager, $decorators, $classprefix );

		$classprefix = '\\Aimeos\\MShop\\Common\\Manager\\Decorator\\';
		$decorators = $config->get( 'mshop/' . $domain . '/manager/' . $managerpath . '/decorators/global', [] );
		$manager = $this->addDecorators( $this->context, $manager, $decorators, $classprefix );

		$subpath = $this->createSubNames( $managerpath );
		$classprefix = 'MShop_' . ucfirst( $domain ) . '_Manager_' . $subpath . '_Decorator_';
		$decorators = $config->get( 'mshop/' . $domain . '/manager/' . $managerpath . '/decorators/local', [] );

		return $this->addDecorators( $this->context, $manager, $decorators, $classprefix );
	}


	/**
	 * Transforms the manager path to the appropriate class names.
	 *
	 * @param string $manager Path of manager names, e.g. "list/type"
	 * @return string Class names, e.g. "List_Type"
	 */
	protected function createSubNames( $manager )
	{
		$names = explode( '/', $manager );

		foreach( $names as $key => $subname )
		{
			if( empty( $subname ) || ctype_alnum( $subname ) === false ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Invalid characters in manager name "%1$s"', $manager ) );
			}

			$names[$key] = ucfirst( $subname );
		}

		return implode( '\\', $names );
	}


	/**
	 * Returns the item for the given search key/value pairs.
	 *
	 * @param array $pairs Search key/value pairs for the item
	 * @param string[] $ref List of domains whose items should be fetched too
	 * @return \Aimeos\MShop\Common\Item\Iface Requested item
	 * @throws \Aimeos\MShop\Exception if no item with the given ID found
	 */
	protected function findItemBase( array $pairs, array $ref = [] )
	{
		$expr = [];
		$criteria = $this->createSearch();

		foreach( $pairs as $key => $value )
		{
			if( $value === null ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Required value for "%1$s" is missing', $key ) );
			}
			$expr[] = $criteria->compare( '==', $key, $value );
		}

		$criteria->setConditions( $criteria->combine( '&&', $expr ) );
		$items = $this->searchItems( $criteria, $ref );

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
	 * @param integer $id Unique ID to search for
	 * @param string[] $ref List of domains whose items should be fetched too
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MShop\Common\Item\Iface Requested item
	 * @throws \Aimeos\MShop\Exception if no item with the given ID found
	 */
	protected function getItemBase( $key, $id, array $ref = [], $default = false )
	{
		$criteria = $this->createSearch( $default );
		$expr = [
			$criteria->compare( '==', $key, $id ),
			$criteria->getConditions()
		];
		$criteria->setConditions( $criteria->combine( '&&', $expr ) );
		$items = $this->searchItems( $criteria, $ref );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Item with ID "%2$s" in "%1$s" not found', $key, $id ) );
		}

		return $item;
	}


	/**
	 * Returns the SQL strings for joining dependent tables.
	 *
	 * @param array $attributes List of search attributes
	 * @param string $prefix Search key prefix
	 * @return array List of JOIN SQL strings
	 */
	private function getJoins( array $attributes, $prefix )
	{
		$iface = '\\Aimeos\\MW\\Criteria\\Attribute\\Iface';
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
	 * @param string Main manager type
	 * @param string $path Configuration path to the sub-domains
	 * @param array $default List of sub-domains if no others are configured
	 * @param boolean $withsub Return also the resource type of sub-managers if true
	 * @return array Type of the manager and submanagers, subtypes are separated by slashes
	 */
	protected function getResourceTypeBase( $type, $path, array $default, $withsub )
	{
		$list = array( $type );

		foreach( $this->context->getConfig()->get( $path, $default ) as $domain ) {
			$list = array_merge( $list, $this->getSubManager( $domain )->getResourceType( $withsub ) );
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
	 */
	protected function setResourceName( $name )
	{
		$config = $this->context->getConfig();

		if( $config->get( 'resource/' . $name ) === null ) {
			$this->resourceName = $config->get( 'resource/default', 'db' );
		} else {
			$this->resourceName = $name;
		}
	}


	/**
	 * Replaces ":site" marker in a search config item array.
	 *
	 * @param array &$searchAttr Single search config definition including the "internalcode" key
	 * @param string $column Name (including alias) of the column containing the site ID in the storage
	 * @param integer|array $value Site ID or list of site IDs
	 * @param string $marker Marker to replace
	 */
	protected function replaceSiteMarker( &$searchAttr, $column, $value, $marker = ':site' )
	{
		$types = array( 'siteid' => \Aimeos\MW\DB\Statement\Base::PARAM_INT );
		$translations = array( 'siteid' => $column );
		$conn = new \Aimeos\MW\DB\Connection\None();

		$search = new \Aimeos\MW\Criteria\SQL( $conn );

		$expr = $search->compare( '==', 'siteid', $value );
		$string = $expr->toString( $types, $translations );

		$searchAttr['internalcode'] = str_replace( $marker, $string, $searchAttr['internalcode'] );
	}


	/**
	 * Returns the site coditions for the search request
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $keys Sorted list of criteria keys
	 * @param array $attributes Associative list of search keys and objects implementing the \Aimeos\MW\Criteria\Attribute\Iface
	 * @param string[] $siteIds List of site IDs that should be used for searching
	 * @return array List of search conditions implementing \Aimeos\MW\Criteria\Expression\Iface
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
	 * Returns the search result of the statement combined with the given criteria.
	 *
	 * @param \Aimeos\MW\DB\Connection\Iface $conn Database connection
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string $cfgPathSearch Path to SQL statement in configuration for searching
	 * @param string $cfgPathCount Path to SQL statement in configuration for counting
	 * @param string[] $required Additional search keys to add conditions for even if no conditions are available
	 * @param integer|null $total Contains the number of all records matching the criteria if not null
	 * @param integer $sitelevel Constant from \Aimeos\MShop\Locale\Manager\Base for defining which site IDs should be used for searching
	 * @param array $plugins Associative list of item keys and plugin objects implementing \Aimeos\MW\Criteria\Plugin\Iface
	 * @return \Aimeos\MW\DB\Result\Iface SQL result object for accessing the found records
	 * @throws \Aimeos\MShop\Exception if no number of all matching records is available
	 */
	protected function searchItemsBase( \Aimeos\MW\DB\Connection\Iface $conn, \Aimeos\MW\Criteria\Iface $search,
		$cfgPathSearch, $cfgPathCount, array $required, &$total = null,
		$sitelevel = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL, array $plugins = [] )
	{
		$joins = [];
		$conditions = $search->getConditions();
		$attributes = $this->getSearchAttributes();
		$siteIds = $this->getSiteIds( $sitelevel );
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


		$types = $this->getSearchTypes( $attributes );
		$translations = $this->getSearchTranslations( $attributes );

		$find = array( ':joins', ':cond', ':start', ':size' );
		$replace = array(
			implode( "\n", array_unique( $joins ) ),
			$search->getConditionString( $types, $translations, $plugins ),
			$search->getSliceStart(),
			$search->getSliceSize(),
		);

		if( count( $search->getSortations() ) > 0 )
		{
			$keys[] = 'orderby';
			$find[] = ':order';
			$replace[] = $search->getSortationString( $types, $translations );

			$keys[] = 'columns';
			$find[] = ':columns';
			$replace[] = $search->getColumnString( $search->getSortations(), $translations );
		}


		if( $total !== null )
		{
			$sql = new \Aimeos\MW\Template\SQL( $this->getSqlConfig( $cfgPathCount ) );
			$sql->replace( $find, $replace )->enable( $keys );

			$time = microtime( true );
			$stmt = $conn->create( $sql->str() );
			$results = $stmt->execute();
			$row = $results->fetch();
			$results->finish();
			$this->context->getLogger()->log( __METHOD__ . '(' . ( ( microtime( true ) - $time ) * 1000 ) . 'ms): SQL statement: ' . $stmt, \Aimeos\MW\Logger\Base::DEBUG );

			if( $row === false ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Total results value not found' ) );
			}

			$total = (int) $row['count'];
		}


		$sql = new \Aimeos\MW\Template\SQL( $this->getSqlConfig( $cfgPathSearch ) );
		$sql->replace( $find, $replace )->enable( $keys );

		$time = microtime( true );
		$stmt = $conn->create( $sql->str() );
		$results = $stmt->execute();
		$this->context->getLogger()->log( __METHOD__ . '(' . ( ( microtime( true ) - $time ) * 1000 ) . 'ms): SQL statement: ' . $stmt, \Aimeos\MW\Logger\Base::DEBUG );

		return $results;
	}


	/**
	 * Deletes items specified by its IDs.
	 *
	 * @param array $ids List of IDs
	 * @param string $cfgpath Configuration path to the SQL statement
	 * @param boolean $siteidcheck If siteid should be used in the statement
	 * @param string $name Name of the ID column
	 */
	protected function deleteItemsBase( array $ids, $cfgpath, $siteidcheck = true, $name = 'id' )
	{
		if( empty( $ids ) ) { return; }

		$context = $this->getContext();
		$dbname = $this->getResourceName();

		$search = $this->createSearch();
		$search->setConditions( $search->compare( '==', $name, $ids ) );

		$types = array( $name => \Aimeos\MW\DB\Statement\Base::PARAM_STR );
		$translations = array( $name => '"' . $name . '"' );

		$cond = $search->getConditionString( $types, $translations );
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
	}


	/**
	 * Starts a database transaction on the connection identified by the given name.
	 *
	 * @param string $dbname Name of the database settings in the resource configuration
	 */
	protected function beginTransation( $dbname = 'db' )
	{
		$dbm = $this->context->getDatabaseManager();

		$conn = $dbm->acquire( $dbname );
		$conn->begin();
		$dbm->release( $conn, $dbname );
	}


	/**
	 * Commits the running database transaction on the connection identified by the given name.
	 *
	 * @param string $dbname Name of the database settings in the resource configuration
	 */
	protected function commitTransaction( $dbname = 'db' )
	{
		$dbm = $this->context->getDatabaseManager();

		$conn = $dbm->acquire( $dbname );
		$conn->commit();
		$dbm->release( $conn, $dbname );
	}


	/**
	 * Rolls back the running database transaction on the connection identified by the given name.
	 *
	 * @param string $dbname Name of the database settings in the resource configuration
	 */
	protected function rollbackTransaction( $dbname = 'db' )
	{
		$dbm = $this->context->getDatabaseManager();

		$conn = $dbm->acquire( $dbname );
		$conn->rollback();
		$dbm->release( $conn, $dbname );
	}
}
