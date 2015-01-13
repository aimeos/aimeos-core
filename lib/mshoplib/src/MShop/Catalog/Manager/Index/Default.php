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
	/**
	 * Initializes the manager instance.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );
		$this->_setResourceName( 'db-product' );
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
		return MShop_Factory::createManager( $this->_getContext(), 'product' )->createItem();
	}


	/**
	 * Creates a search object and optionally sets base criteria.
	 *
	 * @param boolean $default Add default criteria
	 * @return MW_Common_Criteria_Interface Criteria object
	 */
	public function createSearch( $default = false )
	{
		return MShop_Factory::createManager( $this->_getContext(), 'product' )->createSearch( $default );
	}


	/**
	 * Removes multiple items from the index.
	 *
	 * @param array $ids list of product IDs
	 */
	public function deleteItems( array $ids )
	{
		if( empty( $ids ) ) { return; }

		foreach( $this->_getSubManagers() as $submanager ) {
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
		return MShop_Factory::createManager( $this->_getContext(), 'product' )->getItem( $id, $ref );
	}


	/**
	 * Returns a list of objects describing the available criterias for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** mshop/catalog/manager/index/default/submanagers
		 * List of manager names that can be instantiated by the catalog index manager
		 *
		 * Managers provide a generic interface to the underlying storage.
		 * Each manager has or can have sub-managers caring about particular
		 * aspects. Each of these sub-managers can be instantiated by its
		 * parent manager using the getSubManager() method.
		 *
		 * The search keys from sub-managers can be normally used in the
		 * manager as well. It allows you to search for items of the manager
		 * using the search keys of the sub-managers to further limit the
		 * retrieved list of items.
		 *
		 * @param array List of sub-manager names
		 * @since 2014.03
		 * @category Developer
		 */
		$path = 'mshop/catalog/manager/index/default/submanagers';
		$default = array( 'price', 'catalog', 'attribute', 'text' );

		$list = MShop_Factory::createManager( $this->_getContext(), 'product' )->getSearchAttributes( $withsub );
		$list += $this->_getSearchAttributes( array(), $path, $default, $withsub );

		return $list;
	}


	/**
	 * Returns a new manager for product extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return MShop_Common_Manager_Interface Manager for different extensions, e.g stock, tags, locations, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		/** classes/catalog/manager/index/name
		 * Class name of the used catalog index manager implementation
		 *
		 * Each default catalog index manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Catalog_Manager_Index_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Catalog_Manager_Index_Myindex
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/catalog/manager/index/name = Myindex
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyIndex"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */

		/** mshop/catalog/manager/index/decorators/excludes
		 * Excludes decorators added by the "common" option from the catalog index manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the catalog index manager.
		 *
		 *  mshop/catalog/manager/index/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "mshop/common/manager/decorators/default" for the catalog index manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/catalog/manager/index/decorators/global
		 * @see mshop/catalog/manager/index/decorators/local
		 */

		/** mshop/catalog/manager/index/decorators/global
		 * Adds a list of globally available decorators only to the catalog index manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the catalog index manager.
		 *
		 *  mshop/catalog/manager/index/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the catalog controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/catalog/manager/index/decorators/excludes
		 * @see mshop/catalog/manager/index/decorators/local
		 */

		/** mshop/catalog/manager/index/decorators/local
		 * Adds a list of local decorators only to the catalog index manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the catalog index manager.
		 *
		 *  mshop/catalog/manager/index/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the catalog
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/catalog/manager/index/decorators/excludes
		 * @see mshop/catalog/manager/index/decorators/global
		 */

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
		$dbname = $this->_getResourceName();
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


		foreach( $this->_getSubManagers() as $submanager ) {
			$submanager->optimize();
		}
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param array $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		foreach ( $this->_getSubManagers() as $submanager ) {
			$submanager->cleanup( $siteids );
		}
	}


	/**
	 * Removes all entries not touched after the given timestamp in the catalog index.
	 * This can be a long lasting operation.
	 *
	 * @param string $timestamp Timestamp in ISO format (YYYY-MM-DD HH:mm:ss)
	 */
	public function cleanupIndex( $timestamp )
	{
		foreach ( $this->_getSubManagers() as $submanager ) {
			$submanager->cleanupIndex( $timestamp );
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

		$manager = MShop_Factory::createManager( $context, 'product' );
		$search = $manager->createSearch( true );
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
			$this->_clearCache( $paramIds );
			return;
		}

		// index categorized product items only
		$catalogListManager = MShop_Factory::createManager( $context, 'catalog/list' );
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

			$prodIds = array_keys( $result );
			$expr = array(
				$search->compare( '==', 'product.id', $prodIds ),
				$defaultConditions,
			);
			$search->setConditions( $search->combine( '&&', $expr ) );

			$this->_writeIndex( $search, $domains, $size );
			$this->_clearCache( $prodIds );

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
		$dbname = $this->_getResourceName();
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

		$manager = MShop_Factory::createManager( $context, 'product' );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.id', $ids ) );
		$products = $manager->searchItems( $search, $ref );

		foreach( $ids as $id )
		{
			if( isset( $products[$id] ) ) {
				$items[$id] = $products[$id];
			}
		}

		return $items;
	}


	/**
	 * Deletes the cache entries using the given product IDs.
	 *
	 * @param array $productIds List of product IDs
	 */
	protected function _clearCache( array $productIds )
	{
		$tags = array();

		foreach( $productIds as $prodId ) {
			$tags[] = 'product-' . $prodId;
		}

		$this->_getContext()->getCache()->deleteByTags( $tags );
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
		$manager = MShop_Factory::createManager( $this->_getContext(), 'product' );
		$start = 0;

		do
		{
			$search->setSlice( $start, $size );
			$products = $manager->searchItems( $search, $domains );

			try
			{
				$this->begin();

				$this->deleteItems( array_keys( $products ) );

				foreach ( $this->_getSubManagers() as $submanager ) {
					$submanager->rebuildIndex( $products );
				}

				$this->_saveSubProducts( $products );

				$this->commit();
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
		
		// Including "text" and "price" messes up the sortation 
		$default = array( 'attribute', 'product' );
		$domains = $context->getConfig()->get( 'mshop/catalog/manager/index/default/subdomains', $default );
		$size = $context->getConfig()->get( 'mshop/catalog/manager/index/default/chunksize', 1000 );

		$manager = MShop_Factory::createManager( $context, 'product' );
		$search = $manager->createSearch( true );
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
		$manager = MShop_Factory::createManager( $this->_getContext(), 'product' );
		$submanagers = array();
		$start = 0;

		// Execute only the sub-managers which correspond to one of the given domains
		// This will prevent adding product names of sub-products which messes up the sortation
		foreach( $this->_getSubManagers() as $domain => $submanager )
		{
			if( in_array( $domain, $domains ) ) {
				$submanagers[$domain] = $submanager;
			}
		}

		do
		{
			$result = $manager->searchItems( $search, $domains );

			if( !empty( $result ) )
			{
				foreach( $result as $refId => $refItem )
				{
					$refItem->setId( null );
					$refItem->setId( $list[$refId] );
				}
			}

			foreach( $submanagers as $submanager ) {
				$submanager->rebuildIndex( $result );
			}

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start, $size );
		}
		while( $count == $size );
	}


	/**
	 * Returns the list of sub-managers available for the catalog index attribute manager.
	 *
	 * @return array Associative list of the sub-domain as key and the manager object as value
	 */
	protected function _getSubManagers()
	{
		$list = array();
		$path = 'mshop/catalog/manager/index/default/submanagers';
		$default = array( 'price', 'catalog', 'attribute', 'text' );

		foreach( $this->_getContext()->getConfig()->get( $path, $default ) as $domain ) {
			$list[$domain] = $this->getSubManager( $domain );
		}

		return $list;
	}
}