<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Manager\Product;


/**
 * Default order product manager.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Order\Manager\Product\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private array $searchConfig = [
		'order.product.parentid' => [
			'code' => 'order.product.parentid',
			'internalcode' => 'parentid',
			'label' => 'Product base ID',
			'type' => 'int',
			'public' => false,
		],
		'order.product.orderaddressid' => [
			'code' => 'order.product.orderaddressid',
			'internalcode' => 'ordaddrid',
			'label' => 'Address ID for the product',
			'type' => 'int',
			'public' => false,
		],
		'order.product.orderproductid' => [
			'code' => 'order.product.orderproductid',
			'internalcode' => 'ordprodid',
			'label' => 'Product parent ID',
			'type' => 'int',
			'public' => false,
		],
		'order.product.parentproductid' => [
			'code' => 'order.product.parentproductid',
			'internalcode' => 'parentprodid',
			'label' => 'Parent product ID',
		],
		'order.product.productid' => [
			'code' => 'order.product.productid',
			'internalcode' => 'prodid',
			'label' => 'Product original ID',
		],
		'order.product.name' => [
			'code' => 'order.product.name',
			'internalcode' => 'name',
			'label' => 'Product name',
		],
		'order.product.description' => [
			'code' => 'order.product.description',
			'internalcode' => 'description',
			'label' => 'Product description',
		],
		'order.product.prodcode' => [
			'code' => 'order.product.prodcode',
			'internalcode' => 'prodcode',
			'label' => 'Product code',
		],
		'order.product.type' => [
			'code' => 'order.product.type',
			'internalcode' => 'type',
			'label' => 'Product type',
		],
		'order.product.vendor' => [
			'code' => 'order.product.vendor',
			'internalcode' => 'vendor',
			'label' => 'Product vendor',
		],
		'order.product.stocktype' => [
			'code' => 'order.product.stocktype',
			'internalcode' => 'stocktype',
			'label' => 'Product stock type',
		],
		'order.product.timeframe' => [
			'code' => 'order.product.timeframe',
			'internalcode' => 'timeframe',
			'label' => 'Delivery time frame',
		],
		'order.product.quantity' => [
			'code' => 'order.product.quantity',
			'internalcode' => 'quantity',
			'label' => 'Product quantity',
			'type' => 'float',
		],
		'order.product.scale' => [
			'code' => 'order.product.scale',
			'internalcode' => 'scale',
			'label' => 'Product quantity scale',
			'type' => 'float',
		],
		'order.product.qtyopen' => [
			'code' => 'order.product.qtyopen',
			'internalcode' => 'qtyopen',
			'label' => 'Product quantity not yet delivered',
			'type' => 'float',
		],
		'order.product.position' => [
			'code' => 'order.product.position',
			'internalcode' => 'pos',
			'label' => 'Product position',
			'type' => 'int',
		],
		'order.product.statuspayment' => [
			'code' => 'order.product.statuspayment',
			'internalcode' => 'statuspayment',
			'label' => 'Product payment status',
			'type' => 'int',
		],
		'order.product.statusdelivery' => [
			'code' => 'order.product.statusdelivery',
			'internalcode' => 'statusdelivery',
			'label' => 'Product delivery status',
			'type' => 'int',
		],
		'order.product.mediaurl' => [
			'code' => 'order.product.mediaurl',
			'internalcode' => 'mediaurl',
			'label' => 'Product media url',
		],
		'order.product.target' => [
			'code' => 'order.product.target',
			'internalcode' => 'target',
			'label' => 'Product url target',
		],
		'order.product.notes' => [
			'code' => 'order.product.notes',
			'internalcode' => 'notes',
			'label' => 'Product notes',
		],
		'order.product.flags' => [
			'code' => 'order.product.flags',
			'internalcode' => 'flags',
			'label' => 'Product flags',
			'type' => 'int',
		],
	];


	/**
	 * Counts the number items that are available for the values of the given key.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $search Search criteria
	 * @param array|string $key Search key or list of keys to aggregate items for
	 * @param string|null $value Search key for aggregating the value column
	 * @param string|null $type Type of the aggregation, empty string for count or "sum" or "avg" (average)
	 * @return \Aimeos\Map List of the search keys as key and the number of counted items as value
	 */
	public function aggregate( \Aimeos\Base\Criteria\Iface $search, $key, ?string $value = null, ?string $type = null ) : \Aimeos\Map
	{
		/** mshop/order/manager/product/aggregate/mysql
		 * Counts the number of records grouped by the values in the key column and matched by the given criteria
		 *
		 * @see mshop/order/manager/product/aggregate/ansi
		 */

		/** mshop/order/manager/product/aggregate/ansi
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
		 * @since 2015.10
		 * @see mshop/order/manager/product/insert/ansi
		 * @see mshop/order/manager/product/update/ansi
		 * @see mshop/order/manager/product/newid/ansi
		 * @see mshop/order/manager/product/delete/ansi
		 * @see mshop/order/manager/product/search/ansi
		 * @see mshop/order/manager/product/count/ansi
		 */

		/** mshop/order/manager/product/aggregateavg/mysql
		 * Computes the average of all values grouped by the key column and matched by the given criteria
		 *
		 * @param string SQL statement for aggregating the order product items and computing the average value
		 * @since 2017.10
		 * @see mshop/order/manager/product/aggregateavg/ansi
		 * @see mshop/order/manager/product/aggregate/mysql
		 */

		/** mshop/order/manager/product/aggregateavg/ansi
		 * Computes the average of all values grouped by the key column and matched by the given criteria
		 *
		 * @param string SQL statement for aggregating the order product items and computing the average value
		 * @since 2017.10
		 * @see mshop/order/manager/product/aggregate/ansi
		 */

		/** mshop/order/manager/product/aggregatesum/mysql
		 * Computes the sum of all values grouped by the key column and matched by the given criteria
		 *
		 * @param string SQL statement for aggregating the order product items and computing the sum
		 * @since 2017.10
		 * @see mshop/order/manager/product/aggregatesum/ansi
		 * @see mshop/order/manager/product/aggregate/mysql
		 */

		/** mshop/order/manager/product/aggregatesum/ansi
		 * Computes the sum of all values grouped by the key column and matched by the given criteria
		 *
		 * @param string SQL statement for aggregating the order product items and computing the sum
		 * @since 2017.10
		 * @see mshop/order/manager/product/aggregate/ansi
		 */

		$cfgkey = 'mshop/order/manager/product/aggregate';
		return $this->aggregateBase( $search, $key, $cfgkey, ['order.product'], $value, $type );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Product\Iface New order product item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$context = $this->context();
		$values['.price'] = $values['.price'] ?? \Aimeos\MShop::create( $context, 'price' )->create();
		$values['order.product.siteid'] = $values['order.product.siteid'] ?? $context->locale()->getSiteId();

		return new \Aimeos\MShop\Order\Item\Product\Standard( 'order.product.', $values );
	}


	/**
	 * Creates a new order product attribute item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface New order product attribute item object
	 */
	public function createAttributeItem( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->object()->getSubManager( 'attribute' )->create( $values );
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
		$filter = parent::filter( $default )->order( 'order.product.id' );

		if( $site === true )
		{
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_SUBTREE;
			$filter->add( $this->siteCondition( 'order.product.siteid', $level ) );
		}

		return $filter;
	}


	/**
	 * Returns the additional column/search definitions
	 *
	 * @return array Associative list of column names as keys and items implementing \Aimeos\Base\Criteria\Attribute\Iface
	 */
	public function getSaveAttributes() : array
	{
		return $this->createAttributes( $this->searchConfig );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		return array_replace( parent::getSearchAttributes( $withsub ), $this->createAttributes( [
			'order.product.id' => [
				'code' => 'order.product.id',
				'internalcode' => 'id',
				'internaldeps' => ['LEFT JOIN "mshop_order_product" AS mordpr ON ( mord."id" = mordpr."parentid" )'],
				'label' => 'Order product ID',
				'type' => 'int',
				'public' => false,
			],
			'order.product.currencyid' => [
				'code' => 'order.product.currencyid',
				'internalcode' => 'currencyid',
				'label' => 'Product currencyid code',
			],
			'order.product.price' => [
				'code' => 'order.product.price',
				'internalcode' => 'price',
				'label' => 'Product price',
				'type' => 'decimal',
			],
			'order.product.costs' => [
				'code' => 'order.product.costs',
				'internalcode' => 'costs',
				'label' => 'Product shipping',
				'type' => 'decimal',
			],
			'order.product.rebate' => [
				'code' => 'order.product.rebate',
				'internalcode' => 'rebate',
				'label' => 'Product rebate',
				'type' => 'decimal',
			],
			'order.product.taxrates' => [
				'code' => 'order.product.taxrates',
				'internalcode' => 'taxrate',
				'label' => 'Product taxrates',
				'type' => 'json',
			],
			'order.product.taxvalue' => [
				'code' => 'order.product.taxvalue',
				'internalcode' => 'tax',
				'label' => 'Product tax value',
				'type' => 'decimal',
			],
			'order.product.taxflag' => [
				'code' => 'order.product.taxflag',
				'internalcode' => 'taxflag',
				'label' => 'Product tax flag (0=net, 1=gross)',
				'type' => 'int',
			],
			'agg:order.product:count' => [
				'code' => 'agg:order.product:count()',
				'internalcode' => '( SELECT COUNT(*) FROM mshop_order_product AS mordpr_count
					WHERE mordpr."parentid" = mordpr_count."parentid" AND mordpr_count."prodid" = $1 )',
				'label' => 'Order base product count, parameter(<product IDs>)',
				'type' => 'int',
				'public' => false,
			],
			'agg:order.product:total' => [
				'code' => 'agg:order.product:total()',
				'internalcode' => 'mordpr."quantity" * ( mordpr."price" + mordpr."costs" )',
				'label' => 'Product price total',
				'type' => 'float',
				'public' => false,
			],
		] ) );
	}


	/**
	 * Adds or updates an item object or a list of them.
	 *
	 * @param \Aimeos\Map|\Aimeos\MShop\Common\Item\Iface[]|\Aimeos\MShop\Common\Item\Iface $items Item or list of items whose data should be saved
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\Map|\Aimeos\MShop\Common\Item\Iface Saved item or items
	 */
	public function save( $items, bool $fetch = true )
	{
		foreach( map( $items ) as $id => $item )
		{
			$this->saveBase( $item, $item->getProducts()->isEmpty() ? $fetch : true );

			foreach( $item->getProducts() as $subItem ) {
				$this->saveBase( $subItem->setOrderProductId( $item->getId() ), $fetch );
			}
		}

		return $items;
	}


	/**
	 * Saves the dependent items of the item
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Item object
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\Iface Updated item
	 */
	public function saveRefs( \Aimeos\MShop\Common\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Common\Item\Iface
	{
		$attrItems = $item->getAttributeItems();

		foreach( $attrItems as $attrItem )
		{
			if( $attrItem->getParentId() != $item->getId() ) {
				$attrItem->setId( null ); // create new property item if copied
			}

			$attrItem->setParentId( $item->getId() );
		}

		$this->object()->getSubManager( 'attribute' )->save( $attrItems, $fetch );
		return $item;
	}


	/**
	 * Merges the data from the given map and the referenced items
	 *
	 * @param array $entries Associative list of ID as key and the associative list of property key/value pairs as values
	 * @param array $ref List of referenced items to fetch and add to the entries
	 * @return array Associative list of ID as key and the updated entries as value
	 */
	public function searchRefs( array $entries, array $ref ) : array
	{
		$manager = \Aimeos\MShop::create( $this->context(), 'price' );
		$attributes = $this->getAttributeItems( array_keys( $entries ) );

		if( $this->hasRef( $ref, 'product' ) )
		{
			$ids = map( $entries )->col( 'order.product.productid' );
			$ids->merge( map( $entries )->col( 'order.product.parentproductid' ) );
			$prodItems = $this->getProductItems( $ids->filter()->all(), $ref );
		}

		foreach( $entries as $id => $row )
		{
			$entries[$id]['.price'] = $manager->create( [
				'price.currencyid' => $row['order.product.currencyid'],
				'price.taxrates' => $row['order.product.taxrates'],
				'price.value' => $row['order.product.price'],
				'price.costs' => $row['order.product.costs'],
				'price.rebate' => $row['order.product.rebate'],
				'price.taxflag' => $row['order.product.taxflag'],
				'price.taxvalue' => $row['order.product.taxvalue'],
				'price.siteid' => $row['order.product.siteid'],
			] );

			$entries[$id]['.parentproduct'] = $prodItems[$row['order.product.parentproductid']] ?? null;
			$entries[$id]['.product'] = $prodItems[$row['order.product.productid']] ?? null;
			$entries[$id]['.attributes'] = $attributes[$id] ?? map();
		}

		return $entries;
	}


	/**
	 * Binds additional values to the statement before execution.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Item object
	 * @param \Aimeos\Base\DB\Statement\Iface $stmt Database statement object
	 * @param int $idx Current bind index
	 * @return \Aimeos\Base\DB\Statement\Iface Database statement object with bound values
	 */
	protected function bind( \Aimeos\MShop\Common\Item\Iface $item, \Aimeos\Base\DB\Statement\Iface $stmt, int &$idx ) : \Aimeos\Base\DB\Statement\Iface
	{
		$price = $item->getPrice();

		$stmt->bind( $idx++, $price->getCurrencyId() );
		$stmt->bind( $idx++, $price->getValue() );
		$stmt->bind( $idx++, $price->getCosts() );
		$stmt->bind( $idx++, $price->getRebate() );
		$stmt->bind( $idx++, $price->getTaxValue() );
		$stmt->bind( $idx++, json_encode( $price->getTaxRates(), JSON_FORCE_OBJECT ) );
		$stmt->bind( $idx++, $price->getTaxFlag(), \Aimeos\Base\DB\Statement\Base::PARAM_INT );

		return $stmt;
	}


	/**
	 * Fetches attribute items connected with order product item.
	 *
	 * @param string[] $ids List of order product item IDs
	 * @return \Aimeos\Map Associative list of order product IDs as keys and order product attribute items
	 *  implementing \Aimeos\MShop\Order\Item\Product\Attribute\Iface as values
	 */
	protected function getAttributeItems( array $ids ) : \Aimeos\Map
	{
		$manager = $this->getSubmanager( 'attribute' );
		$search = $manager->filter()->add( 'order.product.attribute.parentid', '==', $ids )->slice( 0, 0x7fffffff );

		return $manager->search( $search )->groupBy( 'order.product.attribute.parentid' );
	}


	/**
	 * Fetches product items connected with order product item.
	 *
	 * @param string[] $ids List of order product item IDs
	 * @return \Aimeos\Map Associative list of order product IDs as keys and order product attribute items
	 *  implementing \Aimeos\MShop\Order\Item\Product\Attribute\Iface as values
	 */
	protected function getProductItems( array $ids, array $ref ) : \Aimeos\Map
	{
		$manager = \Aimeos\MShop::create( $this->context(), 'product' );
		$search = $manager->filter()->add( 'product.id', '==', array_unique( $ids ) )->slice( 0, 0x7fffffff );

		return $manager->search( $search, $ref );
	}


	/**
	 * Checks if the item is modified
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Item object
	 * @return bool True if the item is modified, false if not
	 */
	protected function isModified( \Aimeos\MShop\Common\Item\Iface $item ) : bool
	{
		return $item->isModified() || $item->getPrice()->isModified();
	}


	/**
	 * Returns the prefix for the item properties and search keys.
	 *
	 * @return string Prefix for the item properties and search keys
	 */
	protected function prefix() : string
	{
		return 'order.product.';
	}


	/** mshop/order/manager/product/delete/mysql
	 * Deletes the items matched by the given IDs from the database
	 *
	 * @see mshop/order/manager/product/delete/ansi
	 */

	/** mshop/order/manager/product/delete/ansi
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
	 * @since 2015.10
	 * @see mshop/order/manager/product/insert/ansi
	 * @see mshop/order/manager/product/update/ansi
	 * @see mshop/order/manager/product/newid/ansi
	 * @see mshop/order/manager/product/search/ansi
	 * @see mshop/order/manager/product/count/ansi
	 */

	/** mshop/order/manager/product/submanagers
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
	 * @since 2015.10
	 */

	/** mshop/order/manager/product/name
	 * Class name of the used order base product manager implementation
	 *
	 * Each default order base product manager can be replaced by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the manager factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\MShop\Order\Manager\Product\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\MShop\Order\Manager\Product\Myproduct
	 *
	 * then you have to set the this configuration option:
	 *
	 *  mshop/order/manager/product/name = Myproduct
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
	 * @since 2015.10
	 */

	/** mshop/order/manager/product/decorators/excludes
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
	 *  mshop/order/manager/product/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
	 * "mshop/common/manager/decorators/default" for the order base product manager.
	 *
	 * @param array List of decorator names
	 * @since 2015.10
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/order/manager/product/decorators/global
	 * @see mshop/order/manager/product/decorators/local
	 */

	/** mshop/order/manager/product/decorators/global
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
	 *  mshop/order/manager/product/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the order base
	 * product manager.
	 *
	 * @param array List of decorator names
	 * @since 2015.10
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/order/manager/product/decorators/excludes
	 * @see mshop/order/manager/product/decorators/local
	 */

	/** mshop/order/manager/product/decorators/local
	 * Adds a list of local decorators only to the order base product manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\MShop\Order\Manager\Product\Decorator\*") around the
	 * order base product manager.
	 *
	 *  mshop/order/manager/product/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\MShop\Order\Manager\Product\Decorator\Decorator2" only
	 * to the order base product manager.
	 *
	 * @param array List of decorator names
	 * @since 2015.10
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/order/manager/product/decorators/excludes
	 * @see mshop/order/manager/product/decorators/global
	 */

	/** mshop/order/manager/product/insert/mysql
	 * Inserts a new order record into the database table
	 *
	 * @see mshop/order/manager/product/insert/ansi
	 */

	/** mshop/order/manager/product/insert/ansi
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
	 * @since 2015.10
	 * @see mshop/order/manager/product/update/ansi
	 * @see mshop/order/manager/product/newid/ansi
	 * @see mshop/order/manager/product/delete/ansi
	 * @see mshop/order/manager/product/search/ansi
	 * @see mshop/order/manager/product/count/ansi
	 */

	/** mshop/order/manager/product/update/mysql
	 * Updates an existing order record in the database
	 *
	 * @see mshop/order/manager/product/update/ansi
	 */

	/** mshop/order/manager/product/update/ansi
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
	 * @since 2015.10
	 * @see mshop/order/manager/product/insert/ansi
	 * @see mshop/order/manager/product/newid/ansi
	 * @see mshop/order/manager/product/delete/ansi
	 * @see mshop/order/manager/product/search/ansi
	 * @see mshop/order/manager/product/count/ansi
	 */

	/** mshop/order/manager/product/newid/mysql
	 * Retrieves the ID generated by the database when inserting a new record
	 *
	 * @see mshop/order/manager/product/newid/ansi
	 */

	/** mshop/order/manager/product/newid/ansi
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
	 * @since 2015.10
	 * @see mshop/order/manager/product/insert/ansi
	 * @see mshop/order/manager/product/update/ansi
	 * @see mshop/order/manager/product/delete/ansi
	 * @see mshop/order/manager/product/search/ansi
	 * @see mshop/order/manager/product/count/ansi
	 */

	/** mshop/order/manager/product/search/mysql
	 * Retrieves the records matched by the given criteria in the database
	 *
	 * @see mshop/order/manager/product/search/ansi
	 */

	/** mshop/order/manager/product/search/ansi
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
	 * @since 2015.10
	 * @see mshop/order/manager/product/insert/ansi
	 * @see mshop/order/manager/product/update/ansi
	 * @see mshop/order/manager/product/newid/ansi
	 * @see mshop/order/manager/product/delete/ansi
	 * @see mshop/order/manager/product/count/ansi
	 */

	/** mshop/order/manager/product/count/mysql
	 * Counts the number of records matched by the given criteria in the database
	 *
	 * @see mshop/order/manager/product/count/ansi
	 */

	/** mshop/order/manager/product/count/ansi
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
	 * @since 2015.10
	 * @see mshop/order/manager/product/insert/ansi
	 * @see mshop/order/manager/product/update/ansi
	 * @see mshop/order/manager/product/newid/ansi
	 * @see mshop/order/manager/product/delete/ansi
	 * @see mshop/order/manager/product/search/ansi
	 */
}
