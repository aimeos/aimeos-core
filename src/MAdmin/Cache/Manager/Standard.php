<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2023
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
	/** madmin/cache/manager/name
	 * Class name of the used cache manager implementation
	 *
	 * Each default manager can be replace by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the manager factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\MShop\Cache\Manager\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\MShop\Cache\Manager\Mymanager
	 *
	 * then you have to set the this configuration option:
	 *
	 *  madmin/cache/manager/name = Mymanager
	 *
	 * The value is the last part of your own class name and it's case sensitive,
	 * so take care that the configuration value is exactly named like the last
	 * part of the class name.
	 *
	 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
	 * characters are possible! You should always start the last part of the class
	 * name with an upper case character and continue only with lower case characters
	 * or numbers. Avoid chamel case names like "MyManager"!
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
	 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
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
	 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the cache manager.
	 *
	 *  madmin/cache/manager/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the cache controller.
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
	 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the cache manager.
	 *
	 *  madmin/cache/manager/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator2" only to the cache
	 * controller.
	 *
	 * @param array List of decorator names
	 * @since 2014.03
	 * @category Developer
	 * @see madmin/common/manager/decorators/default
	 * @see madmin/cache/manager/decorators/excludes
	 * @see madmin/cache/manager/decorators/global
	 */


	private ?\Aimeos\Base\Cache\Iface $object = null;
	private ?\Aimeos\Base\DB\Connection\Iface $conn = null;

	private array $searchConfig = array(
		'cache.id' => array(
			'code' => 'cache.id',
			'internalcode' => '"id"',
			'label' => 'ID',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'cache.value' => array(
			'code' => 'cache.value',
			'internalcode' => '"value"',
			'label' => 'Cached value',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'cache.expire' => array(
			'code' => 'cache.expire',
			'internalcode' => '"expire"',
			'label' => 'Expiration date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'cache.tag.name' => array(
			'code' => 'cache.tag.name',
			'internalcode' => '"tname"',
			'label' => 'Tag name',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
	);


	/**
	 * Creates the cache manager that will use the given context object.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object with required objects
	 */
	public function __construct( \Aimeos\MShop\ContextIface $context )
	{
		parent::__construct( $context );

		/** madmin/cache/manager/resource
		 * Name of the database connection resource to use
		 *
		 * You can configure a different database connection for each data domain
		 * and if no such connection name exists, the "db" connection will be used.
		 * It's also possible to use the same database connection for different
		 * data domains by configuring the same connection name using this setting.
		 *
		 * @param string Database connection name
		 * @since 2023.04
		 */
		$this->setResourceName( $context->config()->get( 'madmin/cache/manager/resource', 'db-cache' ) );
	}


	public function __destruct()
	{
		unset( $this->conn, $this->object );
	}


	/**
	 * Returns the cache object
	 *
	 * @return \Aimeos\Base\Cache\Iface Cache object
	 */
	public function getCache() : \Aimeos\Base\Cache\Iface
	{
		if( !isset( $this->object ) )
		{
			$context = $this->context();
			$cfg = [
				'cleanup' => $context->config()->get( 'madmin/cache/manager/cleanup' ),
				'clear' => $context->config()->get( 'madmin/cache/manager/clear' ),
				'delete' => $context->config()->get( 'madmin/cache/manager/delete' ),
				'deletebytag' => $context->config()->get( 'madmin/cache/manager/deletebytag' ),
				'get' => $context->config()->get( 'madmin/cache/manager/get' ),
				'set' => $context->config()->get( 'madmin/cache/manager/set' ),
				'settag' => $context->config()->get( 'madmin/cache/manager/settag' ),
			];

			$this->conn = $context->db( 'db-cache' );
			$this->object = \Aimeos\Base\Cache\Factory::create( 'DB', $cfg, $this->conn );
		}

		return $this->object;
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MAdmin\Cache\Manager\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$this->getCache()->clear();
		return $this;
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MAdmin\Cache\Item\Iface New cache item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->createItemBase( $values );
	}


	/**
	 * Adds a new cache to the storage.
	 *
	 * @param \Aimeos\MAdmin\Cache\Item\Iface $item Cache item that should be saved to the storage
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MAdmin\Cache\Item\Iface Updated item including the generated ID
	 */
	protected function saveItem( \Aimeos\MAdmin\Cache\Item\Iface $item, bool $fetch = true ) : \Aimeos\MAdmin\Cache\Item\Iface
	{
		if( $item->getId() === null ) {
			throw new \Aimeos\MAdmin\Cache\Exception( 'ID is required for caching' );
		}

		if( !$item->isModified() ) {
			return $item;
		}

		/** madmin/cache/manager/set/mysql
		 * Inserts the cache entry into the database
		 *
		 * @see madmin/cache/manager/set/ansi
		 */

		/** madmin/cache/manager/set/ansi
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
		 * @see madmin/cache/manager/delete/ansi
		 * @see madmin/cache/manager/deletebytag/ansi
		 * @see madmin/cache/manager/get/ansi
		 * @see madmin/cache/manager/settag/ansi
		 * @see madmin/cache/manager/search/ansi
		 * @see madmin/cache/manager/count/ansi
		 */

		/** madmin/cache/manager/settag/mysql
		 * Inserts a new tag to an existing cache entry
		 *
		 * @see madmin/cache/manager/settag/ansi
		 */

		/** madmin/cache/manager/settag/ansi
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
		 * the save() method, so the correct values are bound to the
		 * columns.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for inserting a new tag to an existing cache entry
		 * @since 2014.03
		 * @category Developer
		 * @see madmin/cache/manager/delete/ansi
		 * @see madmin/cache/manager/deletebytag/ansi
		 * @see madmin/cache/manager/get/ansi
		 * @see madmin/cache/manager/set/ansi
		 * @see madmin/cache/manager/search/ansi
		 * @see madmin/cache/manager/count/ansi
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
	 * @param \Aimeos\MShop\Common\Item\Iface|array|string $items List of item objects or IDs of the items
	 * @return \Aimeos\MAdmin\Cache\Manager\Iface Manager object for chaining method calls
	 */
	public function delete( $items ) : \Aimeos\MShop\Common\Manager\Iface
	{
		if( empty( $items ) ) { return $this; }

		$this->getCache()->deleteMultiple( map( $items ) );
		return $this;
	}


	/**
	 * Creates the cache object for the given cache id.
	 *
	 * @param string $id Cache ID to fetch cache object for
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MAdmin\Cache\Item\Iface Returns the cache item of the given id
	 * @throws \Aimeos\MAdmin\Cache\Exception If item couldn't be found
	 */
	public function get( string $id, array $ref = [], ?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		if( ( $value = $this->getCache()->get( $id ) ) === null ) {
			throw new \Aimeos\MAdmin\Cache\Exception( sprintf( 'Item with ID "%1$s" not found', $id ) );
		}

		return $this->createItemBase( array( 'id' => $id, 'value' => $value ) );
	}


	/**
	 * Search for cache entries based on the given criteria.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $search Search object containing the conditions
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MAdmin\Cache\Item\Iface with ids as keys
	 */
	public function search( \Aimeos\Base\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$items = [];
		$context = $this->context();
		$conn = $context->db( $this->getResourceName() );

		$required = array( 'cache' );
		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ONE;

		/** madmin/cache/manager/search/mysql
		 * Retrieves the records matched by the given criteria in the database
		 *
		 * @see madmin/cache/manager/search/ansi
		 */

		/** madmin/cache/manager/search/ansi
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
		 * @see madmin/cache/manager/get/ansi
		 * @see madmin/cache/manager/delete/ansi
		 * @see madmin/cache/manager/deletebytag/ansi
		 * @see madmin/cache/manager/set/ansi
		 * @see madmin/cache/manager/settag/ansi
		 * @see madmin/cache/manager/count/ansi
		 */
		$cfgPathSearch = 'madmin/cache/manager/search';

		/** madmin/cache/manager/count/mysql
		 * Retrieves the records matched by the given criteria in the database
		 *
		 * @see madmin/cache/manager/count/ansi
		 */

		/** madmin/cache/manager/count/ansi
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
		 * @see madmin/cache/manager/get/ansi
		 * @see madmin/cache/manager/delete/ansi
		 * @see madmin/cache/manager/deletebytag/ansi
		 * @see madmin/cache/manager/set/ansi
		 * @see madmin/cache/manager/settag/ansi
		 * @see madmin/cache/manager/search/ansi
		 */
		$cfgPathCount = 'madmin/cache/manager/count';

		$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

		while( ( $row = $results->fetch() ) !== null )
		{
			if( $item = $this->applyFilter( $this->createItemBase( $row ) ) ) {
				$items[$row['id']] = $item;
			}
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
	 * @return \Aimeos\Base\Criteria\Attribute\Iface[] Returns a list of attributes
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
		return new \Aimeos\MAdmin\Cache\Item\Standard( $values );
	}
}
