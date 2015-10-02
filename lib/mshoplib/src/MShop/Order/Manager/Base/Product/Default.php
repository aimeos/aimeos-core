<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Product
 */


/**
 * Default order manager base product.
 *
 * @package MShop
 * @subpackage Order
 */
class MShop_Order_Manager_Base_Product_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Order_Manager_Base_Product_Interface
{
	private $searchConfig = array(
		'order.base.product.id' => array(
			'code'=>'order.base.product.id',
			'internalcode'=>'mordbapr."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_order_base_product" AS mordbapr ON ( mordba."id" = mordbapr."baseid" )' ),
			'label'=>'Order base product ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.product.baseid' => array(
			'code'=>'order.base.product.baseid',
			'internalcode'=>'mordbapr."baseid"',
			'label'=>'Order base product base ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.product.siteid' => array(
			'code'=>'order.base.product.siteid',
			'internalcode'=>'mordbapr."siteid"',
			'label'=>'Order base product site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.product.orderproductid' => array(
			'code'=>'order.base.product.orderproductid',
			'internalcode'=>'mordbapr."ordprodid"',
			'label'=>'Order base product parent ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.product.type' => array(
			'code'=>'order.base.product.type',
			'internalcode'=>'mordbapr."type"',
			'label'=>'Order base product type',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
			'public' => true,
		),
		'order.base.product.productid' => array(
			'code'=>'order.base.product.productid',
			'internalcode'=>'mordbapr."prodid"',
			'label'=>'Order base product original ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.product.prodcode' => array(
			'code'=>'order.base.product.prodcode',
			'internalcode'=>'mordbapr."prodcode"',
			'label'=>'Order base product code',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.product.suppliercode' => array(
			'code'=>'order.base.product.suppliercode',
			'internalcode'=>'mordbapr."suppliercode"',
			'label'=>'Order base product supplier code',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.product.warehousecode' => array(
			'code'=>'order.base.product.warehousecode',
			'internalcode'=>'mordbapr."warehousecode"',
			'label'=>'Order base product warehouse code',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.product.name' => array(
			'code'=>'order.base.product.name',
			'internalcode'=>'mordbapr."name"',
			'label'=>'Order base product name',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.product.mediaurl' => array(
			'code'=>'order.base.product.mediaurl',
			'internalcode'=>'mordbapr."mediaurl"',
			'label'=>'Order base product media url',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.product.quantity' => array(
			'code'=>'order.base.product.quantity',
			'internalcode'=>'mordbapr."amount"',
			'label'=>'Order base product quantity',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'order.base.product.price' => array(
			'code'=>'order.base.product.price',
			'internalcode'=>'mordbapr."price"',
			'label'=>'Order base product price',
			'type'=> 'decimal',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.product.costs' => array(
			'code'=>'order.base.product.costs',
			'internalcode'=>'mordbapr."costs"',
			'label'=>'Order base product shipping',
			'type'=> 'decimal',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.product.rebate' => array(
			'code'=>'order.base.product.rebate',
			'internalcode'=>'mordbapr."rebate"',
			'label'=>'Order base product rebate',
			'type'=> 'decimal',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.product.taxrate' => array(
			'code'=>'order.base.product.taxrate',
			'internalcode'=>'mordbapr."taxrate"',
			'label'=>'Order base product taxrate',
			'type'=> 'decimal',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.product.quantity' => array(
			'code'=>'order.base.product.quantity',
			'internalcode'=>'mordbapr."quantity"',
			'label'=>'Order base product quantity',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'order.base.product.flags' => array(
			'code'=>'order.base.product.flags',
			'internalcode'=>'mordbapr."flags"',
			'label'=>'Order base product flags',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'order.base.product.position' => array(
			'code'=>'order.base.product.position',
			'internalcode'=>'mordbapr."pos"',
			'label'=>'Order base product position',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'order.base.product.status' => array(
			'code'=>'order.base.product.status',
			'internalcode'=>'mordbapr."status"',
			'label'=>'Order base product status',
			'type'=> 'boolean',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_BOOL,
		),
		'order.base.product.mtime' => array(
			'code'=>'order.base.product.mtime',
			'internalcode'=>'mordbapr."mtime"',
			'label'=>'Order base product modification time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.product.ctime'=> array(
			'code'=>'order.base.product.ctime',
			'internalcode'=>'mordbapr."ctime"',
			'label'=>'Order base product create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR
		),
		'order.base.product.editor'=> array(
			'code'=>'order.base.product.editor',
			'internalcode'=>'mordbapr."editor"',
			'label'=>'Order base product editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR
		),
		'order.base.product.count()' => array(
			'code'=>'order.base.product.count()',
			'internalcode'=>'( SELECT COUNT(*) FROM mshop_order_base_product AS mordbapr_count
				WHERE mordbapr."baseid" = mordbapr_count."baseid" AND mordbapr_count."prodid" = $1 )',
			'label'=>'Order base product count, parameter(<product IDs>)',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
	);


	/**
	 * Initializes the object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );
		$this->setResourceName( 'db-order' );
	}


	/**
	 * Counts the number items that are available for the values of the given key.
	 *
	 * @param MW_Common_Criteria_Interface $search Search criteria
	 * @param string $key Search key to aggregate items for
	 * @return array List of the search keys as key and the number of counted items as value
	 */
	public function aggregate( MW_Common_Criteria_Interface $search, $key )
	{
		/** mshop/order/manager/base/product/default/aggregate
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
		 * @see mshop/order/manager/base/product/default/item/insert
		 * @see mshop/order/manager/base/product/default/item/update
		 * @see mshop/order/manager/base/product/default/item/newid
		 * @see mshop/order/manager/base/product/default/item/delete
		 * @see mshop/order/manager/base/product/default/item/search
		 * @see mshop/order/manager/base/product/default/item/count
		 */
		$cfgkey = 'mshop/order/manager/base/product/default/aggregate';
		return $this->aggregateBase( $search, $key, $cfgkey, array( 'order.base.product' ) );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param integer[] $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'classes/order/manager/base/product/submanagers';
		foreach( $this->getContext()->getConfig()->get( $path, array( 'attribute' ) ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->cleanupBase( $siteids, 'mshop/order/manager/base/product/default/item/delete' );
	}


	/**
	 * Create new order base product item object.
	 *
	 * @return MShop_Order_Item_Base_Product_Interface
	 */
	public function createItem()
	{
		$context = $this->getContext();
		$priceManager = MShop_Factory::createManager( $context, 'price' );
		$values = array( 'siteid' => $context->getLocale()->getSiteId() );

		return $this->createItemBase( $priceManager->createItem(), $values );
	}


	/**
	 * Returns order base product for the given product ID.
	 *
	 * @param integer $id Product ids to create product object for
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Order_Item_Base_Product_Interface Returns order base product item of the given id
	 * @throws MShop_Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->getItemBase( 'order.base.product.id', $id, $ref );
	}


	/**
	 * Adds or updates a order base product item to the storage.
	 *
	 * @param MShop_Common_Item_Interface $item New or existing product item that should be saved to the storage
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Order_Item_Base_Product_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Order_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if( !$item->isModified() ) { return; }

		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();
			$price = $item->getPrice();
			$date = date( 'Y-m-d H:i:s' );

			if( $id === null )
			{
				/** mshop/order/manager/base/product/default/item/insert
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
				 * @see mshop/order/manager/base/product/default/item/update
				 * @see mshop/order/manager/base/product/default/item/newid
				 * @see mshop/order/manager/base/product/default/item/delete
				 * @see mshop/order/manager/base/product/default/item/search
				 * @see mshop/order/manager/base/product/default/item/count
				 */
				$path = 'mshop/order/manager/base/product/default/item/insert';
			}
			else
			{
				/** mshop/order/manager/base/product/default/item/update
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
				 * @see mshop/order/manager/base/product/default/item/insert
				 * @see mshop/order/manager/base/product/default/item/newid
				 * @see mshop/order/manager/base/product/default/item/delete
				 * @see mshop/order/manager/base/product/default/item/search
				 * @see mshop/order/manager/base/product/default/item/count
				 */
				$path = 'mshop/order/manager/base/product/default/item/update';
			}

			$stmt = $this->getCachedStatement( $conn, $path );
			$stmt->bind( 1, $item->getBaseId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 3, $item->getOrderProductId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 4, $item->getType() );
			$stmt->bind( 5, $item->getProductId() );
			$stmt->bind( 6, $item->getProductCode() );
			$stmt->bind( 7, $item->getSupplierCode() );
			$stmt->bind( 8, $item->getWarehouseCode() );
			$stmt->bind( 9, $item->getName() );
			$stmt->bind( 10, $item->getMediaUrl() );
			$stmt->bind( 11, $item->getQuantity() );
			$stmt->bind( 12, $price->getValue() );
			$stmt->bind( 13, $price->getCosts() );
			$stmt->bind( 14, $price->getRebate() );
			$stmt->bind( 15, $price->getTaxRate() );
			$stmt->bind( 16, $item->getFlags(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 17, $item->getStatus(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 18, $item->getPosition(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 19, $date ); // mtime
			$stmt->bind( 20, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind( 21, $id, MW_DB_Statement_Abstract::PARAM_INT );
				$item->setId( $id );
			} else {
				$stmt->bind( 21, $date ); // ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** mshop/order/manager/base/product/default/item/newid
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
				 * @see mshop/order/manager/base/product/default/item/insert
				 * @see mshop/order/manager/base/product/default/item/update
				 * @see mshop/order/manager/base/product/default/item/delete
				 * @see mshop/order/manager/base/product/default/item/search
				 * @see mshop/order/manager/base/product/default/item/count
				 */
				$path = 'mshop/order/manager/base/product/default/item/newid';
				$item->setId( $this->newId( $conn, $context->getConfig()->get( $path, $path ) ) );
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}
	}


	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		/** mshop/order/manager/base/product/default/item/delete
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
		 * @see mshop/order/manager/base/product/default/item/insert
		 * @see mshop/order/manager/base/product/default/item/update
		 * @see mshop/order/manager/base/product/default/item/newid
		 * @see mshop/order/manager/base/product/default/item/search
		 * @see mshop/order/manager/base/product/default/item/count
		 */
		$path = 'mshop/order/manager/base/product/default/item/delete';
		$this->deleteItemsBase( $ids, $this->getContext()->getConfig()->get( $path, $path ) );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array Returns a list of attributes implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** classes/order/manager/base/product/submanagers
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
		$path = 'classes/order/manager/base/product/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, array( 'attribute' ), $withsub );
	}


	/**
	 * Returns a new sub manager specified by its name.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return MShop_Common_Manager_Interface Manager object
	 */
	public function getSubManager( $manager, $name = null )
	{
		/** classes/order/manager/base/product/name
		 * Class name of the used order base product manager implementation
		 *
		 * Each default order base product manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Order_Manager_Base_Product_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Order_Manager_Base_Product_Myproduct
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/order/manager/base/product/name = Myproduct
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
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
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
		 * ("MShop_Common_Manager_Decorator_*") around the order base product manager.
		 *
		 *  mshop/order/manager/base/product/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the order controller.
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
		 * ("MShop_Common_Manager_Decorator_*") around the order base product manager.
		 *
		 *  mshop/order/manager/base/product/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the order
		 * controller.
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
	 * Searches for order base products item based on the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object containing the conditions
	 * @param integer &$total Number of items that are available in total
	 * @return array List of products implementing MShop_Order_Item_Base_Product_Interface's
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$items = array();
		$context = $this->getContext();
		$priceManager = MShop_Factory::createManager( $context, 'price' );

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$required = array( 'order.base.product' );
			$sitelevel = MShop_Locale_Manager_Abstract::SITE_SUBTREE;

			/** mshop/order/manager/base/product/default/item/search
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
			 * @see mshop/order/manager/base/product/default/item/insert
			 * @see mshop/order/manager/base/product/default/item/update
			 * @see mshop/order/manager/base/product/default/item/newid
			 * @see mshop/order/manager/base/product/default/item/delete
			 * @see mshop/order/manager/base/product/default/item/count
			 */
			$cfgPathSearch = 'mshop/order/manager/base/product/default/item/search';

			/** mshop/order/manager/base/product/default/item/count
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
			 * @see mshop/order/manager/base/product/default/item/insert
			 * @see mshop/order/manager/base/product/default/item/update
			 * @see mshop/order/manager/base/product/default/item/newid
			 * @see mshop/order/manager/base/product/default/item/delete
			 * @see mshop/order/manager/base/product/default/item/search
			 */
			$cfgPathCount = 'mshop/order/manager/base/product/default/item/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount,
				$required, $total, $sitelevel );

			try
			{
				while( ( $row = $results->fetch() ) !== false )
				{
					$price = $priceManager->createItem();
					$price->setValue( $row['price'] );
					$price->setRebate( $row['rebate'] );
					$price->setCosts( $row['costs'] );
					$price->setTaxRate( $row['taxrate'] );
					$items[$row['id']] = array( 'price' => $price, 'item' => $row );
				}
			}
			catch( Exception $e )
			{
				$results->finish();
				throw $e;
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		$result = array();
		$attributes = $this->getAttributeItems( array_keys( $items ) );

		foreach( $items as $id => $row )
		{
			$attrList = ( isset( $attributes[$id] ) ? $attributes[$id] : array() );
			$result[$id] = $this->createItemBase( $row['price'], $row['item'], $attrList );
		}

		return $result;
	}


	/**
	 * Creates new order base product item object initialized with given parameters.
	 *
	 * @return MShop_Order_Item_Base_Product_Interface
	 */
	protected function createItemBase( MShop_Price_Item_Interface $price, array $values = array(), array $attributes = array() )
	{
		return new MShop_Order_Item_Base_Product_Default( $price, $values, $attributes );
	}

	/**
	 * Searches for attribute items connected with order product item.
	 *
	 * @param string[] $ids List of order product item IDs
	 * @return array List of items implementing MShop_Order_Item_Base_Product_Attribute_Interface
	 */
	protected function getAttributeItems( $ids )
	{
		$manager = $this->getSubmanager( 'attribute' );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.product.attribute.productid', $ids ) );
		$search->setSortations( array( $search->sort( '+', 'order.base.product.attribute.code' ) ) );
		$search->setSlice( 0, 0x7fffffff );

		$result = array();
		foreach( $manager->searchItems( $search ) as $item ) {
			$result[$item->getProductId()][$item->getId()] = $item;
		}

		return $result;
	}
}
