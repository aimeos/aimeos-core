<?php
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Index
 */


namespace Aimeos\MShop\Index\Manager\Catalog;


/**
 * Submanager for catalog.
 *
 * @package MShop
 * @subpackage Index
 */
class Standard
	extends \Aimeos\MShop\Index\Manager\DBBase
	implements \Aimeos\MShop\Index\Manager\Catalog\Iface
{
	private $searchConfig = array(
		'index.catalog.id' => array(
			'code'=>'index.catalog.id',
			'internalcode'=>'mindca."catid"',
			'internaldeps'=>array( 'LEFT JOIN "mshop_index_catalog" AS mindca ON mindca."prodid" = mpro."id"' ),
			'label'=>'Product index category ID',
			'type'=> 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'index.catalogaggregate' => array(
			'code'=>'index.catalogaggregate()',
			'internalcode'=>'( SELECT COUNT(DISTINCT mindca_agg."catid")
				FROM "mshop_index_catalog" AS mindca_agg
				WHERE mpro."id" = mindca_agg."prodid" AND :site
				AND mindca_agg."catid" IN ( $1 ) )',
			'label'=>'Number of product categories, parameter(<category IDs>)',
			'type'=> 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'index.catalogcount' => array(
			'code'=>'index.catalogcount()',
			'internalcode'=>'( SELECT COUNT(DISTINCT mindca_cnt."catid")
				FROM "mshop_index_catalog" AS mindca_cnt
				WHERE mpro."id" = mindca_cnt."prodid" AND :site
				AND mindca_cnt."catid" IN ( $2 ) AND mindca_cnt."listtype" = $1 )',
			'label'=>'Number of product categories, parameter(<list type code>,<category IDs>)',
			'type'=> 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'index.catalog.position' => array(
			'code'=>'index.catalog.position()',
			'internalcode'=>':site AND mindca."catid" IN ( $2 ) AND mindca."listtype" = $1 AND mindca."pos"',
			'label'=>'Product position in category, parameter(<list type code>,<category ID>)',
			'type'=> 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'sort:index.catalog.position' => array(
			'code'=>'sort:index.catalog.position()',
			'internalcode'=>'mindca."pos"',
			'label'=>'Sort product position in category, parameter(<list type code>,<category ID>)',
			'type'=> 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		)
	);

	private $subManagers;


	/**
	 * Initializes the manager instance.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );

		$site = $context->getLocale()->getSitePath();

		$this->replaceSiteMarker( $this->searchConfig['index.catalog.position'], 'mindca."siteid"', $site );
		$this->replaceSiteMarker( $this->searchConfig['index.catalogaggregate'], 'mindca_agg."siteid"', $site );
		$this->replaceSiteMarker( $this->searchConfig['index.catalogcount'], 'mindca_cnt."siteid"', $site );
	}


	/**
	 * Counts the number products that are available for the values of the given key.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria
	 * @param string $key Search key (usually the ID) to aggregate products for
	 * @return array List of ID values as key and the number of counted products as value
	 */
	public function aggregate( \Aimeos\MW\Criteria\Iface $search, $key )
	{
		return $this->aggregateBase( $search, $key, 'mshop/index/manager/standard/aggregate' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param integer[] $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		parent::cleanup( $siteids );

		$this->cleanupBase( $siteids, 'mshop/index/manager/catalog/standard/delete' );
	}


	/**
	 * Removes all entries not touched after the given timestamp in the index.
	 * This can be a long lasting operation.
	 *
	 * @param string $timestamp Timestamp in ISO format (YYYY-MM-DD HH:mm:ss)
	 */
	public function cleanupIndex( $timestamp )
	{
		/** mshop/index/manager/catalog/standard/cleanup/mysql
		 * Deletes the index catalog records that haven't been touched
		 *
		 * @see mshop/index/manager/catalog/standard/cleanup/ansi
		 */

		/** mshop/index/manager/catalog/standard/cleanup/ansi
		 * Deletes the index catalog records that haven't been touched
		 *
		 * During the rebuild process of the product index, the entries of all
		 * active products will be removed and readded. Thus, no stale data for
		 * these products will remain in the database.
		 *
		 * All products that have been disabled since the last rebuild will be
		 * still part of the index. The cleanup statement removes all records
		 * that belong to products that haven't been touched during the index
		 * rebuild because these are the disabled ones.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for deleting the outdated index records
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/index/manager/catalog/standard/count/ansi
		 * @see mshop/index/manager/catalog/standard/delete/ansi
		 * @see mshop/index/manager/catalog/standard/insert/ansi
		 * @see mshop/index/manager/catalog/standard/search/ansi
		 */
		$this->cleanupIndexBase( $timestamp, 'mshop/index/manager/catalog/standard/cleanup' );
	}


	/**
	 * Removes multiple items from the index.
	 *
	 * @param array $ids list of Product IDs
	 */
	public function deleteItems( array $ids )
	{
		/** mshop/index/manager/catalog/standard/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/index/manager/catalog/standard/delete/ansi
		 */

		/** mshop/index/manager/catalog/standard/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the index database.
		 * The records must be from the site that is configured via the
		 * context item.
		 *
		 * The ":cond" placeholder is replaced by the name of the ID column and
		 * the given ID or list of IDs while the site ID is bound to the question
		 * mark.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for deleting index catalog records
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/index/manager/catalog/standard/count/ansi
		 * @see mshop/index/manager/catalog/standard/cleanup/ansi
		 * @see mshop/index/manager/catalog/standard/insert/ansi
		 * @see mshop/index/manager/catalog/standard/search/ansi
		 */
		$this->deleteItemsBase( $ids, 'mshop/index/manager/catalog/standard/delete' );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param boolean $withsub Return also the resource type of sub-managers if true
	 * @return array Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( $withsub = true )
	{
		$path = 'mshop/index/manager/catalog/submanagers';

		return $this->getResourceTypeBase( 'index/catalog', $path, [], $withsub );
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

		/** mshop/index/manager/catalog/submanagers
		 * List of manager names that can be instantiated by the index attribute manager
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
		$path = 'mshop/index/manager/catalog/submanagers';

		$list += $this->getSearchAttributesBase( $this->searchConfig, $path, [], $withsub );

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
		/** mshop/index/manager/catalog/name
		 * Class name of the used index catalog manager implementation
		 *
		 * Each default index catalog manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\MShop\Index\Manager\Catalog\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\MShop\Index\Manager\Catalog\Mycatalog
		 *
		 * then you have to set the this configuration option:
		 *
		 *  mshop/index/manager/catalog/name = Mycatalog
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyCatalog"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */

		/** mshop/index/manager/catalog/decorators/excludes
		 * Excludes decorators added by the "common" option from the index catalog manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the index catalog manager.
		 *
		 *  mshop/index/manager/catalog/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
		 * "mshop/common/manager/decorators/default" for the index catalog manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/index/manager/catalog/decorators/global
		 * @see mshop/index/manager/catalog/decorators/local
		 */

		/** mshop/index/manager/catalog/decorators/global
		 * Adds a list of globally available decorators only to the index catalog manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the index catalog manager.
		 *
		 *  mshop/index/manager/catalog/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the catalog controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/index/manager/catalog/decorators/excludes
		 * @see mshop/index/manager/catalog/decorators/local
		 */

		/** mshop/index/manager/catalog/decorators/local
		 * Adds a list of local decorators only to the index catalog manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the index catalog manager.
		 *
		 *  mshop/index/manager/catalog/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator2" only to the catalog
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/index/manager/catalog/decorators/excludes
		 * @see mshop/index/manager/catalog/decorators/global
		 */

		return $this->getSubManagerBase( 'index', 'catalog/' . $manager, $name );
	}


	/**
	 * Optimizes the index if necessary.
	 * Execution of this operation can take a very long time and shouldn't be
	 * called through a web server enviroment.
	 */
	public function optimize()
	{
		/** mshop/index/manager/catalog/standard/optimize/mysql
		 * Optimizes the stored catalog data for retrieving the records faster
		 *
		 * @see mshop/index/manager/catalog/standard/optimize/ansi
		 */

		/** mshop/index/manager/catalog/standard/optimize/ansi
		 * Optimizes the stored catalog data for retrieving the records faster
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
		 * @param string SQL statement for optimizing the stored catalog data
		 * @since 2014.09
		 * @category Developer
		 * @see mshop/index/manager/catalog/standard/count/ansi
		 * @see mshop/index/manager/catalog/standard/search/ansi
		 * @see mshop/index/manager/catalog/standard/aggregate/ansi
		 */
		$this->optimizeBase( 'mshop/index/manager/catalog/standard/optimize' );
	}


	/**
	 * Rebuilds the index catalog for searching products or specified list of products.
	 * This can be a long lasting operation.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[] $items Associative list of product IDs and items implementing \Aimeos\MShop\Product\Item\Iface
	 */
	public function rebuildIndex( array $items = [] )
	{
		if( empty( $items ) ) { return; }

		\Aimeos\MW\Common\Base::checkClassList( '\\Aimeos\\MShop\\Product\\Item\\Iface', $items );
		$listItems = $this->getListItems( $items );

		$context = $this->getContext();
		$editor = $context->getEditor();
		$siteid = $context->getLocale()->getSiteId();
		$date = date( 'Y-m-d H:i:s' );

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			/** mshop/index/manager/catalog/standard/insert/mysql
			 * Inserts a new catalog record into the product index database
			 *
			 * @see mshop/index/manager/catalog/standard/insert/ansi
			 */

			/** mshop/index/manager/catalog/standard/insert/ansi
			 * Inserts a new catalog record into the product index database
			 *
			 * During the product index rebuild, categories related to a
			 * product will be stored in the index for this product. All
			 * records are deleted before the new ones are inserted.
			 *
			 * The SQL statement must be a string suitable for being used as
			 * prepared statement. It must include question marks for binding
			 * the values from the order item to the statement before they are
			 * sent to the database server. The number of question marks must
			 * be the same as the number of columns listed in the INSERT
			 * statement. The order of the columns must correspond to the
			 * order in the rebuildIndex() method, so the correct values are
			 * bound to the columns.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for inserting records
			 * @since 2014.03
			 * @category Developer
			 * @see mshop/index/manager/catalog/standard/cleanup/ansi
			 * @see mshop/index/manager/catalog/standard/delete/ansi
			 * @see mshop/index/manager/catalog/standard/search/ansi
			 * @see mshop/index/manager/catalog/standard/count/ansi
			 */
			$stmt = $this->getCachedStatement( $conn, 'mshop/index/manager/catalog/standard/insert' );

			foreach( $items as $id => $item )
			{
				$parentId = $item->getId(); // $id is not $item->getId() for sub-products

				if( !array_key_exists( $parentId, $listItems ) ) { continue; }

				foreach( (array) $listItems[$parentId] as $listItem )
				{
					$stmt->bind( 1, $parentId, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 2, $siteid, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 3, $listItem->getParentId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 4, $listItem->getType() );
					$stmt->bind( 5, $listItem->getPosition(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 6, $date ); //mtime
					$stmt->bind( 7, $editor );
					$stmt->bind( 8, $date ); //ctime

					try {
						$stmt->execute()->finish();
					} catch( \Aimeos\MW\DB\Exception $e ) { ; } // Ignore duplicates
				}
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}


		foreach( $this->getSubManagers() as $submanager ) {
			$submanager->rebuildIndex( $items );
		}
	}


	/**
	 * Searches for items matching the given criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param integer|null &$total Number of items that are available in total
	 * @return array List of items implementing \Aimeos\MShop\Product\Item\Iface with ids as keys
	 */
	public function searchItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], &$total = null )
	{
		/** mshop/index/manager/catalog/standard/search/mysql
		 * Retrieves the records matched by the given criteria in the database
		 *
		 * @see mshop/index/manager/catalog/standard/search/ansi
		 */

		/** mshop/index/manager/catalog/standard/search/ansi
		 * Retrieves the records matched by the given criteria in the database
		 *
		 * Fetches the records matched by the given criteria from the product index
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
		 * @see mshop/index/manager/catalog/standard/count/ansi
		 * @see mshop/index/manager/catalog/standard/optimize/ansi
		 * @see mshop/index/manager/catalog/standard/aggregate/ansi
		 */
		$cfgPathSearch = 'mshop/index/manager/catalog/standard/search';

		/** mshop/index/manager/catalog/standard/count/mysql
		 * Counts the number of records matched by the given criteria in the database
		 *
		 * @see mshop/index/manager/catalog/standard/count/ansi
		 */

		/** mshop/index/manager/catalog/standard/count/ansi
		 * Counts the number of records matched by the given criteria in the database
		 *
		 * Counts all records matched by the given criteria from the product index
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
		 * @see mshop/index/manager/catalog/standard/search/ansi
		 * @see mshop/index/manager/catalog/standard/optimize/ansi
		 * @see mshop/index/manager/catalog/standard/aggregate/ansi
		 */
		$cfgPathCount = 'mshop/index/manager/catalog/standard/count';

		return $this->searchItemsIndexBase( $search, $ref, $total, $cfgPathSearch, $cfgPathCount );
	}


	/**
	 * Returns the list items referencing the given products
	 *
	 * @param array $items List of product items implementing \Aimeos\MShop\Product\Item\Iface
	 * @return array Associative list of product IDs as keys and lists of list items as values
	 */
	protected function getListItems( array $items )
	{
		$listItems = [];
		$listManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'catalog/lists' );

		$search = $listManager->createSearch( true );
		$expr = array(
			$search->compare( '==', 'catalog.lists.refid', array_keys( $items ) ),
			$search->compare( '==', 'catalog.lists.domain', 'product' ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 0x7FFFFFFF );

		$result = $listManager->searchItems( $search );

		foreach( $result as $listItem ) {
			$listItems[$listItem->getRefId()][] = $listItem;
		}

		return $listItems;
	}


	/**
	 * Returns the list of sub-managers available for the index catalog manager.
	 *
	 * @return array Associative list of the sub-domain as key and the manager object as value
	 */
	protected function getSubManagers()
	{
		if( $this->subManagers === null )
		{
			$this->subManagers = [];

			/** mshop/index/manager/catalog/submanagers
			 * A list of sub-manager names used for indexing associated items to categories
			 *
			 * All items referenced by a product (e.g. texts, prices, media,
			 * etc.) are added to the product index via specialized index
			 * managers. You can add the name of new sub-managers to add more
			 * data to the index or remove existing ones if you don't want to
			 * index that data at all.
			 *
			 * This option configures the sub-managers that cares about
			 * indexing data associated to product categories.
			 *
			 * @param string List of index sub-manager names
			 * @since 2014.09
			 * @category User
			 * @category Developer
			 * @see mshop/index/manager/standard/submanagers
			 */
			$path = 'mshop/index/manager/catalog/submanagers';

			foreach( $this->getContext()->getConfig()->get( $path, [] ) as $domain ) {
				$this->subManagers[$domain] = $this->getSubManager( $domain );
			}

			return $this->subManagers;
		}

		return $this->subManagers;
	}
}