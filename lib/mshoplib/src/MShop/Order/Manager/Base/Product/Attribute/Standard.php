<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
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
	implements \Aimeos\MShop\Order\Manager\Base\Product\Attribute\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private $searchConfig = array(
		'order.base.product.attribute.id' => array(
			'code' => 'order.base.product.attribute.id',
			'internalcode' => 'mordbaprat."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_order_base_product_attr" AS mordbaprat ON ( mordbapr."id" = mordbaprat."ordprodid" )' ),
			'label' => 'Product attribute ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'order.base.product.attribute.siteid' => array(
			'code' => 'order.base.product.attribute.siteid',
			'internalcode' => 'mordbaprat."siteid"',
			'label' => 'Product attribute site ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.base.product.attribute.attributeid' => array(
			'code' => 'order.base.product.attribute.attributeid',
			'internalcode' => 'mordbaprat."attrid"',
			'label' => 'Product attribute original ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.base.product.attribute.parentid' => array(
			'code' => 'order.base.product.attribute.parentid',
			'internalcode' => 'mordbaprat."ordprodid"',
			'label' => 'Product ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'order.base.product.attribute.name' => array(
			'code' => 'order.base.product.attribute.name',
			'internalcode' => 'mordbaprat."name"',
			'label' => 'Product attribute name',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.product.attribute.value' => array(
			'code' => 'order.base.product.attribute.value',
			'internalcode' => 'mordbaprat."value"',
			'label' => 'Product attribute value',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.product.attribute.code' => array(
			'code' => 'order.base.product.attribute.code',
			'internalcode' => 'mordbaprat."code"',
			'label' => 'Product attribute code',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.product.attribute.type' => array(
			'code' => 'order.base.product.attribute.type',
			'internalcode' => 'mordbaprat."type"',
			'label' => 'Product attribute type',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.product.attribute.quantity' => array(
			'code' => 'order.base.product.attribute.quantity',
			'internalcode' => 'mordbaprat."quantity"',
			'label' => 'Product attribute quantity',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'order.base.product.attribute.ctime' => array(
			'code' => 'order.base.product.attribute.ctime',
			'internalcode' => 'mordbaprat."ctime"',
			'label' => 'Product attribute create date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.base.product.attribute.mtime' => array(
			'code' => 'order.base.product.attribute.mtime',
			'internalcode' => 'mordbaprat."mtime"',
			'label' => 'Product attribute modify date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.base.product.attribute.editor' => array(
			'code' => 'order.base.product.attribute.editor',
			'internalcode' => 'mordbaprat."editor"',
			'label' => 'Product attribute editor',
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
	 * @param string $key Search key to aggregate items for
	 * @param string|null $value Search key for aggregating the value column
	 * @param string|null $type Type of the aggregation, empty string for count or "sum" or "avg" (average)
	 * @return \Aimeos\Map List of the search keys as key and the number of counted items as value
	 */
	public function aggregate( \Aimeos\MW\Criteria\Iface $search, string $key, string $value = null, string $type = null ) : \Aimeos\Map
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
		$cfgkey = 'mshop/order/manager/base/product/attribute/standard/aggregate' . $type;
		return $this->aggregateBase( $search, $key, $cfgkey, array( 'order.base.product.attribute' ), $value );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param string[] $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Order\Manager\Base\Product\Attribute\Iface Manager object for chaining method calls
	 */
	public function clear( array $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$path = 'mshop/order/manager/base/product/attribute/submanagers';
		foreach( $this->getContext()->getConfig()->get( $path, [] ) as $domain ) {
			$this->getObject()->getSubManager( $domain )->clear( $siteids );
		}

		return $this->clearBase( $siteids, 'mshop/order/manager/base/product/attribute/standard/delete' );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface New order product attribute item object
	 */
	public function createItem( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values['order.base.product.attribute.siteid'] = $this->getContext()->getLocale()->getSiteId();
		return $this->createItemBase( $values );
	}


	/**
	 * Creates a search critera object
	 *
	 * @param bool $default Add default criteria (optional)
	 * @return \Aimeos\MW\Criteria\Iface New search criteria object
	 */
	public function createSearch( bool $default = false ) : \Aimeos\MW\Criteria\Iface
	{
		$search = parent::createSearch( $default );
		$search->setSortations( [$search->sort( '+', 'order.base.product.attribute.id' )] );

		return $search;
	}


	/**
	 * Returns an item for the given ID.
	 *
	 * @param string $id ID of the item that should be retrieved
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool $default Add default criteria
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface Returns order base product attribute item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function getItem( string $id, array $ref = [], bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->getItemBase( 'order.base.product.attribute.id', $id, $ref, $default );
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Order\Manager\Base\Product\Attribute\Iface Manager object for chaining method calls
	 */
	public function deleteItems( array $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
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
		$path = 'mshop/order/manager/base/product/attribute/submanagers';
		return $this->getResourceTypeBase( 'order/base/product/attribute', $path, [], $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
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
	 * @return \Aimeos\MShop\Common\Manager\Iface Sub manager
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
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
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the order base
		 * product attribute manager.
		 *
		 *  mshop/order/manager/base/product/attribute/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the order
		 * base product attribute manager.
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
		 * ("\Aimeos\MShop\Order\Manager\Base\Product\Attribute\Decorator\*")
		 * around the order base product attribute manager.
		 *
		 *  mshop/order/manager/base/product/attribute/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\MShop\Order\Manager\Base\Product\Attribute\Decorator\Decorator2"
		 * only to the order base product attribute manager.
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
	 * Adds a new item to the storage or updates an existing one.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface $item Item that should be saved to the storage
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface
	{
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
				$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ) );
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
				$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ), false );
			}

			$idx = 1;
			$stmt = $this->getCachedStatement( $conn, $path, $sql );

			foreach( $columns as $name => $entry ) {
				$stmt->bind( $idx++, $item->get( $name ), $entry->getInternalType() );
			}

			$stmt->bind( $idx++, $item->getAttributeId() );
			$stmt->bind( $idx++, $item->getParentId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $item->getType() );
			$stmt->bind( $idx++, $item->getCode() );
			$stmt->bind( $idx++, json_encode( $item->getValue() ) );
			$stmt->bind( $idx++, $item->getQuantity(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $item->getName() );
			$stmt->bind( $idx++, $date ); // mtime
			$stmt->bind( $idx++, $context->getEditor() );
			$stmt->bind( $idx++, $item->getSiteId() );

			if( $id !== null ) {
				$stmt->bind( $idx++, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			} else {
				$stmt->bind( $idx++, $date ); // ctime
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
	 * Searches for order product attributes based on the given criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface with ids as keys
	 */
	public function searchItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		$items = [];

		try
		{
			$required = array( 'order.base.product.attribute' );

			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
			$level = $context->getConfig()->get( 'mshop/order/manager/sitemode', $level );

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
				$required, $total, $level );

			try
			{
				while( ( $row = $results->fetch() ) !== null )
				{
					if( ( $row['order.base.product.attribute.value'] = json_decode( $config = $row['order.base.product.attribute.value'], true ) ) === null )
					{
						$msg = sprintf( 'Invalid JSON as result of search for ID "%2$s" in "%1$s": %3$s', 'mshop_order_base_product_attribute.value', $row['order.base.product.attribute.id'], $config );
						$this->getContext()->getLogger()->log( $msg, \Aimeos\MW\Logger\Base::WARN );
					}

					if( $item = $this->applyFilter( $this->createItemBase( $row ) ) ) {
						$items[$row['order.base.product.attribute.id']] = $item;
					}
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

		return map( $items );
	}


	/**
	 * Creates new order base product attribute item object initialized with given parameters.
	 *
	 * @param array $values Associative array of order product attribute values
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface New order product attriubte item
	 */
	protected function createItemBase( array $values = [] ) : \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface
	{
		return new \Aimeos\MShop\Order\Item\Base\Product\Attribute\Standard( $values );
	}
}
