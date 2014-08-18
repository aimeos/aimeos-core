<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 */


/**
 * Default Manager Order service
 *
 * @package MShop
 * @subpackage Order
 */
class MShop_Order_Manager_Base_Service_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Order_Manager_Base_Service_Interface
{
	private $_searchConfig = array(
		'order.base.service.id' => array(
			'code' => 'order.base.service.id',
			'internalcode' => 'mordbase."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_order_base_service" AS mordbase ON ( mordba."id" = mordbase."baseid" )' ),
			'label' => 'Order base service ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.service.siteid' => array(
			'code' => 'order.base.service.siteid',
			'internalcode' => 'mordbase."siteid"',
			'label' => 'Order base service site ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.service.baseid' => array(
			'code' => 'order.base.service.baseid',
			'internalcode' => 'mordbase."baseid"',
			'label' => 'Order base ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.service.serviceid' => array(
			'code' => 'order.base.service.serviceid',
			'internalcode' => 'mordbase."servid"',
			'label' => 'Order base service original service ID',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.service.type' => array(
			'code' => 'order.base.service.type',
			'internalcode' => 'mordbase."type"',
			'label' => 'Order base service type',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.service.code' => array(
			'code' => 'order.base.service.code',
			'internalcode' => 'mordbase."code"',
			'label' => 'Order base service code',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.service.name' => array(
			'code' => 'order.base.service.name',
			'internalcode' => 'mordbase."name"',
			'label' => 'Order base service name',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.service.mediaurl' => array(
			'code'=>'order.base.service.mediaurl',
			'internalcode'=>'mordbase."mediaurl"',
			'label'=>'Order base service media url',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.service.price' => array(
			'code' => 'order.base.service.price',
			'internalcode' => 'mordbase."price"',
			'label' => 'Order base service price',
			'type' => 'decimal',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.service.costs' => array(
			'code' => 'order.base.service.costs',
			'internalcode' => 'mordbase."costs"',
			'label' => 'Order base service shipping',
			'type' => 'decimal',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.service.rebate' => array(
			'code' => 'order.base.service.rebate',
			'internalcode' => 'mordbase."rebate"',
			'label' => 'Order base service rebate',
			'type' => 'decimal',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.service.taxrate' => array(
			'code' => 'order.base.service.taxrate',
			'internalcode' => 'mordbase."taxrate"',
			'label' => 'Order base service taxrate',
			'type' => 'decimal',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.service.mtime' => array(
			'code' => 'order.base.service.mtime',
			'internalcode' => 'mordbase."mtime"',
			'label' => 'Order base service modification time',
			'type' => 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.service.ctime'=> array(
			'code'=>'order.base.service.ctime',
			'internalcode'=>'mordbase."ctime"',
			'label'=>'Order base service create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR
		),
		'order.base.service.editor'=> array(
			'code'=>'order.base.service.editor',
			'internalcode'=>'mordbase."editor"',
			'label'=>'Order base service editor',
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
	 * Removes old entries from the storage.
	 *
	 * @param integer[] $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'classes/order/manager/base/service/submanagers';
		foreach( $this->_getContext()->getConfig()->get( $path, array( 'attribute' ) ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->_cleanup( $siteids, 'mshop/order/manager/base/service/default/item/delete' );
	}


	/**
	 * Creates new order service item object.
	 *
	 * @return MShop_Order_Item_Base_Service_Interface New object
	 */
	public function createItem()
	{
		$context = $this->_getContext();
		$priceManager = MShop_Factory::createManager( $context, 'price' );
		$values = array( 'siteid'=> $context->getLocale()->getSiteId() );

		return $this->_createItem( $priceManager->createItem(), $values );
	}


	/**
	 * Adds or updates an order base service item to the storage.
	 *
	 * @param MShop_Common_Item_Interface $item Order base service object
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Order_Item_Base_Service_Interface';
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
			$price = $item->getPrice();

			$path = 'mshop/order/manager/base/service/default/item/';
			$path .= ( $id === null ) ? 'insert' : 'update';

			$stmt = $this->_getCachedStatement( $conn, $path );
			$stmt->bind(1, $item->getBaseId(), MW_DB_Statement_Abstract::PARAM_INT);
			$stmt->bind(2, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT);
			$stmt->bind(3, $item->getServiceId(), MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->bind(4, $item->getType(), MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->bind(5, $item->getCode(), MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->bind(6, $item->getName(), MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->bind(7, $item->getMediaUrl(), MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->bind(8, $price->getValue(), MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->bind(9, $price->getCosts(), MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->bind(10, $price->getRebate(), MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->bind(11, $price->getTaxRate(), MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->bind(12, date('Y-m-d H:i:s', time()), MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->bind(13, $context->getEditor() );

			if ( $id !== null ) {
				$stmt->bind(14, $id, MW_DB_Statement_Abstract::PARAM_INT);
			} else {
				$stmt->bind(14, date( 'Y-m-d H:i:s', time() ), MW_DB_Statement_Abstract::PARAM_STR );// ctime
			}

			$stmt->execute()->finish();

			if( $fetch === true )
			{
				if( $id === null ) {
					$path = 'mshop/order/manager/base/service/default/item/newid';
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
		$path = 'mshop/order/manager/base/service/default/item/delete';
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( $path, $path ) );
	}


	/**
	 * Returns the order service item object for the given ID.
	 *
	 * @param integer $id Order service ID
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Order_Item_Base_Service_Interface Returns order base service item of the given id
	 * @throws MShop_Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'order.base.service.id', $id, $ref );
	}


	/**
	 * Searches for order service items based on the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object containing the conditions
	 * @param array $ref Not used
	 * @param integer &$total Number of items that are available in total
	 * @return array List of items implementing MShop_Order_Item_Base_Service_Interface
	 */
	public function searchItems(MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null)
	{
		$items = array();
		$context = $this->_getContext();
		$priceManager = MShop_Factory::createManager( $context, 'price' );

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$sitelevel = MShop_Locale_Manager_Abstract::SITE_SUBTREE;
			$cfgPathSearch = 'mshop/order/manager/base/service/default/item/search';
			$cfgPathCount =  'mshop/order/manager/base/service/default/item/count';
			$required = array( 'order.base.service' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount,
				$required, $total, $sitelevel );

			try
			{
				while( ( $row = $results->fetch() ) !== false )
				{
					$price = $priceManager->createItem();
					$price->setValue($row['price']);
					$price->setRebate($row['rebate']);
					$price->setCosts($row['costs']);
					$price->setTaxRate($row['taxrate']);
					$items[ $row['id'] ] = array( 'price' => $price, 'item' => $row );
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
		$attributes = $this->_getAttributeItems( array_keys( $items ) );

		foreach ( $items as $id => $row )
		{
			$attrList = array();
			if( isset( $attributes[$id] ) ) {
				$attrList = $attributes[$id];
			}
			$result[ $id ] = $this->_createItem( $row['price'], $row['item'], $attrList );
		}

		return $result;
	}


	/**
	 * Returns the search attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attributes implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes($withsub = true)
	{
		/** classes/order/manager/base/service/submanagers
		 * List of manager names that can be instantiated by the order base service manager
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
		$path = 'classes/order/manager/base/service/submanagers';

		return $this->_getSearchAttributes( $this->_searchConfig, $path, array( 'attribute' ), $withsub );
	}


	/**
	 * Returns a new manager for order service extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation (from configuration or "Default" if null)
	 * @return MShop_Common_Manager_Interface Manager for different extensions, e.g attribute
	 */
	public function getSubManager($manager, $name = null)
	{
		/** classes/order/manager/base/service/name
		 * Class name of the used order base service manager implementation
		 *
		 * Each default order base service manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Order_Manager_Base_Service_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Order_Manager_Base_Service_Myservice
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/order/manager/base/service/name = Myservice
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyService"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */

		/** mshop/order/manager/base/service/decorators/excludes
		 * Excludes decorators added by the "common" option from the order base service manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the order base service manager.
		 *
		 *  mshop/order/manager/base/service/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "mshop/common/manager/decorators/default" for the order base service manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/base/service/decorators/global
		 * @see mshop/order/manager/base/service/decorators/local
		 */

		/** mshop/order/manager/base/service/decorators/global
		 * Adds a list of globally available decorators only to the order base service manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the order base service manager.
		 *
		 *  mshop/order/manager/base/service/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the order controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/base/service/decorators/excludes
		 * @see mshop/order/manager/base/service/decorators/local
		 */

		/** mshop/order/manager/base/service/decorators/local
		 * Adds a list of local decorators only to the order base service manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the order base service manager.
		 *
		 *  mshop/order/manager/base/service/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the order
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/base/service/decorators/excludes
		 * @see mshop/order/manager/base/service/decorators/global
		 */

		return $this->_getSubManager( 'order', 'base/service/' . $manager, $name );
	}


	/**
	 * Creates a new order service item object initialized with given parameters.
	 *
	 * @param MShop_Price_Item_Interface $price Price object
	 * @param array $values Associative list of values from the database
	 * @param array $attributes List of order service attribute items
	 * @return MShop_Order_Item_Base_Service_Interface Order item service object
	 */
	protected function _createItem( MShop_Price_Item_Interface $price,
		array $values = array(), array $attributes = array() )
	{
		return new MShop_Order_Item_Base_Service_Default( $price, $values, $attributes );
	}


	/**
	 * Searches for attribute items connected with order service item.
	 *
	 * @param string[] $ids List of order service item IDs
	 * @return array List of items implementing MShop_Order_Item_Base_Service_Attribute_Interface
	 */
	protected function _getAttributeItems( $ids )
	{
		$manager = $this->getSubManager( 'attribute' );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.service.attribute.serviceid', $ids ) );
		$search->setSortations( array( $search->sort( '+', 'order.base.service.attribute.code' ) ) );
		$search->setSlice( 0, 0x7fffffff );

		$result = array();
		foreach ($manager->searchItems( $search ) as $item) {
			$result[ $item->getServiceId() ][ $item->getId() ] = $item;
		}

		return $result;
	}
}
