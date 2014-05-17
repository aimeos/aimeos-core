<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 */


/**
 * Order base dicount manager class.
 *
 * @package MShop
 * @subpackage Order
 */
class MShop_Order_Manager_Base_Coupon_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Order_Manager_Base_Coupon_Interface
{
	private $_dbname = 'db';

	private $_searchConfig = array(
		'order.base.coupon.id'=> array(
			'code'=>'order.base.coupon.id',
			'internalcode'=>'mordbaco."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_order_base_coupon" AS mordbaco ON ( mordba."id" = mordbaco."baseid" )' ),
			'label'=>'Order base coupon ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.coupon.siteid'=> array(
			'code'=>'order.base.coupon.siteid',
			'internalcode'=>'mordbaco."siteid"',
			'label'=>'Order base coupon site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.coupon.baseid'=> array(
			'code'=>'order.base.coupon.baseid',
			'internalcode'=>'mordbaco."baseid"',
			'label'=>'Order base ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.coupon.ordprodid'=> array(
			'code'=>'order.base.coupon.productid',
			'internalcode'=>'mordbaco."ordprodid"',
			'label'=>'Order coupon product ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'order.base.coupon.code'=> array(
			'code'=>'order.base.coupon.code',
			'internalcode'=>'mordbaco."code"',
			'label'=>'Order base coupon code',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.coupon.mtime'=> array(
			'code'=>'order.base.coupon.mtime',
			'internalcode'=>'mordbaco."mtime"',
			'label'=>'Order base coupon modification time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.coupon.ctime'=> array(
			'code'=>'order.base.coupon.ctime',
			'internalcode'=>'mordbaco."ctime"',
			'label'=>'Order base coupon creation date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.coupon.editor'=> array(
			'code'=>'order.base.coupon.editor',
			'internalcode'=>'mordbaco."editor"',
			'label'=>'Order base coupon editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);


	/**
	 * Creates a new order base coupon object.
	 *
	 * @return MShop_Order_Item_Base_Coupon_Interface New order coupon object
	 */
	public function createItem()
	{
		$values = array('siteid'=> $this->_getContext()->getLocale()->getSiteId());
		return $this->_createItem($values);
	}


	/**
	 * Returns the order coupon item for the given ID.
	 *
	 * @param integer $id ID of the item that should be retrieved
	 * @return MShop_Order_Item_Base_Coupon_Interface Item for the given ID
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'order.base.coupon.id', $id, $ref );
	}


	/**
	 * Adds a new item to the storage or updates an existing one.
	 *
	 * @param MShop_Order_Item_Base_Coupon_Interface $item Item that should be saved to the storage
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $coupon, $fetch = true )
	{
		$iface = 'MShop_Order_Item_Base_Coupon_Interface';
		if( !( $coupon instanceof $iface ) ) {
			throw new MShop_Order_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if ( !$coupon->isModified() ) {
			return;
		}

		$context = $this->_getContext();

		$dbname = $this->_getResourceName( 'db-order' );
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $coupon->getId();

			$path = 'mshop/order/manager/base/coupon/default/item/';
			$path .=  ( $id === null ? 'insert' : 'update' );

			$stmt = $this->_getCachedStatement($conn, $path);

			$stmt->bind( 1, $coupon->getBaseId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 3, $coupon->getProductId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 4, $coupon->getCode() );
			$stmt->bind( 5, date( 'Y-m-d H:i:s' ) );
			$stmt->bind( 6, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind( 7, $id, MW_DB_Statement_Abstract::PARAM_INT );
			} else {
				$stmt->bind( 7, date( 'Y-m-d H:i:s' ) );// ctime
			}

			$stmt->execute()->finish();

			if( $fetch === true )
			{
				if( $id === null ) {
					$path = 'mshop/order/manager/base/coupon/default/item/newid';
					$coupon->setId( $this->_newId( $conn, $context->getConfig()->get( $path, $path ) ) );
				} else {
					$coupon->setId( $id );
				}
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
		$dbname = $this->_getResourceName( 'db-order' );
		$path = 'mshop/order/manager/base/coupon/default/item/delete';
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( $path, $path ), true, 'id', $dbname );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array Returns a list of attributes implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes($withsub = true)
	{
		$list = array();

		foreach( $this->_searchConfig as $key => $fields ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default($fields);
		}

		if($withsub === true)
		{
			$cfg = $this->_getContext()->getConfig()->get('classes/order/manager/base/coupon/submanagers', array());
			foreach ($cfg as $domain) {
				$list = array_merge($list, $this->getSubManager($domain)->getSearchAttributes());
			}
		}

		return $list;
	}


	/**
	 * Returns the item objects matched by the given search criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search criteria object
	 * @param integer &$total Number of items that are available in total
	 * @return array Return a list of items implementing MShop_Order_Item_Base_Coupon_Interface
	 * @throws MShop_Order_Exception If creation of an item fails
	 */
	public function searchItems(MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null)
	{
		$context = $this->_getContext();
		$logger = $context->getLogger();

		$dbname = $this->_getResourceName( 'db-order' );
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire( $dbname );

		$items = array();
		$config = $context->getConfig();

		try
		{
			$level = MShop_Locale_Manager_Abstract::SITE_SUBTREE;
			$cfgPathSearch = 'mshop/order/manager/base/coupon/default/item/search';
			$cfgPathCount =  'mshop/order/manager/base/coupon/default/item/count';
			$required = array( 'order.base.coupon' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

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
	 * Returns a new sub manager specified by its name.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager object
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'order', 'base/coupon/' . $manager, $name );
	}


	/**
	 * Create new order base coupon item object initialized with given parameters.
	 *
	 * @return MShop_Order_Item_Base_Product_Interface New item
	 */
	protected function _createItem(array $values = array())
	{
		return new MShop_Order_Item_Base_Coupon_Default($values);
	}


	/**
	 * Returns the name of the requested resource or the name of the default resource.
	 *
	 * @param string $name Name of the requested resource
	 * @return string Name of the resource
	 */
	protected function _getResourceName( $name = 'db-order' )
	{
		return parent::_getResourceName( $name );
	}
}
