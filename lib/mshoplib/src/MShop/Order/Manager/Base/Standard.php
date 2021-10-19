<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	implements \Aimeos\MShop\Order\Manager\Base\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private $searchConfig = array(
		'order.base.id' => array(
			'code' => 'order.base.id',
			'internalcode' => 'mordba."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_order_base" AS mordba ON ( mord."baseid" = mordba."id" )' ),
			'label' => 'Order ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'order.base.siteid' => array(
			'code' => 'order.base.siteid',
			'internalcode' => 'mordba."siteid"',
			'label' => 'Order site ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.base.sitecode' => array(
			'code' => 'order.base.sitecode',
			'internalcode' => 'mordba."sitecode"',
			'label' => 'Order site code',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.base.customerid' => array(
			'code' => 'order.base.customerid',
			'internalcode' => 'mordba."customerid"',
			'label' => 'Order customer ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.customerref' => array(
			'code' => 'order.base.customerref',
			'internalcode' => 'mordba."customerref"',
			'label' => 'Order customer reference',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.languageid' => array(
			'code' => 'order.base.languageid',
			'internalcode' => 'mordba."langid"',
			'label' => 'Order language code',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.currencyid' => array(
			'code' => 'order.base.currencyid',
			'internalcode' => 'mordba."currencyid"',
			'label' => 'Order currencyid code',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.price' => array(
			'code' => 'order.base.price',
			'internalcode' => 'mordba."price"',
			'label' => 'Order price amount',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.costs' => array(
			'code' => 'order.base.costs',
			'internalcode' => 'mordba."costs"',
			'label' => 'Order shipping amount',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.rebate' => array(
			'code' => 'order.base.rebate',
			'internalcode' => 'mordba."rebate"',
			'label' => 'Order rebate amount',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.taxvalue' => array(
			'code' => 'order.base.taxvalue',
			'internalcode' => 'mordba."tax"',
			'label' => 'Order tax amount',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.taxflag' => array(
			'code' => 'order.base.taxflag',
			'internalcode' => 'mordba."taxflag"',
			'label' => 'Order tax flag (0=net, 1=gross)',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'order.base.comment' => array(
			'code' => 'order.base.comment',
			'internalcode' => 'mordba."comment"',
			'label' => 'Order comment',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.ctime' => array(
			'code' => 'order.base.ctime',
			'internalcode' => 'mordba."ctime"',
			'label' => 'Order create date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.base.mtime' => array(
			'code' => 'order.base.mtime',
			'internalcode' => 'mordba."mtime"',
			'label' => 'Order modify date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.base.editor' => array(
			'code' => 'order.base.editor',
			'internalcode' => 'mordba."editor"',
			'label' => 'Order editor',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
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
	 * @param array|string $key Search key or list of keys to aggregate items for
	 * @param string|null $value Search key for aggregating the value column
	 * @param string|null $type Type of the aggregation, empty string for count or "sum" or "avg" (average)
	 * @return \Aimeos\Map List of the search keys as key and the number of counted items as value
	 */
	public function aggregate( \Aimeos\MW\Criteria\Iface $search, $key, string $value = null, string $type = null ) : \Aimeos\Map
	{
		/** mshop/order/manager/base/aggregate/mysql
		 * Counts the number of records grouped by the values in the key column and matched by the given criteria
		 *
		 * @see mshop/order/manager/base/aggregate/ansi
		 */

		/** mshop/order/manager/base/aggregate/ansi
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
		 * @see mshop/order/manager/base/insert/ansi
		 * @see mshop/order/manager/base/update/ansi
		 * @see mshop/order/manager/base/newid/ansi
		 * @see mshop/order/manager/base/delete/ansi
		 * @see mshop/order/manager/base/search/ansi
		 * @see mshop/order/manager/base/count/ansi
		 */

		/** mshop/order/manager/base/aggregateavg/mysql
		 * Computes the average of all values grouped by the key column and matched by the given criteria
		 *
		 * @param string SQL statement for aggregating the order base items and computing the average value
		 * @since 2017.10
		 * @category Developer
		 * @see mshop/order/manager/base/aggregateavg/ansi
		 * @see mshop/order/manager/base/aggregate/mysql
		 */

		/** mshop/order/manager/base/aggregateavg/ansi
		 * Computes the average of all values grouped by the key column and matched by the given criteria
		 *
		 * @param string SQL statement for aggregating the order base items and computing the average value
		 * @since 2017.10
		 * @category Developer
		 * @see mshop/order/manager/base/aggregate/ansi
		 */

		/** mshop/order/manager/base/aggregatesum/mysql
		 * Computes the sum of all values grouped by the key column and matched by the given criteria
		 *
		 * @param string SQL statement for aggregating the order base items and computing the sum
		 * @since 2017.10
		 * @category Developer
		 * @see mshop/order/manager/base/aggregatesum/ansi
		 * @see mshop/order/manager/base/aggregate/mysql
		 */

		/** mshop/order/manager/base/aggregatesum/ansi
		 * Computes the sum of all values grouped by the key column and matched by the given criteria
		 *
		 * @param string SQL statement for aggregating the order base items and computing the sum
		 * @since 2017.10
		 * @category Developer
		 * @see mshop/order/manager/base/aggregate/ansi
		 */

		$cfgkey = 'mshop/order/manager/base/aggregate';
		return $this->aggregateBase( $search, $key, $cfgkey, ['order.base'], $value, $type );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Order\Manager\Base\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$path = 'mshop/order/manager/base/submanagers';
		$default = array( 'address', 'coupon', 'product', 'service' );

		foreach( $this->getContext()->getConfig()->get( $path, $default ) as $domain ) {
			$this->getObject()->getSubManager( $domain )->clear( $siteids );
		}

		return $this->clearBase( $siteids, 'mshop/order/manager/base/delete' );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Base\Iface New order base item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$context = $this->getContext();
		$locale = $context->getLocale();

		$values['order.base.siteid'] = $locale->getSiteId();
		$priceManager = \Aimeos\MShop::create( $context, 'price' );

		$base = $this->createItemBase( $priceManager->create(), clone $locale, $values );

		\Aimeos\MShop::create( $context, 'plugin' )->register( $base, 'order' );

		return $base;
	}


	/**
	 * Creates a search critera object
	 *
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @param bool $site TRUE to add site criteria to show orders with available products only
	 * @return \Aimeos\MW\Criteria\Iface New search criteria object
	 */
	public function filter( ?bool $default = false, bool $site = false ) : \Aimeos\MW\Criteria\Iface
	{
		$search = parent::filter( $default );
		$context = $this->getContext();

		if( $default !== false )
		{
			$search->setConditions( $search->and( [
				$search->compare( '==', 'order.base.customerid', $context->getUserId() ),
				$search->getConditions(),
			] ) );
		}

		if( $site === true )
		{
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_SUBTREE;
			$search->setConditions( $search->and( [
				$this->getSiteCondition( $search, 'order.base.product.siteid', $level ),
				$search->getConditions()
			] ) );
		}

		return $search;
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Order\Manager\Base\Iface Manager object for chaining method calls
	 */
	public function delete( $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/order/manager/base/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/order/manager/base/delete/ansi
		 */

		/** mshop/order/manager/base/delete/ansi
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
		 * @see mshop/order/manager/base/insert/ansi
		 * @see mshop/order/manager/base/update/ansi
		 * @see mshop/order/manager/base/newid/ansi
		 * @see mshop/order/manager/base/search/ansi
		 * @see mshop/order/manager/base/count/ansi
		 */
		$path = 'mshop/order/manager/base/delete';

		return $this->deleteItemsBase( $itemIds, $path );
	}


	/**
	 * Returns the order base item specified by the given ID.
	 *
	 * @param string $id Unique id of the order base
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Returns Order base item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function get( string $id, array $ref = [], ?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->getItemBase( 'order.base.id', $id, $ref, $default );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		$path = 'mshop/order/manager/base/submanagers';
		return $this->getResourceTypeBase( 'order/base', $path, ['address', 'coupon', 'product', 'service'], $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
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
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
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
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the order base
		 * manager.
		 *
		 *  mshop/order/manager/base/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the order
		 * base manager.
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
		 * ("\Aimeos\MShop\Order\Manager\Base\Decorator\*") around the order base
		 * manager.
		 *
		 *  mshop/order/manager/base/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\MShop\Order\Manager\Base\Decorator\Decorator2" only to the
		 * order base manager.
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
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $item Order base object (sub-items are not saved)
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Order\Item\Base\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Order\Item\Base\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Order\Item\Base\Iface
	{
		if( !$item->isModified() && !$item->getLocale()->isModified() ) {
			return $item;
		}

		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();
			$date = date( 'Y-m-d H:i:s' );
			$columns = $this->getObject()->getSaveAttributes();

			if( $id === null )
			{
				/** mshop/order/manager/base/insert/mysql
				 * Inserts a new order record into the database table
				 *
				 * @see mshop/order/manager/base/insert/ansi
				 */

				/** mshop/order/manager/base/insert/ansi
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
				 * @category Developer
				 * @see mshop/order/manager/base/update/ansi
				 * @see mshop/order/manager/base/newid/ansi
				 * @see mshop/order/manager/base/delete/ansi
				 * @see mshop/order/manager/base/search/ansi
				 * @see mshop/order/manager/base/count/ansi
				 */
				$path = 'mshop/order/manager/base/insert';
				$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ) );
			}
			else
			{
				/** mshop/order/manager/base/update/mysql
				 * Updates an existing order record in the database
				 *
				 * @see mshop/order/manager/base/update/ansi
				 */

				/** mshop/order/manager/base/update/ansi
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
				 * @category Developer
				 * @see mshop/order/manager/base/insert/ansi
				 * @see mshop/order/manager/base/newid/ansi
				 * @see mshop/order/manager/base/delete/ansi
				 * @see mshop/order/manager/base/search/ansi
				 * @see mshop/order/manager/base/count/ansi
				 */
				$path = 'mshop/order/manager/base/update';
				$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ), false );
			}

			$priceItem = $item->getPrice();
			$localeItem = $context->getLocale();

			$idx = 1;
			$stmt = $this->getCachedStatement( $conn, $path, $sql );

			foreach( $columns as $name => $entry ) {
				$stmt->bind( $idx++, $item->get( $name ), $entry->getInternalType() );
			}

			$stmt->bind( $idx++, $item->getCustomerId() );
			$stmt->bind( $idx++, $localeItem->getSiteItem()->getCode() );
			$stmt->bind( $idx++, $item->getLocale()->getLanguageId() );
			$stmt->bind( $idx++, $priceItem->getCurrencyId() );
			$stmt->bind( $idx++, $priceItem->getValue() );
			$stmt->bind( $idx++, $priceItem->getCosts() );
			$stmt->bind( $idx++, $priceItem->getRebate() );
			$stmt->bind( $idx++, $priceItem->getTaxValue() );
			$stmt->bind( $idx++, $priceItem->getTaxFlag(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $item->getCustomerReference() );
			$stmt->bind( $idx++, $item->getComment() );
			$stmt->bind( $idx++, $date ); // mtime
			$stmt->bind( $idx++, $context->getEditor() );
			$stmt->bind( $idx++, $localeItem->getSiteId() );

			if( $id !== null ) {
				$stmt->bind( $idx++, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			} else {
				$stmt->bind( $idx++, $date ); // ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** mshop/order/manager/base/newid/mysql
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * @see mshop/order/manager/base/newid/ansi
				 */

				/** mshop/order/manager/base/newid/ansi
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
				 * @see mshop/order/manager/base/insert/ansi
				 * @see mshop/order/manager/base/update/ansi
				 * @see mshop/order/manager/base/delete/ansi
				 * @see mshop/order/manager/base/search/ansi
				 * @see mshop/order/manager/base/count/ansi
				 */
				$path = 'mshop/order/manager/base/newid';
				$id = $this->newId( $conn, $path );
			}

			$item->setId( $id );

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $item;
	}


	/**
	 * Search for orders based on the given criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for, e.g.
	 *	"order/base/address", "order/base/coupon", "order/base/product", "order/base/service"
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Order\Item\Base\Iface with ids as keys
	 */
	public function search( \Aimeos\MW\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$context = $this->getContext();
		$priceManager = \Aimeos\MShop::create( $context, 'price' );
		$localeManager = \Aimeos\MShop::create( $context, 'locale' );

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		$map = $items = $custItems = [];

		try
		{
			$required = array( 'order.base' );

			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
			$level = $context->getConfig()->get( 'mshop/order/manager/sitemode', $level );

			/** mshop/order/manager/base/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see mshop/order/manager/base/search/ansi
			 */

			/** mshop/order/manager/base/search/ansi
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
			 * @see mshop/order/manager/base/insert/ansi
			 * @see mshop/order/manager/base/update/ansi
			 * @see mshop/order/manager/base/newid/ansi
			 * @see mshop/order/manager/base/delete/ansi
			 * @see mshop/order/manager/base/count/ansi
			 */
			$cfgPathSearch = 'mshop/order/manager/base/search';

			/** mshop/order/manager/base/count/mysql
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * @see mshop/order/manager/base/count/ansi
			 */

			/** mshop/order/manager/base/count/ansi
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
			 * @see mshop/order/manager/base/insert/ansi
			 * @see mshop/order/manager/base/update/ansi
			 * @see mshop/order/manager/base/newid/ansi
			 * @see mshop/order/manager/base/delete/ansi
			 * @see mshop/order/manager/base/search/ansi
			 */
			$cfgPathCount = 'mshop/order/manager/base/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount,
				$required, $total, $level );

			while( ( $row = $results->fetch() ) !== null ) {
				$map[$row['order.base.id']] = $row;
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		if( ( isset( $ref['customer'] ) || in_array( 'customer', $ref ) )
			&& !( $ids = map( $map )->col( 'order.base.customerid' )->filter() )->empty()
		) {
			$manager = \Aimeos\MShop::create( $context, 'customer' );
			$search = $manager->filter()->slice( 0, count( $ids ) )->add( ['customer.id' => $ids] );
			$custItems = $manager->search( $search, $ref );
		}

		foreach( $map as $id => $row )
		{
			// don't use fromArray() or set*() methods to avoid recalculation of tax value
			$price = $priceManager->create( [
				'price.currencyid' => $row['order.base.currencyid'],
				'price.value' => $row['order.base.price'],
				'price.costs' => $row['order.base.costs'],
				'price.rebate' => $row['order.base.rebate'],
				'price.taxflag' => $row['order.base.taxflag'],
				'price.taxvalue' => $row['order.base.taxvalue'],
			] );

			// you may need the site object! take care!
			$localeItem = $localeManager->create( [
				'locale.currencyid' => $row['order.base.currencyid'],
				'locale.languageid' => $row['order.base.languageid'],
				'locale.siteid' => $row['order.base.siteid'],
			] );

			$map[$id] = [$price, $localeItem, $row, $custItems[$row['order.base.customerid'] ?? null] ?? null];
		}

		return $this->buildItems( $map, $ref );
	}


	/**
	 * Creates a new basket containing the items from the order excluding the coupons.
	 * If the last parameter is ture, the items will be marked as new and
	 * modified so an additional order is stored when the basket is saved.
	 *
	 * @param string $id Base ID of the order to load
	 * @param int $parts Bitmap of the basket parts that should be loaded
	 * @param bool $fresh Create a new basket by copying the existing one and remove IDs
	 * @param bool $default True to use default criteria, false for no limitation
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Basket including all items
	 */
	public function load( string $id, int $parts = \Aimeos\MShop\Order\Item\Base\Base::PARTS_ALL, bool $fresh = false,
		bool $default = false ) : \Aimeos\MShop\Order\Item\Base\Iface
	{
		$search = $this->getObject()->filter( $default );
		$expr = [
			$search->compare( '==', 'order.base.id', $id ),
			$search->getConditions(),
		];
		$search->setConditions( $search->and( $expr ) );

		$context = $this->getContext();
		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$sitelevel = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
			$sitelevel = $context->getConfig()->get( 'mshop/order/manager/sitemode', $sitelevel );

			$cfgPathSearch = 'mshop/order/manager/base/search';
			$cfgPathCount = 'mshop/order/manager/base/count';
			$required = array( 'order.base' );
			$total = null;

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $sitelevel );

			if( ( $row = $results->fetch() ) === null )
			{
				$msg = $this->getContext()->translate( 'mshop', 'Order base item with order ID "%1$s" not found' );
				throw new \Aimeos\MShop\Order\Exception( sprintf( $msg, $id ) );
			}
			$results->finish();

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		$priceManager = \Aimeos\MShop::create( $context, 'price' );
		$localeManager = \Aimeos\MShop::create( $context, 'locale' );

		$price = $priceManager->create( [
			'price.currencyid' => $row['order.base.currencyid'],
			'price.value' => $row['order.base.price'],
			'price.costs' => $row['order.base.costs'],
			'price.rebate' => $row['order.base.rebate'],
			'price.taxflag' => $row['order.base.taxflag'],
			'price.taxvalue' => $row['order.base.taxvalue'],
		] );

		// you may need the site object! take care!
		$localeItem = $localeManager->create( [
			'locale.languageid' => $row['order.base.languageid'],
			'locale.currencyid' => $row['order.base.currencyid'],
			'locale.siteid' => $row['order.base.siteid'],
		] );

		if( $fresh === false ) {
			$basket = $this->loadItems( $id, $price, $localeItem, $row, $parts );
		} else {
			$basket = $this->loadFresh( $id, $price, $localeItem, $row, $parts );
		}

		$pluginManager = \Aimeos\MShop::create( $this->getContext(), 'plugin' );
		$pluginManager->register( $basket, 'order' );

		return $basket;
	}


	/**
	 * Saves the complete basket to the storage including the items attached.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object containing all information
	 * @param int $parts Bitmap of the basket parts that should be stored
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Stored order basket
	 */
	public function store( \Aimeos\MShop\Order\Item\Base\Iface $basket,
		int $parts = \Aimeos\MShop\Order\Item\Base\Base::PARTS_ALL ) : \Aimeos\MShop\Order\Item\Base\Iface
	{
		$basket = $this->getObject()->save( $basket );

		if( $parts & \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT
			|| $parts & \Aimeos\MShop\Order\Item\Base\Base::PARTS_COUPON
		) {
			$this->storeProducts( $basket );
		}

		if( $parts & \Aimeos\MShop\Order\Item\Base\Base::PARTS_COUPON ) {
			$this->storeCoupons( $basket );
		}

		if( $parts & \Aimeos\MShop\Order\Item\Base\Base::PARTS_ADDRESS ) {
			$this->storeAddresses( $basket );
		}

		if( $parts & \Aimeos\MShop\Order\Item\Base\Base::PARTS_SERVICE ) {
			$this->storeServices( $basket );
		}

		return $basket;
	}


	/**
	 * Creates the order base item objects from the map and adds the referenced items
	 *
	 * @param array $map Associative list of order base IDs as keys and list of price/locale/row as values
	 * @param string[] $ref Domain items that should be added as well, e.g.
	 *	"order/base/address", "order/base/coupon", "order/base/product", "order/base/service"
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Order\Item\Base\Iface with IDs as keys
	 */
	protected function buildItems( array $map, array $ref ) : \Aimeos\Map
	{
		$items = [];
		$baseIds = array_keys( $map );
		$addressMap = $couponMap = $productMap = $serviceMap = [];

		if( in_array( 'order/base/address', $ref ) ) {
			$addressMap = $this->getAddresses( $baseIds );
		}

		if( in_array( 'order/base/product', $ref ) ) {
			$productMap = $this->getProducts( $baseIds );
		}

		if( in_array( 'order/base/coupon', $ref ) ) {
			$couponMap = $this->getCoupons( $baseIds, false, $productMap );
		}

		if( in_array( 'order/base/service', $ref ) ) {
			$serviceMap = $this->getServices( $baseIds );
		}

		foreach( $map as $id => $list )
		{
			list( $price, $locale, $row, $custItem ) = $list;

			$addresses = $addressMap[$id] ?? [];
			$coupons = $couponMap[$id] ?? [];
			$products = $productMap[$id] ?? [];
			$services = $serviceMap[$id] ?? [];

			$item = $this->createItemBase( $price, $locale, $row, $products, $addresses, $services, $coupons, $custItem );

			if( $item = $this->applyFilter( $item ) ) {
				$items[$id] = $item;
			}
		}

		return map( $items );
	}


	/**
	 * Returns a new and empty order base item (shopping basket).
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Default price of the basket (usually 0.00)
	 * @param \Aimeos\MShop\Locale\Item\Iface $locale Locale item containing the site, language and currency
	 * @param array $values Associative list of key/value pairs containing, e.g. the order or user ID
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface[] $products List of ordered product items
	 * @param \Aimeos\MShop\Order\Item\Base\Address\Iface[] $addresses List of order address items
	 * @param \Aimeos\MShop\Order\Item\Base\Service\Iface[] $services List of order serviceitems
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface[] $coupons Associative list of coupon codes as keys and items as values
	 * @param \Aimeos\MShop\Customer\Item\Iface|null $custItem Customer item object if requested
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base object
	 */
	protected function createItemBase( \Aimeos\MShop\Price\Item\Iface $price, \Aimeos\MShop\Locale\Item\Iface $locale,
		array $values = [], array $products = [], array $addresses = [], array $services = [], array $coupons = [],
		?\Aimeos\MShop\Customer\Item\Iface $custItem = null ) : \Aimeos\MShop\Order\Item\Base\Iface
	{
		return new \Aimeos\MShop\Order\Item\Base\Standard( $price, $locale,
			$values, $products, $addresses, $services, $coupons, $custItem );
	}
}
