<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
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
	use Session;
	use Update;


	/**
	 * Counts the number items that are available for the values of the given key.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $search Search criteria
	 * @param array|string $key Search key or list of key to aggregate items for
	 * @param string|null $value Search key for aggregating the value column
	 * @param string|null $type Type of the aggregation, empty string for count or "sum" or "avg" (average)
	 * @return \Aimeos\Map List of the search keys as key and the number of counted items as value
	 */
	public function aggregate( \Aimeos\Base\Criteria\Iface $search, $key, ?string $value = null, ?string $type = null ) : \Aimeos\Map
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
		 * @see mshop/order/manager/aggregateavg/ansi
		 * @see mshop/order/manager/aggregate/mysql
		 */

		/** mshop/order/manager/aggregateavg/ansi
		 * Computes the average of all values grouped by the key column and matched by the given criteria
		 *
		 * @param string SQL statement for aggregating the order items and computing the average value
		 * @since 2017.10
		 * @see mshop/order/manager/aggregate/ansi
		 */

		/** mshop/order/manager/aggregatesum/mysql
		 * Computes the sum of all values grouped by the key column and matched by the given criteria
		 *
		 * @param string SQL statement for aggregating the order items and computing the sum
		 * @since 2017.10
		 * @see mshop/order/manager/aggregatesum/ansi
		 * @see mshop/order/manager/aggregate/mysql
		 */

		/** mshop/order/manager/aggregatesum/ansi
		 * Computes the sum of all values grouped by the key column and matched by the given criteria
		 *
		 * @param string SQL statement for aggregating the order items and computing the sum
		 * @since 2017.10
		 * @see mshop/order/manager/aggregate/ansi
		 */

		$cfgkey = 'mshop/order/manager/aggregate';
		return $this->aggregateBase( $search, $key, $cfgkey, ['order'], $value, $type );
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

		$values['.locale'] = $values['.locale'] ?? $locale;
		$values['.price'] = $values['.price'] ?? \Aimeos\MShop::create( $context, 'price' )->create();
		$values['order.siteid'] = $values['order.siteid'] ?? $locale->getSiteId();

		$item = new \Aimeos\MShop\Order\Item\Standard( 'order.', $values );
		\Aimeos\MShop::create( $context, 'plugin' )->register( $item, 'order' );

		return $item;
	}


	/**
	 * Creates a new address item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Address\Iface New order address item object
	 */
	public function createAddress( array $values = [] ) : \Aimeos\MShop\Order\Item\Address\Iface
	{
		return $this->object()->getSubManager( 'address' )->create( $values );
	}


	/**
	 * Creates a new coupon item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Coupon\Iface New order coupon item object
	 */
	public function createCoupon( array $values = [] ) : \Aimeos\MShop\Order\Item\Coupon\Iface
	{
		return $this->object()->getSubManager( 'coupon' )->create( $values );
	}


	/**
	 * Creates a new product item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Product\Iface New order product item object
	 */
	public function createProduct( array $values = [] ) : \Aimeos\MShop\Order\Item\Product\Iface
	{
		return $this->object()->getSubManager( 'product' )->create( $values );
	}


	/**
	 * Creates a new product attribute item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface New order product attribute item object
	 */
	public function createProductAttribute( array $values = [] ) : \Aimeos\MShop\Order\Item\Product\Attribute\Iface
	{
		return $this->object()->getSubManager( 'product' )->getSubManager( 'attribute' )->create( $values );
	}


	/**
	 * Creates a new service item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Service\Iface New order service item object
	 */
	public function createService( array $values = [] ) : \Aimeos\MShop\Order\Item\Service\Iface
	{
		return $this->object()->getSubManager( 'service' )->create( $values );
	}


	/**
	 * Creates a new service attribute item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Service\Attribute\Iface New order service attribute item object
	 */
	public function createServiceAttribute( array $values = [] ) : \Aimeos\MShop\Order\Item\Service\Attribute\Iface
	{
		return $this->object()->getSubManager( 'service' )->getSubManager( 'attribute' )->create( $values );
	}


	/**
	 * Creates a new service transaction item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Service\Transaction\Iface New order service transaction item object
	 */
	public function createServiceTransaction( array $values = [] ) : \Aimeos\MShop\Order\Item\Service\Transaction\Iface
	{
		return $this->object()->getSubManager( 'service' )->getSubManager( 'transaction' )->create( $values );
	}


	/**
	 * Creates a new status item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Status\Iface New order item object
	 */
	public function createStatus( array $values = [] ) : \Aimeos\MShop\Order\Item\Status\Iface
	{
		return $this->object()->getSubManager( 'status' )->create( $values );
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

		if( $default !== false ) {
			$search->add( ['order.customerid' => $this->context()->user()] );
		}

		if( $site === true )
		{
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_SUBTREE;
			$search->add( $this->siteCondition( 'order.product.siteid', $level ) );
		}

		return $search;
	}


	/**
	 * Returns the additional column/search definitions
	 *
	 * @return array Associative list of column names as keys and items implementing \Aimeos\Base\Criteria\Attribute\Iface
	 */
	public function getSaveAttributes() : array
	{
		return $this->createAttributes( [
			'order.invoiceno' => [
				'label' => 'Invoice number',
				'internalcode' => 'invoiceno',
			],
			'order.relatedid' => [
				'label' => 'Related invoice ID',
				'internalcode' => 'relatedid',
			],
			'order.channel' => [
				'label' => 'Order channel',
				'internalcode' => 'channel',
			],
			'order.datepayment' => [
				'label' => 'Purchase date',
				'internalcode' => 'datepayment',
				'type' => 'datetime',
			],
			'order.datedelivery' => [
				'label' => 'Delivery date',
				'internalcode' => 'datedelivery',
				'type' => 'datetime',
			],
			'order.statusdelivery' => [
				'label' => 'Delivery status',
				'internalcode' => 'statusdelivery',
				'type' => 'int',
			],
			'order.statuspayment' => [
				'label' => 'Payment status',
				'internalcode' => 'statuspayment',
				'type' => 'int',
			],
			'order.customerid' => [
				'label' => 'Order customer ID',
				'internalcode' => 'customerid',
			],
			'order.customerref' => [
				'label' => 'Order customer reference',
				'internalcode' => 'customerref',
			],
			'order.comment' => [
				'label' => 'Order comment',
				'internalcode' => 'comment',
			],
		] );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
		$level = $this->context()->config()->get( 'mshop/order/manager/sitemode', $level );
		$expr = $this->siteString( 'mordst_cs."siteid"', $level );

		return array_replace( parent::getSearchAttributes( $withsub ), $this->createAttributes( [
			'order.sitecode' => [
				'label' => 'Order site code',
				'internalcode' => 'sitecode',
				'public' => false,
			],
			'order.languageid' => [
				'label' => 'Order language code',
				'internalcode' => 'langid',
			],
			'order.currencyid' => [
				'label' => 'Order currencyid code',
				'internalcode' => 'currencyid',
			],
			'order.price' => [
				'label' => 'Order price amount',
				'internalcode' => 'price',
			],
			'order.costs' => [
				'label' => 'Order shipping amount',
				'internalcode' => 'costs',
			],
			'order.rebate' => [
				'label' => 'Order rebate amount',
				'internalcode' => 'rebate',
			],
			'order.taxvalue' => [
				'label' => 'Order tax amount',
				'internalcode' => 'tax',
			],
			'order.taxflag' => [
				'label' => 'Order tax flag (0=net, 1=gross)',
				'internalcode' => 'taxflag',
			],
			'order.cdate' => [
				'label' => 'Create date',
				'internalcode' => 'cdate',
			],
			'order.cmonth' => [
				'label' => 'Create month',
				'internalcode' => 'cmonth',
			],
			'order.cweek' => [
				'label' => 'Create week',
				'internalcode' => 'cweek',
			],
			'order.cwday' => [
				'label' => 'Create weekday',
				'internalcode' => 'cwday',
			],
			'order.chour' => [
				'label' => 'Create hour',
				'internalcode' => 'chour',
			],
			'order:status' => [
				'code' => 'order:status()',
				'internalcode' => '( SELECT COUNT(mordst_cs."parentid")
					FROM "mshop_order_status" AS mordst_cs
					WHERE mord."id" = mordst_cs."parentid" AND ' . $expr . '
					AND mordst_cs."type" = $1 AND mordst_cs."value" IN ( $2 ) )',
				'label' => 'Number of order status items, parameter(<type>,<value>)',
				'type' => 'int',
				'public' => false,
			],
		] ) );
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
		$this->addStatus( $item );
		$this->saveAddresses( $item );
		$this->saveServices( $item );
		$this->saveProducts( $item );
		$this->saveCoupons( $item );
		$this->saveStatuses( $item );

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
		$context = $this->context();
		$ids = array_keys( $entries );
		$addresses = $customers = $coupons = $products = $services = $statuses = [];

		$priceManager = \Aimeos\MShop::create( $context, 'price' );
		$localeManager = \Aimeos\MShop::create( $context, 'locale' );

		if( $this->hasRef( $ref, 'customer' ) && !( $cids = map( $entries )->col( 'order.customerid' )->filter() )->empty() )
		{
			$manager = \Aimeos\MShop::create( $this->context(), 'customer' );
			$search = $manager->filter()->slice( 0, 0x7fffffff )->add( ['customer.id' => $cids] );
			$customers = $manager->search( $search, $ref );
		}

		if( $this->hasRef( $ref, 'order/address' ) ) {
			$addresses = $this->getAddresses( $ids, $ref );
		}

		if( $this->hasRef( $ref, 'order/product' ) || $this->hasRef( $ref, 'order/coupon' ) ) {
			$products = $this->getProducts( $ids, $ref );
		}

		if( $this->hasRef( $ref, 'order/coupon' ) ) {
			$coupons = $this->getCoupons( $ids, $ref );
		}

		if( $this->hasRef( $ref, 'order/service' ) ) {
			$services = $this->getServices( $ids, $ref );
		}

		if( $this->hasRef( $ref, 'order/status' ) ) {
			$statuses = $this->getStatuses( $ids, $ref );
		}

		foreach( $entries as $id => $row )
		{
			$entries[$id]['.price'] = $priceManager->create( [
				'price.currencyid' => $row['order.currencyid'],
				'price.value' => $row['order.price'],
				'price.costs' => $row['order.costs'],
				'price.rebate' => $row['order.rebate'],
				'price.taxflag' => $row['order.taxflag'],
				'price.taxvalue' => $row['order.taxvalue'],
				'price.siteid' => $row['order.siteid'],
			] );

			// you may need the site object! take care!
			$entries[$id]['.locale'] = $localeManager->create( [
				'locale.currencyid' => $row['order.currencyid'],
				'locale.languageid' => $row['order.languageid'],
				'locale.siteid' => $row['order.siteid'],
			] );

			$entries[$id]['.customer'] = $customers[$row['order.customerid']] ?? null;
			$entries[$id]['.addresses'] = $addresses[$id] ?? [];
			$entries[$id]['.coupons'] = $coupons[$id] ?? [];
			$entries[$id]['.products'] = $products[$id] ?? [];
			$entries[$id]['.services'] = $services[$id] ?? [];
			$entries[$id]['.statuses'] = $statuses[$id] ?? [];
		}

		return $entries;
	}


	/**
	 * Adds the new payment and delivery values to the order status log.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $item Order item object
	 * @return \Aimeos\MShop\Order\Manager\Iface Manager object for chaining method calls
	 */
	protected function addStatus( \Aimeos\MShop\Order\Item\Iface $item ) : \Aimeos\MShop\Order\Manager\Iface
	{
		$object = $this->object();

		if( ( $status = $item->get( '.statuspayment' ) ) !== null && $status != $item->getStatusPayment() ) {
			$item->addStatus( $object->createStatus()->setType( 'status-payment' )->setValue( $item->getStatusPayment() ) );
		}

		if( ( $status = $item->get( '.statusdelivery' ) ) !== null && $status != $item->getStatusDelivery() ) {
			$item->addStatus( $object->createStatus()->setType( 'status-delivery' )->setValue( $item->getStatusDelivery() ) );
		}

		return $this;
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
		$context = $this->context();

		$stmt->bind( $idx++, $context->locale()->getSiteItem()->getCode() );
		$stmt->bind( $idx++, $item->locale()->getLanguageId() );
		$stmt->bind( $idx++, $price->getCurrencyId() );
		$stmt->bind( $idx++, $price->getValue() );
		$stmt->bind( $idx++, $price->getCosts() );
		$stmt->bind( $idx++, $price->getRebate() );
		$stmt->bind( $idx++, $price->getTaxValue() );
		$stmt->bind( $idx++, $price->getTaxFlag(), \Aimeos\Base\DB\Statement\Base::PARAM_INT );

		if( $item->getId() === null )
		{
			$date = date_create_from_format( 'Y-m-d H:i:s', $context->datetime() );
			$stmt->bind( $idx++, $date->format( 'Y-m-d' ) ); // cdate
			$stmt->bind( $idx++, $date->format( 'Y-m' ) ); // cmonth
			$stmt->bind( $idx++, $date->format( 'Y-W' ) ); // cweek
			$stmt->bind( $idx++, $date->format( 'w' ) ); // cwday
			$stmt->bind( $idx++, $date->format( 'G' ) ); // chour
		}

		return $stmt;
	}


	/**
	 * Creates a new invoice number for the passed order and site.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $item Order item with necessary values
	 * @return string Unique invoice number for the current site
	 */
	protected function createInvoiceNumber( \Aimeos\MShop\Order\Item\Iface $item ) : string
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
	 * Returns the prefix for the item properties and search keys.
	 *
	 * @return string Prefix for the item properties and search keys
	 */
	protected function prefix() : string
	{
		return 'order.';
	}


	/**
	 * Creates a one-time order in the storage from the given invoice object.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Order item with necessary values
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\Iface $item Updated item including the generated ID
	 */
	protected function saveBase( \Aimeos\MShop\Common\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Common\Item\Iface
	{
		if( empty( $item->getInvoiceNumber() ) && $item->getStatusPayment() >= \Aimeos\MShop\Order\Item\Base::PAY_PENDING )
		{
			try {
				$item->setInvoiceNumber( $this->createInvoiceNumber( $item ) );
			} catch( \Exception $e ) { // redo on transaction deadlock
				$item->setInvoiceNumber( $this->createInvoiceNumber( $item ) );
			}
		}

		return parent::saveBase( $item, $fetch );
	}


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
	 * @since 2015.10
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
	 * @since 2015.10
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
	 * @since 2015.10
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
	 * @since 2015.10
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/order/manager/decorators/excludes
	 * @see mshop/order/manager/decorators/global
	 */

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
	 * @since 2015.10
	 * @see mshop/order/manager/insert/ansi
	 * @see mshop/order/manager/update/ansi
	 * @see mshop/order/manager/newid/ansi
	 * @see mshop/order/manager/search/ansi
	 * @see mshop/order/manager/count/ansi
	 */

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
	 * @since 2015.10
	 */

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
	 * statement. The catalog of the columns must correspond to the
	 * catalog in the save() method, so the correct values are
	 * bound to the columns.
	 *
	 * The SQL statement should conform to the ANSI standard to be
	 * compatible with most relational database systems. This also
	 * includes using double quotes for table and column names.
	 *
	 * @param string SQL statement for inserting records
	 * @since 2015.10
	 * @see mshop/order/manager/update/ansi
	 * @see mshop/order/manager/newid/ansi
	 * @see mshop/order/manager/delete/ansi
	 * @see mshop/order/manager/search/ansi
	 * @see mshop/order/manager/count/ansi
	 */

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
	 * sent to the database server. The catalog of the columns must
	 * correspond to the catalog in the save() method, so the
	 * correct values are bound to the columns.
	 *
	 * The SQL statement should conform to the ANSI standard to be
	 * compatible with most relational database systems. This also
	 * includes using double quotes for table and column names.
	 *
	 * @param string SQL statement for updating records
	 * @since 2015.10
	 * @see mshop/order/manager/insert/ansi
	 * @see mshop/order/manager/newid/ansi
	 * @see mshop/order/manager/delete/ansi
	 * @see mshop/order/manager/search/ansi
	 * @see mshop/order/manager/count/ansi
	 */

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
	 *  SELECT currval('seq_mrul_id')
	 * For SQL Server:
	 *  SELECT SCOPE_IDENTITY()
	 * For Oracle:
	 *  SELECT "seq_mrul_id".CURRVAL FROM DUAL
	 *
	 * There's no way to retrive the new ID by a SQL statements that
	 * fits for most database servers as they implement their own
	 * specific way.
	 *
	 * @param string SQL statement for retrieving the last inserted record ID
	 * @since 2015.10
	 * @see mshop/order/manager/insert/ansi
	 * @see mshop/order/manager/update/ansi
	 * @see mshop/order/manager/delete/ansi
	 * @see mshop/order/manager/search/ansi
	 * @see mshop/order/manager/count/ansi
	 */

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
	 * @since 2018.01
	 * @see mshop/locale/manager/sitelevel
	 */

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
	 * If the records that are retrieved should be cataloged by one or more
	 * columns, the generated string of column / sort direction pairs
	 * replaces the ":catalog" placeholder. In case no cataloging is required,
	 * the complete ORDER BY part including the "\/*-catalogby*\/...\/*catalogby-*\/"
	 * markers is removed to speed up retrieving the records. Columns of
	 * sub-managers can also be used for cataloging the result set but then
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
	 * @see mshop/order/manager/insert/ansi
	 * @see mshop/order/manager/update/ansi
	 * @see mshop/order/manager/newid/ansi
	 * @see mshop/order/manager/delete/ansi
	 * @see mshop/order/manager/count/ansi
	 */

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
	 * @since 2015.10
	 * @see mshop/order/manager/insert/ansi
	 * @see mshop/order/manager/update/ansi
	 * @see mshop/order/manager/newid/ansi
	 * @see mshop/order/manager/delete/ansi
	 * @see mshop/order/manager/search/ansi
	 */
}
