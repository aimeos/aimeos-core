<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
class Standard
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Order\Manager\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private $searchConfig = array(
		'order.id' => array(
			'code' => 'order.id',
			'internalcode' => 'mord."id"',
			'label' => 'Invoice ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'order.siteid' => array(
			'code' => 'order.siteid',
			'internalcode' => 'mord."siteid"',
			'label' => 'Invoice site ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.baseid' => array(
			'code' => 'order.baseid',
			'internalcode' => 'mord."baseid"',
			'label' => 'Invoice base ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'order.relatedid' => array(
			'code' => 'order.relatedid',
			'internalcode' => 'mord."relatedid"',
			'label' => 'Related invoice ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.type' => array(
			'code' => 'order.type',
			'internalcode' => 'mord."type"',
			'label' => 'Invoice type',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.datepayment' => array(
			'code' => 'order.datepayment',
			'internalcode' => 'mord."datepayment"',
			'label' => 'Purchase date',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.datedelivery' => array(
			'code' => 'order.datedelivery',
			'internalcode' => 'mord."datedelivery"',
			'label' => 'Delivery date',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.statusdelivery' => array(
			'code' => 'order.statusdelivery',
			'internalcode' => 'mord."statusdelivery"',
			'label' => 'Delivery status',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'order.statuspayment' => array(
			'code' => 'order.statuspayment',
			'internalcode' => 'mord."statuspayment"',
			'label' => 'Payment status',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'order.cdate' => array(
			'code' => 'order.cdate',
			'internalcode' => 'mord."cdate"',
			'label' => 'Create date',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.cmonth' => array(
			'code' => 'order.cmonth',
			'internalcode' => 'mord."cmonth"',
			'label' => 'Create month',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.cweek' => array(
			'code' => 'order.cweek',
			'internalcode' => 'mord."cweek"',
			'label' => 'Create week',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.cwday' => array(
			'code' => 'order.cwday',
			'internalcode' => 'mord."cwday"',
			'label' => 'Create weekday',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.chour' => array(
			'code' => 'order.chour',
			'internalcode' => 'mord."chour"',
			'label' => 'Create hour',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.ctime' => array(
			'code' => 'order.ctime',
			'internalcode' => 'mord."ctime"',
			'label' => 'Create date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.mtime' => array(
			'code' => 'order.mtime',
			'internalcode' => 'mord."mtime"',
			'label' => 'Modify date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.editor' => array(
			'code' => 'order.editor',
			'internalcode' => 'mord."editor"',
			'label' => 'Editor',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
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
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
	);


	/**
	 * Creates the manager that will use the given context object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );
		$this->setResourceName( 'db-order' );

		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
		$level = $context->getConfig()->get( 'mshop/order/manager/sitemode', $level );

		$name = 'order:status';
		$expr = $this->getSiteString( 'mordst_cs."siteid"', $level );
		$this->searchConfig[$name] = str_replace( ':site', $expr, $this->searchConfig[$name] );
	}


	/**
	 * Counts the number items that are available for the values of the given key.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria
	 * @param array|string $key Search key or list of key to aggregate items for
	 * @param string|null $value Search key for aggregating the value column
	 * @param string|null $type Type of the aggregation, empty string for count or "sum" or "avg" (average)
	 * @return \Aimeos\Map List of the search keys as key and the number of counted items as value
	 */
	public function aggregate( \Aimeos\MW\Criteria\Iface $search, $key, string $value = null, string $type = null ) : \Aimeos\Map
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
		foreach( $this->getContext()->getConfig()->get( $path, array( 'status', 'base' ) ) as $domain ) {
			$this->getObject()->getSubManager( $domain )->clear( $siteids );
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
		$values['order.siteid'] = $this->getContext()->getLocale()->getSiteId();
		return $this->createItemBase( $values );
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
		$search = parent::filter();
		$context = $this->getContext();

		if( $default !== false )
		{
			$search->setConditions( $search->and( [
				$search->compare( '==', 'order.base.customerid', $context->getUserId() ),
				$search->getConditions()
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
	 * Creates a one-time order in the storage from the given invoice object.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $item Order item with necessary values
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Order\Item\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Order\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Order\Item\Iface
	{
		if( $item->getBaseId() === null ) {
			throw new \Aimeos\MShop\Order\Exception( 'Required order base ID is missing' );
		}

		if( !$item->isModified() ) {
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
			$stmt = $this->getCachedStatement( $conn, $path, $sql );

			foreach( $columns as $name => $entry ) {
				$stmt->bind( $idx++, $item->get( $name ), $entry->getInternalType() );
			}

			$stmt->bind( $idx++, $item->getBaseId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $item->getType() );
			$stmt->bind( $idx++, $item->getDatePayment() );
			$stmt->bind( $idx++, $item->getDateDelivery() );
			$stmt->bind( $idx++, $item->getStatusDelivery(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $item->getStatusPayment(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $item->getRelatedId(), \Aimeos\MW\DB\Statement\Base::PARAM_STR );
			$stmt->bind( $idx++, $date ); // mtime
			$stmt->bind( $idx++, $context->getEditor() );
			$stmt->bind( $idx++, $context->getLocale()->getSiteId() );

			if( $id !== null ) {
				$stmt->bind( $idx++, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			} else {
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

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}


		$this->addStatus( $item );

		return $item;
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
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		$path = 'mshop/order/manager/submanagers';
		return $this->getResourceTypeBase( 'order', $path, array( 'base', 'status' ), $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] List of search attribute items
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
		$default = array( 'base', 'status' );

		return $this->getSearchAttributesBase( $this->searchConfig, $path, $default, $withsub );
	}


	/**
	 * Searches for orders based on the given criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Order\Item\Iface with ids as keys
	 */
	public function search( \Aimeos\MW\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		$map = $items = $baseItems = [];

		try
		{
			$required = array( 'order' );

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
			$level = $context->getConfig()->get( 'mshop/order/manager/sitemode', $level );

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

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}


		if( in_array( 'order/base', $ref ) )
		{
			$ids = [];
			foreach( $map as $row ) {
				$ids[] = $row['order.baseid'];
			}

			$manager = $this->getObject()->getSubManager( 'base' );
			$search = $manager->filter()->slice( 0, count( $ids ) );
			$search->setConditions( $search->compare( '==', 'order.base.id', $ids ) );
			$baseItems = $manager->search( $search, $ref );
		}

		foreach( $map as $id => $row )
		{
			$baseItem = $baseItems[$row['order.baseid']] ?? null;

			if( $item = $this->applyFilter( $this->createItemBase( $row, $baseItem ) ) ) {
				$items[$id] = $item;
			}
		}

		return map( $items );
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
		$statusManager = \Aimeos\MShop::create( $this->getContext(), 'order/status' );

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
	 * Creates a new order item.
	 *
	 * @param array $values List of attributes for order item
	 * @param \Aimeos\MShop\Order\Item\Base\Iface|null $baseItem Order basket if requested and available
	 * @return \Aimeos\MShop\Order\Item\Iface New order item
	 */
	protected function createItemBase( array $values = [], ?\Aimeos\MShop\Order\Item\Base\Iface $baseItem = null ) : \Aimeos\MShop\Order\Item\Iface
	{
		return new \Aimeos\MShop\Order\Item\Standard( $values, $baseItem );
	}
}
