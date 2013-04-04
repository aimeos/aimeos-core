<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 * @version $Id: Default.php 14854 2012-01-13 12:54:14Z doleiynyk $
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
	private $_dbname = 'db';

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
	 * @return MShop_Order_Item_Base_Service_Attribute_Interface Service attribute object
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'order.base.service.attribute.id', $id, $ref );
	}


	/**
	 * Adds or updates an order service attribute item to the storage.
	 *
	 * @param MShop_Order_Item_Base_Service_Attribute_Interface $attribute Service attribute object
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
		$config = $context->getConfig();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire( $this->_dbname );

		try
		{
			$id = $item->getId();

			$path = 'mshop/order/manager/base/service/attribute/default/item/';
			$path .= ( $id === null ) ? 'insert' : 'update';

			$stmt = $this->_getCachedStatement( $conn, $path );
			$stmt->bind(1, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind(2, $item->getServiceId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind(3, $item->getType() );
			$stmt->bind(4, $item->getCode() );
			$stmt->bind(5, json_encode( $item->getValue() ) );
			$stmt->bind(6, $item->getName() );
			$stmt->bind(7, date( 'Y-m-d H:i:s', time() ), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind(8, $context->getEditor() );

			if ( $id !== null ) {
				$stmt->bind(9, $id, MW_DB_Statement_Abstract::PARAM_INT);
			} else {
				$stmt->bind(9, date( 'Y-m-d H:i:s', time() ), MW_DB_Statement_Abstract::PARAM_STR );// ctime
			}

			$stmt->execute()->finish();

			if( $fetch === true )
			{
				if( $id === null ) {
					$path = 'mshop/order/manager/base/service/attribute/default/item/newid';
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
	 * Deletes an existing order service attribute item from the storage.
	 *
	 * @param integer $id Order service attribute ID
	 */
	public function deleteItem($id)
	{
		$dbm = $this->_getContext()->getDatabaseManager();
		$conn = $dbm->acquire( $this->_dbname );

		try
		{
			$stmt = $this->_getCachedStatement( $conn, 'mshop/order/manager/base/service/attribute/default/item/delete' );
			$stmt->bind(1, $id, MW_DB_Statement_Abstract::PARAM_INT );
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
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array Returns a list of attributes implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = array();

		foreach( $this->_searchConfig as $key => $fields ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default( $fields );
		}

		if ( $withsub === true )
		{
			$path = 'classes/order/manager/base/service/attribute/submanagers';
			foreach ( $this->_getContext()->getConfig()->get( $path, array() ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Searches for order service attribute items based on the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object containing the conditions
	 * @param integer &$total Number of items that are available in total
	 * @return array List of items implementing MShop_Order_Item_Base_Service_Attribute_Interface
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$context = $this->_getContext();
		$dbm = $context->getDatabaseManager();
		$logger = $context->getLogger();
		$conn = $dbm->acquire( $this->_dbname );

		$items = array();
		$config = $context->getConfig();

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
					$row['value'] = json_decode( $row['value'], true );
					if( is_null( $row['value'] ) ) {
						throw new MShop_Order_Exception( sprintf( 'Invalid JSON as result of search for order service attribute with ID "%1$d".', $row['id'] ) );
					}
					$items[ $row['id'] ] = $this->_createItem( $row );
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

		return $items;
	}


	/**
	 * Returns a new manager for order service extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation (from configuration or "Default" if null)
	 * @return mixed Manager for different extensions, e.g attribute
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'order', 'base/service/attribute/' . $manager, $name );
	}


	/**
	 * Creates a new order service attribute item object initialized with given parameters.
	 *
	 * @return MShop_Order_Item_Base_Product_Interface New item
	 */
	protected function _createItem( array $values = array() )
	{
		return new MShop_Order_Item_Base_Service_Attribute_Default( $values );
	}
}
