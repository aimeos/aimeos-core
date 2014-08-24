<?php
/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 */

/**
 * Default implementation for order status manager.
 *
 * @package MShop
 * @subpackage Order
 */
class MShop_Order_Manager_Status_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Order_Manager_Status_Interface
{
	private $_searchConfig = array(
		'order.status.id'=> array(
			'code'=>'order.status.id',
			'internalcode'=>'mordst."id"',
			'internaldeps'=> array( 'LEFT JOIN "mshop_order_status" AS mordst ON ( mord."id" = mordst."parentid" )' ),
			'label'=>'Order status ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.status.siteid'=> array(
			'code'=>'order.status.siteid',
			'internalcode'=>'mordst."siteid"',
			'label'=>'Order status site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.status.parentid'=> array(
			'code'=>'order.status.parentid',
			'internalcode'=>'mordst."parentid"',
			'label'=>'Order status parent id',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.status.type'=> array(
			'code'=>'order.status.type',
			'internalcode'=>'mordst."type"',
			'label'=>'Order status type',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.status.value'=> array(
			'code'=>'order.status.value',
			'internalcode'=>'mordst."value"',
			'label'=>'Order status value',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.status.mtime'=> array(
			'code'=>'order.status.mtime',
			'internalcode'=>'mordst."mtime"',
			'label'=>'Order status modification time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.status.ctime'=> array(
			'code'=>'order.status.ctime',
			'internalcode'=>'mordst."ctime"',
			'label'=>'Order status create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.status.editor'=> array(
			'code'=>'order.status.editor',
			'internalcode'=>'mordst."editor"',
			'label'=>'Order status editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
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
		$this->_setResourceName( 'db-order' );
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
		/** mshop/order/manager/status/default/aggregate
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
		 * @see mshop/order/manager/status/default/item/insert
		 * @see mshop/order/manager/status/default/item/update
		 * @see mshop/order/manager/status/default/item/newid
		 * @see mshop/order/manager/status/default/item/delete
		 * @see mshop/order/manager/status/default/item/search
		 * @see mshop/order/manager/status/default/item/count
		 */
		$cfgkey = 'mshop/order/manager/status/default/aggregate';
		return $this->_aggregate( $search, $key, $cfgkey, array( 'order.status' ) );
	}
	

	/**
	 * Removes old entries from the storage.
	 *
	 * @param integer[] $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'classes/order/manager/status/submanagers';
		foreach( $this->_getContext()->getConfig()->get( $path, array() ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->_cleanup( $siteids, 'mshop/order/manager/status/default/item/delete' );
	}


	/**
	 * Creates a new order status object.
	 *
	 * @return MShop_Order_Item_Status_Interface New item object
	 */
	public function createItem()
	{
		$values = array('siteid'=> $this->_getContext()->getLocale()->getSiteId());
		return $this->_createItem($values);
	}


	/**
	 * Adds or updates an order status object.
	 *
	 * @param MShop_Order_Item_Status_Interface $item Order status object whose data should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Order_Item_Status_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Order_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if( !$item->isModified() ) { return; }

		$context = $this->_getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();

			$path = 'mshop/order/manager/status/default/item/';
			$path .= ( $id === null ) ? 'insert' : 'update';

			$stmt = $this->_getCachedStatement( $conn, $path );

			$stmt->bind(1, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind(2, $item->getParentID(), MW_DB_Statement_Abstract::PARAM_INT);
			$stmt->bind(3, $item->getType() );
			$stmt->bind(4, $item->getValue() );
			$stmt->bind(5, date('Y-m-d H:i:s', time()));//mtime
			$stmt->bind(6, $context->getEditor());

			if( $id !== null )
			{
				$stmt->bind(7, $id, MW_DB_Statement_Abstract::PARAM_INT);
				$item->setId($id); //is not modified anymore
			}
			else {
				$stmt->bind(7, date('Y-m-d H:i:s', time()));//ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				$path = 'mshop/order/manager/status/default/item/newid';
				$item->setId( $this->_newId( $conn, $context->getConfig()->get( $path, $path ) ) );
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
	 * Returns the order status item specified by its ID.
	 *
	 * @param integer $id Unique ID of the order status item
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Order_Item_Status_Interface Returns order status item of the given id
	 * @throws MShop_Order_Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'order.status.id', $id, $ref );
	}


	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		$path = 'mshop/order/manager/status/default/item/delete';
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( $path, $path ) );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** classes/order/manager/status/submanagers
		 * List of manager names that can be instantiated by the order status manager
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
		$path = 'classes/order/manager/status/submanagers';

		return $this->_getSearchAttributes( $this->_searchConfig, $path, array(), $withsub );
	}


	/**
	 * Returns a new manager for order status extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return MShop_Common_Manager_Interface Manager extending the domain functionality
	 */
	public function getSubManager( $manager, $name = null )
	{
		/** classes/order/manager/status/name
		 * Class name of the used order status manager implementation
		 *
		 * Each default order status manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Order_Manager_Status_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Order_Manager_Status_Mystatus
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/order/manager/status/name = Mystatus
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyStatus"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */

		/** mshop/order/manager/status/decorators/excludes
		 * Excludes decorators added by the "common" option from the order status manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the order status manager.
		 *
		 *  mshop/order/manager/status/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "mshop/common/manager/decorators/default" for the order status manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/status/decorators/global
		 * @see mshop/order/manager/status/decorators/local
		 */

		/** mshop/order/manager/status/decorators/global
		 * Adds a list of globally available decorators only to the order status manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the order status manager.
		 *
		 *  mshop/order/manager/status/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the order controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/status/decorators/excludes
		 * @see mshop/order/manager/status/decorators/local
		 */

		/** mshop/order/manager/status/decorators/local
		 * Adds a list of local decorators only to the order status manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the order status manager.
		 *
		 *  mshop/order/manager/status/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the order
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/status/decorators/excludes
		 * @see mshop/order/manager/status/decorators/global
		 */

		return $this->_getSubManager( 'order', 'status/' . $manager, $name );
	}


	/**
	 * Searches for all items matching the given critera.
	 *
	 * @param MW_Common_Criteria_Interface $search Criteria object with conditions, sortations, etc.
	 * @param array $ref Not used
	 * @param integer &$total Number of items that are available in total
	 * @return array List of items implementing MShop_Order_Item_Status_Interface
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$items = array();
		$context = $this->_getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$sitelevel = MShop_Locale_Manager_Abstract::SITE_SUBTREE;
			$cfgPathSearch = 'mshop/order/manager/status/default/item/search';
			$cfgPathCount =  'mshop/order/manager/status/default/item/count';
			$required = array( 'order.status' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount,
				$required, $total, $sitelevel );

			while( ( $row = $results->fetch() ) !== false ) {
				$items[ $row['id'] ] = $this->_createItem( $row );
			}

			$dbm->release( $conn, $dbname );

		}
		catch ( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $items;
	}


	/**
	 * Creates a new order status object.
	 *
	 * @param array $values List of attributes for the order status object
	 * @return MShop_Order_Item_Status_Interface New order status object
	 */
	protected function _createItem( array $values = array() )
	{
		return new MShop_Order_Item_Status_Default( $values );
	}
}