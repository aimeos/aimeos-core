<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MAdmin
 * @subpackage Cache
 */


/**
 * Default cache manager implementation.
 *
 * @package MAdmin
 * @subpackage Cache
 */
class MAdmin_Cache_Manager_Default
	extends MAdmin_Common_Manager_Abstract
	implements MAdmin_Cache_Manager_Interface
{
	private $_object;

	private $_searchConfig = array(
		'cache.id' => array(
			'code' => 'cache.id',
			'internalcode' => '"id"',
			'label' => 'Cache ID',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'cache.siteid' => array(
			'code' => 'cache.siteid',
			'internalcode' => '"siteid"',
			'label' => 'Cache site ID',
			'type' => 'integer',
			'public' => false,
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'cache.value' => array(
			'code' => 'cache.value',
			'internalcode' => '"value"',
			'label' => 'Cached value',
			'type' => 'string',
			'public' => false,
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'cache.expire' => array(
			'code' => 'cache.expire',
			'internalcode' => '"expire"',
			'label' => 'Cache expiration date/time',
			'type' => 'datetime',
			'public' => false,
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'cache.tag.name' => array(
			'code' => 'cache.tag.name',
			'internalcode' => '"tname"',
			'label' => 'Cache tag name',
			'type' => 'string',
			'public' => false,
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
	);


	/**
	 * Creates the cache manager that will use the given context object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );
		$this->_setResourceName( 'db-cache' );
	}


	/**
	 * Returns the cache object
	 *
	 * @return MW_Cache_Interface Cache object
	 */
	public function getCache()
	{
		if( !isset( $this->_object ) )
		{
			/** madmin/cache/manager/default/deletebytag
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
			 * @see madmin/cache/manager/default/delete
			 * @see madmin/cache/manager/default/get
			 * @see madmin/cache/manager/default/getbytag
			 * @see madmin/cache/manager/default/set
			 * @see madmin/cache/manager/default/settag
			 * @see madmin/cache/manager/default/search
			 * @see madmin/cache/manager/default/count
			 */

			/** madmin/cache/manager/default/getbytag
			 * Retrieves the records from the database matched by the given tags
			 *
			 * Fetches the records matched by the given tags from the cache
			 * database. The records must be from the sites that is
			 * configured in the context item.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for retrieving items by tag
			 * @since 2014.03
			 * @category Developer
			 * @see madmin/cache/manager/default/delete
			 * @see madmin/cache/manager/default/deletebytag
			 * @see madmin/cache/manager/default/get
			 * @see madmin/cache/manager/default/set
			 * @see madmin/cache/manager/default/settag
			 * @see madmin/cache/manager/default/search
			 * @see madmin/cache/manager/default/count
			 */

			/** madmin/cache/manager/default/get
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
			 * @see madmin/cache/manager/default/getbytag
			 * @see madmin/cache/manager/default/delete
			 * @see madmin/cache/manager/default/deletebytag
			 * @see madmin/cache/manager/default/set
			 * @see madmin/cache/manager/default/settag
			 * @see madmin/cache/manager/default/search
			 * @see madmin/cache/manager/default/count
			 */

			$context = $this->_getContext();
			$config = $context->getConfig();

			$name = $config->get( 'resource/db/adapter' );
			$name = $config->get( 'resource/db-cache/adapter', $name );

			/** classes/cache/name
			 * Specifies the name of the cache class implementation
			 *
			 * There are several implementations available for integrating caches
			 * or providing optimized implementations for certain environments.
			 * This configuration option allows to change the cache implementation
			 * by setting the name of the MW_Cache_* class.
			 *
			 * @param string Name of the cache class
			 * @since 2014.09
			 * @category Developer
			 */
			$name = $config->get( 'classes/cache/name', $name );
			$config = array(
				'search' => $this->_searchConfig,
				'dbname' => $this->_getResourceName(),
				'siteid' => $context->getLocale()->getSiteId(),
				'sql' => array(
					'delete' => $config->get( 'madmin/cache/manager/default/delete' ),
					'deletebytag' => $config->get( 'madmin/cache/manager/default/deletebytag' ),
					'getbytag' => $config->get( 'madmin/cache/manager/default/getbytag' ),
					'get' => $config->get( 'madmin/cache/manager/default/get' ),
					'set' => $config->get( 'madmin/cache/manager/default/set' ),
					'settag' => $config->get( 'madmin/cache/manager/default/settag' ),
				),
			);
			$dbm = $context->getDatabaseManager();

			try {
				$this->_object = MW_Cache_Factory::createManager( $name, $config, $dbm );
			} catch( Exception $e ) {
				$this->_object = MW_Cache_Factory::createManager( 'DB', $config, $dbm );
			}
		}

		return $this->_object;
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param integer[] $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$context = $this->_getContext();
		$config = $context->getConfig();

		$path = 'classes/cache/manager/submanagers';
		foreach( $config->get( $path, array() ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		/** madmin/cache/manager/default/delete
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
		 * @see madmin/cache/manager/default/deletebytag
		 * @see madmin/cache/manager/default/get
		 * @see madmin/cache/manager/default/getbytag
		 * @see madmin/cache/manager/default/set
		 * @see madmin/cache/manager/default/settag
		 * @see madmin/cache/manager/default/search
		 * @see madmin/cache/manager/default/count
		 */

		$this->_cleanup( $siteids, 'madmin/cache/manager/default/delete' );
	}


	/**
	 * Create new cache item object.
	 *
	 * @return MAdmin_Cache_Item_Interface
	 */
	public function createItem()
	{
		try {
			$values = array( 'siteid' => $this->_getContext()->getLocale()->getSiteId() );
		} catch( Exception $e ) {
			$values = array( 'siteid' => null );
		}

		return $this->_createItem( $values );
	}


	/**
	 * Adds a new cache to the storage.
	 *
	 * @param MAdmin_Cache_Item_Interface $item Cache item that should be saved to the storage
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MAdmin_Cache_Item_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MAdmin_Cache_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if( ! $item->isModified() ) {
			return;
		}

		/** madmin/cache/manager/default/set
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
		 * @see madmin/cache/manager/default/delete
		 * @see madmin/cache/manager/default/deletebytag
		 * @see madmin/cache/manager/default/get
		 * @see madmin/cache/manager/default/getbytag
		 * @see madmin/cache/manager/default/settag
		 * @see madmin/cache/manager/default/search
		 * @see madmin/cache/manager/default/count
		 */

		/** madmin/cache/manager/default/settag
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
		 * @see madmin/cache/manager/default/delete
		 * @see madmin/cache/manager/default/deletebytag
		 * @see madmin/cache/manager/default/get
		 * @see madmin/cache/manager/default/getbytag
		 * @see madmin/cache/manager/default/set
		 * @see madmin/cache/manager/default/search
		 * @see madmin/cache/manager/default/count
		 */

		$id = $item->getId();
		$cache = $this->getCache();

		$cache->delete( $id );
		$cache->set( $id, $item->getValue(), $item->getTags(), $item->getTimeExpire() );
	}


	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		$this->getCache()->deleteList( $ids );
	}


	/**
	 * Creates the cache object for the given cache id.
	 *
	 * @param integer $id Cache ID to fetch cache object for
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MAdmin_Cache_Item_Interface Returns the cache item of the given id
	 * @throws MAdmin_Cache_Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		if( ( $value = $this->getCache()->get( $id ) ) === null ) {
			throw new MAdmin_Cache_Exception( sprintf( 'Item with ID "%1$s" not found', $id ) );
		}

		return $this->_createItem( array( 'id' => $id, 'value' => $value ) );
	}


	/**
	 * Search for cache entries based on the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object containing the conditions
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @param integer &$total Number of items that are available in total
	 * @return array List of cache items implementing MAdmin_Cache_Item_Interface
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$items = array();
		$context = $this->_getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$required = array( 'cache' );
			$level = MShop_Locale_Manager_Abstract::SITE_ONE;

			/** madmin/cache/manager/default/search
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
			 * @see madmin/cache/manager/default/get
			 * @see madmin/cache/manager/default/getbytag
			 * @see madmin/cache/manager/default/delete
			 * @see madmin/cache/manager/default/deletebytag
			 * @see madmin/cache/manager/default/set
			 * @see madmin/cache/manager/default/settag
			 * @see madmin/cache/manager/default/count
			 */
			$cfgPathSearch = 'madmin/cache/manager/default/search';

			/** madmin/cache/manager/default/count
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
			 * @see madmin/cache/manager/default/get
			 * @see madmin/cache/manager/default/getbytag
			 * @see madmin/cache/manager/default/delete
			 * @see madmin/cache/manager/default/deletebytag
			 * @see madmin/cache/manager/default/set
			 * @see madmin/cache/manager/default/settag
			 * @see madmin/cache/manager/default/search
			 */
			$cfgPathCount =  'madmin/cache/manager/default/count';

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false ) {
				$items[ $row['id'] ] = $this->_createItem( $row );
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $items;
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array Returns a list of attribtes implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** classes/cache/manager/submanagers
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
		$path = 'classes/cache/manager/submanagers';

		return $this->_getSearchAttributes( $this->_searchConfig, $path, array(), $withsub );
	}


	/**
	 * Returns a new manager for cache extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return MShop_Common_Manager_Interface Manager for different extensions, e.g stock, tags, locations, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		/** classes/cache/manager/name
		 * Class name of the used cache manager implementation
		 *
		 * Each default cache manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MAdmin_Cache_Manager_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MAdmin_Cache_Manager_Mycache
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/cache/manager/name = Mycache
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyCache"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */

		/** madmin/cache/manager/decorators/excludes
		 * Excludes decorators added by the "common" option from the cache manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. cache what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for cacheged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "madmin/common/manager/decorators/default" before they are wrapped
		 * around the cache manager.
		 *
		 *  madmin/cache/manager/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "madmin/common/manager/decorators/default" for the cache manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see madmin/common/manager/decorators/default
		 * @see madmin/cache/manager/decorators/global
		 * @see madmin/cache/manager/decorators/local
		 */

		/** madmin/cache/manager/decorators/global
		 * Adds a list of globally available decorators only to the cache manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. cache what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for cacheged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the cache manager.
		 *
		 *  madmin/cache/manager/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the cache controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see madmin/common/manager/decorators/default
		 * @see madmin/cache/manager/decorators/excludes
		 * @see madmin/cache/manager/decorators/local
		 */

		/** madmin/cache/manager/decorators/local
		 * Adds a list of local decorators only to the cache manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. cache what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for cacheged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the cache manager.
		 *
		 *  madmin/cache/manager/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the cache
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see madmin/common/manager/decorators/default
		 * @see madmin/cache/manager/decorators/excludes
		 * @see madmin/cache/manager/decorators/global
		 */

		return $this->_getSubManager( 'cache', $manager, $name );
	}


	/**
	 * Create new admin cache item object initialized with given parameters.
	 *
	 * @param array $values Associative list of key/value pairs of a job
	 * @return MAdmin_Cache_Item_Interface
	 */
	protected function _createItem( array $values = array() )
	{
		$values['siteid'] = $this->_getContext()->getLocale()->getSiteId();

		return new MAdmin_Cache_Item_Default( $values );
	}
}
