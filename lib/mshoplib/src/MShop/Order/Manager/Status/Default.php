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
		$config = $context->getConfig();
		$dbm = $context->getDatabaseManager();
		$dbname = $config->get( 'resource/default', 'db' );
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

			$result = $stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				$path = 'mshop/order/manager/status/default/item/newid';
				$item->setId( $this->_newId( $conn, $config->get($path, $path) ) );
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
		$list = array();

		foreach( $this->_searchConfig as $key => $fields ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default( $fields );
		}

		if( $withsub === true )
		{
			$path = 'classes/order/manager/status/submanagers';
			foreach( $this->_getContext()->getConfig()->get( $path, array() ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
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
		return $this->_getSubManager( 'order', 'status/' . $manager, $name );
	}


	/**
	 * Searches for all items matching the given critera.
	 *
	 * @param MW_Common_Criteria_Interface $search Criteria object with conditions, sortations, etc.
	 * @param integer &$total Number of items that are available in total
	 * @return array List of items implementing MShop_Order_Item_Status_Interface
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$items = array();

		$context = $this->_getContext();
		$localeManager = MShop_Locale_Manager_Factory::createManager( $context );

		$dbm = $context->getDatabaseManager();
		$dbname = $context->getConfig()->get( 'resource/default', 'db' );
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