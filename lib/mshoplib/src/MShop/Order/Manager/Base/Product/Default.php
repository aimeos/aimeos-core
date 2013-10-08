<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Product
 */


/**
 * Default order manager base product.
 *
 * @package MShop
 * @subpackage Order
 */
class MShop_Order_Manager_Base_Product_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Order_Manager_Base_Product_Interface
{
	private $_dbname = 'db';

	private $_searchConfig = array(
		'order.base.product.id' => array(
			'code'=>'order.base.product.id',
			'internalcode'=>'mordbapr."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_order_base_product" AS mordbapr ON ( mordba."id" = mordbapr."baseid" )' ),
			'label'=>'Order base product ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.product.baseid' => array(
			'code'=>'order.base.product.baseid',
			'internalcode'=>'mordbapr."baseid"',
			'label'=>'Order base product base ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.product.siteid' => array(
			'code'=>'order.base.product.siteid',
			'internalcode'=>'mordbapr."siteid"',
			'label'=>'Order base product site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.product.orderproductid' => array(
			'code'=>'order.base.product.orderproductid',
			'internalcode'=>'mordbapr."ordprodid"',
			'label'=>'Order base product parent ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.product.type' => array(
			'code'=>'order.base.product.type',
			'internalcode'=>'mordbapr."type"',
			'label'=>'Order base product type',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
			'public' => true,
		),
		'order.base.product.productid' => array(
			'code'=>'order.base.product.productid',
			'internalcode'=>'mordbapr."prodid"',
			'label'=>'Order base product original ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.product.prodcode' => array(
			'code'=>'order.base.product.prodcode',
			'internalcode'=>'mordbapr."prodcode"',
			'label'=>'Order base product code',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.product.suppliercode' => array(
			'code'=>'order.base.product.suppliercode',
			'internalcode'=>'mordbapr."suppliercode"',
			'label'=>'Order base product supplier code',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.product.name' => array(
			'code'=>'order.base.product.name',
			'internalcode'=>'mordbapr."name"',
			'label'=>'Order base product name',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.product.mediaurl' => array(
			'code'=>'order.base.product.mediaurl',
			'internalcode'=>'mordbapr."mediaurl"',
			'label'=>'Order base product media url',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.product.quantity' => array(
			'code'=>'order.base.product.quantity',
			'internalcode'=>'mordbapr."amount"',
			'label'=>'Order base product quantity',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'order.base.product.price' => array(
			'code'=>'order.base.product.price',
			'internalcode'=>'mordbapr."price"',
			'label'=>'Order base product price',
			'type'=> 'decimal',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.product.costs' => array(
			'code'=>'order.base.product.costs',
			'internalcode'=>'mordbapr."costs"',
			'label'=>'Order base product shipping',
			'type'=> 'decimal',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.product.rebate' => array(
			'code'=>'order.base.product.rebate',
			'internalcode'=>'mordbapr."rebate"',
			'label'=>'Order base product rebate',
			'type'=> 'decimal',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.product.taxrate' => array(
			'code'=>'order.base.product.taxrate',
			'internalcode'=>'mordbapr."taxrate"',
			'label'=>'Order base product taxrate',
			'type'=> 'decimal',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.product.quantity' => array(
			'code'=>'order.base.product.quantity',
			'internalcode'=>'mordbapr."quantity"',
			'label'=>'Order base product quantity',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'order.base.product.flags' => array(
			'code'=>'order.base.product.flags',
			'internalcode'=>'mordbapr."flags"',
			'label'=>'Order base product flags',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'order.base.product.position' => array(
			'code'=>'order.base.product.position',
			'internalcode'=>'mordbapr."pos"',
			'label'=>'Order base product position',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'order.base.product.status' => array(
			'code'=>'order.base.product.status',
			'internalcode'=>'mordbapr."status"',
			'label'=>'Order base product status',
			'type'=> 'boolean',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_BOOL,
		),
		'order.base.product.mtime' => array(
			'code'=>'order.base.product.mtime',
			'internalcode'=>'mordbapr."mtime"',
			'label'=>'Order base product modification time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.product.ctime'=> array(
			'code'=>'order.base.product.ctime',
			'internalcode'=>'mordbapr."ctime"',
			'label'=>'Order base product create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR
		),
		'order.base.product.editor'=> array(
			'code'=>'order.base.product.editor',
			'internalcode'=>'mordbapr."editor"',
			'label'=>'Order base product editor',
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
	 * Create new order base product item object.
	 *
	 * @return MShop_Order_Item_Base_Product_Interface
	 */
	public function createItem()
	{
		$context = $this->_getContext();
		$priceManager = MShop_Price_Manager_Factory::createManager( $context );
		$values = array( 'siteid' => $context->getLocale()->getSiteId() );

		return $this->_createItem( $priceManager->createItem(), $values );
	}


	/**
	 * Returns order base product for the given product ID.
	 *
	 * @param integer $id Product ids to create product object for
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Order_Item_Base_Product_Interface Returns order base product item of the given id
	 * @throws MShop_Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'order.base.product.id', $id, $ref );
	}


	/**
	 * Adds or updates a order base product item to the storage.
	 *
	 * @param MShop_Order_Item_Base_Product_Interface $product New or existing
	 * product item that should be saved to the storage.
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Order_Item_Base_Product_Interface';
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

			$path = 'mshop/order/manager/base/product/default/item/';
			$path .= ( $id === null ) ? 'insert' : 'update';

			$stmt = $this->_getCachedStatement( $conn, $path );
			$stmt->bind(1, $item->getBaseId(), MW_DB_Statement_Abstract::PARAM_INT);
			$stmt->bind(2, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT);
			$stmt->bind(3, $item->getOrderProductId(), MW_DB_Statement_Abstract::PARAM_INT);
			$stmt->bind(4, $item->getType(), MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->bind(5, $item->getProductId(), MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->bind(6, $item->getProductCode(), MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->bind(7, $item->getSupplierCode(), MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->bind(8, $item->getName(), MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->bind(9, $item->getMediaUrl(), MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->bind(10, $item->getQuantity(), MW_DB_Statement_Abstract::PARAM_INT);
			$stmt->bind(11, $price->getValue(), MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->bind(12, $price->getCosts(), MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->bind(13, $price->getRebate(), MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->bind(14, $price->getTaxRate(), MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->bind(15, $item->getFlags(), MW_DB_Statement_Abstract::PARAM_INT);
			$stmt->bind(16, $item->getStatus(), MW_DB_Statement_Abstract::PARAM_INT);
			$stmt->bind(17, date('Y-m-d H:i:s', time()), MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->bind(18, $context->getEditor() );

			if ( $id !== null ) {
				$stmt->bind(19, $item->getPosition(), MW_DB_Statement_Abstract::PARAM_INT);
				$stmt->bind(20, $id, MW_DB_Statement_Abstract::PARAM_INT);
			} else {
				$stmt->bind(19, date('Y-m-d H:i:s', time()), MW_DB_Statement_Abstract::PARAM_STR);// ctime
				$stmt->bind(20, $item->getBaseId(), MW_DB_Statement_Abstract::PARAM_INT);
			}

			$stmt->execute()->finish();


			if ( $id === null ) {
				$path = 'mshop/order/manager/base/product/default/item/newid';
				$item->setId( $this->_newId( $conn, $config->get($path, $path) ) );
			} else {
				$item->setId($id);
			}

			$dbm->release( $conn );
		}
		catch(Exception $e)
		{
			$dbm->release( $conn );
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
		$path = 'mshop/order/manager/base/product/default/item/delete';
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( $path, $path ) );
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
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default( $fields );
		}

		if ($withsub === true)
		{
			$path = 'classes/order/manager/base/product/submanagers';
			foreach( $this->_getContext()->getConfig()->get( $path, array( 'attribute' ) ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
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
		return $this->_getSubManager( 'order', 'base/product/' . $manager, $name );
	}


	/**
	 * Searches for order base products item based on the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object containing the conditions
	 * @param integer &$total Number of items that are available in total
	 * @return array List of products implementing MShop_Order_Item_Base_Product_Interface's
	 */
	public function searchItems(MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null)
	{
		$context = $this->_getContext();
		$logger = $context->getLogger();
		$config = $context->getConfig();
		$dbm = $context->getDatabaseManager();

		$priceManager = MShop_Price_Manager_Factory::createManager( $context );

		$items = array();
		$conn = $dbm->acquire( $this->_dbname );

		try
		{
			$sitelevel = MShop_Locale_Manager_Abstract::SITE_SUBTREE;
			$cfgPathSearch = 'mshop/order/manager/base/product/default/item/search';
			$cfgPathCount =  'mshop/order/manager/base/product/default/item/count';
			$required = array( 'order.base.product' );

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

			$dbm->release( $conn, $this->_dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $this->_dbname );
			throw $e;
		}

		$result = array();
		$attributes = $this->_getAttributeItems( array_keys( $items ) );

		foreach ( $items as $id => $row )
		{
			$attrList = ( isset( $attributes[$id] ) ? $attributes[$id] : array() );
			$result[ $id ] = $this->_createItem( $row['price'], $row['item'], $attrList );
		}

		return $result;
	}


	/**
	 * Creates new order base product item object initialized with given parameters.
	 *
	 * @return MShop_Order_Item_Base_Product_Interface
	 */
	protected function _createItem(MShop_Price_Item_Interface $price, array $values = array(), array $attributes = array())
	{
		return new MShop_Order_Item_Base_Product_Default( $price, $values, $attributes );
	}

	/**
	 * Searches for attribute items connected with order product item.
	 *
	 * @param array $ids Ids of order product item
	 * @return array List of items implementing MShop_Order_Item_Base_Product_Attribute_Interface
	 */
	protected function _getAttributeItems( $ids )
	{
		$manager = $this->getSubmanager( 'attribute' );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.product.attribute.productid', $ids ) );
		$search->setSortations( array( $search->sort( '+', 'order.base.product.attribute.code' ) ) );
		$search->setSlice( 0, 0x7fffffff );

		$result = array();
		foreach ($manager->searchItems( $search ) as $item) {
			$result[ $item->getProductId() ][ $item->getId() ] = $item;
		}

		return $result;
	}
}
