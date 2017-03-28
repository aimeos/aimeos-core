<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Product
 */


namespace Aimeos\MShop\Order\Manager\Base\Product\Attribute;


/**
 * Default order manager base product attribute.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Order\Manager\Base\Product\Attribute\Iface
{
	private $searchConfig = array(
		'order.base.product.attribute.id' => array(
			'code'=>'order.base.product.attribute.id',
			'internalcode'=>'mordbaprat."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_order_base_product_attr" AS mordbaprat ON ( mordbapr."id" = mordbaprat."ordprodid" )' ),
			'label'=>'Order base product attribute ID',
			'type'=> 'integer',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'order.base.product.attribute.siteid' => array(
			'code'=>'order.base.product.attribute.siteid',
			'internalcode'=>'mordbaprat."siteid"',
			'label'=>'Order base product attribute site ID',
			'type'=> 'integer',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'order.base.product.attribute.attributeid' => array(
			'code'=>'order.base.product.attribute.attributeid',
			'internalcode'=>'mordbaprat."attrid"',
			'label'=>'Order base product attribute original ID',
			'type'=> 'string',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.base.product.attribute.parentid' => array(
			'code'=>'order.base.product.attribute.parentid',
			'internalcode'=>'mordbaprat."ordprodid"',
			'label'=>'Order base product ID',
			'type'=> 'integer',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'order.base.product.attribute.type' => array(
			'code'=>'order.base.product.attribute.type',
			'internalcode'=>'mordbaprat."type"',
			'label'=>'Order base product attribute type',
			'type'=> 'string',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.product.attribute.code' => array(
			'code'=>'order.base.product.attribute.code',
			'internalcode'=>'mordbaprat."code"',
			'label'=>'Order base product attribute code',
			'type'=> 'string',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.product.attribute.value' => array(
			'code'=>'order.base.product.attribute.value',
			'internalcode'=>'mordbaprat."value"',
			'label'=>'Order base product attribute value',
			'type'=> 'string',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.product.attribute.name' => array(
			'code'=>'order.base.product.attribute.name',
			'internalcode'=>'mordbaprat."name"',
			'label'=>'Order base product attribute name',
			'type'=> 'string',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.product.attribute.mtime' => array(
			'code'=>'order.base.product.attribute.mtime',
			'internalcode'=>'mordbaprat."mtime"',
			'label'=>'Order base product attribute modification time',
			'type'=> 'datetime',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.product.attribute.ctime'=> array(
			'code'=>'order.base.product.attribute.ctime',
			'internalcode'=>'mordbaprat."ctime"',
			'label'=>'Order base product attribute create date/time',
			'type'=> 'datetime',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR
		),
		'order.base.product.attribute.editor'=> array(
			'code'=>'order.base.product.attribute.editor',
			'internalcode'=>'mordbaprat."editor"',
			'label'=>'Order base product attribute editor',
			'type'=> 'string',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR
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
		/** mshop/order/manager/base/product/attribute/standard/aggregate/mysql
		 * Counts the number of records grouped by the values in the key column and matched by the given criteria
		 *
		 * @see mshop/order/manager/base/product/attribute/standard/aggregate/ansi
		 */

		/** mshop/order/manager/base/product/attribute/standard/aggregate/ansi
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
		 * @see mshop/order/manager/base/product/attribute/standard/insert/ansi
		 * @see mshop/order/manager/base/product/attribute/standard/update/ansi
		 * @see mshop/order/manager/base/product/attribute/standard/newid/ansi
		 * @see mshop/order/manager/base/product/attribute/standard/delete/ansi
		 * @see mshop/order/manager/base/product/attribute/standard/search/ansi
		 * @see mshop/order/manager/base/product/attribute/standard/count/ansi
		 */
		$cfgkey = 'mshop/order/manager/base/product/attribute/standard/aggregate';
		return $this->aggregateBase( $search, $key, $cfgkey, array( 'order.base.product.attribute' ) );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param integer[] $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'mshop/order/manager/base/product/attribute/submanagers';
		foreach( $this->getContext()->getConfig()->get( $path, [] ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->cleanupBase( $siteids, 'mshop/order/manager/base/product/attribute/standard/delete' );
	}


	/**
	 * Creates a new order base product attribute object.
	 *
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface New media object
	 */
	public function createItem()
	{
		$values = array( 'order.base.product.attribute.siteid'=> $this->getContext()->getLocale()->getSiteId() );
		return $this->createItemBase( $values );
	}


	/**
	 * Returns an item for the given ID.
	 *
	 * @param integer $id ID of the item that should be retrieved
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface Returns order base product attribute item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = [], $default = false )
	{
		return $this->getItemBase( 'order.base.product.attribute.id', $id, $ref, $default );
	}


	/**
	 * Adds a new item to the storage or updates an existing one.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface $item New item that should
	 * be saved to the storage
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Iface $item, $fetch = true )
	{
		$iface = '\\Aimeos\\MShop\\Order\\Item\\Base\\Product\\Attribute\\Iface';
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
				/** mshop/order/manager/base/product/attribute/standard/insert/mysql
				 * Inserts a new order record into the database table
				 *
				 * @see mshop/order/manager/base/product/attribute/standard/insert/ansi
				 */

				/** mshop/order/manager/base/product/attribute/standard/insert/ansi
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
				 * @see mshop/order/manager/base/product/attribute/standard/update/ansi
				 * @see mshop/order/manager/base/product/attribute/standard/newid/ansi
				 * @see mshop/order/manager/base/product/attribute/standard/delete/ansi
				 * @see mshop/order/manager/base/product/attribute/standard/search/ansi
				 * @see mshop/order/manager/base/product/attribute/standard/count/ansi
				 */
				$path = 'mshop/order/manager/base/product/attribute/standard/insert';
			}
			else
			{
				/** mshop/order/manager/base/product/attribute/standard/update/mysql
				 * Updates an existing order record in the database
				 *
				 * @see mshop/order/manager/base/product/attribute/standard/update/ansi
				 */

				/** mshop/order/manager/base/product/attribute/standard/update/ansi
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
				 * @see mshop/order/manager/base/product/attribute/standard/insert/ansi
				 * @see mshop/order/manager/base/product/attribute/standard/newid/ansi
				 * @see mshop/order/manager/base/product/attribute/standard/delete/ansi
				 * @see mshop/order/manager/base/product/attribute/standard/search/ansi
				 * @see mshop/order/manager/base/product/attribute/standard/count/ansi
				 */
				$path = 'mshop/order/manager/base/product/attribute/standard/update';
			}

			$stmt = $this->getCachedStatement( $conn, $path );
			$stmt->bind( 1, $item->getSiteId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 2, $item->getAttributeId() );
			$stmt->bind( 3, $item->getParentId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 4, $item->getType() );
			$stmt->bind( 5, $item->getCode() );
			$stmt->bind( 6, json_encode( $item->getValue() ) );
			$stmt->bind( 7, $item->getName() );
			$stmt->bind( 8, $date ); // mtime
			$stmt->bind( 9, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind( 10, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$item->setId( $id );
			} else {
				$stmt->bind( 10, $date ); // ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** mshop/order/manager/base/product/attribute/standard/newid/mysql
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * @see mshop/order/manager/base/product/attribute/standard/newid/ansi
				 */

				/** mshop/order/manager/base/product/attribute/standard/newid/ansi
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
				 * @see mshop/order/manager/base/product/attribute/standard/insert/ansi
				 * @see mshop/order/manager/base/product/attribute/standard/update/ansi
				 * @see mshop/order/manager/base/product/attribute/standard/delete/ansi
				 * @see mshop/order/manager/base/product/attribute/standard/search/ansi
				 * @see mshop/order/manager/base/product/attribute/standard/count/ansi
				 */
				$path = 'mshop/order/manager/base/product/attribute/standard/newid';
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
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		/** mshop/order/manager/base/product/attribute/standard/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/order/manager/base/product/attribute/standard/delete/ansi
		 */

		/** mshop/order/manager/base/product/attribute/standard/delete/ansi
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
		 * @see mshop/order/manager/base/product/attribute/standard/insert/ansi
		 * @see mshop/order/manager/base/product/attribute/standard/update/ansi
		 * @see mshop/order/manager/base/product/attribute/standard/newid/ansi
		 * @see mshop/order/manager/base/product/attribute/standard/search/ansi
		 * @see mshop/order/manager/base/product/attribute/standard/count/ansi
		 */
		$path = 'mshop/order/manager/base/product/attribute/standard/delete';
		$this->deleteItemsBase( $ids, $path );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param boolean $withsub Return also the resource type of sub-managers if true
	 * @return array Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( $withsub = true )
	{
		$path = 'mshop/order/manager/base/product/attribute/submanagers';

		return $this->getResourceTypeBase( 'order/base/product/attribute', $path, [], $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attributes implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** mshop/order/manager/base/product/attribute/submanagers
		 * List of manager names that can be instantiated by the order base product attribute manager
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
		$path = 'mshop/order/manager/base/product/attribute/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, [], $withsub );
	}


	/**
	 * Returns a new sub manager specified by its name.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Lists\Iface List manager
	 */
	public function getSubManager( $manager, $name = null )
	{
		/** mshop/order/manager/base/product/attribute/name
		 * Class name of the used order base product attribute manager implementation
		 *
		 * Each default order base product attribute manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\MShop\Order\Manager\Base\Product\Attribute\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\MShop\Order\Manager\Base\Product\Attribute\Myattribute
		 *
		 * then you have to set the this configuration option:
		 *
		 *  mshop/order/manager/base/product/attribute/name = Myattribute
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyAttribute"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */

		/** mshop/order/manager/base/product/attribute/decorators/excludes
		 * Excludes decorators added by the "common" option from the order base product attribute manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the order base product attribute manager.
		 *
		 *  mshop/order/manager/base/product/attribute/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
		 * "mshop/common/manager/decorators/default" for the order base product attribute manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/base/product/attribute/decorators/global
		 * @see mshop/order/manager/base/product/attribute/decorators/local
		 */

		/** mshop/order/manager/base/product/attribute/decorators/global
		 * Adds a list of globally available decorators only to the order base product attribute manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the order base product attribute manager.
		 *
		 *  mshop/order/manager/base/product/attribute/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the order controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/base/product/attribute/decorators/excludes
		 * @see mshop/order/manager/base/product/attribute/decorators/local
		 */

		/** mshop/order/manager/base/product/attribute/decorators/local
		 * Adds a list of local decorators only to the order base product attribute manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the order base product attribute manager.
		 *
		 *  mshop/order/manager/base/product/attribute/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator2" only to the order
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/base/product/attribute/decorators/excludes
		 * @see mshop/order/manager/base/product/attribute/decorators/global
		 */

		return $this->getSubManagerBase( 'order', 'base/product/attribute/' . $manager, $name );
	}


	/**
	 * Searches for order product attributes based on the given criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param integer|null &$total Number of items that are available in total
	 * @return array List of products implementing \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface
	 */
	public function searchItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], &$total = null )
	{
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		$items = [];

		try
		{
			$required = array( 'order.base.product.attribute' );
			$sitelevel = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;

			/** mshop/order/manager/base/product/attribute/standard/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see mshop/order/manager/base/product/attribute/standard/search/ansi
			 */

			/** mshop/order/manager/base/product/attribute/standard/search/ansi
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
			 * @see mshop/order/manager/base/product/attribute/standard/insert/ansi
			 * @see mshop/order/manager/base/product/attribute/standard/update/ansi
			 * @see mshop/order/manager/base/product/attribute/standard/newid/ansi
			 * @see mshop/order/manager/base/product/attribute/standard/delete/ansi
			 * @see mshop/order/manager/base/product/attribute/standard/count/ansi
			 */
			$cfgPathSearch = 'mshop/order/manager/base/product/attribute/standard/search';

			/** mshop/order/manager/base/product/attribute/standard/count/mysql
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * @see mshop/order/manager/base/product/attribute/standard/count/ansi
			 */

			/** mshop/order/manager/base/product/attribute/standard/count/ansi
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
			 * @see mshop/order/manager/base/product/attribute/standard/insert/ansi
			 * @see mshop/order/manager/base/product/attribute/standard/update/ansi
			 * @see mshop/order/manager/base/product/attribute/standard/newid/ansi
			 * @see mshop/order/manager/base/product/attribute/standard/delete/ansi
			 * @see mshop/order/manager/base/product/attribute/standard/search/ansi
			 */
			$cfgPathCount = 'mshop/order/manager/base/product/attribute/standard/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount,
				$required, $total, $sitelevel );

			try
			{
				while( ( $row = $results->fetch() ) !== false )
				{
					if( ( $value = json_decode( $row['order.base.product.attribute.value'], true ) ) !== null ) {
						$row['order.base.product.attribute.value'] = $value;
					}
					$items[$row['order.base.product.attribute.id']] = $this->createItemBase( $row );
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

		return $items;
	}


	/**
	 * Creates new order base product attribute item object initialized with given parameters.
	 *
	 * @param array $values Associative array of order product attribute values
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface
	 */
	protected function createItemBase( array $values = [] )
	{
		return new \Aimeos\MShop\Order\Item\Base\Product\Attribute\Standard( $values );
	}
}
