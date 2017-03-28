<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Manager\Base;


/**
 * Default implementation for order base manager.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard extends Base
{
	private $searchConfig = array(
		'order.base.id'=> array(
			'code'=>'order.base.id',
			'internalcode'=>'mordba."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_order_base" AS mordba ON ( mord."baseid" = mordba."id" )' ),
			'label'=>'Order base ID',
			'type'=> 'integer',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'order.base.siteid'=> array(
			'code'=>'order.base.siteid',
			'internalcode'=>'mordba."siteid"',
			'label'=>'Order base site ID',
			'type'=> 'integer',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'order.base.sitecode'=> array(
			'code'=>'order.base.sitecode',
			'internalcode'=>'mordba."sitecode"',
			'label'=>'Order base site code',
			'type'=> 'string',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.customerid'=> array(
			'code'=>'order.base.customerid',
			'internalcode'=>'mordba."customerid"',
			'label'=>'Order base customer ID',
			'type'=> 'string',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.languageid'=> array(
			'code'=>'order.base.languageid',
			'internalcode'=>'mordba."langid"',
			'label'=>'Order base language code',
			'type'=> 'string',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.currencyid'=> array(
			'code'=>'order.base.currencyid',
			'internalcode'=>'mordba."currencyid"',
			'label'=>'Order base currencyid code',
			'type'=> 'string',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.price'=> array(
			'code'=>'order.base.price',
			'internalcode'=>'mordba."price"',
			'label'=>'Order base price amount',
			'type'=> 'string',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.costs'=> array(
			'code'=>'order.base.costs',
			'internalcode'=>'mordba."costs"',
			'label'=>'Order base shipping amount',
			'type'=> 'string',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.rebate'=> array(
			'code'=>'order.base.rebate',
			'internalcode'=>'mordba."rebate"',
			'label'=>'Order base rebate amount',
			'type'=> 'string',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.taxvalue'=> array(
			'code'=>'order.base.taxvalue',
			'internalcode'=>'mordba."tax"',
			'label'=>'Order base tax amount',
			'type'=> 'string',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.taxflag'=> array(
			'code'=>'order.base.taxflag',
			'internalcode'=>'mordba."taxflag"',
			'label'=>'Order base tax flag (0=net price, 1=gross price)',
			'type'=> 'string',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'order.base.comment'=> array(
			'code'=>'order.base.comment',
			'internalcode'=>'mordba."comment"',
			'label'=>'Order base comment',
			'type'=> 'string',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.status'=> array(
			'code'=>'order.base.status',
			'internalcode'=>'mordba."status"',
			'label'=>'Order base status',
			'type'=> 'integer',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'order.base.mtime'=> array(
			'code'=>'order.base.mtime',
			'internalcode'=>'mordba."mtime"',
			'label'=>'Order base modification time',
			'type'=> 'datetime',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.ctime'=> array(
			'code'=>'order.base.ctime',
			'internalcode'=>'mordba."ctime"',
			'label'=>'Order base create date/time',
			'type'=> 'datetime',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.editor'=> array(
			'code'=>'order.base.editor',
			'internalcode'=>'mordba."editor"',
			'label'=>'Order base editor',
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
		/** mshop/order/manager/base/standard/aggregate/mysql
		 * Counts the number of records grouped by the values in the key column and matched by the given criteria
		 *
		 * @see mshop/order/manager/base/standard/aggregate/ansi
		 */

		/** mshop/order/manager/base/standard/aggregate/ansi
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
		 * @see mshop/order/manager/base/standard/insert/ansi
		 * @see mshop/order/manager/base/standard/update/ansi
		 * @see mshop/order/manager/base/standard/newid/ansi
		 * @see mshop/order/manager/base/standard/delete/ansi
		 * @see mshop/order/manager/base/standard/search/ansi
		 * @see mshop/order/manager/base/standard/count/ansi
		 */
		$cfgkey = 'mshop/order/manager/base/standard/aggregate';
		return $this->aggregateBase( $search, $key, $cfgkey, array( 'order.base' ) );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param integer[] $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'mshop/order/manager/base/submanagers';
		$default = array( 'address', 'coupon', 'product', 'service' );

		foreach( $this->getContext()->getConfig()->get( $path, $default ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->cleanupBase( $siteids, 'mshop/order/manager/base/standard/delete' );
	}


	/**
	 * Returns a new and empty order base item (shopping basket).
	 *
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base object
	 */
	public function createItem()
	{
		$context = $this->getContext();
		$priceManager = \Aimeos\MShop\Factory::createManager( $context, 'price' );
		$values = array( 'order.base.siteid' => $context->getLocale()->getSiteId() );

		$base = $this->createItemBase( $priceManager->createItem(), clone $context->getLocale(), $values );

		\Aimeos\MShop\Factory::createManager( $context, 'plugin' )->register( $base, 'order' );

		return $base;
	}


	/**
	 * Creates a search object
	 *
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MW\Criteria\Iface
	 */
	public function createSearch( $default = false )
	{
		$search = parent::createSearch( $default );

		if( $default !== false )
		{
			$userId = $this->getContext()->getUserId();
			$expr = [
				$search->compare( '==', 'order.base.customerid', $userId ),
				$search->getConditions(),
			];
			$search->setConditions( $search->combine( '&&', $expr ) );
		}

		return $search;
	}


	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		/** mshop/order/manager/base/standard/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/order/manager/base/standard/delete/ansi
		 */

		/** mshop/order/manager/base/standard/delete/ansi
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
		 * @see mshop/order/manager/base/standard/insert/ansi
		 * @see mshop/order/manager/base/standard/update/ansi
		 * @see mshop/order/manager/base/standard/newid/ansi
		 * @see mshop/order/manager/base/standard/search/ansi
		 * @see mshop/order/manager/base/standard/count/ansi
		 */
		$path = 'mshop/order/manager/base/standard/delete';
		$this->deleteItemsBase( $ids, $path );
	}


	/**
	 * Returns the order base item specified by the given ID.
	 *
	 * @param integer $id Unique id of the order base
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Returns Order base item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = [], $default = false )
	{
		return $this->getItemBase( 'order.base.id', $id, $ref, $default );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param boolean $withsub Return also the resource type of sub-managers if true
	 * @return array Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( $withsub = true )
	{
		$path = 'mshop/order/manager/base/submanagers';

		return $this->getResourceTypeBase( 'order/base', $path, array( 'address', 'coupon', 'product', 'service' ), $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** mshop/order/manager/base/submanagers
		 * List of manager names that can be instantiated by the order base manager
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
		$path = 'mshop/order/manager/base/submanagers';
		$default = array( 'address', 'coupon', 'product', 'service' );

		return $this->getSearchAttributesBase( $this->searchConfig, $path, $default, $withsub );
	}


	/**
	 * Returns a new manager for order base extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g address, coupon, product, service, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		/** mshop/order/manager/base/name
		 * Class name of the used order base manager implementation
		 *
		 * Each default order base manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\MShop\Order\Manager\Base\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\MShop\Order\Manager\Base\Mybase
		 *
		 * then you have to set the this configuration option:
		 *
		 *  mshop/order/manager/base/name = Mybase
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyBase"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */

		/** mshop/order/manager/base/decorators/excludes
		 * Excludes decorators added by the "common" option from the order base manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the order base manager.
		 *
		 *  mshop/order/manager/base/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
		 * "mshop/common/manager/decorators/default" for the order base manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/base/decorators/global
		 * @see mshop/order/manager/base/decorators/local
		 */

		/** mshop/order/manager/base/decorators/global
		 * Adds a list of globally available decorators only to the order base manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the order base manager.
		 *
		 *  mshop/order/manager/base/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the order controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/base/decorators/excludes
		 * @see mshop/order/manager/base/decorators/local
		 */

		/** mshop/order/manager/base/decorators/local
		 * Adds a list of local decorators only to the order base manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the order base manager.
		 *
		 *  mshop/order/manager/base/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator2" only to the order
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/base/decorators/excludes
		 * @see mshop/order/manager/base/decorators/global
		 */

		return $this->getSubManagerBase( 'order', 'base/' . $manager, $name );
	}


	/**
	 * Adds or updates an order base item in the storage.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Order base object (sub-items are not saved)
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Iface $item, $fetch = true )
	{
		$iface = '\\Aimeos\\MShop\\Order\\Item\\Base\\Iface';
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
				/** mshop/order/manager/base/standard/insert/mysql
				 * Inserts a new order record into the database table
				 *
				 * @see mshop/order/manager/base/standard/insert/ansi
				 */

				/** mshop/order/manager/base/standard/insert/ansi
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
				 * @see mshop/order/manager/base/standard/update/ansi
				 * @see mshop/order/manager/base/standard/newid/ansi
				 * @see mshop/order/manager/base/standard/delete/ansi
				 * @see mshop/order/manager/base/standard/search/ansi
				 * @see mshop/order/manager/base/standard/count/ansi
				 */
				$path = 'mshop/order/manager/base/standard/insert';
			}
			else
			{
				/** mshop/order/manager/base/standard/update/mysql
				 * Updates an existing order record in the database
				 *
				 * @see mshop/order/manager/base/standard/update/ansi
				 */

				/** mshop/order/manager/base/standard/update/ansi
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
				 * @see mshop/order/manager/base/standard/insert/ansi
				 * @see mshop/order/manager/base/standard/newid/ansi
				 * @see mshop/order/manager/base/standard/delete/ansi
				 * @see mshop/order/manager/base/standard/search/ansi
				 * @see mshop/order/manager/base/standard/count/ansi
				 */
				$path = 'mshop/order/manager/base/standard/update';
			}

			$priceItem = $item->getPrice();
			$localeItem = $context->getLocale();

			$stmt = $this->getCachedStatement( $conn, $path );

			$stmt->bind( 1, $localeItem->getSiteId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 2, $item->getCustomerId() );
			$stmt->bind( 3, $localeItem->getSite()->getCode() );
			$stmt->bind( 4, $item->getLocale()->getLanguageId() );
			$stmt->bind( 5, $priceItem->getCurrencyId() );
			$stmt->bind( 6, $priceItem->getValue() );
			$stmt->bind( 7, $priceItem->getCosts() );
			$stmt->bind( 8, $priceItem->getRebate() );
			$stmt->bind( 9, $priceItem->getTaxValue() );
			$stmt->bind( 10, $priceItem->getTaxFlag(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 11, $item->getComment() );
			$stmt->bind( 12, $item->getStatus(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 13, $date ); // mtime
			$stmt->bind( 14, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind( 15, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$item->setId( $id );
			} else {
				$stmt->bind( 15, $date ); // ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** mshop/order/manager/base/standard/newid/mysql
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * @see mshop/order/manager/base/standard/newid/ansi
				 */

				/** mshop/order/manager/base/standard/newid/ansi
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
				 * @see mshop/order/manager/base/standard/insert/ansi
				 * @see mshop/order/manager/base/standard/update/ansi
				 * @see mshop/order/manager/base/standard/delete/ansi
				 * @see mshop/order/manager/base/standard/search/ansi
				 * @see mshop/order/manager/base/standard/count/ansi
				 */
				$path = 'mshop/order/manager/base/standard/newid';
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
	 * Search for orders based on the given criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param integer|null &$total Number of items that are available in total
	 * @return array List of items implementing \Aimeos\MShop\Order\Item\Base\Iface
	 */
	public function searchItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], &$total = null )
	{
		$items = [];

		$context = $this->getContext();
		$priceManager = \Aimeos\MShop\Factory::createManager( $context, 'price' );
		$localeManager = \Aimeos\MShop\Factory::createManager( $context, 'locale' );

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$required = array( 'order.base' );
			$sitelevel = \Aimeos\MShop\Locale\Manager\Base::SITE_SUBTREE;

			/** mshop/order/manager/base/standard/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see mshop/order/manager/base/standard/search/ansi
			 */

			/** mshop/order/manager/base/standard/search/ansi
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
			 * @see mshop/order/manager/base/standard/insert/ansi
			 * @see mshop/order/manager/base/standard/update/ansi
			 * @see mshop/order/manager/base/standard/newid/ansi
			 * @see mshop/order/manager/base/standard/delete/ansi
			 * @see mshop/order/manager/base/standard/count/ansi
			 */
			$cfgPathSearch = 'mshop/order/manager/base/standard/search';

			/** mshop/order/manager/base/standard/count/mysql
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * @see mshop/order/manager/base/standard/count/ansi
			 */

			/** mshop/order/manager/base/standard/count/ansi
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
			 * @see mshop/order/manager/base/standard/insert/ansi
			 * @see mshop/order/manager/base/standard/update/ansi
			 * @see mshop/order/manager/base/standard/newid/ansi
			 * @see mshop/order/manager/base/standard/delete/ansi
			 * @see mshop/order/manager/base/standard/search/ansi
			 */
			$cfgPathCount = 'mshop/order/manager/base/standard/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount,
				$required, $total, $sitelevel );

			while( ( $row = $results->fetch() ) !== false )
			{
				$price = $priceManager->createItem();
				$price->setCurrencyId( $row['order.base.currencyid'] );
				$price->setValue( $row['order.base.price'] );
				$price->setCosts( $row['order.base.costs'] );
				$price->setRebate( $row['order.base.rebate'] );
				$price->setTaxValue( $row['order.base.taxvalue'] );
				$price->setTaxFlag( $row['order.base.taxflag'] );

				// you may need the site object! take care!
				$localeItem = $localeManager->createItem();
				$localeItem->setLanguageId( $row['order.base.languageid'] );
				$localeItem->setCurrencyId( $row['order.base.currencyid'] );
				$localeItem->setSiteId( $row['order.base.siteid'] );

				$items[$row['order.base.id']] = $this->createItemBase( $price, $localeItem, $row );
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
	 * Creates a new basket containing the items from the order excluding the coupons.
	 * If the last parameter is ture, the items will be marked as new and
	 * modified so an additional order is stored when the basket is saved.
	 *
	 * @param integer $id Base ID of the order to load
	 * @param integer $parts Bitmap of the basket parts that should be loaded
	 * @param boolean $fresh Create a new basket by copying the existing one and remove IDs
	 * @param boolean $default True to use default criteria, false for no limitation
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Basket including all items
	 */
	public function load( $id, $parts = \Aimeos\MShop\Order\Manager\Base\Base::PARTS_ALL, $fresh = false, $default = false )
	{
		$search = $this->createSearch( $default );
		$expr = [
			$search->compare( '==', 'order.base.id', $id ),
			$search->getConditions(),
		];
		$search->setConditions( $search->combine( '&&', $expr ) );

		$context = $this->getContext();
		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$sitelevel = \Aimeos\MShop\Locale\Manager\Base::SITE_SUBTREE;
			$cfgPathSearch = 'mshop/order/manager/base/standard/search';
			$cfgPathCount = 'mshop/order/manager/base/standard/count';
			$required = array( 'order.base' );
			$total = null;

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $sitelevel );

			if( ( $row = $results->fetch() ) === false ) {
				throw new \Aimeos\MShop\Order\Exception( sprintf( 'Order base item with order ID "%1$s" not found', $id ) );
			}
			$results->finish();

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		$priceManager = \Aimeos\MShop\Factory::createManager( $context, 'price' );
		$localeManager = \Aimeos\MShop\Factory::createManager( $context, 'locale' );

		$price = $priceManager->createItem();
		$price->setCurrencyId( $row['order.base.currencyid'] );
		$price->setValue( $row['order.base.price'] );
		$price->setCosts( $row['order.base.costs'] );
		$price->setRebate( $row['order.base.rebate'] );
		$price->setTaxFlag( $row['order.base.taxflag'] );
		$price->setTaxValue( $row['order.base.taxvalue'] );

		// you may need the site object! take care!
		$localeItem = $localeManager->createItem();
		$localeItem->setLanguageId( $row['order.base.languageid'] );
		$localeItem->setCurrencyId( $row['order.base.currencyid'] );
		$localeItem->setSiteId( $row['order.base.siteid'] );

		if( $fresh === false ) {
			$basket = $this->loadItems( $id, $price, $localeItem, $row, $parts );
		} else {
			$basket = $this->loadFresh( $id, $price, $localeItem, $row, $parts );
		}

		$pluginManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'plugin' );
		$pluginManager->register( $basket, 'order' );

		return $basket;
	}


	/**
	 * Saves the complete basket to the storage including the items attached.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object containing all information
	 * @param integer $parts Bitmap of the basket parts that should be stored
	 */
	public function store( \Aimeos\MShop\Order\Item\Base\Iface $basket, $parts = \Aimeos\MShop\Order\Manager\Base\Base::PARTS_ALL )
	{
		$this->saveItem( $basket );

		if( $parts & \Aimeos\MShop\Order\Manager\Base\Base::PARTS_PRODUCT
			|| $parts & \Aimeos\MShop\Order\Manager\Base\Base::PARTS_COUPON
		) {
			$this->storeProducts( $basket );
		}

		if( $parts & \Aimeos\MShop\Order\Manager\Base\Base::PARTS_COUPON ) {
			$this->storeCoupons( $basket );
		}

		if( $parts & \Aimeos\MShop\Order\Manager\Base\Base::PARTS_ADDRESS ) {
			$this->storeAddresses( $basket );
		}

		if( $parts & \Aimeos\MShop\Order\Manager\Base\Base::PARTS_SERVICE ) {
			$this->storeServices( $basket );
		}
	}
}
