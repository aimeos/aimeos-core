<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Manager;


/**
 * Default order manager implementation.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard extends Base
	implements \Aimeos\MShop\Order\Manager\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	/** mshop/order/manager/name
	 * Class name of the used order manager implementation
	 *
	 * Each default manager can be replace by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the manager factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\MShop\Order\Manager\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\MShop\Order\Manager\Mymanager
	 *
	 * then you have to set the this configuration option:
	 *
	 *  mshop/order/manager/name = Mymanager
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

	/** mshop/order/manager/decorators/excludes
	 * Excludes decorators added by the "common" option from the order manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to remove a decorator added via
	 * "mshop/common/manager/decorators/default" before they are wrapped
	 * around the order manager.
	 *
	 *  mshop/order/manager/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
	 * "mshop/common/manager/decorators/default" for the order manager.
	 *
	 * @param array List of decorator names
	 * @since 2014.03
	 * @category Developer
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/order/manager/decorators/global
	 * @see mshop/order/manager/decorators/local
	 */

	/** mshop/order/manager/decorators/global
	 * Adds a list of globally available decorators only to the order manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap global decorators
	 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the order manager.
	 *
	 *  mshop/order/manager/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the order
	 * manager.
	 *
	 * @param array List of decorator names
	 * @since 2014.03
	 * @category Developer
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/order/manager/decorators/excludes
	 * @see mshop/order/manager/decorators/local
	 */

	/** mshop/order/manager/decorators/local
	 * Adds a list of local decorators only to the order manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\MShop\Order\Manager\Decorator\*") around the order manager.
	 *
	 *  mshop/order/manager/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\MShop\Order\Manager\Decorator\Decorator2" only to the order
	 * manager.
	 *
	 * @param array List of decorator names
	 * @since 2014.03
	 * @category Developer
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/order/manager/decorators/excludes
	 * @see mshop/order/manager/decorators/global
	 */


	private array $searchConfig = array(
		'order.id' => array(
			'code' => 'order.id',
			'internalcode' => 'mord."id"',
			'label' => 'Invoice ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_INT,
		),
		'order.siteid' => array(
			'code' => 'order.siteid',
			'internalcode' => 'mord."siteid"',
			'label' => 'Invoice site ID',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.invoiceno' => array(
			'code' => 'order.invoiceno',
			'internalcode' => 'mord."invoiceno"',
			'label' => 'Invoice number',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'order.relatedid' => array(
			'code' => 'order.relatedid',
			'internalcode' => 'mord."relatedid"',
			'label' => 'Related invoice ID',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'order.channel' => array(
			'code' => 'order.channel',
			'internalcode' => 'mord."channel"',
			'label' => 'Order channel',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'order.datepayment' => array(
			'code' => 'order.datepayment',
			'internalcode' => 'mord."datepayment"',
			'label' => 'Purchase date',
			'type' => 'datetime',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'order.datedelivery' => array(
			'code' => 'order.datedelivery',
			'internalcode' => 'mord."datedelivery"',
			'label' => 'Delivery date',
			'type' => 'datetime',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'order.statusdelivery' => array(
			'code' => 'order.statusdelivery',
			'internalcode' => 'mord."statusdelivery"',
			'label' => 'Delivery status',
			'type' => 'integer',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_INT,
		),
		'order.statuspayment' => array(
			'code' => 'order.statuspayment',
			'internalcode' => 'mord."statuspayment"',
			'label' => 'Payment status',
			'type' => 'integer',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_INT,
		),
		'order.sitecode' => array(
			'code' => 'order.sitecode',
			'internalcode' => 'mord."sitecode"',
			'label' => 'Order site code',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.customerid' => array(
			'code' => 'order.customerid',
			'internalcode' => 'mord."customerid"',
			'label' => 'Order customer ID',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'order.customerref' => array(
			'code' => 'order.customerref',
			'internalcode' => 'mord."customerref"',
			'label' => 'Order customer reference',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'order.languageid' => array(
			'code' => 'order.languageid',
			'internalcode' => 'mord."langid"',
			'label' => 'Order language code',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'order.currencyid' => array(
			'code' => 'order.currencyid',
			'internalcode' => 'mord."currencyid"',
			'label' => 'Order currencyid code',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'order.price' => array(
			'code' => 'order.price',
			'internalcode' => 'mord."price"',
			'label' => 'Order price amount',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'order.costs' => array(
			'code' => 'order.costs',
			'internalcode' => 'mord."costs"',
			'label' => 'Order shipping amount',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'order.rebate' => array(
			'code' => 'order.rebate',
			'internalcode' => 'mord."rebate"',
			'label' => 'Order rebate amount',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'order.taxvalue' => array(
			'code' => 'order.taxvalue',
			'internalcode' => 'mord."tax"',
			'label' => 'Order tax amount',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'order.taxflag' => array(
			'code' => 'order.taxflag',
			'internalcode' => 'mord."taxflag"',
			'label' => 'Order tax flag (0=net, 1=gross)',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_INT,
		),
		'order.comment' => array(
			'code' => 'order.comment',
			'internalcode' => 'mord."comment"',
			'label' => 'Order comment',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'order.cdate' => array(
			'code' => 'order.cdate',
			'internalcode' => 'mord."cdate"',
			'label' => 'Create date',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'order.cmonth' => array(
			'code' => 'order.cmonth',
			'internalcode' => 'mord."cmonth"',
			'label' => 'Create month',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'order.cweek' => array(
			'code' => 'order.cweek',
			'internalcode' => 'mord."cweek"',
			'label' => 'Create week',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'order.cwday' => array(
			'code' => 'order.cwday',
			'internalcode' => 'mord."cwday"',
			'label' => 'Create weekday',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'order.chour' => array(
			'code' => 'order.chour',
			'internalcode' => 'mord."chour"',
			'label' => 'Create hour',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'order.ctime' => array(
			'code' => 'order.ctime',
			'internalcode' => 'mord."ctime"',
			'label' => 'Create date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.mtime' => array(
			'code' => 'order.mtime',
			'internalcode' => 'mord."mtime"',
			'label' => 'Modify date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.editor' => array(
			'code' => 'order.editor',
			'internalcode' => 'mord."editor"',
			'label' => 'Editor',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order:status' => array(
			'code' => 'order:status()',
			'internalcode' => '( SELECT COUNT(mordst_cs."parentid")
				FROM "mshop_order_status" AS mordst_cs
				WHERE mord."id" = mordst_cs."parentid" AND :site
				AND mordst_cs."type" = $1 AND mordst_cs."value" IN ( $2 ) )',
			'label' => 'Number of order status items, parameter(<type>,<value>)',
			'type' => 'integer',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
	);


	/**
	 * Creates the manager that will use the given context object.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object with required objects
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

		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
		$level = $context->config()->get( 'mshop/order/manager/sitemode', $level );

		$name = 'order:status';
		$expr = $this->siteString( 'mordst_cs."siteid"', $level );
		$this->searchConfig[$name]['internalcode'] = str_replace( ':site', $expr, $this->searchConfig[$name]['internalcode'] );
	}


	/**
	 * Counts the number items that are available for the values of the given key.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $search Search criteria
	 * @param array|string $key Search key or list of key to aggregate items for
	 * @param string|null $value Search key for aggregating the value column
	 * @param string|null $type Type of the aggregation, empty string for count or "sum" or "avg" (average)
	 * @return \Aimeos\Map List of the search keys as key and the number of counted items as value
	 */
	public function aggregate( \Aimeos\Base\Criteria\Iface $search, $key, string $value = null, string $type = null ) : \Aimeos\Map
	{
		/** mshop/order/manager/aggregate/mysql
		 * Counts the number of records grouped by the values in the key column and matched by the given criteria
		 *
		 * @see mshop/order/manager/aggregate/ansi
		 */

		/** mshop/order/manager/aggregate/ansi
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
		 * @see mshop/order/manager/insert/ansi
		 * @see mshop/order/manager/update/ansi
		 * @see mshop/order/manager/newid/ansi
		 * @see mshop/order/manager/delete/ansi
		 * @see mshop/order/manager/search/ansi
		 * @see mshop/order/manager/count/ansi
		 */

		/** mshop/order/manager/aggregateavg/mysql
		 * Computes the average of all values grouped by the key column and matched by the given criteria
		 *
		 * @param string SQL statement for aggregating the order items and computing the average value
		 * @since 2017.10
		 * @category Developer
		 * @see mshop/order/manager/aggregateavg/ansi
		 * @see mshop/order/manager/aggregate/mysql
		 */

		/** mshop/order/manager/aggregateavg/ansi
		 * Computes the average of all values grouped by the key column and matched by the given criteria
		 *
		 * @param string SQL statement for aggregating the order items and computing the average value
		 * @since 2017.10
		 * @category Developer
		 * @see mshop/order/manager/aggregate/ansi
		 */

		/** mshop/order/manager/aggregatesum/mysql
		 * Computes the sum of all values grouped by the key column and matched by the given criteria
		 *
		 * @param string SQL statement for aggregating the order items and computing the sum
		 * @since 2017.10
		 * @category Developer
		 * @see mshop/order/manager/aggregatesum/ansi
		 * @see mshop/order/manager/aggregate/mysql
		 */

		/** mshop/order/manager/aggregatesum/ansi
		 * Computes the sum of all values grouped by the key column and matched by the given criteria
		 *
		 * @param string SQL statement for aggregating the order items and computing the sum
		 * @since 2017.10
		 * @category Developer
		 * @see mshop/order/manager/aggregate/ansi
		 */

		$cfgkey = 'mshop/order/manager/aggregate';
		return $this->aggregateBase( $search, $key, $cfgkey, ['order'], $value, $type );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Order\Manager\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$path = 'mshop/order/manager/submanagers';
		$default = ['address', 'coupon', 'product', 'service', 'status'];

		foreach( $this->context()->config()->get( $path, $default ) as $domain ) {
			$this->object()->getSubManager( $domain )->clear( $siteids );
		}

		return $this->clearBase( $siteids, 'mshop/order/manager/delete' );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Iface New order item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$context = $this->context();
		$locale = $context->locale();

		$price = \Aimeos\MShop::create( $context, 'price' )->create();
		$values['order.siteid'] = $values['order.siteid'] ?? $locale->getSiteId();

		$base = $this->createItemBase( $price, clone $locale, $values );
		\Aimeos\MShop::create( $context, 'plugin' )->register( $base, 'order' );

		return $base;
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Order\Manager\Iface Manager object for chaining method calls
	 */
	public function delete( $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/order/manager/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/order/manager/delete/ansi
		 */

		/** mshop/order/manager/delete/ansi
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
		 * @see mshop/order/manager/insert/ansi
		 * @see mshop/order/manager/update/ansi
		 * @see mshop/order/manager/newid/ansi
		 * @see mshop/order/manager/search/ansi
		 * @see mshop/order/manager/count/ansi
		 */
		$path = 'mshop/order/manager/delete';

		return $this->deleteItemsBase( $itemIds, $path );
	}


	/**
	 * Creates a search critera object
	 *
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @param bool $site TRUE to add site criteria to show orders with available products only
	 * @return \Aimeos\Base\Criteria\Iface New search criteria object
	 */
	public function filter( ?bool $default = false, bool $site = false ) : \Aimeos\Base\Criteria\Iface
	{
		$search = parent::filter( $default );
		$context = $this->context();

		if( $default !== false ) {
			$search->add( ['order.customerid' => $context->user()] );
		}

		if( $site === true )
		{
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_SUBTREE;
			$search->add( $this->siteCondition( 'order.product.siteid', $level ) );
		}

		return $search;
	}


	/**
	 * Returns an order invoice item built from database values.
	 *
	 * @param string $id Unique id of the order invoice
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Order\Item\Iface Returns order invoice item of the given id
	 * @throws \Aimeos\MShop\Order\Exception If item couldn't be found
	 */
	public function get( string $id, array $ref = [], ?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->getItemBase( 'order.id', $id, $ref, $default );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		$path = 'mshop/order/manager/submanagers';
		$default = ['address', 'coupon', 'product', 'service', 'status'];

		return $this->getResourceTypeBase( 'order', $path, $default, $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		/** mshop/order/manager/submanagers
		 * List of manager names that can be instantiated by the order manager
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
		$path = 'mshop/order/manager/submanagers';
		$default = ['address', 'coupon', 'product', 'service', 'status'];

		return $this->getSearchAttributesBase( $this->searchConfig, $path, $default, $withsub );
	}


	/**
	 * Creates a one-time order in the storage from the given invoice object.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $item Order item with necessary values
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Order\Item\Iface $item Updated item including the generated ID
	 */
	protected function saveItem( \Aimeos\MShop\Order\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Order\Item\Iface
	{
		if( !$item->isModified() ) {
			return $this->saveBasket( $item );
		}

		if( empty( $item->getInvoiceNumber() ) && $item->getStatusPayment() >= \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED )
		{
			try {
				$item->setInvoiceNumber( $this->createInvoiceNumber( $item ) );
			} catch( \Exception $e ) { // redo on transaction deadlock
				$item->setInvoiceNumber( $this->createInvoiceNumber( $item ) );
			}
		}

		$context = $this->context();
		$conn = $context->db( $this->getResourceName() );

		$id = $item->getId();
		$date = date( 'Y-m-d H:i:s' );
		$columns = $this->object()->getSaveAttributes();

		if( $id === null )
		{
			/** mshop/order/manager/insert/mysql
			 * Inserts a new order record into the database table
			 *
			 * @see mshop/order/manager/insert/ansi
			 */

			/** mshop/order/manager/insert/ansi
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
			 * @see mshop/order/manager/update/ansi
			 * @see mshop/order/manager/newid/ansi
			 * @see mshop/order/manager/delete/ansi
			 * @see mshop/order/manager/search/ansi
			 * @see mshop/order/manager/count/ansi
			 */
			$path = 'mshop/order/manager/insert';
			$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ) );
		}
		else
		{
			/** mshop/order/manager/update/mysql
			 * Updates an existing order record in the database
			 *
			 * @see mshop/order/manager/update/ansi
			 */

			/** mshop/order/manager/update/ansi
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
			 * @see mshop/order/manager/insert/ansi
			 * @see mshop/order/manager/newid/ansi
			 * @see mshop/order/manager/delete/ansi
			 * @see mshop/order/manager/search/ansi
			 * @see mshop/order/manager/count/ansi
			 */
			$path = 'mshop/order/manager/update';
			$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ), false );
		}

		$idx = 1;
		$priceItem = $item->getPrice();
		$stmt = $this->getCachedStatement( $conn, $path, $sql );

		foreach( $columns as $name => $entry ) {
			$stmt->bind( $idx++, $item->get( $name ), $entry->getInternalType() );
		}

		$stmt->bind( $idx++, $item->getInvoiceNumber() );
		$stmt->bind( $idx++, $item->getChannel() );
		$stmt->bind( $idx++, $item->getDatePayment() );
		$stmt->bind( $idx++, $item->getDateDelivery() );
		$stmt->bind( $idx++, $item->getStatusDelivery(), \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		$stmt->bind( $idx++, $item->getStatusPayment(), \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		$stmt->bind( $idx++, $item->getRelatedId(), \Aimeos\Base\DB\Statement\Base::PARAM_STR );
		$stmt->bind( $idx++, $item->getCustomerId() );
		$stmt->bind( $idx++, $context->locale()->getSiteItem()->getCode() );
		$stmt->bind( $idx++, $item->locale()->getLanguageId() );
		$stmt->bind( $idx++, $priceItem->getCurrencyId() );
		$stmt->bind( $idx++, $priceItem->getValue() );
		$stmt->bind( $idx++, $priceItem->getCosts() );
		$stmt->bind( $idx++, $priceItem->getRebate() );
		$stmt->bind( $idx++, $priceItem->getTaxValue() );
		$stmt->bind( $idx++, $priceItem->getTaxFlag(), \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		$stmt->bind( $idx++, $item->getCustomerReference() );
		$stmt->bind( $idx++, $item->getComment() );
		$stmt->bind( $idx++, $date ); // mtime
		$stmt->bind( $idx++, $context->editor() );

		if( $id !== null ) {
			$stmt->bind( $idx++, $context->locale()->getSiteId() . '%' );
			$stmt->bind( $idx++, $id, \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		} else {
			$stmt->bind( $idx++, $this->siteId( $item->getSiteId(), \Aimeos\MShop\Locale\Manager\Base::SITE_SUBTREE ) );
			$stmt->bind( $idx++, $date ); // ctime
			$stmt->bind( $idx++, date( 'Y-m-d' ) ); // cdate
			$stmt->bind( $idx++, date( 'Y-m' ) ); // cmonth
			$stmt->bind( $idx++, date( 'Y-W' ) ); // cweek
			$stmt->bind( $idx++, date( 'w' ) ); // cwday
			$stmt->bind( $idx++, date( 'H' ) ); // chour
		}

		$stmt->execute()->finish();

		if( $id === null && $fetch === true )
		{
			/** mshop/order/manager/newid/mysql
			 * Retrieves the ID generated by the database when inserting a new record
			 *
			 * @see mshop/order/manager/newid/ansi
			 */

			/** mshop/order/manager/newid/ansi
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
			 * @see mshop/order/manager/insert/ansi
			 * @see mshop/order/manager/update/ansi
			 * @see mshop/order/manager/delete/ansi
			 * @see mshop/order/manager/search/ansi
			 * @see mshop/order/manager/count/ansi
			 */
			$path = 'mshop/order/manager/newid';
			$id = $this->newId( $conn, $path );
		}

		$item->setId( $id );

		$this->addStatus( $item );

		return $this->saveBasket( $item );
	}


	/**
	 * Searches for orders based on the given criteria.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Order\Item\Iface with ids as keys
	 */
	public function search( \Aimeos\Base\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$context = $this->context();
		$conn = $context->db( $this->getResourceName() );

		$priceManager = \Aimeos\MShop::create( $context, 'price' );
		$localeManager = \Aimeos\MShop::create( $context, 'locale' );

		$map = $custItems = [];
		$required = ['order'];

		/** mshop/order/manager/sitemode
		 * Mode how items from levels below or above in the site tree are handled
		 *
		 * By default, only items from the current site are fetched from the
		 * storage. If the ai-sites extension is installed, you can create a
		 * tree of sites. Then, this setting allows you to define for the
		 * whole order domain if items from parent sites are inherited,
		 * sites from child sites are aggregated or both.
		 *
		 * Available constants for the site mode are:
		 * * 0 = only items from the current site
		 * * 1 = inherit items from parent sites
		 * * 2 = aggregate items from child sites
		 * * 3 = inherit and aggregate items at the same time
		 *
		 * You also need to set the mode in the locale manager
		 * (mshop/locale/manager/sitelevel) to one of the constants.
		 * If you set it to the same value, it will work as described but you
		 * can also use different modes. For example, if inheritance and
		 * aggregation is configured the locale manager but only inheritance
		 * in the domain manager because aggregating items makes no sense in
		 * this domain, then items wil be only inherited. Thus, you have full
		 * control over inheritance and aggregation in each domain.
		 *
		 * @param int Constant from Aimeos\MShop\Locale\Manager\Base class
		 * @category Developer
		 * @since 2018.01
		 * @see mshop/locale/manager/sitelevel
		 */
		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
		$level = $context->config()->get( 'mshop/order/manager/sitemode', $level );

		/** mshop/order/manager/search/mysql
		 * Retrieves the records matched by the given criteria in the database
		 *
		 * @see mshop/order/manager/search/ansi
		 */

		/** mshop/order/manager/search/ansi
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
		 * @see mshop/order/manager/insert/ansi
		 * @see mshop/order/manager/update/ansi
		 * @see mshop/order/manager/newid/ansi
		 * @see mshop/order/manager/delete/ansi
		 * @see mshop/order/manager/count/ansi
		 */
		$cfgPathSearch = 'mshop/order/manager/search';

		/** mshop/order/manager/count/mysql
		 * Counts the number of records matched by the given criteria in the database
		 *
		 * @see mshop/order/manager/count/ansi
		 */

		/** mshop/order/manager/count/ansi
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
		 * @see mshop/order/manager/insert/ansi
		 * @see mshop/order/manager/update/ansi
		 * @see mshop/order/manager/newid/ansi
		 * @see mshop/order/manager/delete/ansi
		 * @see mshop/order/manager/search/ansi
		 */
		$cfgPathCount = 'mshop/order/manager/count';

		$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount,
			$required, $total, $level );

		try
		{
			while( ( $row = $results->fetch() ) !== null ) {
				$map[$row['order.id']] = $row;
			}
		}
		catch( \Exception $e )
		{
			$results->finish();
			throw $e;
		}


		if( ( isset( $ref['customer'] ) || in_array( 'customer', $ref ) )
			&& !( $ids = map( $map )->col( 'order.customerid' )->filter() )->empty()
		) {
			$manager = \Aimeos\MShop::create( $context, 'customer' );
			$search = $manager->filter()->slice( 0, count( $ids ) )->add( ['customer.id' => $ids] );
			$custItems = $manager->search( $search, $ref )->all();
		}

		foreach( $map as $id => $row )
		{
			// don't use fromArray() or set*() methods to avoid recalculation of tax value
			$price = $priceManager->create( [
				'price.currencyid' => $row['order.currencyid'],
				'price.value' => $row['order.price'],
				'price.costs' => $row['order.costs'],
				'price.rebate' => $row['order.rebate'],
				'price.taxflag' => $row['order.taxflag'],
				'price.taxvalue' => $row['order.taxvalue'],
			] );

			// you may need the site object! take care!
			$localeItem = $localeManager->create( [
				'locale.currencyid' => $row['order.currencyid'],
				'locale.languageid' => $row['order.languageid'],
				'locale.siteid' => $row['order.siteid'],
			] );

			$map[$id] = [$price, $localeItem, $row, $custItems[$row['order.customerid'] ?? null] ?? null];
		}

		return $this->buildItems( $map, $ref );
	}


	/**
	 * Returns a new manager for order extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g base, etc.
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->getSubManagerBase( 'order', $manager, $name );
	}


	/**
	 * Adds the new payment and delivery values to the order status log.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $item Order item object
	 */
	protected function addStatus( \Aimeos\MShop\Order\Item\Iface $item )
	{
		$statusManager = \Aimeos\MShop::create( $this->context(), 'order/status' );

		$statusItem = $statusManager->create();
		$statusItem->setParentId( $item->getId() );

		if( ( $status = $item->get( '.statuspayment' ) ) !== null && $status != $item->getStatusPayment() )
		{
			$statusItem->setId( null )->setValue( $item->getStatusPayment() )
				->setType( \Aimeos\MShop\Order\Item\Status\Base::STATUS_PAYMENT );

			$statusManager->save( $statusItem, false );
		}

		if( ( $status = $item->get( '.statusdelivery' ) ) !== null && $status != $item->getStatusDelivery() )
		{
			$statusItem->setId( null )->setValue( $item->getStatusDelivery() )
				->setType( \Aimeos\MShop\Order\Item\Status\Base::STATUS_DELIVERY );

			$statusManager->save( $statusItem, false );
		}
	}


	/**
	 * Creates the order base item objects from the map and adds the referenced items
	 *
	 * @param array $map Associative list of order base IDs as keys and list of price/locale/row as values
	 * @param string[] $ref Domain items that should be added as well, e.g.
	 *	"order/address", "order/coupon", "order/product", "order/service"
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Order\Item\Iface with IDs as keys
	 */
	protected function buildItems( array $map, array $ref ) : \Aimeos\Map
	{
		$items = [];
		$baseIds = array_keys( $map );
		$addressMap = $couponMap = $productMap = $serviceMap = [];

		if( in_array( 'order/address', $ref ) ) {
			$addressMap = $this->getAddresses( $baseIds );
		}

		if( in_array( 'order/product', $ref ) || in_array( 'order/coupon', $ref ) ) {
			$productMap = $this->getProducts( $baseIds );
		}

		if( in_array( 'order/coupon', $ref ) ) {
			$couponMap = $this->getCoupons( $baseIds, $productMap );
		}

		if( in_array( 'order/service', $ref ) ) {
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
	 * Creates a new invoice number for the passed order and site.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $item Order item with necessary values
	 * @return string Unique invoice number for the current site
	 */
	public function createInvoiceNumber( \Aimeos\MShop\Order\Item\Iface $item ) : string
	{
		$context = $this->context();
		$siteId = $context->locale()->getSiteId();
		$conn = $context->db( 'db-locale', true );

		try
		{
			$conn->query( 'SET TRANSACTION ISOLATION LEVEL SERIALIZABLE' )->finish();
			$conn->query( 'START TRANSACTION' )->finish();

			$result = $conn->query( 'SELECT "invoiceno" FROM "mshop_locale_site" where "siteid" = ?', [$siteId] );
			$row = $result->fetch();
			$result->finish();

			$conn->create( 'UPDATE "mshop_locale_site" SET "invoiceno" = "invoiceno" + 1 WHERE "siteid" = ?' )
				->bind( 1, $siteId )->execute()->finish();

			$conn->query( 'COMMIT' )->finish();
		}
		catch( \Exception $e )
		{
			$conn->close();
			throw $e;
		}

		return $row['invoiceno'] ?? '';
	}


	/**
	 * Returns a new and empty order base item (shopping basket).
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Default price of the basket (usually 0.00)
	 * @param \Aimeos\MShop\Locale\Item\Iface $locale Locale item containing the site, language and currency
	 * @param array $values Associative list of key/value pairs containing, e.g. the order or user ID
	 * @param \Aimeos\MShop\Order\Item\Product\Iface[] $products List of ordered product items
	 * @param \Aimeos\MShop\Order\Item\Address\Iface[] $addresses List of order address items
	 * @param \Aimeos\MShop\Order\Item\Service\Iface[] $services List of order serviceitems
	 * @param \Aimeos\MShop\Order\Item\Product\Iface[] $coupons Associative list of coupon codes as keys and items as values
	 * @param \Aimeos\MShop\Customer\Item\Iface|null $custItem Customer item object if requested
	 * @return \Aimeos\MShop\Order\Item\Iface Order base object
	 */
	protected function createItemBase( \Aimeos\MShop\Price\Item\Iface $price, \Aimeos\MShop\Locale\Item\Iface $locale,
		array $values = [], array $products = [], array $addresses = [], array $services = [], array $coupons = [],
		?\Aimeos\MShop\Customer\Item\Iface $custItem = null ) : \Aimeos\MShop\Order\Item\Iface
	{
		return new \Aimeos\MShop\Order\Item\Standard( $price, $locale,
			$values, $products, $addresses, $services, $coupons, $custItem );
	}


	/**
	 * Saves the modified basket content
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $basket Basket content
	 * @return \Aimeos\MShop\Order\Item\Iface Saved basket content
	 */
	protected function saveBasket( \Aimeos\MShop\Order\Item\Iface $basket ) : \Aimeos\MShop\Order\Item\Iface
	{
		$this->saveAddresses( $basket );
		$this->saveServices( $basket );
		$this->saveProducts( $basket );
		$this->saveCoupons( $basket );

		return $basket;
	}
}
