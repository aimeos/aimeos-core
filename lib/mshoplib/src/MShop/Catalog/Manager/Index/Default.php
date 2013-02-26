<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Catalog
 * @version $Id: Default.php 14754 2012-01-09 13:26:10Z nsendetzky $
 */


/**
 * Simple catalog index for searching in product tables.
 *
 * @package MShop
 * @subpackage Catalog
 */
class MShop_Catalog_Manager_Index_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Catalog_Manager_Index_Interface
{
	private $_productManager;
	private $_submanagers = array();


	/**
	 * Initializes the manager instance.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );

		$this->_productManager = MShop_Product_Manager_Factory::createManager( $context );

		$confpath = 'mshop/catalog/manager/index/default/submanagers';
		$default = array( 'price', 'catalog', 'attribute', 'text' );

		foreach( $context->getConfig()->get( $confpath, $default ) as $domain ) {
			$this->_submanagers[ $domain ] = $this->getSubManager( $domain );
		}
	}


	/**
	 * Create new product item object.
	 *
	 * @return MShop_Product_Item_Interface
	 */
	public function createItem()
	{
		return $this->_productManager->createItem();
	}


	/**
	 * Creates a search object and optionally sets base criteria.
	 *
	 * @param boolean $default Add default criteria
	 * @return MW_Common_Criteria_Interface Criteria object
	 */
	public function createSearch( $default = false )
	{
		return $this->_productManager->createSearch( $default );
	}


	/**
	 * Removes an item from the index.
	 *
	 * @param integer $id Product ID
	 */
	public function deleteItem( $id )
	{
		$this->deleteItems( array( $id ) );
	}


	/**
	 * Removes multiple items from the index.
	 *
	 * @param array $ids list of product IDs
	 */
	public function deleteItems( array $ids )
	{
		foreach( $this->_submanagers as $submanager ) {
			$submanager->deleteItems( $ids );
		}
	}


	/**
	 * Returns the product item for the given product ID.
	 *
	 * @param integer $id Unique ID to search for
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Product_Item_Interface Product item
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_productManager->getItem( $id, $ref );
	}


	/**
	 * Returns a list of objects describing the available criterias for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = $this->_productManager->getSearchAttributes( $withsub );

		foreach( $this->_submanagers as $submanager ) {
			$list = array_merge( $list, $submanager->getSearchAttributes( $withsub ) );
		}

		return $list;
	}


	/**
	 * Returns a new manager for product extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g stock, tags, locations, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'catalog', 'index/' . $manager, $name );
	}


	/**
	 * Optimizes the index if necessary.
	 * Execution of this operation can take a very long time and shouldn't be
	 * called through a web server enviroment.
	 */
	public function optimize()
	{
		$context = $this->_getContext();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$path = 'mshop/catalog/manager/index/default/optimize';
			foreach( $context->getConfig()->get( $path, array() ) as $sql ) {
				$conn->create( $sql )->execute()->finish();
			}

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}


