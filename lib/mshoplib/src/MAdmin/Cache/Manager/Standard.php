<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2020
 * @package MAdmin
 * @subpackage Cache
 */


namespace Aimeos\MAdmin\Cache\Manager;


/**
 * Default cache manager implementation.
 *
 * @package MAdmin
 * @subpackage Cache
 */
class Standard
	extends \Aimeos\MAdmin\Common\Manager\Base
	implements \Aimeos\MAdmin\Cache\Manager\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private $object;

	private $searchConfig = array(
		'cache.id' => array(
			'code' => 'cache.id',
			'internalcode' => '"id"',
			'label' => 'ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'cache.value' => array(
			'code' => 'cache.value',
			'internalcode' => '"value"',
			'label' => 'Cached value',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'cache.expire' => array(
			'code' => 'cache.expire',
			'internalcode' => '"expire"',
			'label' => 'Expiration date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'cache.tag.name' => array(
			'code' => 'cache.tag.name',
			'internalcode' => '"tname"',
			'label' => 'Tag name',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
	);


	/**
	 * Creates the cache manager that will use the given context object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );
		$this->setResourceName( 'db-cache' );
	}


	/**
	 * Returns the cache object
	 *
	 * @return \Aimeos\MW\Cache\Iface Cache object
	 */
	public function getCache() : \Aimeos\MW\Cache\Iface
	{
		if( !isset( $this->object ) )
		{
			/** madmin/cache/manager/standard/deletebytag/mysql
			 * Deletes the items from the database matched by the given tags
			 *
			 * @see madmin/cache/manager/standard/deletebytag/ansi
			 */

			/** madmin/cache/manager/standard/deletebytag/ansi
			 * Deletes the items from the database matched by the given tags
			 *
			 * Removes the records specified by the given tags from the cache database.
			 * The records must be from the site that is configured via the
			 * context item.
			 *
			 * The ":cond" placeholder is replaced by the name of the tag column and
			 * the given tag or list of tags.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for deleting items by tags
			 * @since 2014.03
			 * @category Developer
			 * @see madmin/cache/manager/standard/delete/ansi
			 * @see madmin/cache/manager/standard/get/ansi
			 * @see madmin/cache/manager/standard/set/ansi
			 * @see madmin/cache/manager/standard/settag/ansi
			 * @see madmin/cache/manager/standard/search/ansi
			 * @see madmin/cache/manager/standard/count/ansi
			 */

			/** madmin/cache/manager/standard/get/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see madmin/cache/manager/standard/get/ansi
			 */

			/** madmin/cache/manager/standard/get/ansi
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * Fetches the records matched by the given criteria from the cache
			 * database. The records must be from the sites that is
			 * configured in the context item.
			 *
			 * To limit the records matched, conditions can be added to the given
			 * criteria object. It can contain comparisons like column names that
			 * must match specific values which can be combined by AND, OR or NOT
			 * operators. The resulting string of SQL conditions replaces the
			 * ":cond" placeholder before the statement is sent to the database
			 * server.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for searching items
			 * @since 2014.03
			 * @category Developer
			 * @see madmin/cache/manager/standard/delete/ansi
			 * @see madmin/cache/manager/standard/deletebytag/ansi
			 * @see madmin/cache/manager/standard/set/ansi
			 * @see madmin/cache/manager/standard/settag/ansi
			 * @see madmin/cache/manager/standard/search/ansi
			 * @see madmin/cache/manager/standard/count/ansi
			 */

			$context = $this->getContext();
			$config = $context->getConfig();

			$name = $config->get( 'resource/db/adapter' );
			$name = $config->get( 'resource/db-cache/adapter', $name );

			/** madmin/cache/name
			 * Specifies the name of the cache class implementation
			 *
			 * There are several implementations available for integrating caches
			 * or providing optimized implementations for certain environments.
			 * This configuration option allows to change the cache implementation
			 * by setting the name of the \Aimeos\MW\Cache\* class.
			 *
			 * @param string Name of the cache class
			 * @since 2014.09
			 * @category Developer
			 */
			$name = $config->get( 'madmin/cache/name', $name );
			$config = array(
				'search' => $this->searchConfig,
				'dbname' => $this->getResourceName(),
				'siteid' => $context->getLocale()->getSiteId(),
				'sql' => array(
					'delete' => $this->getSqlConfig( 'madmin/cache/manager/standard/delete' ),
					'deletebytag' => $this->getSqlConfig( 'madmin/cache/manager/standard/deletebytag' ),
					'get' => $this->getSqlConfig( 'madmin/cache/manager/standard/get' ),
					'set' => $this->getSqlConfig( 'madmin/cache/manager/standard/set' ),
					'settag' => $this->getSqlConfig( 'madmin/cache/manager/standard/settag' ),
				),
			);
			$dbm = $context->getDatabaseManager();
			$this->object = \Aimeos\MW\Cache\Factory::create( 'DB', $config, $dbm );
		}

		return $this->object;
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param string[] $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MAdmin\Cache\Manager\Iface Manager object for chaining method calls
	 */
	public function clear( array $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$context = $this->getContext();
		$config = $context->getConfig();

		foreach( $config->get( 'madmin/cache/manager/submanagers', [] ) as $domain ) {
			$this->getObject()->getSubManager( $domain )->clear( $siteids );
		}

		/** madmin/cache/manager/standard/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see madmin/cache/manager/standard/delete/ansi
		 */

		/** madmin/cache/manager/standard/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the cache database.
		 * The records must be from the site that is configured via the
		 * context item.
		 *
		 * The ":cond" placeholder is replaced by the name of the ID column and
		 * the given ID or list of IDs while the site ID is bound to the question
		 * mark.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for deleting items
		 * @since 2014.03
		 * @category Developer
		 * @see madmin/cache/manager/standard/deletebytag/ansi
		 * @see madmin/cache/manager/standard/get/ansi
		 * @see madmin/cache/manager/standard/set/ansi
		 * @see madmin/cache/manager/standard/settag/ansi
		 * @see madmin/cache/manager/standard/search/ansi
		 * @see madmin/cache/manager/standard/count/ansi
		 */

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$sql = str_replace( ':cond', '1=1', $this->getSqlConfig( 'madmin/cache/manager/standard/delete' ) );
			$stmt = $conn->create( $sql )->execute()->finish();

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
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MAdmin\Cache\Item\Iface New cache item object
	 */
	public function createItem( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		try {
			$values['siteid'] = $this->getContext()->getLocale()->getSiteId();
		} catch( \Exception $e ) {
			$values['siteid'] = null;
		}

		return $this->createItemBase( $values );
	}


	/**
	 * Adds a new cache to the storage.
	 *
	 * @param \Aimeos\MAdmin\Cache\Item\Iface $item Cache item that should be saved to the storage
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MAdmin\Cache\Item\Iface Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MAdmin\Cache\Item\Iface $item, bool $fetch = true ) : \Aimeos\MAdmin\Cache\Item\Iface
	{
		if( $item->getId() === null ) {
			throw new \Aimeos\MAdmin\Cache\Exception( 'ID is required for caching' );
		}

		if( !$item->isModified() ) {
			return $item;
		}

		/** madmin/cache/manager/standard/set/mysql
		 * Inserts the cache entry into the database
		 *
		 * @see madmin/cache/manager/standard/set/ansi
		 */

		/** madmin/cache/manager/standard/set/ansi
		 * Inserts the cache entry into the database
		 *
		 * The ID, value and expiration timestamp are inserted as new record
		 * into the cache database. Any existing record must be deleted before
		 * the new one can be inserted.
		 *
		 * The SQL statement must be a string suitable for being used as
		 * prepared statement. It must include question marks for binding
		 * the values from the cache item to the statement before they are
		 * sent to the database server. The number of question marks must
		 * be the same as the number of columns listed in the INSERT
		 * statement. The order of the columns must correspond to the
		 * order in the set() method, so the correct values are bound to the
		 * columns.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for inserting a new cache entry
		 * @since 2014.03
		 * @category Developer
		 * @see madmin/cache/manager/standard/delete/ansi
		 * @see madmin/cache/manager/standard/deletebytag/ansi
		 * @see madmin/cache/manager/standard/get/ansi
		 * @see madmin/cache/manager/standard/settag/ansi
		 * @see madmin/cache/manager/standard/search/ansi
		 * @see madmin/cache/manager/standard/count/ansi
		 */

		/** madmin/cache/manager/standard/settag/mysql
		 * Inserts a new tag to an existing cache entry
		 *
		 * @see madmin/cache/manager/standard/settag/ansi
		 */

		/** madmin/cache/manager/standard/settag/ansi
		 * Inserts a new tag to an existing cache entry
		 *
		 * The ID of the cache entry and the tag name are inserted as a new
		 * record into the cache database. Any existing tag record that
		 * conflicts with the new one must be deleted before it can be inserted.
		 *
		 * The SQL statement must be a string suitable for being used as
		 * prepared statement. It must include question marks for binding
		 * the cache ID and tag name from the cache item to the statement
		 * before they are sent to the database server. The number of question
		 * marks must be the same as the number of columns listed in the INSERT
		 * statement. The order of the columns must correspond to the order in
		 * the saveItems() method, so the correct values are bound to the
		 * columns.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for inserting a new tag to an existing cache entry
		 * @since 2014.03
		 * @category Developer
		 * @see madmin/cache/manager/standard/delete/ansi
		 * @see madmin/cache/manager/standard/deletebytag/ansi
		 * @see madmin/cache/manager/standard/get/ansi
		 * @see madmin/cache/manager/standard/set/ansi
		 * @see madmin/cache/manager/standard/search/ansi
		 * @see madmin/cache/manager/standard/count/ansi
		 */

		$id = $item->getId();
		$cache = $this->getCache();

		$cache->delete( $id );
		$cache->set( $id, $item->getValue(), $item->getTimeExpire(), $item->getTags() );

		return $item;
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MAdmin\Cache\Manager\Iface Manager object for chaining method calls
	 */
	public function deleteItems( array $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$this->getCache()->deleteMultiple( $itemIds );
		return $this;
	}


	/**
	 * Creates the cache object for the given cache id.
	 *
	 * @param string $id Cache ID to fetch cache object for
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool $default Add default criteria
	 * @return \Aimeos\MAdmin\Cache\Item\Iface Returns the cache item of the given id
	 * @throws \Aimeos\MAdmin\Cache\Exception If item couldn't be found
	 */
	public function getItem( string $id, array $ref = [], bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		if( ( $value = $this->getCache()->get( $id ) ) === null ) {
			throw new \Aimeos\MAdmin\Cache\Exception( sprintf( 'Item with ID "%1$s" not found', $id ) );
		}

		return $this->createItemBase( array( 'id' => $id, 'value' => $value ) );
	}


	/**
	 * Search for cache entries based on the given criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search object containing the conditions
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MAdmin\Cache\Item\Iface with ids as keys
	 */
	public function searchItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$items = [];
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$required = array( 'cache' );
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ONE;

			/** madmin/cache/manager/standard/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see madmin/cache/manager/standard/search/ansi
			 */

			/** madmin/cache/manager/standard/search/ansi
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * Fetches the records matched by the given criteria from the cache
			 * database. The records must be from the sites that is
			 * configured in the context item.
			 *
			 * To limit the records matched, conditions can be added to the given
			 * criteria object. It can contain comparisons like column names that
			 * must match specific values which can be combined by AND, OR or NOT
			 * operators. The resulting string of SQL conditions replaces the
			 * ":cond" placeholder before the statement is sent to the database
			 * server.
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
			 * @since 2014.03
			 * @category Developer
			 * @see madmin/cache/manager/standard/get/ansi
			 * @see madmin/cache/manager/standard/delete/ansi
			 * @see madmin/cache/manager/standard/deletebytag/ansi
			 * @see madmin/cache/manager/standard/set/ansi
			 * @see madmin/cache/manager/standard/settag/ansi
			 * @see madmin/cache/manager/standard/count/ansi
			 */
			$cfgPathSearch = 'madmin/cache/manager/standard/search';

			/** madmin/cache/manager/standard/count/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see madmin/cache/manager/standard/count/ansi
			 */

			/** madmin/cache/manager/standard/count/ansi
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * Fetches the records matched by the given criteria from the cache
			 * database. The records must be from the sites that is
			 * configured in the context item.
			 *
			 * To limit the records matched, conditions can be added to the given
			 * criteria object. It can contain comparisons like column names that
			 * must match specific values which can be combined by AND, OR or NOT
			 * operators. The resulting string of SQL conditions replaces the
			 * ":cond" placeholder before the statement is sent to the database
			 * server.
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
			 * @param string SQL statement for searching items
			 * @since 2014.03
			 * @category Developer
			 * @see madmin/cache/manager/standard/get/ansi
			 * @see madmin/cache/manager/standard/delete/ansi
			 * @see madmin/cache/manager/standard/deletebytag/ansi
			 * @see madmin/cache/manager/standard/set/ansi
			 * @see madmin/cache/manager/standard/settag/ansi
			 * @see madmin/cache/manager/standard/search/ansi
			 */
			$cfgPathCount = 'madmin/cache/manager/standard/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== null )
			{
				if( $item = $this->applyFilter( $this->createItemBase( $row ) ) ) {
					$items[$row['id']] = $item;
				}
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return map( $items );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		$path = 'madmin/cache/manager/submanagers';
		return $this->getResourceTypeBase( 'cache', $path, [], $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] Returns a list of attributes
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		/** madmin/cache/manager/submanagers
		 * List of manager names that can be instantiated by the cache manager
		 *
		 * Managers provide a generic interface to the underlying storage.
		 * Each manager has or can have sub-managers caring about particular
		 * aspects. Each of these sub-managers can be instantiated by its
		 * parent manager using the getSubManager() method.
		 *
		 * The search keys from sub-managers can be normally used in the
		 * manager as well. It allows you to search for items of the manager
		 * using the search keys of the sub-managers to further limit the
		 * retrieved list of items.
		 *
		 * @param array List of sub-manager names
		 * @since 2014.03
		 * @category Developer
		 */
		$path = 'madmin/cache/manager/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, [], $withsub );
	}


	/**
	 * Returns a new manager for cache extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g stock, tags, locations, etc.
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->getSubManagerBase( 'cache', $manager, $name );
	}


	/**
	 * Create new admin cache item object initialized with given parameters.
	 *
	 * @param array $values Associative list of key/value pairs of a job
	 * @return \Aimeos\MAdmin\Cache\Item\Iface New cache item
	 */
	protected function createItemBase( array $values = [] ) : \Aimeos\MAdmin\Cache\Item\Iface
	{
		$values['siteid'] = $this->getContext()->getLocale()->getSiteId();
		return new \Aimeos\MAdmin\Cache\Item\Standard( $values );
	}
}
