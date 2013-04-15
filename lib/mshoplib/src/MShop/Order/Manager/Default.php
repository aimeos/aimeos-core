<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 * @version $Id: Default.php 14854 2012-01-13 12:54:14Z doleiynyk $
 */


/**
 * Default order manager implementation.
 *
 * @package MShop
 * @subpackage Order
 */
class MShop_Order_Manager_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Order_Manager_Interface
{
	private $_dbname = 'db';

	private $_searchConfig = array(
		'order.id'=> array(
			'code'=>'order.id',
			'internalcode'=>'mord."id"',
			'label'=>'Order invoice ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'order.siteid'=> array(
			'code'=>'order.siteid',
			'internalcode'=>'mord."siteid"',
			'label'=>'Order invoice site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.baseid'=> array(
			'code'=>'order.baseid',
			'internalcode'=>'mord."baseid"',
			'label'=>'Order base ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.type'=> array(
			'code'=>'order.type',
			'internalcode'=>'mord."type"',
			'label'=>'Order type',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.datepayment'=> array(
			'code'=>'order.datepayment',
			'internalcode'=>'mord."datepayment"',
			'label'=>'Order purchase date',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.datedelivery'=> array(
			'code'=>'order.datedelivery',
			'internalcode'=>'mord."datedelivery"',
			'label'=>'Order delivery date',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.statusdelivery'=> array(
			'code'=>'order.statusdelivery',
			'internalcode'=>'mord."statusdelivery"',
			'label'=>'Order delivery status',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'order.statuspayment'=> array(
			'code'=>'order.statuspayment',
			'internalcode'=>'mord."statuspayment"',
			'label'=>'Order payment status',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'order.flag'=> array(
			'code'=>'order.flag',
			'internalcode'=>'mord."flag"',
			'label'=>'Order flag',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'order.emailflag'=> array(
			'code'=>'order.emailflag',
			'internalcode'=>'mord."emailflag"',
			'label'=>'Order email flag',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'order.relatedid'=> array(
			'code'=>'order.relatedid',
			'internalcode'=>'mord."relatedid"',
			'label'=>'Order related order ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'order.mtime'=> array(
			'code'=>'order.mtime',
			'internalcode'=>'mord."mtime"',
			'label'=>'Order modification date',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.ctime'=> array(
			'code'=>'order.ctime',
			'internalcode'=>'mord."ctime"',
			'label'=>'Order creation date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.editor'=> array(
			'code'=>'order.editor',
			'internalcode'=>'mord."editor"',
			'label'=>'Order editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
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
	 * Returns a new and empty invoice.
	 *
	 * @return MShop_Order_Item_Interface Invoice without assigned values or items
	 */
	public function createItem()
	{
		$values = array('siteid'=> $this->_getContext()->getLocale()->getSiteId());
		return $this->_createItem($values);
	}


	/**
	 * Creates a search object.
	 *
	 * @param boolean $default Add default criteria; Optional
	 * @return MW_Common_Criteria_Interface
	 */
	public function createSearch( $default = false )
	{
		$search = parent::createSearch( $default );

		if( $default === true )
		{
			$expr = array(
				$search->getConditions(),
				$search->compare( '!=', 'order.statuspayment', MShop_Order_Item_Abstract::PAY_UNFINISHED ),
				$search->compare( '!=', 'order.statusdelivery', MShop_Order_Item_Abstract::STAT_UNFINISHED ),
			);

			$search->setConditions( $search->combine( '&&', $expr ) );
		}

		return $search;
	}


	/**
	 * Creates a one-time order in the storage from the given invoice object.
	 *
	 * @param MShop_Order_Item_Interface $item Invoice with necessary values
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Order_Item_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Order_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if($item->getBaseId() === null) {
			throw new MShop_Order_Exception('Required order base ID is missing');
		}

		if( !$item->isModified() ) { return; }

		$context = $this->_getContext();
		$config = $context->getConfig();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire( $this->_dbname );

		try
		{
			$id = $item->getId();

			$path = 'mshop/order/manager/default/item/';
			$path .= ( $id === null ) ? 'insert' : 'update';

			$stmt = $this->_getCachedStatement( $conn, $path );

			$stmt->bind(1, $item->getBaseId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind(2, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT);
			$stmt->bind(3, $item->getType() );
			$stmt->bind(4, $item->getDatePayment() );
			$stmt->bind(5, $item->getDateDelivery() );
			$stmt->bind(6, $item->getDeliveryStatus(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind(7, $item->getPaymentStatus(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind(8, $item->getFlag(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind(9, $item->getEmailFlag(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind(10, $item->getRelatedId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind(11, date('Y-m-d H:i:s', time()));//mtime
			$stmt->bind(12, $context->getEditor());

			if( $id !== null ) {
				$stmt->bind(13, $id, MW_DB_Statement_Abstract::PARAM_INT);
				$item->setId($id); //is not modified anymore
			} else {
				$stmt->bind(13, date('Y-m-d H:i:s', time()));//ctime
			}

			$result = $stmt->execute()->finish();

			if( $id === null && $fetch === true ) {
				$path = 'mshop/order/manager/default/item/newid';
				$item->setId( $this->_newId( $conn, $config->get( $path, $path ) ) );
			}

			$dbm->release( $conn, $this->_dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $this->_dbname );
			throw $e;
		}
	}


	/**
	 * Returns an order invoice item built from database values.
	 *
	 * @param integer $id Unique id of the order invoice
	 * @return MShop_Order_Item_Interface order Invoice item
	 */
	public function getItem( $id, array $ref = array())
	{
		return $this->_getItem( 'order.id', $id, $ref );
	}


	/**
	 * Deletes an item with given ID.
	 *
	 * @param integer $id Unique ID of the invoice
	 */
	public function deleteItem( $id )
	{
		$context = $this->_getContext();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire( $this->_dbname );

		try
		{
			$stmt = $this->_getCachedStatement($conn, 'mshop/order/manager/default/item/delete');
			$stmt->bind(1, $id, MW_DB_Statement_Abstract::PARAM_INT);
			$result = $stmt->execute()->finish();

			$dbm->release( $conn, $this->_dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $this->_dbname );
			throw $e;
		}
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
			$path = 'classes/order/manager/submanagers';
			$default = array( 'base', 'status' );
			foreach( $this->_getContext()->getConfig()->get( $path, $default ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Searches for orders based on the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object containing the conditions
	 * @param integer &$total Number of items that are available in total
	 * @return array List of items implementing MShop_Order_Item_Interface
	 * @throws MShop_Order_Exception If creating items failed
	 * @throws MW_DB_Exception If a database operation fails
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
			$cfgPathSearch = 'mshop/order/manager/default/item/search';
			$cfgPathCount =  'mshop/order/manager/default/item/count';
			$required = array( 'order' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount,
				$required, $total, $sitelevel );

			try
			{
				while( ( $row = $results->fetch() ) !== false ) {
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
	 * Returns a new manager for order extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g base, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'order', $manager, $name );
	}


	/**
	 * Creates a new order item.
	 *
	 * @param array $values List of attributes for order item
	 * @return MShop_Order_Item_Interface New order item
	 */
	protected function _createItem( array $values = array() )
	{
		return new MShop_Order_Item_Default( $values );
	}
}
