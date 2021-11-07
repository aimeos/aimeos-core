<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Product
 */


namespace Aimeos\MShop\Order\Manager\Base\Product;


/**
 * Default order manager base product.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Order\Manager\Base\Product\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private $searchConfig = array(
		'order.base.product.id' => array(
			'code' => 'order.base.product.id',
			'internalcode' => 'mordbapr."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_order_base_product" AS mordbapr ON ( mordba."id" = mordbapr."baseid" )' ),
			'label' => 'Product ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'order.base.product.baseid' => array(
			'code' => 'order.base.product.baseid',
			'internalcode' => 'mordbapr."baseid"',
			'label' => 'Product base ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'order.base.product.siteid' => array(
			'code' => 'order.base.product.siteid',
			'internalcode' => 'mordbapr."siteid"',
			'label' => 'Product site ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.base.product.orderaddressid' => array(
			'code' => 'order.base.product.orderaddressid',
			'internalcode' => 'mordbapr."ordaddrid"',
			'label' => 'Address ID for the product',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.base.product.orderproductid' => array(
			'code' => 'order.base.product.orderproductid',
			'internalcode' => 'mordbapr."ordprodid"',
			'label' => 'Product parent ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'order.base.product.parentproductid' => array(
			'code' => 'order.base.product.parentproductid',
			'internalcode' => 'mordbapr."parentprodid"',
			'label' => 'Parent product ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.base.product.productid' => array(
			'code' => 'order.base.product.productid',
			'internalcode' => 'mordbapr."prodid"',
			'label' => 'Product original ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.base.product.name' => array(
			'code' => 'order.base.product.name',
			'internalcode' => 'mordbapr."name"',
			'label' => 'Product name',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.product.description' => array(
			'code' => 'order.base.product.description',
			'internalcode' => 'mordbapr."description"',
			'label' => 'Product description',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.product.prodcode' => array(
			'code' => 'order.base.product.prodcode',
			'internalcode' => 'mordbapr."prodcode"',
			'label' => 'Product code',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.product.type' => array(
			'code' => 'order.base.product.type',
			'internalcode' => 'mordbapr."type"',
			'label' => 'Product type',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.product.supplierid' => array(
			'code' => 'order.base.product.supplierid',
			'internalcode' => 'mordbapr."supplierid"',
			'label' => 'Product supplier ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.product.suppliername' => array(
			'code' => 'order.base.product.suppliername',
			'internalcode' => 'mordbapr."suppliername"',
			'label' => 'Product supplier name',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.product.stocktype' => array(
			'code' => 'order.base.product.stocktype',
			'internalcode' => 'mordbapr."stocktype"',
			'label' => 'Product stock type',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.product.timeframe' => array(
			'code' => 'order.base.product.timeframe',
			'internalcode' => 'mordbapr."timeframe"',
			'label' => 'Delivery time frame',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.product.quantity' => array(
			'code' => 'order.base.product.quantity',
			'internalcode' => 'mordbapr."quantity"',
			'label' => 'Product quantity',
			'type' => 'float',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT,
		),
		'order.base.product.qtyopen' => array(
			'code' => 'order.base.product.qtyopen',
			'internalcode' => 'mordbapr."qtyopen"',
			'label' => 'Product quantity not yet delivered',
			'type' => 'float',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT,
		),
		'order.base.product.currencyid' => array(
			'code' => 'order.base.product.currencyid',
			'internalcode' => 'mordbapr."currencyid"',
			'label' => 'Product currencyid code',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.product.price' => array(
			'code' => 'order.base.product.price',
			'internalcode' => 'mordbapr."price"',
			'label' => 'Product price',
			'type' => 'decimal',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.product.costs' => array(
			'code' => 'order.base.product.costs',
			'internalcode' => 'mordbapr."costs"',
			'label' => 'Product shipping',
			'type' => 'decimal',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.product.rebate' => array(
			'code' => 'order.base.product.rebate',
			'internalcode' => 'mordbapr."rebate"',
			'label' => 'Product rebate',
			'type' => 'decimal',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.product.taxrates' => array(
			'code' => 'order.base.product.taxrates',
			'internalcode' => 'mordbapr."taxrate"',
			'label' => 'Product taxrates',
			'type' => 'decimal',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.product.taxvalue' => array(
			'code' => 'order.base.product.taxvalue',
			'internalcode' => 'mordbapr."tax"',
			'label' => 'Product tax value',
			'type' => 'decimal',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.product.taxflag' => array(
			'code' => 'order.base.product.taxflag',
			'internalcode' => 'mordbapr."taxflag"',
			'label' => 'Product tax flag (0=net, 1=gross)',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'order.base.product.position' => array(
			'code' => 'order.base.product.position',
			'internalcode' => 'mordbapr."pos"',
			'label' => 'Product position',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'order.base.product.statuspayment' => array(
			'code' => 'order.base.product.statuspayment',
			'internalcode' => 'mordbapr."statuspayment"',
			'label' => 'Product payment status',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'order.base.product.statusdelivery' => array(
			'code' => 'order.base.product.statusdelivery',
			'internalcode' => 'mordbapr."statusdelivery"',
			'label' => 'Product delivery status',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'order.base.product.mediaurl' => array(
			'code' => 'order.base.product.mediaurl',
			'internalcode' => 'mordbapr."mediaurl"',
			'label' => 'Product media url',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.base.product.target' => array(
			'code' => 'order.base.product.target',
			'internalcode' => 'mordbapr."target"',
			'label' => 'Product url target',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.base.product.notes' => array(
			'code' => 'order.base.product.notes',
			'internalcode' => 'mordbapr."notes"',
			'label' => 'Product notes',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.base.product.flags' => array(
			'code' => 'order.base.product.flags',
			'internalcode' => 'mordbapr."flags"',
			'label' => 'Product flags',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'order.base.product.ctime' => array(
			'code' => 'order.base.product.ctime',
			'internalcode' => 'mordbapr."ctime"',
			'label' => 'Product create date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.base.product.mtime' => array(
			'code' => 'order.base.product.mtime',
			'internalcode' => 'mordbapr."mtime"',
			'label' => 'Order base product modify date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.base.product.editor' => array(
			'code' => 'order.base.product.editor',
			'internalcode' => 'mordbapr."editor"',
			'label' => 'Product editor',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.base.product.count()' => array(
			'code' => 'order.base.product.count()',
			'internalcode' => '( SELECT COUNT(*) FROM mshop_order_base_product AS mordbapr_count
				WHERE mordbapr."baseid" = mordbapr_count."baseid" AND mordbapr_count."prodid" = $1 )',
			'label' => 'Order base product count, parameter(<product IDs>)',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
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
		/** mshop/order/manager/base/product/aggregate/mysql
		 * Counts the number of records grouped by the values in the key column and matched by the given criteria
		 *
		 * @see mshop/order/manager/base/product/aggregate/ansi
		 */

		/** mshop/order/manager/base/product/aggregate/ansi
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
		 * @see mshop/order/manager/base/product/insert/ansi
		 * @see mshop/order/manager/base/product/update/ansi
		 * @see mshop/order/manager/base/product/newid/ansi
		 * @see mshop/order/manager/base/product/delete/ansi
		 * @see mshop/order/manager/base/product/search/ansi
		 * @see mshop/order/manager/base/product/count/ansi
		 */

		/** mshop/order/manager/base/product/aggregateavg/mysql
		 * Computes the average of all values grouped by the key column and matched by the given criteria
		 *
		 * @param string SQL statement for aggregating the order product items and computing the average value
		 * @since 2017.10
		 * @category Developer
		 * @see mshop/order/manager/base/product/aggregateavg/ansi
		 * @see mshop/order/manager/base/product/aggregate/mysql
		 */

		/** mshop/order/manager/base/product/aggregateavg/ansi
		 * Computes the average of all values grouped by the key column and matched by the given criteria
		 *
		 * @param string SQL statement for aggregating the order product items and computing the average value
		 * @since 2017.10
		 * @category Developer
		 * @see mshop/order/manager/base/product/aggregate/ansi
		 */

		/** mshop/order/manager/base/product/aggregatesum/mysql
		 * Computes the sum of all values grouped by the key column and matched by the given criteria
		 *
		 * @param string SQL statement for aggregating the order product items and computing the sum
		 * @since 2017.10
		 * @category Developer
		 * @see mshop/order/manager/base/product/aggregatesum/ansi
		 * @see mshop/order/manager/base/product/aggregate/mysql
		 */

		/** mshop/order/manager/base/product/aggregatesum/ansi
		 * Computes the sum of all values grouped by the key column and matched by the given criteria
		 *
		 * @param string SQL statement for aggregating the order product items and computing the sum
		 * @since 2017.10
		 * @category Developer
		 * @see mshop/order/manager/base/product/aggregate/ansi
		 */

		$cfgkey = 'mshop/order/manager/base/product/aggregate';
		return $this->aggregateBase( $search, $key, $cfgkey, ['order.base.product'], $value, $type );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Order\Manager\Base\Product\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$path = 'mshop/order/manager/base/product/submanagers';
		foreach( $this->getContext()->getConfig()->get( $path, array( 'attribute' ) ) as $domain ) {
			$this->getObject()->getSubManager( $domain )->clear( $siteids );
		}

		return $this->clearBase( $siteids, 'mshop/order/manager/base/product/delete' );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface New order product item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$context = $this->getContext();
		$priceManager = \Aimeos\MShop::create( $context, 'price' );

		$values['order.base.product.siteid'] = $context->getLocale()->getSiteId();

		return $this->createItemBase( $priceManager->create(), $values );
	}


	/**
	 * Creates a filter object.
	 *
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @param bool $site TRUE for adding site criteria to limit items by the site of related items
	 * @return \Aimeos\MW\Criteria\Iface Returns the filter object
	 */
	public function filter( ?bool $default = false, bool $site = false ) : \Aimeos\MW\Criteria\Iface
	{
		$search = parent::filter( $default );
		$search->setSortations( [$search->sort( '+', 'order.base.product.id' )] );

		if( $site === true )
		{
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
			$search->setConditions( $search->and( [
				$this->getSiteCondition( $search, 'order.base.product.siteid', $level ),
				$search->getConditions()
			] ) );
		}

		return $search;
	}


	/**
	 * Returns order base product for the given product ID.
	 *
	 * @param string $id Product ids to create product object for
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Returns order base product item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function get( string $id, array $ref = [], ?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->getItemBase( 'order.base.product.id', $id, $ref, $default );
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Order\Manager\Base\Product\Iface Manager object for chaining method calls
	 */
	public function delete( $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/order/manager/base/product/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/order/manager/base/product/delete/ansi
		 */

		/** mshop/order/manager/base/product/delete/ansi
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
		 * @see mshop/order/manager/base/product/insert/ansi
		 * @see mshop/order/manager/base/product/update/ansi
		 * @see mshop/order/manager/base/product/newid/ansi
		 * @see mshop/order/manager/base/product/search/ansi
		 * @see mshop/order/manager/base/product/count/ansi
		 */
		$path = 'mshop/order/manager/base/product/delete';

		return $this->deleteItemsBase( $itemIds, $path );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		$path = 'mshop/order/manager/base/product/submanagers';
		return $this->getResourceTypeBase( 'order/base/product', $path, array( 'attribute' ), $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		/** mshop/order/manager/base/product/submanagers
		 * List of manager names that can be instantiated by the order base product manager
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
		$path = 'mshop/order/manager/base/product/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, array( 'attribute' ), $withsub );
	}


	/**
	 * Returns a new sub manager specified by its name.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/order/manager/base/product/name
		 * Class name of the used order base product manager implementation
		 *
		 * Each default order base product manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\MShop\Order\Manager\Base\Product\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\MShop\Order\Manager\Base\Product\Myproduct
		 *
		 * then you have to set the this configuration option:
		 *
		 *  mshop/order/manager/base/product/name = Myproduct
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyProduct"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */

		/** mshop/order/manager/base/product/decorators/excludes
		 * Excludes decorators added by the "common" option from the order base product manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the order base product manager.
		 *
		 *  mshop/order/manager/base/product/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
		 * "mshop/common/manager/decorators/default" for the order base product manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/base/product/decorators/global
		 * @see mshop/order/manager/base/product/decorators/local
		 */

		/** mshop/order/manager/base/product/decorators/global
		 * Adds a list of globally available decorators only to the order base product manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the order base
		 * product manager.
		 *
		 *  mshop/order/manager/base/product/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the order
		 * base product manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/base/product/decorators/excludes
		 * @see mshop/order/manager/base/product/decorators/local
		 */

		/** mshop/order/manager/base/product/decorators/local
		 * Adds a list of local decorators only to the order base product manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\MShop\Order\Manager\Base\Product\Decorator\*") around the
		 * order base product manager.
		 *
		 *  mshop/order/manager/base/product/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\MShop\Order\Manager\Base\Product\Decorator\Decorator2" only
		 * to the order base product manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/base/product/decorators/excludes
		 * @see mshop/order/manager/base/product/decorators/global
		 */

		return $this->getSubManagerBase( 'order', 'base/product/' . $manager, $name );
	}


	/**
	 * Adds or updates a order base product item to the storage.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface $item New or existing product item that should be saved to the storage
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Order\Item\Base\Product\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		if( !$item->isModified() ) {
			return $this->saveAttributeItems( $item, $fetch );
		}

		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();
			$price = $item->getPrice();
			$date = date( 'Y-m-d H:i:s' );
			$columns = $this->getObject()->getSaveAttributes();

			if( $id === null )
			{
				/** mshop/order/manager/base/product/insert/mysql
				 * Inserts a new order record into the database table
				 *
				 * @see mshop/order/manager/base/product/insert/ansi
				 */

				/** mshop/order/manager/base/product/insert/ansi
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
				 * @see mshop/order/manager/base/product/update/ansi
				 * @see mshop/order/manager/base/product/newid/ansi
				 * @see mshop/order/manager/base/product/delete/ansi
				 * @see mshop/order/manager/base/product/search/ansi
				 * @see mshop/order/manager/base/product/count/ansi
				 */
				$path = 'mshop/order/manager/base/product/insert';
				$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ) );
			}
			else
			{
				/** mshop/order/manager/base/product/update/mysql
				 * Updates an existing order record in the database
				 *
				 * @see mshop/order/manager/base/product/update/ansi
				 */

				/** mshop/order/manager/base/product/update/ansi
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
				 * @see mshop/order/manager/base/product/insert/ansi
				 * @see mshop/order/manager/base/product/newid/ansi
				 * @see mshop/order/manager/base/product/delete/ansi
				 * @see mshop/order/manager/base/product/search/ansi
				 * @see mshop/order/manager/base/product/count/ansi
				 */
				$path = 'mshop/order/manager/base/product/update';
				$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ), false );
			}

			$idx = 1;
			$stmt = $this->getCachedStatement( $conn, $path, $sql );

			foreach( $columns as $name => $entry ) {
				$stmt->bind( $idx++, $item->get( $name ), $entry->getInternalType() );
			}

			$stmt->bind( $idx++, $item->getBaseId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $item->getOrderProductId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $item->getOrderAddressId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $item->getType() );
			$stmt->bind( $idx++, $item->getParentProductId() );
			$stmt->bind( $idx++, $item->getProductId() );
			$stmt->bind( $idx++, $item->getProductCode() );
			$stmt->bind( $idx++, $item->getSupplierId() );
			$stmt->bind( $idx++, $item->getSupplierName() );
			$stmt->bind( $idx++, $item->getStockType() );
			$stmt->bind( $idx++, $item->getName() );
			$stmt->bind( $idx++, $item->getDescription() );
			$stmt->bind( $idx++, $item->getMediaUrl() );
			$stmt->bind( $idx++, $item->getTimeFrame() );
			$stmt->bind( $idx++, $item->getQuantity() );
			$stmt->bind( $idx++, $price->getCurrencyId() );
			$stmt->bind( $idx++, $price->getValue() );
			$stmt->bind( $idx++, $price->getCosts() );
			$stmt->bind( $idx++, $price->getRebate() );
			$stmt->bind( $idx++, $price->getTaxValue() );
			$stmt->bind( $idx++, json_encode( $price->getTaxRates(), JSON_FORCE_OBJECT ) );
			$stmt->bind( $idx++, $price->getTaxFlag(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $item->getFlags(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $item->getStatusPayment(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $item->getStatusDelivery(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, (int) $item->getPosition(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $date ); // mtime
			$stmt->bind( $idx++, $context->getEditor() );
			$stmt->bind( $idx++, $item->getTarget() );
			$stmt->bind( $idx++, $item->getQuantityOpen() );
			$stmt->bind( $idx++, $item->getNotes() );
			$stmt->bind( $idx++, $item->getSiteId() );

			if( $id !== null ) {
				$stmt->bind( $idx++, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			} else {
				$stmt->bind( $idx++, $date ); // ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** mshop/order/manager/base/product/newid/mysql
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * @see mshop/order/manager/base/product/newid/ansi
				 */

				/** mshop/order/manager/base/product/newid/ansi
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
				 * @see mshop/order/manager/base/product/insert/ansi
				 * @see mshop/order/manager/base/product/update/ansi
				 * @see mshop/order/manager/base/product/delete/ansi
				 * @see mshop/order/manager/base/product/search/ansi
				 * @see mshop/order/manager/base/product/count/ansi
				 */
				$path = 'mshop/order/manager/base/product/newid';
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

		return $this->saveAttributeItems( $item, $fetch );
	}


	/**
	 * Searches for order base products item based on the given criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Order\Item\Base\Product\Iface with ids as keys
	 */
	public function search( \Aimeos\MW\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$context = $this->getContext();
		$priceManager = \Aimeos\MShop::create( $context, 'price' );

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		$map = $items = $prodItems = [];

		try
		{
			$required = array( 'order.base.product' );

			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
			$level = $context->getConfig()->get( 'mshop/order/manager/sitemode', $level );

			/** mshop/order/manager/base/product/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see mshop/order/manager/base/product/search/ansi
			 */

			/** mshop/order/manager/base/product/search/ansi
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
			 * @see mshop/order/manager/base/product/insert/ansi
			 * @see mshop/order/manager/base/product/update/ansi
			 * @see mshop/order/manager/base/product/newid/ansi
			 * @see mshop/order/manager/base/product/delete/ansi
			 * @see mshop/order/manager/base/product/count/ansi
			 */
			$cfgPathSearch = 'mshop/order/manager/base/product/search';

			/** mshop/order/manager/base/product/count/mysql
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * @see mshop/order/manager/base/product/count/ansi
			 */

			/** mshop/order/manager/base/product/count/ansi
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
			 * @see mshop/order/manager/base/product/insert/ansi
			 * @see mshop/order/manager/base/product/update/ansi
			 * @see mshop/order/manager/base/product/newid/ansi
			 * @see mshop/order/manager/base/product/delete/ansi
			 * @see mshop/order/manager/base/product/search/ansi
			 */
			$cfgPathCount = 'mshop/order/manager/base/product/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount,
				$required, $total, $level );

			try
			{
				while( ( $row = $results->fetch() ) !== null )
				{
					if( ( $row['order.base.product.taxrates'] = json_decode( $config = $row['order.base.product.taxrates'], true ) ) === null )
					{
						$msg = sprintf( 'Invalid JSON as result of search for ID "%2$s" in "%1$s": %3$s', 'mshop_order_base_product.taxrates', $row['order.base.product.id'], $config );
						$this->getContext()->getLogger()->log( $msg, \Aimeos\MW\Logger\Base::WARN, 'core/order' );
					}

					$price = $priceManager->create( [
						'price.currencyid' => $row['order.base.product.currencyid'],
						'price.taxrates' => $row['order.base.product.taxrates'],
						'price.value' => $row['order.base.product.price'],
						'price.costs' => $row['order.base.product.costs'],
						'price.rebate' => $row['order.base.product.rebate'],
						'price.taxflag' => $row['order.base.product.taxflag'],
						'price.taxvalue' => $row['order.base.product.taxvalue'],
						'price.siteid' => $row['order.base.product.siteid'],
					] );

					$map[$row['order.base.product.id']] = ['price' => $price, 'item' => $row];
				}
			}
			catch( \Exception $e )
			{
				$results->finish();
				throw $e;
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}


		if( isset( $ref['product'] ) || in_array( 'product', $ref ) )
		{
			$domains = isset( $ref['product'] ) && is_array( $ref['product'] ) ? $ref['product'] : [];

			$ids = [];
			foreach( $map as $list ) {
				$ids[] = $list['item']['order.base.product.productid'] ?? null;
			}

			$manager = \Aimeos\MShop::create( $context, 'product' );
			$search = $manager->filter()->slice( 0, count( $ids ) )->add( ['product.id' => array_unique( array_filter( $ids ) )] );
			$prodItems = $manager->search( $search, $domains );
		}

		if( isset( $ref['supplier'] ) || in_array( 'supplier', $ref ) )
		{
			$domains = isset( $ref['supplier'] ) && is_array( $ref['supplier'] ) ? $ref['supplier'] : [];

			$ids = [];
			foreach( $map as $list ) {
				$ids[] = $list['item']['order.base.product.supplierid'] ?? null;
			}

			$manager = \Aimeos\MShop::create( $context, 'supplier' );
			$search = $manager->filter()->slice( 0, count( $ids ) )->add( ['supplier.id' => array_unique( array_filter( $ids ) )] );
			$supItems = $manager->search( $search, $domains );
		}

		$attributes = $this->getAttributeItems( array_keys( $map ) );

		foreach( $map as $id => $list )
		{
			$list['item']['.product'] = $prodItems[$list['item']['order.base.product.productid'] ?? null] ?? null;
			$list['item']['.supplier'] = $supItems[$list['item']['order.base.product.supplierid'] ?? null] ?? null;
			$item = $this->createItemBase( $list['price'], $list['item'], $attributes[$id] ?? [] );

			if( $item = $this->applyFilter( $item ) ) {
				$items[$id] = $item;
			}
		}

		return map( $items );
	}


	/**
	 * Creates new order base product item object initialized with given parameters.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Price item object with product price
	 * @param array $values Associative list of order product properties
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface[] $attributes List of order product attributes
	 * @param \Aimeos\MShop\Product\Item\Iface|null $prodItem Original product item
	 */
	protected function createItemBase( \Aimeos\MShop\Price\Item\Iface $price, array $values = [],
		array $attributes = [] ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		return new \Aimeos\MShop\Order\Item\Base\Product\Standard( $price, $values, $attributes, [] );
	}


	/**
	 * Searches for attribute items connected with order product item.
	 *
	 * @param string[] $ids List of order product item IDs
	 * @return array Associative list of order product IDs as keys and order product attribute items
	 *  implementing \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface as values
	 */
	protected function getAttributeItems( array $ids ) : array
	{
		$manager = $this->getSubmanager( 'attribute' );
		$search = $manager->filter()->slice( 0, 0x7fffffff );
		$search->setConditions( $search->compare( '==', 'order.base.product.attribute.parentid', $ids ) );

		$result = [];
		foreach( $manager->search( $search ) as $item ) {
			$result[$item->getParentId()][$item->getId()] = $item;
		}

		return $result;
	}


	/**
	 * Saves the attribute items included in the order service item
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface $item Order service item with attribute items
	 * @param bool $fetch True if the new ID should be set in the attribute item
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Object with saved attribute items and IDs
	 */
	protected function saveAttributeItems( \Aimeos\MShop\Order\Item\Base\Product\Iface $item, bool $fetch ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		$attrItems = $item->getAttributeItems();

		foreach( $attrItems as $attrItem )
		{
			if( $attrItem->getParentId() != $item->getId() ) {
				$attrItem->setId( null ); // create new property item if copied
			}

			$attrItem->setParentId( $item->getId() );
		}

		$this->getObject()->getSubManager( 'attribute' )->save( $attrItems, $fetch );
		return $item;
	}
}
