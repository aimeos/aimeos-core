<?php
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Order
 */

namespace Aimeos\MShop\Order\Manager\Status;


/**
 * Default implementation for order status manager.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Order\Manager\Status\Iface
{
	private $searchConfig = array(
		'order.status.id'=> array(
			'code'=>'order.status.id',
			'internalcode'=>'mordst."id"',
			'internaldeps'=> array( 'LEFT JOIN "mshop_order_status" AS mordst ON ( mord."id" = mordst."parentid" )' ),
			'label'=>'Order status ID',
			'type'=> 'integer',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'order.status.siteid'=> array(
			'code'=>'order.status.siteid',
			'internalcode'=>'mordst."siteid"',
			'label'=>'Order status site ID',
			'type'=> 'integer',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'order.status.parentid'=> array(
			'code'=>'order.status.parentid',
			'internalcode'=>'mordst."parentid"',
			'label'=>'Order status parent id',
			'type'=> 'integer',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'order.status.type'=> array(
			'code'=>'order.status.type',
			'internalcode'=>'mordst."type"',
			'label'=>'Order status type',
			'type'=> 'string',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.status.value'=> array(
			'code'=>'order.status.value',
			'internalcode'=>'mordst."value"',
			'label'=>'Order status value',
			'type'=> 'string',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.status.mtime'=> array(
			'code'=>'order.status.mtime',
			'internalcode'=>'mordst."mtime"',
			'label'=>'Order status modification time',
			'type'=> 'datetime',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.status.ctime'=> array(
			'code'=>'order.status.ctime',
			'internalcode'=>'mordst."ctime"',
			'label'=>'Order status create date/time',
			'type'=> 'datetime',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.status.editor'=> array(
			'code'=>'order.status.editor',
			'internalcode'=>'mordst."editor"',
			'label'=>'Order status editor',
			'type'=> 'string',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
	);


	/**
	 * Initializes the object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );
		$this->setResourceName( 'db-order' );
	}


	/**
	 * Counts the number items that are available for the values of the given key.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria
	 * @param string $key Search key to aggregate items for
	 * @return array List of the search keys as key and the number of counted items as value
	 */
	public function aggregate( \Aimeos\MW\Criteria\Iface $search, $key )
	{
		/** mshop/order/manager/status/standard/aggregate/mysql
		 * Counts the number of records grouped by the values in the key column and matched by the given criteria
		 *
		 * @see mshop/order/manager/status/standard/aggregate/ansi
		 */

		/** mshop/order/manager/status/standard/aggregate/ansi
		 * Counts the number of records grouped by the values in the key column and matched by the given criteria
		 *
		 * Groups all records by the values in the key column and counts their
		 * occurence. The matched records can be limited by the given criteria
		 * from the order database. The records must be from one of the sites
		 * that are configured via the context item. If the current site is part
		 * of a tree of sites, the statement can count all records from the
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
		 * This statement doesn't return any records. Instead, it returns pairs
		 * of the different values found in the key column together with the
		 * number of records that have been found for that key values.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for aggregating order items
		 * @since 2014.09
		 * @category Developer
		 * @see mshop/order/manager/status/standard/insert/ansi
		 * @see mshop/order/manager/status/standard/update/ansi
		 * @see mshop/order/manager/status/standard/newid/ansi
		 * @see mshop/order/manager/status/standard/delete/ansi
		 * @see mshop/order/manager/status/standard/search/ansi
		 * @see mshop/order/manager/status/standard/count/ansi
		 */
		$cfgkey = 'mshop/order/manager/status/standard/aggregate';
		return $this->aggregateBase( $search, $key, $cfgkey, array( 'order.status' ) );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param integer[] $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'mshop/order/manager/status/submanagers';
		foreach( $this->getContext()->getConfig()->get( $path, [] ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->cleanupBase( $siteids, 'mshop/order/manager/status/standard/delete' );
	}


	/**
	 * Creates a new order status object.
	 *
	 * @return \Aimeos\MShop\Order\Item\Status\Iface New item object
	 */
	public function createItem()
	{
		$values = array( 'order.status.siteid'=> $this->getContext()->getLocale()->getSiteId() );
		return $this->createItemBase( $values );
	}


	/**
	 * Adds or updates an order status object.
	 *
	 * @param \Aimeos\MShop\Order\Item\Status\Iface $item Order status object whose data should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Iface $item, $fetch = true )
	{
		$iface = '\\Aimeos\\MShop\\Order\\Item\\Status\\Iface';
		if( !( $item instanceof $iface ) ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if( !$item->isModified() ) { return; }

		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();
			$date = date( 'Y-m-d H:i:s' );

			if( $id === null )
			{
				/** mshop/order/manager/status/standard/insert/mysql
				 * Inserts a new order record into the database table
				 *
				 * @see mshop/order/manager/status/standard/insert/ansi
				 */

				/** mshop/order/manager/status/standard/insert/ansi
				 * Inserts a new order record into the database table
				 *
				 * Items with no ID yet (i.e. the ID is NULL) will be created in
				 * the database and the newly created ID retrieved afterwards
				 * using the "newid" SQL statement.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the order item to the statement before they are
				 * sent to the database server. The number of question marks must
				 * be the same as the number of columns listed in the INSERT
				 * statement. The order of the columns must correspond to the
				 * order in the saveItems() method, so the correct values are
				 * bound to the columns.
				 *
				 * The SQL statement should conform to the ANSI standard to be
				 * compatible with most relational database systems. This also
				 * includes using double quotes for table and column names.
				 *
				 * @param string SQL statement for inserting records
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/order/manager/status/standard/update/ansi
				 * @see mshop/order/manager/status/standard/newid/ansi
				 * @see mshop/order/manager/status/standard/delete/ansi
				 * @see mshop/order/manager/status/standard/search/ansi
				 * @see mshop/order/manager/status/standard/count/ansi
				 */
				$path = 'mshop/order/manager/status/standard/insert';
			}
			else
			{
				/** mshop/order/manager/status/standard/update/mysql
				 * Updates an existing order record in the database
				 *
				 * @see mshop/order/manager/status/standard/update/ansi
				 */

				/** mshop/order/manager/status/standard/update/ansi
				 * Updates an existing order record in the database
				 *
				 * Items which already have an ID (i.e. the ID is not NULL) will
				 * be updated in the database.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the order item to the statement before they are
				 * sent to the database server. The order of the columns must
				 * correspond to the order in the saveItems() method, so the
				 * correct values are bound to the columns.
				 *
				 * The SQL statement should conform to the ANSI standard to be
				 * compatible with most relational database systems. This also
				 * includes using double quotes for table and column names.
				 *
				 * @param string SQL statement for updating records
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/order/manager/status/standard/insert/ansi
				 * @see mshop/order/manager/status/standard/newid/ansi
				 * @see mshop/order/manager/status/standard/delete/ansi
				 * @see mshop/order/manager/status/standard/search/ansi
				 * @see mshop/order/manager/status/standard/count/ansi
				 */
				$path = 'mshop/order/manager/status/standard/update';
			}

			$stmt = $this->getCachedStatement( $conn, $path );

			$stmt->bind( 1, $context->getLocale()->getSiteId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 2, $item->getParentID(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 3, $item->getType() );
			$stmt->bind( 4, $item->getValue() );
			$stmt->bind( 5, $date ); //mtime
			$stmt->bind( 6, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind( 7, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$item->setId( $id ); //is not modified anymore
			} else {
				$stmt->bind( 7, $date ); //ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** mshop/order/manager/status/standard/newid/mysql
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * @see mshop/order/manager/status/standard/newid/ansi
				 */

				/** mshop/order/manager/status/standard/newid/ansi
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
				 *  SELECT currval('seq_mord_id')
				 * For SQL Server:
				 *  SELECT SCOPE_IDENTITY()
				 * For Oracle:
				 *  SELECT "seq_mord_id".CURRVAL FROM DUAL
				 *
				 * There's no way to retrive the new ID by a SQL statements that
				 * fits for most database servers as they implement their own
				 * specific way.
				 *
				 * @param string SQL statement for retrieving the last inserted record ID
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/order/manager/status/standard/insert/ansi
				 * @see mshop/order/manager/status/standard/update/ansi
				 * @see mshop/order/manager/status/standard/delete/ansi
				 * @see mshop/order/manager/status/standard/search/ansi
				 * @see mshop/order/manager/status/standard/count/ansi
				 */
				$path = 'mshop/order/manager/status/standard/newid';
				$item->setId( $this->newId( $conn, $path ) );
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
	 * Returns the order status item specified by its ID.
	 *
	 * @param integer $id Unique ID of the order status item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MShop\Order\Item\Status\Iface Returns order status item of the given id
	 * @throws \Aimeos\MShop\Order\Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = [], $default = false )
	{
		return $this->getItemBase( 'order.status.id', $id, $ref, $default );
	}


	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		/** mshop/order/manager/status/standard/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/order/manager/status/standard/delete/ansi
		 */

		/** mshop/order/manager/status/standard/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the order database.
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
		 * @see mshop/order/manager/status/standard/insert/ansi
		 * @see mshop/order/manager/status/standard/update/ansi
		 * @see mshop/order/manager/status/standard/newid/ansi
		 * @see mshop/order/manager/status/standard/search/ansi
		 * @see mshop/order/manager/status/standard/count/ansi
		 */
		$path = 'mshop/order/manager/status/standard/delete';
		$this->deleteItemsBase( $ids, $path );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param boolean $withsub Return also the resource type of sub-managers if true
	 * @return array Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( $withsub = true )
	{
		$path = 'mshop/order/manager/status/submanagers';

		return $this->getResourceTypeBase( 'order/status', $path, [], $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** mshop/order/manager/status/submanagers
		 * List of manager names that can be instantiated by the order status manager
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
		$path = 'mshop/order/manager/status/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, [], $withsub );
	}


	/**
	 * Returns a new manager for order status extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager extending the domain functionality
	 */
	public function getSubManager( $manager, $name = null )
	{
		/** mshop/order/manager/status/name
		 * Class name of the used order status manager implementation
		 *
		 * Each default order status manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\MShop\Order\Manager\Status\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\MShop\Order\Manager\Status\Mystatus
		 *
		 * then you have to set the this configuration option:
		 *
		 *  mshop/order/manager/status/name = Mystatus
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyStatus"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */

		/** mshop/order/manager/status/decorators/excludes
		 * Excludes decorators added by the "common" option from the order status manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the order status manager.
		 *
		 *  mshop/order/manager/status/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
		 * "mshop/common/manager/decorators/default" for the order status manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/status/decorators/global
		 * @see mshop/order/manager/status/decorators/local
		 */

		/** mshop/order/manager/status/decorators/global
		 * Adds a list of globally available decorators only to the order status manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the order status manager.
		 *
		 *  mshop/order/manager/status/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the order controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/status/decorators/excludes
		 * @see mshop/order/manager/status/decorators/local
		 */

		/** mshop/order/manager/status/decorators/local
		 * Adds a list of local decorators only to the order status manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the order status manager.
		 *
		 *  mshop/order/manager/status/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator2" only to the order
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/status/decorators/excludes
		 * @see mshop/order/manager/status/decorators/global
		 */

		return $this->getSubManagerBase( 'order', 'status/' . $manager, $name );
	}


	/**
	 * Searches for all items matching the given critera.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param integer|null &$total Number of items that are available in total
	 * @return array List of items implementing \Aimeos\MShop\Order\Item\Status\Iface
	 */
	public function searchItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], &$total = null )
	{
		$items = [];
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$required = array( 'order.status' );
			$sitelevel = \Aimeos\MShop\Locale\Manager\Base::SITE_SUBTREE;

			/** mshop/order/manager/status/standard/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see mshop/order/manager/status/standard/search/ansi
			 */

			/** mshop/order/manager/status/standard/search/ansi
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * Fetches the records matched by the given criteria from the order
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
			 * replaces the ":order" placeholder. In case no ordering is required,
			 * the complete ORDER BY part including the "\/*-orderby*\/...\/*orderby-*\/"
			 * markers is removed to speed up retrieving the records. Columns of
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
			 * @since 2014.03
			 * @category Developer
			 * @see mshop/order/manager/status/standard/insert/ansi
			 * @see mshop/order/manager/status/standard/update/ansi
			 * @see mshop/order/manager/status/standard/newid/ansi
			 * @see mshop/order/manager/status/standard/delete/ansi
			 * @see mshop/order/manager/status/standard/count/ansi
			 */
			$cfgPathSearch = 'mshop/order/manager/status/standard/search';

			/** mshop/order/manager/status/standard/count/mysql
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * @see mshop/order/manager/status/standard/count/ansi
			 */

			/** mshop/order/manager/status/standard/count/ansi
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * Counts all records matched by the given criteria from the order
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
			 * @since 2014.03
			 * @category Developer
			 * @see mshop/order/manager/status/standard/insert/ansi
			 * @see mshop/order/manager/status/standard/update/ansi
			 * @see mshop/order/manager/status/standard/newid/ansi
			 * @see mshop/order/manager/status/standard/delete/ansi
			 * @see mshop/order/manager/status/standard/search/ansi
			 */
			$cfgPathCount = 'mshop/order/manager/status/standard/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount,
				$required, $total, $sitelevel );

			while( ( $row = $results->fetch() ) !== false ) {
				$items[$row['order.status.id']] = $this->createItemBase( $row );
			}

			$dbm->release( $conn, $dbname );

		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $items;
	}


	/**
	 * Creates a new order status object.
	 *
	 * @param array $values List of attributes for the order status object
	 * @return \Aimeos\MShop\Order\Item\Status\Iface New order status object
	 */
	protected function createItemBase( array $values = [] )
	{
		return new \Aimeos\MShop\Order\Item\Status\Standard( $values );
	}
}