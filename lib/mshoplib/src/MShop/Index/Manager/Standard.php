<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Index
 */


namespace Aimeos\MShop\Index\Manager;


/**
 * Index index manager for searching in product tables.
 *
 * @package MShop
 * @subpackage Index
 */
class Standard
	extends \Aimeos\MShop\Index\Manager\DBBase
	implements \Aimeos\MShop\Index\Manager\Iface
{
	private $subManagers;


	/**
	 * Counts the number products that are available for the values of the given key.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria
	 * @param string $key Search key (usually the ID) to aggregate products for
	 * @return array List of ID values as key and the number of counted products as value
	 */
	public function aggregate( \Aimeos\MW\Criteria\Iface $search, $key )
	{
		/** mshop/index/manager/standard/aggregate/mysql
		 * Counts the number of records grouped by the values in the key column and matched by the given criteria
		 *
		 * @see mshop/index/manager/standard/aggregate/ansi
		 */

		/** mshop/index/manager/standard/aggregate/ansi
		 * Counts the number of records grouped by the values in the key column and matched by the given criteria
		 *
		 * Groups all records by the values in the key column and counts their
		 * occurence. The matched records can be limited by the given criteria
		 * from the order database. The records must be from one of the sites
		 * that are configured via the context item. If the current site is part
		 * of a tree of sites, the statement can count all records from the
		 * current site and the complete sub-tree of sites.
		 *
		 * As the records can normally be limited by criteria from sub-managers,
		 * their tables must be joined in the SQL context. This is done by
		 * using the "internaldeps" property from the definition of the ID
		 * column of the sub-managers. These internal dependencies specify
		 * the JOIN between the tables and the used columns for joining. The
		 * ":joins" placeholder is then replaced by the JOIN strings from
		 * the sub-managers.
		 *
		 * To limit the records matched, conditions can be added to the given
		 * criteria object. It can contain comparisons like column names that
		 * must match specific values which can be combined by AND, OR or NOT
		 * operators. The resulting string of SQL conditions replaces the
		 * ":cond" placeholder before the statement is sent to the database
		 * server.
		 *
		 * This statement doesn't return any records. Instead, it returns pairs
		 * of the different values found in the key column together with the
		 * number of records that have been found for that key values.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for aggregating order items
		 * @since 2014.09
		 * @category Developer
		 * @see mshop/index/manager/standard/count/ansi
		 * @see mshop/index/manager/standard/optimize/ansi
		 * @see mshop/index/manager/standard/search/ansi
		 */
		return $this->aggregateBase( $search, $key, 'mshop/index/manager/standard/aggregate', array( 'product' ) );
	}


	/**
	 * Removes multiple items from the index.
	 *
	 * @param array $ids list of product IDs
	 */
	public function deleteItems( array $ids )
	{
		if( empty( $ids ) ) { return; }

		foreach( $this->getSubManagers() as $submanager ) {
			$submanager->deleteItems( $ids );
		}
	}


	/**
	 * Returns the available manager types
	 *
	 * @param boolean $withsub Return also the resource type of sub-managers if true
	 * @return array Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( $withsub = true )
	{
		$path = 'mshop/index/manager/submanagers';

		return $this->getResourceTypeBase( 'index', $path, array( 'attribute', 'catalog', 'price', 'text' ), $withsub );
	}


	/**
	 * Returns a list of objects describing the available criterias for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of items implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = parent::getSearchAttributes( $withsub );

		/** mshop/index/manager/standard/submanagers
		 * Replaced by mshop/index/manager/submanagers since 2016.01
		 *
		 * @see mshop/index/manager/standard/submanagers
		 */
		$path = 'mshop/index/manager/submanagers';
		$default = array( 'price', 'catalog', 'attribute', 'text' );

		$list += $this->getSearchAttributesBase( [], $path, $default, $withsub );

		return $list;
	}


	/**
	 * Returns a new manager for product extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g stock, tags, locations, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		/** mshop/index/manager/name
		 * Class name of the used index manager implementation
		 *
		 * Each default index manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\MShop\Index\Manager\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\MShop\Index\Manager\Myindex
		 *
		 * then you have to set the this configuration option:
		 *
		 *  mshop/index/manager/name = Myindex
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

		/** mshop/index/manager/decorators/excludes
		 * Excludes decorators added by the "common" option from the index manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the index manager.
		 *
		 *  mshop/index/manager/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
		 * "mshop/common/manager/decorators/default" for the index manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/index/manager/decorators/global
		 * @see mshop/index/manager/decorators/local
		 */

		/** mshop/index/manager/decorators/global
		 * Adds a list of globally available decorators only to the index manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the index manager.
		 *
		 *  mshop/index/manager/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the catalog controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/index/manager/decorators/excludes
		 * @see mshop/index/manager/decorators/local
		 */

		/** mshop/index/manager/decorators/local
		 * Adds a list of local decorators only to the index manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the index manager.
		 *
		 *  mshop/index/manager/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator2" only to the catalog
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/index/manager/decorators/excludes
		 * @see mshop/index/manager/decorators/global
		 */

		return $this->getSubManagerBase( 'index', $manager, $name );
	}


	/**
	 * Optimizes the index if necessary.
	 * Execution of this operation can take a very long time and shouldn't be
	 * called through a web server enviroment.
	 */
	public function optimize()
	{
		/** mshop/index/manager/standard/optimize/mysql
		 * Optimizes the stored product data for retrieving the records faster
		 *
		 * @see mshop/index/manager/standard/optimize/ansi
		 */

		/** mshop/index/manager/standard/optimize/ansi
		 * Optimizes the stored product data for retrieving the records faster
		 *
		 * The SQL statement should reorganize the data in the DBMS storage to
		 * optimize access to the records of the table or tables. Some DBMS
		 * offer specialized statements to optimize indexes and records. This
		 * statement doesn't return any records.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for optimizing the stored product data
		 * @since 2014.09
		 * @category Developer
		 * @see mshop/index/manager/standard/count/ansi
		 * @see mshop/index/manager/standard/search/ansi
		 * @see mshop/index/manager/standard/aggregate/ansi
		 */
		$this->optimizeBase( 'mshop/index/manager/standard/optimize' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param array $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		foreach( $this->getSubManagers() as $submanager ) {
			$submanager->cleanup( $siteids );
		}
	}


	/**
	 * Removes all entries not touched after the given timestamp in the index.
	 * This can be a long lasting operation.
	 *
	 * @param string $timestamp Timestamp in ISO format (YYYY-MM-DD HH:mm:ss)
	 */
	public function cleanupIndex( $timestamp )
	{
		foreach( $this->getSubManagers() as $submanager ) {
			$submanager->cleanupIndex( $timestamp );
		}
	}


	/**
	 * Rebuilds the index for searching products or specified list of products.
	 * This can be a long lasting operation.
	 *
	 * @param array $items Optional product item list
	 */
	public function rebuildIndex( array $items = [] )
	{
		$context = $this->getContext();
		$config = $context->getConfig();

		/** mshop/index/manager/standard/chunksize
		 * Number of products that should be indexed at once
		 *
		 * When rebuilding the product index, several products are updated at
		 * once within a transaction. This speeds up the time that is needed
		 * for reindexing.
		 *
		 * Usually, the more products are updated in one bunch, the faster the
		 * process of rebuilding the index will be up to a certain limit. The
		 * downside of big bunches is a higher memory consumption that can
		 * exceed the maximum allowed memory of the process.
		 *
		 * @param integer Number of products
		 * @since 2014.09
		 * @category User
		 * @category Developer
		 * @see mshop/index/manager/standard/domains
		 * @see mshop/index/manager/standard/index
		 * @see mshop/index/manager/standard/subdomains
		 * @see mshop/index/manager/submanagers
		 */
		$size = $config->get( 'mshop/index/manager/standard/chunksize', 1000 );

		/** mshop/index/manager/standard/index
		 * Index mode for products which determines what products are added to the index
		 *
		 * By default, only products that have been added to a category are
		 * part of the index. Thus, it's possible to have special products like
		 * rebate products that are necessary if you use coupon codes in your
		 * shop but won't be found by e.g. when searching for products.
		 *
		 * Alternatively, you can add all products to the index, even those
		 * which are not listed in any category. This mode should only be
		 * used in special cases when you have no rebate or similar products
		 * that shouldn't be found by users.
		 *
		 * @param integer Number of products
		 * @since 2014.09
		 * @category User
		 * @category Developer
		 * @see mshop/index/manager/standard/chunksize
		 * @see mshop/index/manager/standard/domains
		 * @see mshop/index/manager/standard/subdomains
		 * @see mshop/index/manager/submanagers
		 */
		$mode = $config->get( 'mshop/index/manager/standard/index', 'categorized' );

		/** mshop/index/manager/standard/domains
		 * A list of domain names whose items should be retrieved together with the product
		 *
		 * To speed up the indexing process, items like texts, prices, media,
		 * attributes etc. which have been associated to products can be
		 * retrieved together with the products.
		 *
		 * Please note that the index submanagers expect that the items
		 * associated to the products are fetched together with the products.
		 * Thus, if you leave out a domain, this information won't be part
		 * of the indexed product and therefore won't be found when searching
		 * the index.
		 *
		 * @param string List of MShop domain names
		 * @since 2014.09
		 * @category Developer
		 * @see mshop/index/manager/standard/chunksize
		 * @see mshop/index/manager/standard/index
		 * @see mshop/index/manager/standard/subdomains
		 * @see mshop/index/manager/submanagers
		 */
		$default = array( 'attribute', 'price', 'text', 'product' );
		$domains = $config->get( 'mshop/index/manager/standard/domains', $default );

		$manager = \Aimeos\MShop\Factory::createManager( $context, 'product' );
		$search = $manager->createSearch( true );
		$search->setSortations( array( $search->sort( '+', 'product.id' ) ) );
		$defaultConditions = $search->getConditions();

		$prodIds = [];

		foreach( $items as $item ) {
			$prodIds[] = $item->getId(); // don't rely on array keys
		}


		// index all product items
		if( $mode === 'all' )
		{
			if( !empty( $prodIds ) )
			{
				$expr = array( $search->compare( '==', 'product.id', $prodIds ), $defaultConditions );
				$search->setConditions( $search->combine( '&&', $expr ) );
			}

			$this->writeIndex( $search, $domains, $size );
			return;
		}


		// index categorized product items only
		$catalogListManager = \Aimeos\MShop\Factory::createManager( $context, 'catalog/lists' );
		$catalogSearch = $catalogListManager->createSearch( true );

		$expr = array(
			$catalogSearch->compare( '==', 'catalog.lists.domain', 'product' ),
			$catalogSearch->getConditions(),
		);

		if( !empty( $prodIds ) ) {
			$expr[] = $catalogSearch->compare( '==', 'catalog.lists.refid', $prodIds );
		}

		$catalogSearch->setConditions( $catalogSearch->combine( '&&', $expr ) );
		$catalogSearch->setSortations( array( $catalogSearch->sort( '+', 'catalog.lists.refid' ) ) );

		$start = 0;

		do
		{
			$catalogSearch->setSlice( $start, $size );
			$result = $catalogListManager->aggregate( $catalogSearch, 'catalog.lists.refid' );

			$expr = array(
				$search->compare( '==', 'product.id', array_keys( $result ) ),
				$defaultConditions,
			);
			$search->setConditions( $search->combine( '&&', $expr ) );

			$this->writeIndex( $search, $domains, $size );

			$start += $size;
		}
		while( count( $result ) > 0 );
	}


	/**
	 * Stores a new item in the index.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Product item
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Iface $item, $fetch = true )
	{
		$iface = '\\Aimeos\\MShop\\Product\\Item\\Iface';
		if( !( $item instanceof $iface ) ) {
			throw new \Aimeos\MShop\Index\Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if( $item->getId() === null ) {
			throw new \Aimeos\MShop\Index\Exception( sprintf( 'Item could not be saved using method saveItem(). Item ID not available.' ) );
		}

		$this->rebuildIndex( array( $item->getId() => $item ) );
	}


	/**
	 * Searches for items matching the given criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param integer|null &$total Number of items that are available in total
	 * @return array List of items implementing \Aimeos\MShop\Product\Item\Iface
	 */
	public function searchItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], &$total = null )
	{
		/** mshop/index/manager/standard/search/mysql
		 * Retrieves the records matched by the given criteria in the database
		 *
		 * @see mshop/index/manager/standard/search/ansi
		 */

		/** mshop/index/manager/standard/search/ansi
		 * Retrieves the records matched by the given criteria in the database
		 *
		 * Fetches the records matched by the given criteria from the order
		 * database. The records must be from one of the sites that are
		 * configured via the context item. If the current site is part of
		 * a tree of sites, the SELECT statement can retrieve all records
		 * from the current site and the complete sub-tree of sites.
		 *
		 * As the records can normally be limited by criteria from sub-managers,
		 * their tables must be joined in the SQL context. This is done by
		 * using the "internaldeps" property from the definition of the ID
		 * column of the sub-managers. These internal dependencies specify
		 * the JOIN between the tables and the used columns for joining. The
		 * ":joins" placeholder is then replaced by the JOIN strings from
		 * the sub-managers.
		 *
		 * To limit the records matched, conditions can be added to the given
		 * criteria object. It can contain comparisons like column names that
		 * must match specific values which can be combined by AND, OR or NOT
		 * operators. The resulting string of SQL conditions replaces the
		 * ":cond" placeholder before the statement is sent to the database
		 * server.
		 *
		 * If the records that are retrieved should be ordered by one or more
		 * columns, the generated string of column / sort direction pairs
		 * replaces the ":order" placeholder. In case no ordering is required,
		 * the complete ORDER BY part including the "\/*-orderby*\/...\/*orderby-*\/"
		 * markers is removed to speed up retrieving the records. Columns of
		 * sub-managers can also be used for ordering the result set but then
		 * no index can be used.
		 *
		 * The number of returned records can be limited and can start at any
		 * number between the begining and the end of the result set. For that
		 * the ":size" and ":start" placeholders are replaced by the
		 * corresponding values from the criteria object. The default values
		 * are 0 for the start and 100 for the size value.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for searching items
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/index/manager/standard/count/ansi
		 * @see mshop/index/manager/standard/optimize/ansi
		 * @see mshop/index/manager/standard/aggregate/ansi
		 */
		$cfgPathSearch = 'mshop/index/manager/standard/search';

		/** mshop/index/manager/standard/count/mysql
		 * Counts the number of records matched by the given criteria in the database
		 *
		 * @see mshop/index/manager/standard/count/ansi
		 */

		/** mshop/index/manager/standard/count/ansi
		 * Counts the number of records matched by the given criteria in the database
		 *
		 * Counts all records matched by the given criteria from the order
		 * database. The records must be from one of the sites that are
		 * configured via the context item. If the current site is part of
		 * a tree of sites, the statement can count all records from the
		 * current site and the complete sub-tree of sites.
		 *
		 * As the records can normally be limited by criteria from sub-managers,
		 * their tables must be joined in the SQL context. This is done by
		 * using the "internaldeps" property from the definition of the ID
		 * column of the sub-managers. These internal dependencies specify
		 * the JOIN between the tables and the used columns for joining. The
		 * ":joins" placeholder is then replaced by the JOIN strings from
		 * the sub-managers.
		 *
		 * To limit the records matched, conditions can be added to the given
		 * criteria object. It can contain comparisons like column names that
		 * must match specific values which can be combined by AND, OR or NOT
		 * operators. The resulting string of SQL conditions replaces the
		 * ":cond" placeholder before the statement is sent to the database
		 * server.
		 *
		 * Both, the strings for ":joins" and for ":cond" are the same as for
		 * the "search" SQL statement.
		 *
		 * Contrary to the "search" statement, it doesn't return any records
		 * but instead the number of records that have been found. As counting
		 * thousands of records can be a long running task, the maximum number
		 * of counted records is limited for performance reasons.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for counting items
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/index/manager/standard/search/ansi
		 * @see mshop/index/manager/standard/optimize/ansi
		 * @see mshop/index/manager/standard/aggregate/ansi
		 */
		$cfgPathCount = 'mshop/index/manager/standard/count';

		return $this->searchItemsIndexBase( $search, $ref, $total, $cfgPathSearch, $cfgPathCount );
	}


	/**
	 * Deletes the cache entries using the given product IDs.
	 *
	 * @param array $productIds List of product IDs
	 */
	protected function clearCache( array $productIds )
	{
		$tags = [];

		foreach( $productIds as $prodId ) {
			$tags[] = 'product-' . $prodId;
		}

		$this->getContext()->getCache()->deleteByTags( $tags );
	}


	/**
	 * Re-writes the index entries for all products that are search result of given criteria
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria
	 * @param array $domains List of domains to be
	 * @param integer $size Size of a chunk of products to handle at a time
	 */
	protected function writeIndex( \Aimeos\MW\Criteria\Iface $search, array $domains, $size )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product' );
		$submanagers = $this->getSubManagers();
		$start = 0;

		do
		{
			$search->setSlice( $start, $size );
			$products = $manager->searchItems( $search, $domains );
			$prodIds = array_keys( $products );

			try
			{
				$this->begin();

				$this->deleteItems( $prodIds );

				foreach( $submanagers as $submanager ) {
					$submanager->rebuildIndex( $products );
				}

				$this->saveSubProducts( $products );

				$this->commit();
			}
			catch( \Exception $e )
			{
				$this->rollback();
				throw $e;
			}

			$this->clearCache( $prodIds );

			$count = count( $products );
			$start += $count;
		}
		while( $count == $search->getSliceSize() );
	}


	/**
	 * Saves catalog, price, text and attribute of subproduct.
	 *
	 * @param array $items Associative list of product IDs and items implementing \Aimeos\MShop\Product\Item\Iface
	 */
	protected function saveSubProducts( array $items )
	{
		$context = $this->getContext();

		/** mshop/index/manager/standard/subdomains
		 * A list of domains for sub-products whose items should be added to the parent product
		 *
		 * Data from sub-products like variants or bundled products can be
		 * added to the parent product so that one will be found if the search
		 * criteria of the customer matches.
		 *
		 * Caution: If you include the text and price items of the sub-products,
		 * it will mess up the sortation in the list views because the products
		 * are sorted by names or prices of the sub-products but only the
		 * parent products with their names and prices are shown.
		 *
		 * @param string List of MShop domain names
		 * @since 2014.09
		 * @category Developer
		 * @see mshop/index/manager/standard/chunksize
		 * @see mshop/index/manager/standard/domains
		 * @see mshop/index/manager/standard/index
		 * @see mshop/index/manager/submanagers
		 */
		$default = array( 'attribute', 'product', 'text' ); // Including "price" messes up the sortation
		$domains = $context->getConfig()->get( 'mshop/index/manager/standard/subdomains', $default );
		$size = $context->getConfig()->get( 'mshop/index/manager/standard/chunksize', 1000 );

		$manager = \Aimeos\MShop\Factory::createManager( $context, 'product' );
		$search = $manager->createSearch( true );
		$search->setSortations( array( $search->sort( '+', 'product.id' ) ) );
		$defaultConditions = $search->getConditions();

		$prodList = [];
		$numSubProducts = 0;

		foreach( $items as $id => $product )
		{
			foreach( $product->getRefItems( 'product', null, 'default' ) as $subId => $subItem )
			{
				$prodList[$subId][] = $id;
				$numSubProducts++;
			}

			if( $numSubProducts >= $size )
			{
				$expr = array(
					$search->compare( '==', 'product.id', array_keys( $prodList ) ),
					$defaultConditions,
				);
				$search->setConditions( $search->combine( '&&', $expr ) );

				$this->saveSubProductsChunk( $search, $domains, $prodList, count( $prodList ) );

				$prodList = [];
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

			$this->saveSubProductsChunk( $search, $domains, $prodList, count( $prodList ) );
		}
	}


	/**
	 * Saves one chunk of the sub products.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criterias for retrieving the sub-products
	 * @param array $domains List of domains to fetch list items and referenced items for
	 * @param array $list Associative list of sub-product IDs as keys and parent products IDs as values
	 * @param integer $size Number of products per chunk
	 */
	protected function saveSubProductsChunk( \Aimeos\MW\Criteria\Iface $search, array $domains, array $list, $size )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product' );
		$submanagers = [];
		$start = 0;

		// Execute only the sub-managers which correspond to one of the given domains
		// This will prevent adding product names of sub-products which messes up the sortation
		foreach( $this->getSubManagers() as $domain => $submanager )
		{
			if( in_array( $domain, $domains ) ) {
				$submanagers[$domain] = $submanager;
			}
		}

		do
		{
			$items = [];
			$search->setSlice( $start, $size );
			$result = $manager->searchItems( $search, $domains );

			if( !empty( $result ) )
			{
				foreach( $result as $refId => $refItem )
				{
					$refItem->setLabel( '' ); // keep sorting by name intact

					foreach( $refItem->getRefitems( 'text', 'name' ) as $textItem ) {
						$textItem->setContent( '' ); // keep sorting by name intact
					}

					foreach( (array) $list[$refId] as $parentid )
					{
						$item = clone $refItem;

						$item->setId( null );
						$item->setId( $parentid ); // insert data for parent product

						$items[] = $item;
					}
				}
			}

			foreach( $submanagers as $submanager ) {
				$submanager->rebuildIndex( $items );
			}

			$count = count( $result );
			$start += $count;
		}
		while( $count == $size );
	}


	/**
	 * Returns the list of sub-managers available for the index attribute manager.
	 *
	 * @return array Associative list of the sub-domain as key and the manager object as value
	 */
	protected function getSubManagers()
	{
		if( $this->subManagers === null )
		{
			$this->subManagers = [];

			/** mshop/index/manager/submanagers
			 * A list of sub-manager names used for indexing associated items
			 *
			 * All items referenced by a product (e.g. texts, prices, media,
			 * etc.) are added to the product index via specialized index
			 * managers. You can add the name of new sub-managers to add more
			 * data to the index or remove existing ones if you don't want to
			 * index that data at all.
			 *
			 * Caution: Please note that the list of sub-manager names should
			 * correspond to the list of domains that are fetched together with
			 * the products as the sub-manager depends on the items being
			 * retrieved there and fetching items that won't be indexed is a
			 * waste of resources.
			 *
			 * @param string List of index sub-manager names
			 * @since 2016.02
			 * @category User
			 * @category Developer
			 * @see mshop/index/manager/standard/chunksize
			 * @see mshop/index/manager/standard/domains
			 * @see mshop/index/manager/standard/index
			 * @see mshop/index/manager/standard/subdomains
			 */
			$path = 'mshop/index/manager/submanagers';
			$default = array( 'price', 'catalog', 'attribute', 'text' );

			foreach( $this->getContext()->getConfig()->get( $path, $default ) as $domain ) {
				$this->subManagers[$domain] = $this->getSubManager( $domain );
			}

			return $this->subManagers;
		}

		return $this->subManagers;
	}
}