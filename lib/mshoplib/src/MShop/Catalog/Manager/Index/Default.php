<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Catalog
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
	 * Counts the number products that are available for the values of the given key.
	 *
	 * @param MW_Common_Criteria_Interface $search Search criteria
	 * @param string $key Search key (usually the ID) to aggregate products for
	 * @return array List of ID values as key and the number of counted products as value
	 */
	public function aggregate( MW_Common_Criteria_Interface $search, $key )
	{
		return $this->_aggregate( $search, $key, 'mshop/catalog/manager/index/default/aggregate', array( 'product' ) );
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
	 * Removes multiple items from the index.
	 *
	 * @param array $ids list of product IDs
	 */
	public function deleteItems( array $ids )
	{
		if( empty( $ids ) ) { return; }

		foreach( $this->_submanagers as $submanager ) {
			$submanager->deleteItems( $ids );
		}
	}


	/**
	 * Returns the product item for the given product ID.
	 *
	 * @param integer $id Unique ID to search for
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Product_Item_Interface Returns the product item of the given id
	 * @throws MShop_Exception If product couldn't be found
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
		$config = $context->getConfig();
		$dbm = $context->getDatabaseManager();
		$dbname = $config->get( 'resource/default', 'db' );
		$conn = $dbm->acquire( $dbname );

		try
		{
			$path = 'mshop/catalog/manager/index/default/optimize';
			foreach( $config->get( $path, array() ) as $sql ) {
				$conn->create( $sql )->execute()->finish();
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
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

		$size = $config->get( 'mshop/catalog/manager/index/default/chunksize', 1000 );
		$mode = $config->get( 'mshop/catalog/manager/index/default/index', 'categorized' );

		$default = array( 'attribute', 'price', 'text', 'product' );
		$domains = $config->get( 'mshop/catalog/manager/index/default/domains', $default );

		$search = $this->_productManager->createSearch( true );
		$search->setSortations( array( $search->sort( '+', 'product.id' ) ) );
		$defaultConditions = $search->getConditions();

		$paramIds = array();
		foreach( $items as $item ) {
			$paramIds[] = $item->getId();
		}

		if( $mode === 'all' ) // index all product items
		{
			if( !empty( $paramIds ) )
			{
				$expr = array(
					$search->compare( '==', 'product.id', $paramIds ),
					$defaultConditions,
				);
				$search->setConditions( $search->combine( '&&', $expr ) );
			}

			$this->_writeIndex( $search, $domains, $size );
			return;
		}

		// index categorized product items only
		$catalogListManager = MShop_Catalog_Manager_Factory::createManager( $context )->getSubManager( 'list' );
		$catalogSearch = $catalogListManager->createSearch( true );

		$expr = array( $catalogSearch->compare( '==', 'catalog.list.domain', 'product' ) );

		if( !empty( $paramIds ) ) {
			$expr[] = $catalogSearch->compare( '==', 'catalog.list.refid', $paramIds );
		}

		$expr[] = $catalogSearch->getConditions();

		$catalogSearch->setConditions( $catalogSearch->combine( '&&', $expr ) );
		$catalogSearch->setSortations( array( $catalogSearch->sort( '+', 'catalog.list.refid' ) ) );

		$start = 0;

		do
		{
			$catalogSearch->setSlice( $start, $size );
			$result = $catalogListManager->aggregate( $catalogSearch, 'catalog.list.refid' );

			$expr = array(
				$search->compare( '==', 'product.id', array_keys( $result ) ),
				$defaultConditions,
			);
			$search->setConditions( $search->combine( '&&', $expr ) );

			$this->_writeIndex( $search, $domains, $size );

			$start += $size;
		}
		while( count( $result ) > 0 );
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
			throw new MShop_Catalog_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if( $item->getId() === null ) {
			throw new MShop_Catalog_Exception( sprintf( 'Item could not be saved using method saveItem(). Item ID not available.' ) );
		}

		$this->rebuildIndex( array( $item ) );
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
		$context = $this->_getContext();
		$dbm = $context->getDatabaseManager();
		$dbname = $context->getConfig()->get( 'resource/default', 'db' );
		$conn = $dbm->acquire( $dbname );

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

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
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
		$start = 0;

		do
		{
			$search->setSlice( $start, $size );
			$products = $this->_productManager->searchItems( $search, $domains );

			try
			{
				$this->_begin();

				$this->deleteItems( array_keys( $products ) );

				foreach ( $this->_submanagers as $submanager ) {
					$submanager->rebuildIndex( $products );
				}

				$this->_saveSubProducts( $products );

				$this->_commit();
			}
			catch( Exception $e )
			{
				$this->_rollback();
				throw $e;
			}

			$count = count( $products );
			$start += $count;
		}
		while( $count == $search->getSliceSize() );
	}


	/**
	 * Saves catalog, price, text and attribute of subproduct.
	 *
	 * @param array $items Associative list of product IDs and items implementing MShop_Product_Item_Interface
	 */
	protected function _saveSubProducts( array $items )
	{
		$context = $this->_getContext();
		$default = array( 'attribute', 'price', 'text', 'product' );
		$domains = $context->getConfig()->get( 'mshop/catalog/manager/index/default/domains', $default );
		$size = $context->getConfig()->get( 'mshop/catalog/manager/index/default/chunksize', 1000 );

		$search = $this->_productManager->createSearch( true );
		$search->setSortations( array( $search->sort( '+', 'product.id' ) ) );
		$search->setSlice( 0, $size );
		$defaultConditions = $search->getConditions();

		$prodList = array();
		$numSubProducts = 0;

		foreach( $items as $id => $product )
		{
			foreach( $product->getRefItems( 'product', null, 'default' ) as $subId => $subItem )
			{
				$prodList[$subId] = $id;
				$numSubProducts++;
			}

			if( $numSubProducts >= $size )
			{
				$expr = array(
					$search->compare( '==', 'product.id', array_keys( $prodList ) ),
					$defaultConditions,
				);
				$search->setConditions( $search->combine( '&&', $expr ) );

				$this->_saveSubProductsChunk( $search, $domains, $prodList, $size );

				$prodList = array();
				$numSubProducts = 0;
			}
		}

		if( $numSubProducts > 0 )
		{
			$expr = array(
				$search->compare( '==', 'product.id', array_keys( $prodList ) ),
				$defaultConditions,
			);
			$search->setConditions( $search->combine( '&&', $expr ) );

			$this->_saveSubProductsChunk( $search, $domains, $prodList, $size );
		}
	}


	/**
	 * Saves one chunk of the sub products.
	 *
	 * @param MW_Common_Criteria_Interface $search Search criterias for retrieving the sub-products
	 * @param array $domains List of domains to fetch list items and referenced items for
	 * @param array $list Associative list of sub-product IDs as keys and parent products IDs as values
	 * @param integer $size Number of products per chunk
	 */
	protected function _saveSubProductsChunk( MW_Common_Criteria_Interface $search, array $domains, array $list, $size )
	{
		$start = 0;

		do
		{
			$result = $this->_productManager->searchItems( $search, $domains );

			if( !empty( $result ) )
			{
				foreach( $result as $refId => $refItem )
				{
					$refItem->setId( null );
					$refItem->setId( $list[$refId] );
				}

				foreach( $this->_submanagers as $submanager ) {
					$submanager->rebuildIndex( $result );
				}
			}

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start, $size );
		}
		while( $count == $size );
	}
}