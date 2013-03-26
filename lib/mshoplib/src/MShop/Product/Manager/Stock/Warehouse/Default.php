<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Product
 * @version $Id: Default.php 14682 2012-01-04 11:30:14Z nsendetzky $
 */


/**
 * Default product stock warehouse manager implementation.
 *
 * @package MShop
 * @subpackage Product
 */
class MShop_Product_Manager_Stock_Warehouse_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Product_Manager_Stock_Warehouse_Interface
{
	private $_searchConfig = array(
		'product.stock.warehouse.id'=> array(
			'code'=>'product.stock.warehouse.id',
			'internalcode'=>'mprostwa."id"',
			'internaldeps'=>array( 'LEFT JOIN "mshop_product_stock_warehouse" AS mprostwa ON mprost."warehouseid" = mprostwa."id"' ),
			'label'=>'Product stock warehouse ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.stock.warehouse.siteid'=> array(
			'code'=>'product.stock.warehouse.siteid',
			'internalcode'=>'mprostwa."siteid"',
			'label'=>'Product stock warehouse site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.stock.warehouse.code'=> array(
			'code'=>'product.stock.warehouse.code',
			'internalcode'=>'mprostwa."code"',
			'label'=>'Product stock warehouse code',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.stock.warehouse.label'=> array(
			'code'=>'product.stock.warehouse.label',
			'internalcode'=>'mprostwa."label"',
			'label'=>'Product stock warehouse label',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.stock.warehouse.status'=> array(
			'code'=>'product.stock.warehouse.status',
			'internalcode'=>'mprostwa."status"',
			'label'=>'Product stock warehouse status',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'product.stock.warehouse.mtime'=> array(
			'code'=>'product.stock.warehouse.mtime',
			'internalcode'=>'mprostwa."mtime"',
			'label'=>'Product stock warehouse modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.stock.warehouse.ctime'=> array(
			'code'=>'product.stock.warehouse.ctime',
			'internalcode'=>'mprostwa."ctime"',
			'label'=>'Product stock warehouse creation date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.stock.warehouse.editor'=> array(
			'code'=>'product.stock.warehouse.editor',
			'internalcode'=>'mprostwa."editor"',
			'label'=>'Product stock warehouse editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);


	/**
	 * Returns a new sub manager of the given type and name.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return MShop_Product_Manager_Interface manager
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'product', 'warehouse/' . $manager, $name );
	}


	/**
	 * Creates new warehouse item object.
	 *
	 * @return MShop_Product_Item_Warehouse_Interface New product warehouse item object
	 */
	public function createItem()
	{
		$values = array( 'siteid' => $this->_getContext()->getLocale()->getSiteId() );
		return $this->_createItem( $values );
	}


	/**
	 * Inserts the new warehouse item
	 *
	 * @param MShop_Product_Item_Stock_Warehouse_Interface $item Warehouse item which should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Product_Item_Stock_Warehouse_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Product_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		$context = $this->_getContext();
		$config = $context->getConfig();
		$locale = $context->getLocale();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$id = $item->getId();

			if( $id === null ) {
				$path = 'mshop/product/manager/stock/warehouse/default/item/insert';
			} else {
				$path = 'mshop/product/manager/stock/warehouse/default/item/update';
			}

			$stmt = $this->_getCachedStatement( $conn, $path );

			$stmt->bind( 1, $locale->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $item->getCode(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 3, $item->getLabel(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 4, $item->getStatus(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 5, date('Y-m-d H:i:s', time()));//mtime
			$stmt->bind( 6, $context->getEditor());

			if( $id !== null ) {
				$stmt->bind( 7, $id, MW_DB_Statement_Abstract::PARAM_INT );
			} else {
				$stmt->bind( 7, date('Y-m-d H:i:s', time()));//ctime
			}

			$result = $stmt->execute()->finish();

			if( $fetch === true )
			{
				if( $id === null ) {
					$path = 'mshop/product/manager/stock/warehouse/default/item/newid';
					$item->setId( $this->_newId( $conn, $config->get( $path, $path ) ) );
				} else {
					$item->setId( $id );
				}
			}

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}
	}


	/**
	 * Delete a warehouse item by given Id
	 *
	 * @param Integer $id Id of the warehouse item to delete
	 */
	public function deleteItem( $id )
	{
		$dbm = $this->_getContext()->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$stmt = $this->_getCachedStatement( $conn, 'mshop/product/manager/stock/warehouse/default/item/delete' );
			$stmt->bind( 1, $id, MW_DB_Statement_Abstract::PARAM_INT );
			$result = $stmt->execute()->finish();

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}
	}


	/**
	 * Creates a warehouse item object for the given item id.
	 *
	 * @param Integer $id Id of warehouse item
	 * @return MShop_Product_Item_Warehouse_Interface Product warehouse item
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'product.stock.warehouse.id', $id, $ref );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array Returns a list of attribtes implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = array();

		foreach( $this->_searchConfig as $key => $fields ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default( $fields );
		}

		if( $withsub === true )
		{
			$path = 'classes/product/manager/stock/warehouse/submanagers';
			foreach( $this->_getContext()->getConfig()->get( $path, array() ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Search for warehouse items based on the given critera.
	 *
	 * Possible search keys: 'product.warehouse.id', 'product.warehouse.siteid', 'product.warehouse.code'
	 *
	 * @param MW_Common_Criteria_Interface $search Search object with search conditions
	 * @param integer &$total Number of items that are available in total
	 * @return array List of warehouse items implementing MShop_Product_Item_Warehouse_Interface
	 * @throws MShop_Product_Exception if creating items failed
	 * @see MW_Common_Criteria_SQL
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$dbm = $this->_getContext()->getDatabaseManager();
		$conn = $dbm->acquire();
		$items = array();

		try
		{
			$level = MShop_Locale_Manager_Abstract::SITE_ALL;
			$cfgPathSearch = 'mshop/product/manager/stock/warehouse/default/item/search';
			$cfgPathCount =  'mshop/product/manager/stock/warehouse/default/item/count';
			$required = array( 'product.stock.warehouse' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );
			while( ( $row = $results->fetch() ) !== false ) {
				$items[ $row['id'] ] = $this->_createItem( $row );
			}

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}

		return $items;
	}


	/**
	 * Creates new warehouse item object.
	 *
	 * @param array $values Possible optional array keys can be given: id, siteid, code
	 * @return MShop_Product_Item_Warehouse_Default New Warehouse item object
	 */
	protected function _createItem( array $values = array() )
	{
		return new MShop_Product_Item_Stock_Warehouse_Default( $values );
	}

}