		foreach( $this->_submanagers as $submanager ) {
			$submanager->optimize();
		}
	}


	/**
	 * Rebuilds the catalog index for searching products or specified list of products.
	 * This can be a long lasting operation.
	 *
	 * @param array $items Optional product item list
	 */
	public function rebuildIndex( array $items = array() )
	{
		$context = $this->_getContext();
		$config = $context->getConfig();

		$start = 0;
		$size = $config->get( 'mshop/catalog/manager/index/default/chunksize', 1000 );

		$mode = $config->get( 'mshop/catalog/manager/index/default/index', 'categorized' );

		$default = array( 'attribute', 'price', 'text', 'product' );
		$domains = $config->get( 'mshop/catalog/manager/index/default/domains', $default );

		$search = $this->_productManager->createSearch( true );
		$defaultConditions = $search->getConditions();

		if( count( $items ) > 0 )
		{
			$paramIds = array();
			foreach( $items as $item ) {
				$paramIds[] = $item->getId();
			}

			$expr = array(
				$defaultConditions,
				$search->compare( '==', 'product.id', $paramIds )
			);
			$search->setConditions( $search->combine( '&&', $expr ) );

			$this->_writeIndex( $search, $domains, $size );

			return;
		}

		if( $mode === 'all' )
		{
			$this->_writeIndex( $search, $domains, $size );

			return;
		}

		$ids = array();

		$catalogListManager = MShop_Catalog_Manager_Factory::createManager( $context )->getSubManager('list');
		$categorySearch = $catalogListManager->createSearch( true );

		do
		{
			$categorySearch->setConditions( $categorySearch->compare( '==', 'catalog.list.domain', 'product' ) );
			$categorySearch->setSlice( $start, $size );

			$result = $catalogListManager->searchItems( $categorySearch );

			$ids = array();
			foreach( $result as $catalogListItem ) {
				$ids[] = $catalogListItem->getRefId();
			}

			$count = count( $ids );
			if( $count === 0 ) { continue; }

			$start += $count;

			$expr = array(
				$defaultConditions,
				$search->compare( '==', 'product.id', $ids )
			);
			$search->setConditions( $search->combine( '&&', $expr ) );

			$this->_writeIndex( $search, $domains, $size );
		}
		while( $count > 0 );
	}


	/**
	 * Stores a new item in the index.
	 *
	 * @param MShop_Common_Item_Interface $item Product item
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Product_Item_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Catalog_Exception( sprintf( 'Object does not implement "%1$s"', $iface ) );
		}


		$itemId = $item->getId();

		if( $itemId === null ) {
			throw new MShop_Catalog_Exception( 'Item ID must not be null' );
		}


		$confpath = 'mshop/catalog/manager/index/default/domains';
		$default = array( 'attribute', 'price', 'text', 'product' );

		$item = $this->getItem( $itemId, $this->_getContext()->getConfig()->get( $confpath, $default ) );

		$this->deleteItem( $itemId );

		foreach ( $this->_submanagers as $submanager ) {
			$submanager->saveItem( $item );
		}

		$this->_saveSubProducts( array ( $itemId => $item ) );
	}


	/**
	 * Searches for items matching the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search criteria
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @param integer &$total Total number of items matched by the given criteria
	 * @return array List of items implementing MShop_Product_Item_Interface
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$items = $ids = array();
		$dbm = $this->_getContext()->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$level = MShop_Locale_Manager_Abstract::SITE_ALL;
			$cfgPathSearch = 'mshop/catalog/manager/index/default/item/search';
			$cfgPathCount =  'mshop/catalog/manager/index/default/item/count';
			$required = array( 'product' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false ) {
				$ids[] = $row['id'];
			}

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}

		$search = $this->_productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.id', $ids ) );
		$products = $this->_productManager->searchItems( $search, $ref );

		foreach( $ids as $id )
		{
			if( isset( $products[$id] ) ) {
				$items[$id] = $products[$id];
			}
		}

		return $items;
	}


	/**
	* Re-writes the index entries for all products that are search result of given criteria
	*
	* @param MW_Common_Criteria_Interface $search Search criteria
	* @param array $domains List of domains to be
	* @param integer $size Size of a chunk of products to handle at a time
	*/
	protected function _writeIndex( MW_Common_Criteria_Interface $search, array $domains, $size )
	{
		$config = $this->_getContext()->getConfig();

		$start = 0;
		$search->setSlice( $start, $size );

		do
		{
			$products = $this->_productManager->searchItems( $search, $domains );
			$count = count( $products );
			if( $count === 0 ) { continue; }

			$start += $count;
			$search->setSlice( $start, $size );

			try
			{
				$this->_begin();

				$this->deleteItems( array_keys( $products ) );

				foreach ( $this->_submanagers as $submanager ) {
					$submanager->rebuildIndex( $products );
				}

				$this->_commit();
			}
			catch( Exception $e )
			{
				$this->_rollback();
				throw $e;
			}

			$this->_saveSubProducts( $products );
		}
		while( $count > 0 );
	}


	/**
	 * Saves catalog, price, text and attribute of subproduct.
	 *
	 * @param MShop_Product_Item_Interface $items Product items
	 */
	protected function _saveSubProducts( array $items )
	{
		$context = $this->_getContext();
		$default = array( 'attribute', 'price', 'text', 'product' );
		$domains = $context->getConfig()->get( 'mshop/catalog/manager/index/default/domains', $default );
		$size = $context->getConfig()->get( 'mshop/catalog/manager/index/default/chunksize', 1000 );

		foreach( $items as $id => $product )
		{
			$search = $this->_productManager->createSearch( true );
			$search->setSlice( 0, $size );
			$start = 0;

			do
			{
				$ids = array_keys( $product->getRefItems( 'product', null, 'default' ) );

				$expr = array(
					$search->compare( '==', 'product.id', $ids ),
					$search->getConditions(),
				);
				$search->setConditions( $search->combine( '&&', $expr ) );

				$result = $this->_productManager->searchItems( $search, $domains );

				$itemList = array();
				foreach( $result as $refItem )
				{
					$refItem->setId( null );
					$refItem->setId( $id );
					$itemList[] = $refItem;
				}

				try
				{
					$this->_begin();

					foreach( $this->_submanagers as $submanager ) {
						$submanager->rebuildIndex( $itemList );
					}

					$this->optimize();

					$this->_commit();
				}
				catch( Exception $e )
				{
					$this->_rollback();
					throw $e;
				}

				$count = count( $result );
				$start += $count;
				$search->setSlice( $start, $size );
			}
			while( $count > 0 );

		}
	}
}