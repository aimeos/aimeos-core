<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2024
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Manager\Service;


/**
 * Default Manager Order service
 *
 * @package MShop
 * @subpackage Order
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Order\Manager\Service\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private array $searchConfig = array(
		'order.service.id' => array(
			'code' => 'order.service.id',
			'internalcode' => 'mordse."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_order_service" AS mordse ON ( mord."id" = mordse."parentid" )' ),
			'label' => 'Service ID',
			'type' => 'int',
			'public' => false,
		),
		'order.service.siteid' => array(
			'code' => 'order.service.siteid',
			'internalcode' => 'mordse."siteid"',
			'label' => 'Service site ID',
			'public' => false,
		),
		'order.service.parentid' => array(
			'code' => 'order.service.parentid',
			'internalcode' => 'mordse."parentid"',
			'label' => 'Order ID',
			'type' => 'int',
			'public' => false,
		),
		'order.service.serviceid' => array(
			'code' => 'order.service.serviceid',
			'internalcode' => 'mordse."servid"',
			'label' => 'Service original service ID',
			'public' => false,
		),
		'order.service.name' => array(
			'code' => 'order.service.name',
			'internalcode' => 'mordse."name"',
			'label' => 'Service name',
		),
		'order.service.code' => array(
			'code' => 'order.service.code',
			'internalcode' => 'mordse."code"',
			'label' => 'Service code',
		),
		'order.service.type' => array(
			'code' => 'order.service.type',
			'internalcode' => 'mordse."type"',
			'label' => 'Service type',
		),
		'order.service.currencyid' => array(
			'code' => 'order.service.currencyid',
			'internalcode' => 'mordse."currencyid"',
			'label' => 'Service currencyid code',
		),
		'order.service.price' => array(
			'code' => 'order.service.price',
			'internalcode' => 'mordse."price"',
			'label' => 'Service price',
			'type' => 'decimal',
		),
		'order.service.costs' => array(
			'code' => 'order.service.costs',
			'internalcode' => 'mordse."costs"',
			'label' => 'Service shipping',
			'type' => 'decimal',
		),
		'order.service.rebate' => array(
			'code' => 'order.service.rebate',
			'internalcode' => 'mordse."rebate"',
			'label' => 'Service rebate',
			'type' => 'decimal',
		),
		'order.service.taxrates' => array(
			'code' => 'order.service.taxrates',
			'internalcode' => 'mordse."taxrate"',
			'label' => 'Service taxrates',
			'type' => 'json',
		),
		'order.service.taxvalue' => array(
			'code' => 'order.service.taxvalue',
			'internalcode' => 'mordse."tax"',
			'label' => 'Service tax value',
			'type' => 'decimal',
		),
		'order.service.taxflag' => array(
			'code' => 'order.service.taxflag',
			'internalcode' => 'mordse."taxflag"',
			'label' => 'Service tax flag (0=net, 1=gross)',
			'type' => 'int',
		),
		'order.service.mediaurl' => array(
			'code' => 'order.service.mediaurl',
			'internalcode' => 'mordse."mediaurl"',
			'label' => 'Service media url',
			'public' => false,
		),
		'order.service.position' => array(
			'code' => 'order.service.position',
			'internalcode' => 'mordse."pos"',
			'label' => 'Service position',
			'type' => 'int',
			'public' => false,
		),
		'order.service.ctime' => array(
			'code' => 'order.service.ctime',
			'internalcode' => 'mordse."ctime"',
			'label' => 'Service create date/time',
			'type' => 'datetime',
			'public' => false,
		),
		'order.service.mtime' => array(
			'code' => 'order.service.mtime',
			'internalcode' => 'mordse."mtime"',
			'label' => 'Service modify date/time',
			'type' => 'datetime',
			'public' => false,
		),
		'order.service.editor' => array(
			'code' => 'order.service.editor',
			'internalcode' => 'mordse."editor"',
			'label' => 'Service editor',
			'public' => false,
		),
	);


	/**
	 * Initializes the object.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 */
	public function __construct( \Aimeos\MShop\ContextIface $context )
	{
		parent::__construct( $context );

		/** mshop/order/manager/resource
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
		$this->setResourceName( $context->config()->get( 'mshop/order/manager/resource', 'db-order' ) );
	}


	/**
	 * Counts the number items that are available for the values of the given key.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $search Search criteria
	 * @param array|string $key Search key or list of keys to aggregate items for
	 * @param string|null $value Search key for aggregating the value column
	 * @param string|null $type Type of the aggregation, empty string for count or "sum" or "avg" (average)
	 * @return \Aimeos\Map List of the search keys as key and the number of counted items as value
	 */
	public function aggregate( \Aimeos\Base\Criteria\Iface $search, $key, string $value = null, string $type = null ) : \Aimeos\Map
	{
		/** mshop/order/manager/service/aggregate/mysql
		 * Counts the number of records grouped by the values in the key column and matched by the given criteria
		 *
		 * @see mshop/order/manager/service/aggregate/ansi
		 */

		/** mshop/order/manager/service/aggregate/ansi
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
		 * @see mshop/order/manager/service/insert/ansi
		 * @see mshop/order/manager/service/update/ansi
		 * @see mshop/order/manager/service/newid/ansi
		 * @see mshop/order/manager/service/delete/ansi
		 * @see mshop/order/manager/service/search/ansi
		 * @see mshop/order/manager/service/count/ansi
		 */

		/** mshop/order/manager/service/aggregateavg/mysql
		 * Computes the average of all values grouped by the key column and matched by the given criteria
		 *
		 * @param string SQL statement for aggregating the order service items and computing the average value
		 * @since 2017.10
		 * @see mshop/order/manager/service/aggregateavg/ansi
		 * @see mshop/order/manager/service/aggregate/mysql
		 */

		/** mshop/order/manager/service/aggregateavg/ansi
		 * Computes the average of all values grouped by the key column and matched by the given criteria
		 *
		 * @param string SQL statement for aggregating the order service items and computing the average value
		 * @since 2017.10
		 * @see mshop/order/manager/service/aggregate/ansi
		 */

		/** mshop/order/manager/service/aggregatesum/mysql
		 * Computes the sum of all values grouped by the key column and matched by the given criteria
		 *
		 * @param string SQL statement for aggregating the order service items and computing the sum
		 * @since 2017.10
		 * @see mshop/order/manager/service/aggregatesum/ansi
		 * @see mshop/order/manager/service/aggregate/mysql
		 */

		/** mshop/order/manager/service/aggregatesum/ansi
		 * Computes the sum of all values grouped by the key column and matched by the given criteria
		 *
		 * @param string SQL statement for aggregating the order service items and computing the sum
		 * @since 2017.10
		 * @see mshop/order/manager/service/aggregate/ansi
		 */

		$cfgkey = 'mshop/order/manager/service/aggregate';
		return $this->aggregateBase( $search, $key, $cfgkey, ['order.service'], $value, $type );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Order\Manager\Service\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$path = 'mshop/order/manager/service/submanagers';
		foreach( $this->context()->config()->get( $path, array( 'attribute' ) ) as $domain ) {
			$this->object()->getSubManager( $domain )->clear( $siteids );
		}

		return $this->clearBase( $siteids, 'mshop/order/manager/service/delete' );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Service\Iface New order service item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$context = $this->context();
		$priceManager = \Aimeos\MShop::create( $context, 'price' );

		$values['order.service.siteid'] = $values['order.service.siteid'] ?? $context->locale()->getSiteId();

		return $this->createItemBase( $priceManager->create(), $values );
	}


	/**
	 * Creates a new order service attribute item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Service\Attribute\Iface New order service attribute item object
	 */
	public function createAttributeItem( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->object()->getSubManager( 'attribute' )->create( $values );
	}


	/**
	 * Creates a new order service transaction item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Service\Transaction\Iface New order service transaction item object
	 */
	public function createTransaction( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->object()->getSubManager( 'transaction' )->create( $values );
	}


	/**
	 * Creates a filter object.
	 *
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @param bool $site TRUE for adding site criteria to limit items by the site of related items
	 * @return \Aimeos\Base\Criteria\Iface Returns the filter object
	 */
	public function filter( ?bool $default = false, bool $site = false ) : \Aimeos\Base\Criteria\Iface
	{
		$search = parent::filter( $default );
		$search->setSortations( [$search->sort( '+', 'order.service.id' )] );

		return $search;
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Order\Manager\Service\Iface Manager object for chaining method calls
	 */
	public function delete( $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/order/manager/service/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/order/manager/service/delete/ansi
		 */

		/** mshop/order/manager/service/delete/ansi
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
		 * @see mshop/order/manager/service/insert/ansi
		 * @see mshop/order/manager/service/update/ansi
		 * @see mshop/order/manager/service/newid/ansi
		 * @see mshop/order/manager/service/search/ansi
		 * @see mshop/order/manager/service/count/ansi
		 */
		$path = 'mshop/order/manager/service/delete';

		return $this->deleteItemsBase( $itemIds, $path );
	}


	/**
	 * Returns the order service item object for the given ID.
	 *
	 * @param string $id Order service ID
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Order\Item\Service\Iface Returns order base service item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function get( string $id, array $ref = [], ?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->getItemBase( 'order.service.id', $id, $ref, $default );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		$path = 'mshop/order/manager/service/submanagers';
		return $this->getResourceTypeBase( 'order/service', $path, array( 'attribute' ), $withsub );
	}


	/**
	 * Returns the search attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		/** mshop/order/manager/service/submanagers
		 * List of manager names that can be instantiated by the order base service manager
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
		 */
		$path = 'mshop/order/manager/service/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, array( 'attribute' ), $withsub );
	}


	/**
	 * Returns a new manager for order service extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation (from configuration or "Standard" if null)
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g attribute
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/order/manager/service/name
		 * Class name of the used order base service manager implementation
		 *
		 * Each default order base service manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\MShop\Order\Manager\Service\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\MShop\Order\Manager\Service\Myservice
		 *
		 * then you have to set the this configuration option:
		 *
		 *  mshop/order/manager/service/name = Myservice
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyService"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 */

		/** mshop/order/manager/service/decorators/excludes
		 * Excludes decorators added by the "common" option from the order base service manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the order base service manager.
		 *
		 *  mshop/order/manager/service/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
		 * "mshop/common/manager/decorators/default" for the order base service manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/service/decorators/global
		 * @see mshop/order/manager/service/decorators/local
		 */

		/** mshop/order/manager/service/decorators/global
		 * Adds a list of globally available decorators only to the order base service manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the order base
		 * service manager.
		 *
		 *  mshop/order/manager/service/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the order
		 * base service manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/service/decorators/excludes
		 * @see mshop/order/manager/service/decorators/local
		 */

		/** mshop/order/manager/service/decorators/local
		 * Adds a list of local decorators only to the order base service manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\MShop\Order\Manager\Service\Decorator\*") around the
		 * order base service manager.
		 *
		 *  mshop/order/manager/service/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\MShop\Order\Manager\Service\Decorator\Decorator2" only
		 * to the order base service manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/service/decorators/excludes
		 * @see mshop/order/manager/service/decorators/global
		 */

		return $this->getSubManagerBase( 'order', 'service/' . $manager, $name );
	}


	/**
	 * Adds or updates an order base service item to the storage.
	 *
	 * @param \Aimeos\MShop\Order\Item\Service\Iface $item Order base service object
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Order\Item\Service\Iface $item Updated item including the generated ID
	 */
	protected function saveItem( \Aimeos\MShop\Order\Item\Service\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Order\Item\Service\Iface
	{
		if( !$item->isModified() && !$item->getPrice()->isModified() )
		{
			$this->saveAttributeItems( $item, $fetch );
			$this->saveTransactions( $item, $fetch );
			return $item;
		}

		$context = $this->context();
		$conn = $context->db( $this->getResourceName() );

		$id = $item->getId();
		$price = $item->getPrice();
		$columns = $this->object()->getSaveAttributes();

		if( $id === null )
		{
			/** mshop/order/manager/service/insert/mysql
			 * Inserts a new order record into the database table
			 *
			 * @see mshop/order/manager/service/insert/ansi
			 */

			/** mshop/order/manager/service/insert/ansi
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
			 * order in the save() method, so the correct values are
			 * bound to the columns.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for inserting records
			 * @since 2014.03
			 * @see mshop/order/manager/service/update/ansi
			 * @see mshop/order/manager/service/newid/ansi
			 * @see mshop/order/manager/service/delete/ansi
			 * @see mshop/order/manager/service/search/ansi
			 * @see mshop/order/manager/service/count/ansi
			 */
			$path = 'mshop/order/manager/service/insert';
			$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ) );
		}
		else
		{
			/** mshop/order/manager/service/update/mysql
			 * Updates an existing order record in the database
			 *
			 * @see mshop/order/manager/service/update/ansi
			 */

			/** mshop/order/manager/service/update/ansi
			 * Updates an existing order record in the database
			 *
			 * Items which already have an ID (i.e. the ID is not NULL) will
			 * be updated in the database.
			 *
			 * The SQL statement must be a string suitable for being used as
			 * prepared statement. It must include question marks for binding
			 * the values from the order item to the statement before they are
			 * sent to the database server. The order of the columns must
			 * correspond to the order in the save() method, so the
			 * correct values are bound to the columns.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for updating records
			 * @since 2014.03
			 * @see mshop/order/manager/service/insert/ansi
			 * @see mshop/order/manager/service/newid/ansi
			 * @see mshop/order/manager/service/delete/ansi
			 * @see mshop/order/manager/service/search/ansi
			 * @see mshop/order/manager/service/count/ansi
			 */
			$path = 'mshop/order/manager/service/update';
			$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ), false );
		}

		$idx = 1;
		$stmt = $this->getCachedStatement( $conn, $path, $sql );

		foreach( $columns as $name => $entry ) {
			$stmt->bind( $idx++, $item->get( $name ), \Aimeos\Base\Criteria\SQL::type( $entry->getType() ) );
		}

		$stmt->bind( $idx++, $item->getParentId(), \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		$stmt->bind( $idx++, $item->getServiceId() );
		$stmt->bind( $idx++, $item->getType() );
		$stmt->bind( $idx++, $item->getCode() );
		$stmt->bind( $idx++, $item->getName() );
		$stmt->bind( $idx++, $item->getMediaUrl() );
		$stmt->bind( $idx++, $price->getCurrencyId() );
		$stmt->bind( $idx++, $price->getValue() );
		$stmt->bind( $idx++, $price->getCosts() );
		$stmt->bind( $idx++, $price->getRebate() );
		$stmt->bind( $idx++, $price->getTaxValue() );
		$stmt->bind( $idx++, json_encode( $price->getTaxRates(), JSON_FORCE_OBJECT ) );
		$stmt->bind( $idx++, $price->getTaxFlag(), \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		$stmt->bind( $idx++, (int) $item->getPosition(), \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		$stmt->bind( $idx++, $context->datetime() ); // mtime
		$stmt->bind( $idx++, $context->editor() );
		$stmt->bind( $idx++, $this->siteId( $item->getSiteId(), \Aimeos\MShop\Locale\Manager\Base::SITE_SUBTREE ) );

		if( $id !== null ) {
			$stmt->bind( $idx++, $id, \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		} else {
			$stmt->bind( $idx++, $context->datetime() ); // ctime
		}

		$stmt->execute()->finish();

		if( $id === null && $fetch === true )
		{
			/** mshop/order/manager/service/newid/mysql
			 * Retrieves the ID generated by the database when inserting a new record
			 *
			 * @see mshop/order/manager/service/newid/ansi
			 */

			/** mshop/order/manager/service/newid/ansi
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
			 * @see mshop/order/manager/service/insert/ansi
			 * @see mshop/order/manager/service/update/ansi
			 * @see mshop/order/manager/service/delete/ansi
			 * @see mshop/order/manager/service/search/ansi
			 * @see mshop/order/manager/service/count/ansi
			 */
			$path = 'mshop/order/manager/service/newid';
			$id = $this->newId( $conn, $path );
		}

		$item->setId( $id );

		$this->saveAttributeItems( $item, $fetch );
		$this->saveTransactions( $item, $fetch );

		return $item;
}


	/**
	 * Searches for order service items based on the given criteria.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Order\Item\Service\Iface with ids as keys
	 */
	public function search( \Aimeos\Base\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$context = $this->context();
		$priceManager = \Aimeos\MShop::create( $context, 'price' );

		$conn = $context->db( $this->getResourceName() );
		$map = $items = $servItems = [];

		$required = array( 'order.service' );

		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
		$level = $context->config()->get( 'mshop/order/manager/sitemode', $level );

		/** mshop/order/manager/service/search/mysql
		 * Retrieves the records matched by the given criteria in the database
		 *
		 * @see mshop/order/manager/service/search/ansi
		 */

		/** mshop/order/manager/service/search/ansi
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
		 * @since 2014.03
		 * @see mshop/order/manager/service/insert/ansi
		 * @see mshop/order/manager/service/update/ansi
		 * @see mshop/order/manager/service/newid/ansi
		 * @see mshop/order/manager/service/delete/ansi
		 * @see mshop/order/manager/service/count/ansi
		 */
		$cfgPathSearch = 'mshop/order/manager/service/search';

		/** mshop/order/manager/service/count/mysql
		 * Counts the number of records matched by the given criteria in the database
		 *
		 * @see mshop/order/manager/service/count/ansi
		 */

		/** mshop/order/manager/service/count/ansi
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
		 * @see mshop/order/manager/service/insert/ansi
		 * @see mshop/order/manager/service/update/ansi
		 * @see mshop/order/manager/service/newid/ansi
		 * @see mshop/order/manager/service/delete/ansi
		 * @see mshop/order/manager/service/search/ansi
		 */
		$cfgPathCount = 'mshop/order/manager/service/count';

		$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount,
			$required, $total, $level );

		try
		{
			while( $row = $results->fetch() )
			{
				if( ( $row['order.service.taxrates'] = json_decode( $row['order.service.taxrates'], true ) ) === null ) {
					$row['order.service.taxrates'] = [];
				}

				$price = $priceManager->create( [
					'price.currencyid' => $row['order.service.currencyid'],
					'price.taxrates' => $row['order.service.taxrates'],
					'price.value' => $row['order.service.price'],
					'price.costs' => $row['order.service.costs'],
					'price.rebate' => $row['order.service.rebate'],
					'price.taxflag' => $row['order.service.taxflag'],
					'price.taxvalue' => $row['order.service.taxvalue'],
				] );

				$map[$row['order.service.id']] = ['price' => $price, 'item' => $row];
			}
		}
		catch( \Exception $e )
		{
			$results->finish();
			throw $e;
		}


		if( isset( $ref['service'] ) || in_array( 'service', $ref ) )
		{
			$ids = [];
			foreach( $map as $list ) {
				$ids[] = $list['item']['order.service.serviceid'] ?? null;
			}

			$manager = \Aimeos\MShop::create( $context, 'service' );
			$search = $manager->filter()->slice( 0, count( $ids ) )->add( ['service.id' => array_filter( $ids )] );
			$servItems = $manager->search( $search, $ref );
		}

		$attributes = $this->getAttributeItems( array_keys( $map ) );
		$transactions = $this->getTransactions( array_keys( $map ) );

		foreach( $map as $id => $list )
		{
			$servItem = $servItems[$list['item']['order.service.serviceid'] ?? null] ?? null;
			$item = $this->createItemBase(
				$list['price'], $list['item'], $attributes[$id] ?? [], $transactions[$id] ?? [], $servItem
			);

			if( $item = $this->applyFilter( $item ) ) {
				$items[$id] = $item;
			}
		}

		return map( $items );
	}


	/**
	 * Creates a new order service item object initialized with given parameters.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Price object
	 * @param array $values Associative list of values from the database
	 * @param \Aimeos\MShop\Order\Item\Service\Attribute\Iface[] $attributes List of order service attribute items
	 * @param \Aimeos\MShop\Order\Item\Service\Transaction\Iface[] $transactions List of order service transaction items
	 * @param \Aimeos\MShop\Service\Item\Iface|null $servItem Original service item
	 * @return \Aimeos\MShop\Order\Item\Service\Iface Order service item
	 */
	protected function createItemBase( \Aimeos\MShop\Price\Item\Iface $price, array $values = [], array $attributes = [],
		array $transactions = [], ?\Aimeos\MShop\Service\Item\Iface $servItem = null ) : \Aimeos\MShop\Order\Item\Service\Iface
	{
		return new \Aimeos\MShop\Order\Item\Service\Standard( $price, $values, $attributes, $transactions, $servItem );
	}


	/**
	 * Searches for attribute items connected with order service item.
	 *
	 * @param string[] $ids List of order service item IDs
	 * @return array Associative list of order service IDs as keys and order service attribute items
	 *  implementing \Aimeos\MShop\Order\Item\Service\Attribute\Iface as values
	 */
	protected function getAttributeItems( array $ids ) : array
	{
		$manager = $this->object()->getSubManager( 'attribute' );
		$search = $manager->filter()->slice( 0, 0x7fffffff );
		$search->setConditions( $search->compare( '==', 'order.service.attribute.parentid', $ids ) );

		$result = [];
		foreach( $manager->search( $search ) as $item ) {
			$result[$item->getParentId()][$item->getId()] = $item;
		}

		return $result;
	}


	/**
	 * Searches for transaction items connected with order service item.
	 *
	 * @param string[] $ids List of order service item IDs
	 * @return array Associative list of order service IDs as keys and order service transaction items
	 *  implementing \Aimeos\MShop\Order\Item\Service\Transaction\Iface as values
	 */
	protected function getTransactions( array $ids ) : array
	{
		$manager = $this->object()->getSubManager( 'transaction' );
		$search = $manager->filter()->slice( 0, 0x7fffffff );
		$search->setConditions( $search->compare( '==', 'order.service.transaction.parentid', $ids ) );

		$result = [];
		foreach( $manager->search( $search ) as $item ) {
			$result[$item->getParentId()][$item->getId()] = $item;
		}

		return $result;
	}


	/**
	 * Saves the attribute items included in the order service item
	 *
	 * @param \Aimeos\MShop\Order\Item\Service\Iface $item Order service item with attribute items
	 * @param bool $fetch True if the new ID should be set in the attribute item
	 * @return \Aimeos\MShop\Order\Item\Service\Iface Object with saved attribute items and IDs
	 */
	protected function saveAttributeItems( \Aimeos\MShop\Order\Item\Service\Iface $item, bool $fetch ) : \Aimeos\MShop\Order\Item\Service\Iface
	{
		$attrItems = $item->getAttributeItems();

		foreach( $attrItems as $key => $attrItem )
		{
			if( $attrItem->getType() === 'session' )
			{
				unset( $attrItems[$key] );
				continue;
			}

			if( $attrItem->getParentId() != $item->getId() ) {
				$attrItem->setId( null ); // create new property item if copied
			}

			$attrItem->setParentId( $item->getId() );
		}

		$this->object()->getSubManager( 'attribute' )->save( $attrItems, $fetch );
		return $item;
	}


	/**
	 * Saves the transaction items included in the order service item
	 *
	 * @param \Aimeos\MShop\Order\Item\Service\Iface $item Order service item with transaction items
	 * @param bool $fetch True if the new ID should be set in the transaction item
	 * @return \Aimeos\MShop\Order\Item\Service\Iface Object with saved transaction items and IDs
	 */
	protected function saveTransactions( \Aimeos\MShop\Order\Item\Service\Iface $item, bool $fetch ) : \Aimeos\MShop\Order\Item\Service\Iface
	{
		$list = $item->getTransactions();

		foreach( $list as $key => $txItem )
		{
			if( $txItem->getParentId() != $item->getId() ) {
				$txItem->setId( null ); // create new property item if copied
			}

			$txItem->setParentId( $item->getId() );
		}

		$this->object()->getSubManager( 'transaction' )->save( $list, $fetch );
		return $item;
	}
}
