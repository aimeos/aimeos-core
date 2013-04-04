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
	private $_dbname = 'db';

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
		'order.base.service.shipping' => array(
			'code' => 'order.base.service.shipping',
			'internalcode' => 'mordbase."shipping"',
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
	 * Creates the manager that will use the given context object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );

		if( $context->getConfig()->get( 'resource/db-order/adapter', null ) !== null ) {
			$this->_dbname = 'db-order';
		}
	}


	/**
	 * Creates new order service item object.
	 *
	 * @return MShop_Order_Item_Base_Service_Interface New object
	 */
	public function createItem()
	{
		$context = $this->_getContext();
		$priceManager = MShop_Price_Manager_Factory::createManager($context);
		$values = array('siteid'=> $context->getLocale()->getSiteId());

		return $this->_createItem( $priceManager->createItem(), $values );
	}


	/**
	 * Adds or updates an order base service item to the storage.
	 *
	 * @param MShop_Order_Item_Base_Service_Interface $service Order service object
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
		$config = $context->getConfig();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire( $this->_dbname );

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
			$stmt->bind(9, $price->getShipping(), MW_DB_Statement_Abstract::PARAM_STR);
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
					$item->setId( $this->_newId( $conn, $config->get( $path, $path ) ) );
				} else {
					$item->setId($id);
				}
			}

			$dbm->release( $conn, $this->_dbname );
		}
		catch ( Exception $e )
		{
			$dbm->release( $conn, $this->_dbname );
			throw $e;
		}
	}


	/**
	 * Deletes an existing order service item from the storage.
	 *
	 * @param integer $serviceId Unique order service ID
	 */
	public function deleteItem( $serviceId )
	{
		$dbm = $this->_getContext()->getDatabaseManager();
		$conn = $dbm->acquire( $this->_dbname );

		try
		{
			$stmt = $this->_getCachedStatement($conn, 'mshop/order/manager/base/service/default/item/delete');
			$stmt->bind(1, $serviceId);
			$stmt->execute()->finish();

			$dbm->release( $conn, $this->_dbname );
		}
		catch ( Exception $e )
		{
			$dbm->release( $conn, $this->_dbname );
			throw $e;
		}
	}


	/**
	 * Returns the order service item object for the given ID.
	 *
	 * @param integer $id Order service ID
	 * @return MShop_Order_Item_Base_Service_Interface Order service item
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'order.base.service.id', $id, $ref );
	}


	/**
	 * Searches for order service items based on the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object containing the conditions
	 * @param integer &$total Number of items that are available in total
	 * @return array List of items implementing MShop_Order_Item_Base_Service_Interface
	 */
	public function searchItems(MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null)
	{
		$context = $this->_getContext();
		$priceManager = new MShop_Price_Manager_Default($context);

		$dbm = $context->getDatabaseManager();
		$logger = $context->getLogger();
		$config = $context->getConfig();

		$conn = $dbm->acquire( $this->_dbname );
		$items = array();
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
					$price->setShipping($row['shipping']);
					$price->setTaxRate($row['taxrate']);
					$items[ $row['id'] ] = array( 'price' => $price, 'item' => $row );
				}
			}
			catch( Exception $e )
			{
				$results->finish();
				throw $e;
			}

			$dbm->release( $conn, $this->_dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $this->_dbname );
			throw $e;
		}

		$attributes = $this->_getAttributeItems( array_keys( $items ) );
		$result = array();
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
		$list = array();

		foreach( $this->_searchConfig as $key => $fields ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default( $fields );
		}

		if ( $withsub === true )
		{
			$path = 'classes/order/manager/base/service/submanagers';
			foreach ( $this->_getContext()->getConfig()->get($path, array('attribute')) as $domain ) {
				$list = array_merge($list, $this->getSubManager($domain)->getSearchAttributes());
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for order service extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation (from configuration or "Default" if null)
	 * @return mixed Manager for different extensions, e.g attribute
	 */
	public function getSubManager($manager, $name = null)
	{
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
	protected function _createItem( MShop_Price_Item_Interface $price=null,
		array $values = array(), array $attributes = array() )
	{
		return new MShop_Order_Item_Base_Service_Default($price, $values, $attributes);
	}


	/**
	 * Searches for attribute items connected with order service item.
	 *
	 * @param integer $id of order service item
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
