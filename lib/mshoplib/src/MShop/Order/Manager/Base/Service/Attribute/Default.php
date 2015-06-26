<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 */


/**
 * Order base service manager.
 *
 * @package MShop
 * @subpackage Order
 */
class MShop_Order_Manager_Base_Service_Attribute_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Order_Manager_Base_Service_Attribute_Interface
{
	private $_searchConfig = array(
		'order.base.service.attribute.id' => array(
			'code' => 'order.base.service.attribute.id',
			'internalcode' => 'mordbaseat."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_order_base_service_attr" AS mordbaseat ON ( mordbase."id" = mordbaseat."ordservid" )' ),
			'label' => 'Order base service attribute ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.service.attribute.siteid' => array(
			'code' => 'order.base.service.attribute.siteid',
			'internalcode' => 'mordbaseat."siteid"',
			'label' => 'Order base service attribute site ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.service.attribute.attributeid' => array(
			'code' => 'order.base.service.attribute.attributeid',
			'internalcode' => 'mordbaseat."attrid"',
			'label' => 'Order base service attribute original ID',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
			'public' => false,
		),
		'order.base.service.attribute.serviceid' => array(
			'code' => 'order.base.service.attribute.serviceid',
			'internalcode' => 'mordbaseat."ordservid"',
			'label' => 'Order base service ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.service.attribute.type' => array(
			'code' => 'order.base.service.attribute.type',
			'internalcode' => 'mordbaseat."type"',
			'label' => 'Order base service attribute type',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.service.attribute.code' => array(
			'code' => 'order.base.service.attribute.code',
			'internalcode' => 'mordbaseat."code"',
			'label' => 'Order base service attribute code',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.service.attribute.value' => array(
			'code' => 'order.base.service.attribute.value',
			'internalcode' => 'mordbaseat."value"',
			'label' => 'Order base service attribute value',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.service.attribute.name' => array(
			'code' => 'order.base.service.attribute.name',
			'internalcode' => 'mordbaseat."name"',
			'label' => 'Order base service attribute name',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.service.attribute.mtime' => array(
			'code' => 'order.base.service.attribute.mtime',
			'internalcode' => 'mordbaseat."mtime"',
			'label' => 'Order base service attribute modification time',
			'type' => 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.service.attribute.ctime'=> array(
			'code'=>'order.base.service.attribute.ctime',
			'internalcode'=>'mordbaseat."ctime"',
			'label'=>'Order base service attribute create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR
		),
		'order.base.service.attribute.editor'=> array(
			'code'=>'order.base.service.attribute.editor',
			'internalcode'=>'mordbaseat."editor"',
			'label'=>'Order base service attribute editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR
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
		/** mshop/order/manager/base/service/attribute/default/aggregate
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
		 * @see mshop/order/manager/base/service/attribute/default/item/insert
		 * @see mshop/order/manager/base/service/attribute/default/item/update
		 * @see mshop/order/manager/base/service/attribute/default/item/newid
		 * @see mshop/order/manager/base/service/attribute/default/item/delete
		 * @see mshop/order/manager/base/service/attribute/default/item/search
		 * @see mshop/order/manager/base/service/attribute/default/item/count
		 */
		$cfgkey = 'mshop/order/manager/base/service/attribute/default/aggregate';
		return $this->_aggregate( $search, $key, $cfgkey, array( 'order.base.service.attribute' ) );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param integer[] $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'classes/order/manager/base/service/attribute/submanagers';
		foreach( $this->_getContext()->getConfig()->get( $path, array() ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->_cleanup( $siteids, 'mshop/order/manager/base/service/attribute/default/item/delete' );
	}


	/**
	 * Creates a new order base attribute item object.
	 *
	 * @return MShop_Order_Item_Base_Service_Attribute_Interface
	 */
	public function createItem()
	{
		$values = array( 'siteid' => $this->_getContext()->getLocale()->getSiteId() );
		return $this->_createItem( $values );
	}


	/**
	 * Returns the attribute object for the given ID.
	 *
	 * @param integer $id Attribute ID
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Order_Item_Base_Service_Attribute_Interface Returns order base service attribute item of the given id
	 * @throws MShop_Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'order.base.service.attribute.id', $id, $ref );
	}


	/**
	 * Adds or updates an order service attribute item to the storage.
	 *
	 * @param MShop_Common_Item_Interface $item Order service attribute object
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Order_Item_Base_Service_Attribute_Interface';
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
			$date = date( 'Y-m-d H:i:s' );

			$path = 'mshop/order/manager/base/service/attribute/default/item/';
			$path .= ( $id === null ) ? 'insert' : 'update';

			$stmt = $this->_getCachedStatement( $conn, $path );
			$stmt->bind( 1, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $item->getAttributeId() );
			$stmt->bind( 3, $item->getServiceId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 4, $item->getType() );
			$stmt->bind( 5, $item->getCode() );
			$stmt->bind( 6, json_encode( $item->getValue() ) );
			$stmt->bind( 7, $item->getName() );
			$stmt->bind( 8, $date ); // mtime
			$stmt->bind( 9, $context->getEditor() );

			if ( $id !== null ) {
				$stmt->bind( 10, $id, MW_DB_Statement_Abstract::PARAM_INT );
			} else {
				$stmt->bind( 10, $date ); // ctime
			}

			$stmt->execute()->finish();

			if( $fetch === true )
			{
				if( $id === null ) {
					$path = 'mshop/order/manager/base/service/attribute/default/item/newid';
					$item->setId( $this->_newId( $conn, $context->getConfig()->get( $path, $path ) ) );
				} else {
					$item->setId($id);
				}
			}

			$dbm->release( $conn, $dbname );
		}
		catch ( Exception $e )
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
		$path = 'mshop/order/manager/base/service/attribute/default/item/delete';
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( $path, $path ) );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array Returns a list of attributes implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** classes/order/manager/base/service/attribute/submanagers
		 * List of manager names that can be instantiated by the order base service attribute manager
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
		$path = 'classes/order/manager/base/service/attribute/submanagers';

		return $this->_getSearchAttributes( $this->_searchConfig, $path, array(), $withsub );
	}


	/**
	 * Searches for order service attribute items based on the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object containing the conditions
	 * @param array $ref Not used
	 * @param integer &$total Number of items that are available in total
	 * @return array List of items implementing MShop_Order_Item_Base_Service_Attribute_Interface
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$context = $this->_getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		$items = array();

		try
		{
			$sitelevel = MShop_Locale_Manager_Abstract::SITE_SUBTREE;
			$cfgPathSearch = 'mshop/order/manager/base/service/attribute/default/item/search';
			$cfgPathCount =  'mshop/order/manager/base/service/attribute/default/item/count';
			$required = array( 'order.base.service.attribute' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount,
				$required, $total, $sitelevel );

			try
			{
				while( ( $row = $results->fetch() ) !== false )
				{
					if( ( $value = json_decode( $row['value'], true ) ) !== null ) {
						$row['value'] = $value;
					}
					$items[ $row['id'] ] = $this->_createItem( $row );
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

		return $items;
	}


	/**
	 * Returns a new manager for order service extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation (from configuration or "Default" if null)
	 * @return MShop_Common_Manager_Interface Manager for different extensions, e.g attribute
	 */
	public function getSubManager( $manager, $name = null )
	{
		/** classes/order/manager/base/service/attribute/name
		 * Class name of the used order base service attribute manager implementation
		 *
		 * Each default order base service attribute manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Order_Manager_Base_Service_Attribute_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Order_Manager_Base_Service_Attribute_Myattribute
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/order/manager/base/service/attribute/name = Myattribute
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

		/** mshop/order/manager/base/service/attribute/decorators/excludes
		 * Excludes decorators added by the "common" option from the order base service attribute manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the order base service attribute manager.
		 *
		 *  mshop/order/manager/base/service/attribute/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "mshop/common/manager/decorators/default" for the order base service attribute manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/base/service/attribute/decorators/global
		 * @see mshop/order/manager/base/service/attribute/decorators/local
		 */

		/** mshop/order/manager/base/service/attribute/decorators/global
		 * Adds a list of globally available decorators only to the order base service attribute manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the order base service attribute manager.
		 *
		 *  mshop/order/manager/base/service/attribute/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the order controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/base/service/attribute/decorators/excludes
		 * @see mshop/order/manager/base/service/attribute/decorators/local
		 */

		/** mshop/order/manager/base/service/attribute/decorators/local
		 * Adds a list of local decorators only to the order base service attribute manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the order base service attribute manager.
		 *
		 *  mshop/order/manager/base/service/attribute/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the order
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/base/service/attribute/decorators/excludes
		 * @see mshop/order/manager/base/service/attribute/decorators/global
		 */

		return $this->_getSubManager( 'order', 'base/service/attribute/' . $manager, $name );
	}


	/**
	 * Creates a new order service attribute item object initialized with given parameters.
	 *
	 * @return MShop_Order_Item_Base_Service_Attribute_Default New item
	 */
	protected function _createItem( array $values = array() )
	{
		return new MShop_Order_Item_Base_Service_Attribute_Default( $values );
	}
}
