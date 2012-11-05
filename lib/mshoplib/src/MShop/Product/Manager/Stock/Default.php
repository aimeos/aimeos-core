<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Product
 * @version $Id: Default.php 14854 2012-01-13 12:54:14Z doleiynyk $
 */


/**
 * Default product stock manager implementation.
 *
 * @package MShop
 * @subpackage Product
 */
class MShop_Product_Manager_Stock_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Product_Manager_Stock_Interface
{
	private $_searchConfig = array(
		'product.stock.id'=> array(
			'code'=>'product.stock.id',
			'internalcode'=>'mprost."id"',
			'internaldeps'=>array( 'LEFT JOIN "mshop_product_stock" AS mprost ON ( mprost."prodid" = mpro."id" )' ),
			'label'=>'Product stock ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.stock.siteid'=> array(
			'code'=>'product.stock.siteid',
			'internalcode'=>'mprost."siteid"',
			'label'=>'Product stock site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.stock.productid'=> array(
			'code'=>'product.stock.productid',
			'internalcode'=>'mprost."prodid"',
			'label'=>'Product stock product ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.stock.warehouseid' => array(
			'code'=>'product.stock.warehouseid',
			'internalcode'=>'mprost."warehouseid"',
			'label'=>'Product stock warehouse ID',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.stock.stocklevel' => array(
			'code'=>'product.stock.stocklevel',
			'internalcode'=>'mprost."stocklevel"',
			'label'=>'Product stock level',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'product.stock.dateback' => array(
			'code'=>'product.stock.dateback',
			'internalcode'=>'mprost."backdate"',
			'label'=>'Product stock back in stock date/time',
			'type'=> 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.stock.mtime'=> array(
			'code'=>'product.stock.mtime',
			'internalcode'=>'mprost."mtime"',
			'label'=>'Product stock modification date',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.stock.ctime'=> array(
			'code'=>'product.stock.ctime',
			'internalcode'=>'mprost."ctime"',
			'label'=>'Product stock creation date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.stock.editor'=> array(
			'code'=>'product.stock.editor',
			'internalcode'=>'mprost."editor"',
			'label'=>'Product stock editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);


	/**
	 * Creates new stock item object.
	 *
	 * @return MShop_Product_Item_Stock_Interface New product stock item object
	 */
	public function createItem()
	{
		$values = array('siteid' => $this->_getContext()->getLocale()->getSiteId());
		return $this->_createItem($values);
	}


	/**
	 * Inserts the new stock item
	 *
	 * @param MShop_Product_Item_Stock_Interface $item Stock item which should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Product_Item_Stock_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Product_Exception( sprintf( 'Object does not implement "%1$s"', $iface ) );
		}

		if( !$item->isModified() ) { return; }

		$context = $this->_getContext();
		$config = $context->getConfig();
		$locale = $context->getLocale();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$id = $item->getId();

			$path = 'mshop/product/manager/stock/default/item/';
			$path .= ( $id === null ) ? 'insert' : 'update';

			$stmt = $this->_getCachedStatement( $conn, $path );
			$stmt->bind( 1, $item->getProductId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $locale->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 3, $item->getWarehouseId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 4, $item->getStocklevel(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 5, $item->getDateBack(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 6, date('Y-m-d H:i:s', time()));//mtime
			$stmt->bind( 7, $context->getEditor());

			if ( $id !== null ) {
				$stmt->bind(8, $id, MW_DB_Statement_Abstract::PARAM_INT);
			} else {
				$stmt->bind(8, date('Y-m-d H:i:s', time()));//ctime
			}

			$result = $stmt->execute()->finish();

			if( $fetch === true )
			{
				if( $id === null ) {
					$path = 'mshop/product/manager/stock/default/item/newid';
					$item->setId( $this->_newId( $conn, $config->get($path, $path) ) );
				} else {
					$item->setId( $id ); // modified false
				}
			}

			$dbm->release($conn);
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}
	}


	/**
	 * Delete a stock item by given Id
	 *
	 * @param Integer $id Id of the stock item to delete
	 */
	public function deleteItem( $id )
	{
		$dbm = $this->_getContext()->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$stmt = $this->_getCachedStatement($conn, 'mshop/product/manager/stock/default/item/delete');
			$stmt->bind(1, $id, MW_DB_Statement_Abstract::PARAM_INT);
			$result = $stmt->execute()->finish();

			$dbm->release($conn);
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}
	}


	/**
	 * Creates a stock item object for the given item id.
	 *
	 * @param Integer $id Id of stock item
	 * @return MShop_Product_Item_Stock_Interface Product stock item
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'product.stock.id', $id, $ref );
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
			$path = 'classes/product/manager/stock/submanagers';
			foreach( $this->_getContext()->getConfig()->get( $path, array( 'warehouse' ) ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Search for stock items based on the given critera.
	 *
	 * Possible search keys: 'product.stock.id', 'product.stock.prodid', 'product.stock.siteid',
	 * 'product.stock.warehouseid', 'product.stock.stocklevel', 'product.stock.backdate'
	 *
	 * @param MW_Common_Criteria_Interface $search Search object with search conditions
	 * @param integer &$total Number of items that are available in total
	 * @return array List of stock items implementing MShop_Product_Item_Stock_Interface
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
			$cfgPathSearch = 'mshop/product/manager/stock/default/item/search';
			$cfgPathCount =  'mshop/product/manager/stock/default/item/count';
			$required = array( 'product.stock' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total );
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
	 * Returns a new manager for stock extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g base, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'product', 'stock/' . $manager, $name );
	}


	/**
	 * Decreases the stock level of the product for the warehouse.
	 *
	 * @param string $productCode Unique code of a product
	 * @param string $warehouseCode Unique code of the warehouse
	 * @param integer $amount Amount the stock level should be decreased
	 */
	public function decrease( $productCode, $warehouseCode, $amount )
	{
		$this->increase($productCode, $warehouseCode, -$amount);
	}


	/**
	 * Increases the stock level of the product for the warehouse.
	 *
	 * @param string $productCode Unique code of a product
	 * @param string $warehouseCode Unique code of the warehouse
	 * @param integer $amount Amount the stock level should be increased
	 */
	public function increase( $productCode, $warehouseCode, $amount )
	{
		$context = $this->_getContext();

		$productManager = MShop_Product_Manager_Factory::createManager( $context );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $productCode ) );
		$productIds = array_keys( $productManager->searchItems( $search ) );

		$warehouseManager = $this->getSubManager( 'warehouse' );
		$search = $warehouseManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.stock.warehouse.code', $warehouseCode ) );
		$warehouseIds = array_keys( $warehouseManager->searchItems( $search ) );

		$search = $this->createSearch();
		$expr = array(
			$search->compare( '==', 'product.stock.siteid', $context->getLocale()->getSitePath() ),
			$search->compare( '==', 'product.stock.productid', $productIds ),
			$search->compare( '==', 'product.stock.warehouseid', $warehouseIds ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$types = array(
			'product.stock.siteid' => $this->_searchConfig['product.stock.siteid']['internaltype'],
			'product.stock.productid' => $this->_searchConfig['product.stock.productid']['internaltype'],
			'product.stock.warehouseid' => $this->_searchConfig['product.stock.warehouseid']['internaltype'],
		);
		$translations = array(
			'product.stock.siteid' => 'siteid',
			'product.stock.productid' => 'prodid',
			'product.stock.warehouseid' => 'warehouseid',
		);

		$conditions = $search->getConditionString( $types, $translations );

		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$path = 'mshop/product/manager/stock/default/item/stocklevel';
			$stmt = $conn->create( str_replace( ':cond', $conditions, $context->getConfig()->get( $path, $path ) ) );

			$stmt->bind( 1, $amount, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, date( 'Y-m-d H:i:s' ) ); //mtime
			$stmt->bind( 3, $context->getEditor() );

			$result = $stmt->execute();

			if ($result->affectedRows() !== 1 ) {
				$msg = 'Possible problem while changing stock level for product "%1$s" and warehouse "%2$s" by "%3$s": Affected stocks are "%4$s"';
				$context->getLogger()->log( sprintf( $msg, $productCode, $warehouseCode, $amount, $affectedRows ), MW_Logger_Abstract::WARN );
			}

			$result->finish();

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}
	}


	/**
	 * Creates new stock item object.
	 *
	 * @param array $values Possible optional array keys can be given:
	 * id, prodid, siteid, warehouseid, stocklevel, backdate
	 * @return MShop_Product_Item_Stock_Default New stock item object
	 */
	protected function _createItem( array $values = array() )
	{
		return new MShop_Product_Item_Stock_Default( $values );
	}
}
