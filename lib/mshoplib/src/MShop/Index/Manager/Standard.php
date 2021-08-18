<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	implements \Aimeos\MShop\Index\Manager\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private $subManagers;


	/**
	 * Counts the number products that are available for the values of the given key.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria
	 * @param string $key Search key (usually the ID) to aggregate products for
	 * @param string|null $value Search key for aggregating the value column
	 * @param string|null $type Type of the aggregation, empty string for count or "sum" or "avg" (average)
	 * @return \Aimeos\Map List of ID values as key and the number of counted products as value
	 */
	public function aggregate( \Aimeos\MW\Criteria\Iface $search, $key, string $value = null, string $type = null ) : \Aimeos\Map
	{
		/** mshop/index/manager/aggregate/mysql
		 * Counts the number of records grouped by the values in the key column and matched by the given criteria
		 *
		 * @see mshop/index/manager/aggregate/ansi
		 */

		/** mshop/index/manager/aggregate/ansi
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
		 * @see mshop/index/manager/count/ansi
		 * @see mshop/index/manager/optimize/ansi
		 * @see mshop/index/manager/search/ansi
		 */
		return $this->aggregateBase( $search, $key, 'mshop/index/manager/aggregate', ['product'], $value, $type );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return array Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		return $this->getResourceTypeBase( 'index', 'mshop/index/manager/submanagers', [], $withsub );
	}


	/**
	 * Returns a list of objects describing the available criterias for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		$list = parent::getSearchAttributes( $withsub );

		/** mshop/index/manager/submanagers
		 * Replaced by mshop/index/manager/submanagers since 2016.01
		 *
		 * @see mshop/index/manager/submanagers
		 */
		$path = 'mshop/index/manager/submanagers';

		return $list + $this->getSearchAttributesBase( [], $path, [], $withsub );
	}


	/**
	 * Returns a new manager for product extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g stock, tags, locations, etc.
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->getSubManagerBase( 'index', $manager, $name );
	}


	/**
	 * Optimizes the index if necessary.
	 * Execution of this operation can take a very long time and shouldn't be
	 * called through a web server enviroment.
	 *
	 * @return \Aimeos\MShop\Index\Manager\Iface Manager object for chaining method calls
	 */
	public function optimize() : \Aimeos\MShop\Index\Manager\Iface
	{
		/** mshop/index/manager/optimize/mysql
		 * Optimizes the stored product data for retrieving the records faster
		 *
		 * @see mshop/index/manager/optimize/ansi
		 */

		/** mshop/index/manager/optimize/ansi
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
		 * @see mshop/index/manager/count/ansi
		 * @see mshop/index/manager/search/ansi
		 * @see mshop/index/manager/aggregate/ansi
		 */
		return $this->optimizeBase( 'mshop/index/manager/optimize' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Index\Manager\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		foreach( $this->getSubManagers() as $submanager ) {
			$submanager->clear( $siteids );
		}

		return $this;
	}


	/**
	 * Removes all entries not touched after the given timestamp in the index.
	 * This can be a long lasting operation.
	 *
	 * @param string $timestamp Timestamp in ISO format (YYYY-MM-DD HH:mm:ss)
	 * @return \Aimeos\MShop\Index\Manager\Iface Manager object for chaining method calls
	 */
	public function cleanup( string $timestamp ) : \Aimeos\MShop\Index\Manager\Iface
	{
		foreach( $this->getSubManagers() as $submanager ) {
			$submanager->cleanup( $timestamp );
		}

		return $this;
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Index\Manager\Iface Manager object for chaining method calls
	 */
	public function delete( $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$this->getManager()->delete( $itemIds );
		parent::delete( $itemIds );

		$this->getContext()->cache()->deleteByTags( map( $itemIds ) ->prefix( 'product-' )->toArray() );
		return $this;
	}


	/**
	 * Rebuilds the index for searching products or specified list of products.
	 * This can be a long lasting operation.
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface[] $items Associative list of product IDs as keys and items as values
	 * @return \Aimeos\MShop\Index\Manager\Iface Manager object for chaining method calls
	 */
	public function rebuild( iterable $items = [] ) : \Aimeos\MShop\Index\Manager\Iface
	{
		$context = $this->getContext();
		$config = $context->getConfig();

		/** mshop/index/manager/chunksize
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
		 * @param int Number of products
		 * @since 2014.09
		 * @category User
		 * @category Developer
		 * @see mshop/index/manager/domains
		 * @see mshop/index/manager/index
		 * @see mshop/index/manager/subdomains
		 * @see mshop/index/manager/submanagers
		 */
		$size = $config->get( 'mshop/index/manager/chunksize', 1000 );

		/** mshop/index/manager/domains
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
		 * @see mshop/index/manager/chunksize
		 * @see mshop/index/manager/index
		 * @see mshop/index/manager/subdomains
		 * @see mshop/index/manager/submanagers
		 */
		$domains = $config->get( 'mshop/index/manager/domains', [] );

		$manager = \Aimeos\MShop::create( $context, 'product' );
		$search = $manager->filter()->order( 'product.id' );

		if( !( $prodIds = map( $items )->getId() )->isEmpty() ) { // don't rely on array keys
			$search->add( 'product.id', '==', $prodIds->toArray() );
		}

		$this->writeIndex( $search, $domains, $size );
		return $this;
	}


	/**
	 * Searches for items matching the given criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Product\Item\Iface with ids as keys
	 */
	public function search( \Aimeos\MW\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		/** mshop/index/manager/search/mysql
		 * Retrieves the records matched by the given criteria in the database
		 *
		 * @see mshop/index/manager/search/ansi
		 */

		/** mshop/index/manager/search/ansi
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
		 * @see mshop/index/manager/count/ansi
		 * @see mshop/index/manager/optimize/ansi
		 * @see mshop/index/manager/aggregate/ansi
		 */
		$cfgPathSearch = 'mshop/index/manager/search';

		/** mshop/index/manager/count/mysql
		 * Counts the number of records matched by the given criteria in the database
		 *
		 * @see mshop/index/manager/count/ansi
		 */

		/** mshop/index/manager/count/ansi
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
		 * @see mshop/index/manager/search/ansi
		 * @see mshop/index/manager/optimize/ansi
		 * @see mshop/index/manager/aggregate/ansi
		 */
		$cfgPathCount = 'mshop/index/manager/count';

		return $this->searchItemsIndexBase( $search, $ref, $total, $cfgPathSearch, $cfgPathCount );
	}


	/**
	 * Re-writes the index entries for all products that are search result of given criteria
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria
	 * @param string[] $domains List of domains to be
	 * @param int $size Size of a chunk of products to handle at a time
	 */
	protected function writeIndex( \Aimeos\MW\Criteria\Iface $search, array $domains, int $size )
	{
		$context = $this->getContext();
		$manager = \Aimeos\MShop::create( $context, 'product' );
		$submanagers = $this->getSubManagers();
		$start = 0;

		do
		{
			$search->slice( $start, $size );
			$products = $manager->search( $search, $domains );

			try
			{
				$this->begin();

				$this->remove( $products->toArray() );

				foreach( $submanagers as $submanager ) {
					$submanager->rebuild( $products->toArray() );
				}

				$this->commit();
			}
			catch( \Exception $e )
			{
				$this->rollback();
				throw $e;
			}

			$context->cache()->deleteByTags( $products->keys()->prefix( 'product-' )->toArray() );

			$count = count( $products );
			$start += $count;
		}
		while( $count == $search->getLimit() );
	}


	/**
	 * Returns the list of sub-managers available for the index attribute manager.
	 *
	 * @return \Aimeos\MShop\Index\Manager\Iface[] Associative list of the sub-domain as key and the manager object as value
	 */
	protected function getSubManagers() : array
	{
		if( $this->subManagers === null )
		{
			$this->subManagers = [];
			$config = $this->getContext()->getConfig();

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
			 * @see mshop/index/manager/chunksize
			 * @see mshop/index/manager/domains
			 * @see mshop/index/manager/index
			 * @see mshop/index/manager/subdomains
			 */
			foreach( $config->get( 'mshop/index/manager/submanagers', [] ) as $domain )
			{
				$name = $config->get( 'mshop/index/manager/' . $domain . '/name' );
				$this->subManagers[$domain] = $this->getObject()->getSubManager( $domain, $name );
			}

			return $this->subManagers;
		}

		return $this->subManagers;
	}
}
