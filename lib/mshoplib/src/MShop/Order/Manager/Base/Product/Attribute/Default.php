<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Product
 * @version $Id: Default.php 14854 2012-01-13 12:54:14Z doleiynyk $
 */


/**
 * Default order manager base product attribute.
 *
 * @package MShop
 * @subpackage Order
 */
class MShop_Order_Manager_Base_Product_Attribute_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Order_Manager_Base_Product_Attribute_Interface
{
	private $_dbname = 'db';

	private $_searchConfig = array(
		'order.base.product.attribute.id' => array(
			'code'=>'order.base.product.attribute.id',
			'internalcode'=>'mordbaprat."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_order_base_product_attr" AS mordbaprat ON ( mordbapr."id" = mordbaprat."ordprodid" )' ),
			'label'=>'Order base product attribute ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.product.attribute.siteid' => array(
			'code'=>'order.base.product.attribute.siteid',
			'internalcode'=>'mordbaprat."siteid"',
			'label'=>'Order base product attribute site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.product.attribute.productid' => array(
			'code'=>'order.base.product.attribute.productid',
			'internalcode'=>'mordbaprat."ordprodid"',
			'label'=>'Order base product ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.product.attribute.type' => array(
			'code'=>'order.base.product.attribute.type',
			'internalcode'=>'mordbaprat."type"',
			'label'=>'Order base product attribute type',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.product.attribute.code' => array(
			'code'=>'order.base.product.attribute.code',
			'internalcode'=>'mordbaprat."code"',
			'label'=>'Order base product attribute code',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.product.attribute.value' => array(
			'code'=>'order.base.product.attribute.value',
			'internalcode'=>'mordbaprat."value"',
			'label'=>'Order base product attribute value',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.product.attribute.name' => array(
			'code'=>'order.base.product.attribute.name',
			'internalcode'=>'mordbaprat."name"',
			'label'=>'Order base product attribute name',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.product.attribute.mtime' => array(
			'code'=>'order.base.product.attribute.mtime',
			'internalcode'=>'mordbaprat."mtime"',
			'label'=>'Order base product attribute modification time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.product.attribute.ctime'=> array(
			'code'=>'order.base.product.attribute.ctime',
			'internalcode'=>'mordbaprat."ctime"',
			'label'=>'Order base product attribute create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR
		),
		'order.base.product.attribute.editor'=> array(
			'code'=>'order.base.product.attribute.editor',
			'internalcode'=>'mordbaprat."editor"',
			'label'=>'Order base product attribute editor',
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
	 * Creates a new order base product attribute object.
	 *
	 * @return MShop_Order_Item_Base_Product_Attribute_Interface New media object
	 */
	public function createItem()
	{
		$values = array('siteid'=> $this->_getContext()->getLocale()->getSiteId());
		return $this->_createItem($values);
	}


	/**
	 * Returns an item for the given ID.
	 *
	 * @param integer $id ID of the item that should be retrieved
	 * @return MShop_Order_Item_Base_Product_Attribute_Interface Item object
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'order.base.product.attribute.id', $id, $ref );
	}


	/**
	 * Adds a new item to the storage or updates an existing one.
	 *
	 * @param MShop_Order_Item_Base_Product_Attribute_Interface $item New item that should
     * be saved to the storage
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Order_Item_Base_Product_Attribute_Interface';
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

			$path = 'mshop/order/manager/base/product/attribute/default/item/';
			$path .= ( $id === null ) ? 'insert' : 'update';

			$stmt = $this->_getCachedStatement($conn, $path);
			$stmt->bind(1, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT);
			$stmt->bind(2, $item->getProductId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind(3, $item->getType() );
			$stmt->bind(4, $item->getCode() );
			$stmt->bind(5, $item->getValue() );
			$stmt->bind(6, $item->getName() );
			$stmt->bind(7, date('Y-m-d H:i:s', time()) );// mtime
			$stmt->bind(8, $context->getEditor() );

			if ( $id !== null ) {
				$stmt->bind(9, $id, MW_DB_Statement_Abstract::PARAM_INT );
			} else {
				$stmt->bind(9, date( 'Y-m-d H:i:s', time() ), MW_DB_Statement_Abstract::PARAM_STR );// ctime
			}

			$stmt->execute()->finish();

			if( $fetch === true )
			{
				if( $id === null ) {
					$path = 'mshop/order/manager/base/product/attribute/default/item/newid';
					$item->setId( $this->_newId( $conn, $config->get( $path, $path ) ) );
				} else {
					$item->setId($id);
				}
			}

			$dbm->release( $conn, $this->_dbname );
		}
		catch(Exception $e)
		{
			$dbm->release( $conn, $this->_dbname );
			throw $e;
		}
	}


	/**
	 * Deletes a item from the storage.
	 *
	 * @param integer $id ID of an existing item in the storage that should be deleted
	 */
	public function deleteItem($id)
	{
		$this->deleteItems( array( $id ) );
	}


	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids ){
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( 'mshop/order/manager/base/product/attribute/default/item/delete' ) );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attributes implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes($withsub = true)
	{
		$list = array();

		foreach( $this->_searchConfig as $key => $fields ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default($fields);
		}

		if ($withsub === true)
		{
			$path = 'classes/order/manager/base/product/attribute/submanagers';
			foreach ($this->_getContext()->getConfig()->get($path, array()) as $domain) {
				$list = array_merge($list, $this->getSubManager($domain)->getSearchAttributes());
			}
		}

		return $list;
	}


	/**
	 * Returns a new sub manager specified by its name.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return MShop_Common_Manager_List_Interface List manager
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'order', 'base/product/' . $manager, $name );
	}


	/**
	 * Searches for order product attributes based on the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object containing the conditions
	 * @param integer &$total Number of items that are available in total
	 * @return array List of products implementing MShop_Order_Item_Base_Product_Attribute_Interface
	 */
	public function searchItems(MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null)
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
			$cfgPathSearch = 'mshop/order/manager/base/product/attribute/default/item/search';
			$cfgPathCount =  'mshop/order/manager/base/product/attribute/default/item/count';
			$required = array( 'order.base.product.attribute' );

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
	 * Creates new order base product attribute item object initialized with given parameters.
	 *
	 * @param array $values Associative array of order product attribute values
	 * @return MShop_Order_Item_Base_Product_Attribute_Interface
	 */
	protected function _createItem(array $values = array())
	{
		return new MShop_Order_Item_Base_Product_Attribute_Default( $values );
	}
}
