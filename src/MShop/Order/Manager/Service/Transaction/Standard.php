<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Manager\Service\Transaction;


/**
 * Order base service manager.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Order\Manager\Service\Transaction\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private array $searchConfig = array(
		'order.service.transaction.id' => array(
			'code' => 'order.service.transaction.id',
			'internalcode' => 'mordsetx."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_order_service_tx" AS mordsetx ON ( mordse."id" = mordsetx."parentid" )' ),
			'label' => 'Service transaction ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'order.service.transaction.siteid' => array(
			'code' => 'order.service.transaction.siteid',
			'internalcode' => 'mordsetx."siteid"',
			'label' => 'Service transaction site ID',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.service.transaction.parentid' => array(
			'code' => 'order.service.transaction.parentid',
			'internalcode' => 'mordsetx."parentid"',
			'label' => 'Service ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'order.service.transaction.type' => array(
			'code' => 'order.service.transaction.type',
			'internalcode' => 'mordsetx."type"',
			'label' => 'Service transaction type',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'order.service.transaction.currencyid' => array(
			'code' => 'order.service.transaction.currencyid',
			'internalcode' => 'mordsetx."currencyid"',
			'label' => 'Service currencyid code',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'order.service.transaction.price' => array(
			'code' => 'order.service.transaction.price',
			'internalcode' => 'mordsetx."price"',
			'label' => 'Service price',
			'type' => 'decimal',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'order.service.transaction.costs' => array(
			'code' => 'order.service.transaction.costs',
			'internalcode' => 'mordsetx."costs"',
			'label' => 'Service shipping',
			'type' => 'decimal',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'order.service.transaction.rebate' => array(
			'code' => 'order.service.transaction.rebate',
			'internalcode' => 'mordsetx."rebate"',
			'label' => 'Service rebate',
			'type' => 'decimal',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'order.service.transaction.taxvalue' => array(
			'code' => 'order.service.transaction.taxvalue',
			'internalcode' => 'mordsetx."tax"',
			'label' => 'Service tax value',
			'type' => 'decimal',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'order.service.transaction.taxflag' => array(
			'code' => 'order.service.transaction.taxflag',
			'internalcode' => 'mordsetx."taxflag"',
			'label' => 'Service tax flag (0=net, 1=gross)',
			'type' => 'integer',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_INT,
		),
		'order.service.transaction.config' => array(
			'code' => 'order.service.transaction.config',
			'internalcode' => 'mordsetx."config"',
			'label' => 'Transaction data',
			'type' => 'json',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'order.service.transaction.status' => array(
			'code' => 'order.service.transaction.status',
			'internalcode' => 'mordsetx."status"',
			'label' => 'Transaction status',
			'type' => 'integer',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_INT,
		),
		'order.service.transaction.ctime' => array(
			'code' => 'order.service.transaction.ctime',
			'internalcode' => 'mordsetx."ctime"',
			'label' => 'Service transaction create date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.service.transaction.mtime' => array(
			'code' => 'order.service.transaction.mtime',
			'internalcode' => 'mordsetx."mtime"',
			'label' => 'Service transaction modify date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.service.transaction.editor' => array(
			'code' => 'order.service.transaction.editor',
			'internalcode' => 'mordsetx."editor"',
			'label' => 'Service transaction editor',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
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
		/** mshop/order/manager/service/transaction/aggregate/mysql
		 * Counts the number of records grouped by the values in the key column and matched by the given criteria
		 *
		 * @see mshop/order/manager/service/transaction/aggregate/ansi
		 */

		/** mshop/order/manager/service/transaction/aggregate/ansi
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
		 * @since 2023.01
		 * @see mshop/order/manager/service/transaction/insert/ansi
		 * @see mshop/order/manager/service/transaction/update/ansi
		 * @see mshop/order/manager/service/transaction/newid/ansi
		 * @see mshop/order/manager/service/transaction/delete/ansi
		 * @see mshop/order/manager/service/transaction/search/ansi
		 * @see mshop/order/manager/service/transaction/count/ansi
		 */
		$cfgkey = 'mshop/order/manager/service/transaction/aggregate';
		return $this->aggregateBase( $search, $key, $cfgkey, ['order.service.transaction'], $value, $type );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Order\Manager\Service\Transaction\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$path = 'mshop/order/manager/service/transaction/submanagers';
		foreach( $this->context()->config()->get( $path, [] ) as $domain ) {
			$this->object()->getSubManager( $domain )->clear( $siteids );
		}

		return $this->clearBase( $siteids, 'mshop/order/manager/service/transaction/delete' );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Service\Transaction\Iface New order service transaction item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$context = $this->context();

		$price = \Aimeos\MShop::create( $context, 'price' )->create();
		$values['order.service.transaction.siteid'] = $values['order.service.transaction.siteid'] ?? $context->locale()->getSiteId();

		return $this->createItemBase( $price, $values );
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Order\Manager\Service\Transaction\Iface Manager object for chaining method calls
	 */
	public function delete( $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/order/manager/service/transaction/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/order/manager/service/transaction/delete/ansi
		 */

		/** mshop/order/manager/service/transaction/delete/ansi
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
		 * @since 2023.01
		 * @see mshop/order/manager/service/transaction/insert/ansi
		 * @see mshop/order/manager/service/transaction/update/ansi
		 * @see mshop/order/manager/service/transaction/newid/ansi
		 * @see mshop/order/manager/service/transaction/search/ansi
		 * @see mshop/order/manager/service/transaction/count/ansi
		 */
		$path = 'mshop/order/manager/service/transaction/delete';

		return $this->deleteItemsBase( $itemIds, $path );
	}


	/**
	 * Returns the transaction object for the given ID.
	 *
	 * @param string $id Order service transaction ID
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Order\Item\Service\Transaction\Iface Order base service transaction item of the given ID
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function get( string $id, array $ref = [], ?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->getItemBase( 'order.service.transaction.id', $id, $ref, $default );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		$path = 'mshop/order/manager/service/transaction/submanagers';
		return $this->getResourceTypeBase( 'order/service/transaction', $path, [], $withsub );
	}


	/**
	 * Returns the transactions that can be used for searching.
	 *
	 * @param bool $withsub Return also transactions of sub-managers if true
	 * @return \Aimeos\Base\Criteria\Transaction\Iface[] List of search transaction items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		/** mshop/order/manager/service/transaction/submanagers
		 * List of manager names that can be instantiated by the order base service transaction manager
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
		 * @since 2023.01
		 */
		$path = 'mshop/order/manager/service/transaction/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, [], $withsub );
	}


	/**
	 * Returns a new manager for order service extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation (from configuration or "Standard" if null)
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g transaction
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/order/manager/service/transaction/name
		 * Class name of the used order base service transaction manager implementation
		 *
		 * Each default order base service transaction manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\MShop\Order\Manager\Service\Transaction\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\MShop\Order\Manager\Service\Transaction\Mytransaction
		 *
		 * then you have to set the this configuration option:
		 *
		 *  mshop/order/manager/service/transaction/name = Mytransaction
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyTransaction"!
		 *
		 * @param string Last part of the class name
		 * @since 2023.01
		 */

		/** mshop/order/manager/service/transaction/decorators/excludes
		 * Excludes decorators added by the "common" option from the order base service transaction manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the order base service transaction manager.
		 *
		 *  mshop/order/manager/service/transaction/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
		 * "mshop/common/manager/decorators/default" for the order base service transaction manager.
		 *
		 * @param array List of decorator names
		 * @since 2023.01
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/service/transaction/decorators/global
		 * @see mshop/order/manager/service/transaction/decorators/local
		 */

		/** mshop/order/manager/service/transaction/decorators/global
		 * Adds a list of globally available decorators only to the order base service transaction manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the order base
		 * service transaction manager.
		 *
		 *  mshop/order/manager/service/transaction/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the order
		 * base service transaction manager.
		 *
		 * @param array List of decorator names
		 * @since 2023.01
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/service/transaction/decorators/excludes
		 * @see mshop/order/manager/service/transaction/decorators/local
		 */

		/** mshop/order/manager/service/transaction/decorators/local
		 * Adds a list of local decorators only to the order base service transaction manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\MShop\Order\Manager\Service\Transaction\Decorator\*")
		 * around the order base service transaction manager.
		 *
		 *  mshop/order/manager/service/transaction/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\MShop\Order\Manager\Service\Transaction\Decorator\Decorator2"
		 * only to the order base service transaction manager.
		 *
		 * @param array List of decorator names
		 * @since 2023.01
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/service/transaction/decorators/excludes
		 * @see mshop/order/manager/service/transaction/decorators/global
		 */

		return $this->getSubManagerBase( 'order', 'service/transaction/' . $manager, $name );
	}


	/**
	 * Adds or updates an order service transaction item to the storage.
	 *
	 * @param \Aimeos\MShop\Order\Item\Service\Transaction\Iface $item Order service transaction object
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Order\Item\Service\Transaction\Iface $item Updated item including the generated ID
	 */
	protected function saveItem( \Aimeos\MShop\Order\Item\Service\Transaction\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Order\Item\Service\Transaction\Iface
	{
		if( !$item->isModified() ) {
			return $item;
		}

		$context = $this->context();
		$conn = $context->db( $this->getResourceName() );

		$id = $item->getId();
		$date = date( 'Y-m-d H:i:s' );
		$columns = $this->object()->getSaveAttributes();

		if( $id === null )
		{
			/** mshop/order/manager/service/transaction/insert/mysql
			 * Inserts a new order record into the database table
			 *
			 * @see mshop/order/manager/service/transaction/insert/ansi
			 */

			/** mshop/order/manager/service/transaction/insert/ansi
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
			 * @since 2023.01
			 * @see mshop/order/manager/service/transaction/update/ansi
			 * @see mshop/order/manager/service/transaction/newid/ansi
			 * @see mshop/order/manager/service/transaction/delete/ansi
			 * @see mshop/order/manager/service/transaction/search/ansi
			 * @see mshop/order/manager/service/transaction/count/ansi
			 */
			$path = 'mshop/order/manager/service/transaction/insert';
			$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ) );
		}
		else
		{
			/** mshop/order/manager/service/transaction/update/mysql
			 * Updates an existing order record in the database
			 *
			 * @see mshop/order/manager/service/transaction/update/ansi
			 */

			/** mshop/order/manager/service/transaction/update/ansi
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
			 * @since 2023.01
			 * @see mshop/order/manager/service/transaction/insert/ansi
			 * @see mshop/order/manager/service/transaction/newid/ansi
			 * @see mshop/order/manager/service/transaction/delete/ansi
			 * @see mshop/order/manager/service/transaction/search/ansi
			 * @see mshop/order/manager/service/transaction/count/ansi
			 */
			$path = 'mshop/order/manager/service/transaction/update';
			$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ), false );
		}

		$idx = 1;
		$price = $item->getPrice();
		$stmt = $this->getCachedStatement( $conn, $path, $sql );

		foreach( $columns as $name => $entry ) {
			$stmt->bind( $idx++, $item->get( $name ), $entry->getInternalType() );
		}

		$stmt->bind( $idx++, $item->getParentId(), \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		$stmt->bind( $idx++, $item->getType() );
		$stmt->bind( $idx++, $price->getCurrencyId() );
		$stmt->bind( $idx++, $price->getValue() );
		$stmt->bind( $idx++, $price->getCosts() );
		$stmt->bind( $idx++, $price->getRebate() );
		$stmt->bind( $idx++, $price->getTaxValue() );
		$stmt->bind( $idx++, $price->getTaxFlag(), \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		$stmt->bind( $idx++, $item->getStatus(), \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		$stmt->bind( $idx++, json_encode( $item->getConfig(), JSON_FORCE_OBJECT ) );
		$stmt->bind( $idx++, $date ); // mtime
		$stmt->bind( $idx++, $context->editor() );

		if( $id !== null ) {
			$stmt->bind( $idx++, $context->locale()->getSiteId() . '%' );
			$stmt->bind( $idx++, $id, \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		} else {
			$stmt->bind( $idx++, $this->siteId( $item->getSiteId(), \Aimeos\MShop\Locale\Manager\Base::SITE_SUBTREE ) );
			$stmt->bind( $idx++, $date ); // ctime
		}

		$stmt->execute()->finish();

		if( $id === null && $fetch === true )
		{
			/** mshop/order/manager/service/transaction/newid/mysql
			 * Retrieves the ID generated by the database when inserting a new record
			 *
			 * @see mshop/order/manager/service/transaction/newid/ansi
			 */

			/** mshop/order/manager/service/transaction/newid/ansi
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
			 * @since 2023.01
			 * @see mshop/order/manager/service/transaction/insert/ansi
			 * @see mshop/order/manager/service/transaction/update/ansi
			 * @see mshop/order/manager/service/transaction/delete/ansi
			 * @see mshop/order/manager/service/transaction/search/ansi
			 * @see mshop/order/manager/service/transaction/count/ansi
			 */
			$path = 'mshop/order/manager/service/transaction/newid';
			$id = $this->newId( $conn, $path );
		}

		$item->setId( $id );

		return $item;
	}


	/**
	 * Searches for order service transaction items based on the given criteria.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Order\Item\Service\Transaction\Iface with ids as keys
	 */
	public function search( \Aimeos\Base\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$context = $this->context();
		$priceManager = \Aimeos\MShop::create( $context, 'price' );

		$items = [];
		$conn = $context->db( $this->getResourceName() );
		$required = array( 'order.service.transaction' );

		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
		$level = $context->config()->get( 'mshop/order/manager/sitemode', $level );

		/** mshop/order/manager/service/transaction/search/mysql
		 * Retrieves the records matched by the given criteria in the database
		 *
		 * @see mshop/order/manager/service/transaction/search/ansi
		 */

		/** mshop/order/manager/service/transaction/search/ansi
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
		 * @since 2023.01
		 * @see mshop/order/manager/service/transaction/insert/ansi
		 * @see mshop/order/manager/service/transaction/update/ansi
		 * @see mshop/order/manager/service/transaction/newid/ansi
		 * @see mshop/order/manager/service/transaction/delete/ansi
		 * @see mshop/order/manager/service/transaction/count/ansi
		 */
		$cfgPathSearch = 'mshop/order/manager/service/transaction/search';

		/** mshop/order/manager/service/transaction/count/mysql
		 * Counts the number of records matched by the given criteria in the database
		 *
		 * @see mshop/order/manager/service/transaction/count/ansi
		 */

		/** mshop/order/manager/service/transaction/count/ansi
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
		 * @since 2023.01
		 * @see mshop/order/manager/service/transaction/insert/ansi
		 * @see mshop/order/manager/service/transaction/update/ansi
		 * @see mshop/order/manager/service/transaction/newid/ansi
		 * @see mshop/order/manager/service/transaction/delete/ansi
		 * @see mshop/order/manager/service/transaction/search/ansi
		 */
		$cfgPathCount = 'mshop/order/manager/service/transaction/count';

		$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

		try
		{
			while( ( $row = $results->fetch() ) !== null )
			{
				$id = $row['order.service.transaction.id'];
				$data = $row['order.service.transaction.config'];

				if( ( $row['order.service.transaction.config'] = json_decode( $data, true ) ) === null && $data !== 'null' )
				{
					$msg = 'Invalid JSON as result of search for ID "%2$s" in "%1$s": %3$s';
					$msg = sprintf( $msg, 'mshop_order_service_transaction.config', $id, $data );
					$this->context()->logger()->warning( $msg, 'core/order' );
				}

				// don't use fromArray() or set*() methods to avoid recalculation of tax value
				$price = $priceManager->create( [
					'price.currencyid' => $row['order.service.transaction.currencyid'],
					'price.value' => $row['order.service.transaction.price'],
					'price.costs' => $row['order.service.transaction.costs'],
					'price.rebate' => $row['order.service.transaction.rebate'],
					'price.taxflag' => $row['order.service.transaction.taxflag'],
					'price.taxvalue' => $row['order.service.transaction.taxvalue'],
				] );

				if( $item = $this->applyFilter( $this->createItemBase( $price, $row ) ) ) {
					$items[$id] = $item;
				}
			}
		}
		catch( \Exception $e )
		{
			$results->finish();
			throw $e;
		}

		return map( $items );
	}


	/**
	 * Creates a new order service transaction item object initialized with given parameters.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Transaction price item
	 * @param array $values Associative list of order service transaction key/value pairs
	 * @return \Aimeos\MShop\Order\Item\Service\Transaction\Standard New order service transaction item
	 */
	protected function createItemBase( \Aimeos\MShop\Price\Item\Iface $price, array $values = []
		) : \Aimeos\MShop\Order\Item\Service\Transaction\Iface
	{
		return new \Aimeos\MShop\Order\Item\Service\Transaction\Standard( $price, $values );
	}
}
