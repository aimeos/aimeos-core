<?php
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Index
 */


namespace Aimeos\MShop\Index\Manager\Text;


/**
 * Submanager for text.
 *
 * @package MShop
 * @subpackage Index
 */
class Standard
	extends \Aimeos\MShop\Index\Manager\DBBase
	implements \Aimeos\MShop\Index\Manager\Text\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private $searchConfig = array(
		// @deprecated Removed 2019.01
		'index.text.id' => array(
			'code' => 'index.text.id',
			'internalcode' => 'mindte."prodid"',
			'internaldeps' => array( 'LEFT JOIN "mshop_index_text" AS mindte ON mindte."prodid" = mpro."id"' ),
			'label' => 'Product index text ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'index.text:url' => array(
			'code' => 'index.text:url()',
			'internalcode' => ':site AND mindte."url"',
			'label' => 'Product URL',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'index.text:name' => array(
			'code' => 'index.text:name()',
			'internalcode' => ':site AND mindte."langid" = $1 AND mindte."name"',
			'label' => 'Product name, parameter(<language ID>)',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'sort:index.text:name' => array(
			'code' => 'sort:index.text:name()',
			'internalcode' => 'mindte."name"',
			'label' => 'Sort by product name, parameter(<language ID>)',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'index.text:relevance' => array(
			'code' => 'index.text:relevance()',
			'internalcode' => ':site AND mindte."langid" = $1 AND POSITION( $2 IN mindte."content" )',
			'label' => 'Product texts, parameter(<language ID>,<search term>)',
			'type' => 'float',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT,
			'public' => false,
		),
		'sort:index.text:relevance' => array(
			'code' => 'sort:index.text:relevance()',
			'internalcode' => '-POSITION( $2 IN mindte."content" )',
			'label' => 'Product texts, parameter(<language ID>,<search term>)',
			'type' => 'float',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT,
			'public' => false,
		),
	);

	private $languageIds;
	private $subManagers;


	/**
	 * Initializes the manager instance.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );

		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
		$level = $context->getConfig()->get( 'mshop/index/manager/sitemode', $level );

		$this->searchConfig['index.text:relevance']['function'] = $this->getFunctionRelevance();

		foreach( ['index.text:name', 'index.text:url', 'index.text:relevance'] as $key )
		{
			$expr = $this->getSiteString( 'mindte."siteid"', $level );
			$this->searchConfig[$key]['internalcode'] = str_replace( ':site', $expr, $this->searchConfig[$key]['internalcode'] );
		}
	}


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
		return [];
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Index\Manager\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		parent::clear( $siteids );

		return $this->clearBase( $siteids, 'mshop/index/manager/text/delete' );
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
		/** mshop/index/manager/text/cleanup/mysql
		 * Deletes the index text records that haven't been touched
		 *
		 * @see mshop/index/manager/text/cleanup/ansi
		 */

		/** mshop/index/manager/text/cleanup/ansi
		 * Deletes the index text records that haven't been touched
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
		 * @param string SQL statement for deleting the outdated text index records
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/index/manager/text/count/ansi
		 * @see mshop/index/manager/text/delete/ansi
		 * @see mshop/index/manager/text/insert/ansi
		 * @see mshop/index/manager/text/search/ansi
		 * @see mshop/index/manager/text/text/ansi
		 */
		return $this->cleanupBase( $timestamp, 'mshop/index/manager/text/cleanup' );
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Index\Manager\Iface Manager object for chaining method calls
	 */
	public function delete( $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/index/manager/text/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/index/manager/text/delete/ansi
		 */

		/** mshop/index/manager/text/delete/ansi
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
		 * @param string SQL statement for deleting index text records
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/index/manager/text/count/ansi
		 * @see mshop/index/manager/text/cleanup/ansi
		 * @see mshop/index/manager/text/insert/ansi
		 * @see mshop/index/manager/text/search/ansi
		 * @see mshop/index/manager/text/text/ansi
		 */
		return $this->deleteItemsBase( $itemIds, 'mshop/index/manager/text/delete', true, 'prodid' );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		$path = 'mshop/index/manager/text/submanagers';
		return $this->getResourceTypeBase( 'index/text', $path, [], $withsub );
	}


	/**
	 * Returns a list of objects describing the available criterias for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return array List of items implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		$list = parent::getSearchAttributes( $withsub );

		/** mshop/index/manager/text/submanagers
		 * List of manager names that can be instantiated by the index text manager
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
		$path = 'mshop/index/manager/text/submanagers';

		return $list + $this->getSearchAttributesBase( $this->searchConfig, $path, [], $withsub );
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
		/** mshop/index/manager/text/name
		 * Class name of the used index text manager implementation
		 *
		 * Each default index text manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\MShop\Index\Manager\Text\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\MShop\Index\Manager\Text\Mytext
		 *
		 * then you have to set the this configuration option:
		 *
		 *  mshop/index/manager/text/name = Mytext
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyText"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */

		/** mshop/index/manager/text/decorators/excludes
		 * Excludes decorators added by the "common" option from the index text manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the index text manager.
		 *
		 *  mshop/index/manager/text/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
		 * "mshop/common/manager/decorators/default" for the index text manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/index/manager/text/decorators/global
		 * @see mshop/index/manager/text/decorators/local
		 */

		/** mshop/index/manager/text/decorators/global
		 * Adds a list of globally available decorators only to the index text manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the index text
		 * manager.
		 *
		 *  mshop/index/manager/text/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the index
		 * text manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/index/manager/text/decorators/excludes
		 * @see mshop/index/manager/text/decorators/local
		 */

		/** mshop/index/manager/text/decorators/local
		 * Adds a list of local decorators only to the index text manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\MShop\Index\Manager\Text\Decorator\*") around the index text
		 * manager.
		 *
		 *  mshop/index/manager/text/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\MShop\Index\Manager\Text\Decorator\Decorator2" only to the index
		 * text manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/index/manager/text/decorators/excludes
		 * @see mshop/index/manager/text/decorators/global
		 */

		return $this->getSubManagerBase( 'index', 'text/' . $manager, $name );
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
		/** mshop/index/manager/text/optimize/mysql
		 * Optimizes the stored text data for retrieving the records faster
		 *
		 * @see mshop/index/manager/text/optimize/ansi
		 */

		/** mshop/index/manager/text/optimize/ansi
		 * Optimizes the stored text data for retrieving the records faster
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
		 * @param string SQL statement for optimizing the stored text data
		 * @since 2014.09
		 * @category Developer
		 * @see mshop/index/manager/text/aggregate/ansi
		 * @see mshop/index/manager/text/cleanup/ansi
		 * @see mshop/index/manager/text/count/ansi
		 * @see mshop/index/manager/text/insert/ansi
		 * @see mshop/index/manager/text/search/ansi
		 * @see mshop/index/manager/text/text/ansi
		 */
		return $this->optimizeBase( 'mshop/index/manager/text/optimize' );
	}


	/**
	 * Rebuilds the index text for searching products or specified list of products.
	 * This can be a long lasting operation.
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface[] $items Associative list of product IDs as keys and items as values
	 * @return \Aimeos\MShop\Index\Manager\Iface Manager object for chaining method calls
	 */
	public function rebuild( iterable $items = [] ) : \Aimeos\MShop\Index\Manager\Iface
	{
		if( map( $items )->isEmpty() ) { return $this; }

		\Aimeos\MW\Common\Base::checkClassList( \Aimeos\MShop\Product\Item\Iface::class, $items );

		$context = $this->getContext();
		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			/** mshop/index/manager/text/insert/mysql
			 * Inserts a new text record into the product index database
			 *
			 * @see mshop/index/manager/text/insert/ansi
			 */

			/** mshop/index/manager/text/insert/ansi
			 * Inserts a new text record into the product index database
			 *
			 * During the product index rebuild, texts related to a product
			 * will be stored in the index for this product. All records
			 * are deleted before the new ones are inserted.
			 *
			 * The SQL statement must be a string suitable for being used as
			 * prepared statement. It must include question marks for binding
			 * the values from the order item to the statement before they are
			 * sent to the database server. The number of question marks must
			 * be the same as the number of columns listed in the INSERT
			 * statement. The order of the columns must correspond to the
			 * order in the rebuild() method, so the correct values are
			 * bound to the columns.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for inserting records
			 * @since 2014.03
			 * @category Developer
			 * @see mshop/index/manager/text/cleanup/ansi
			 * @see mshop/index/manager/text/count/ansi
			 * @see mshop/index/manager/text/delete/ansi
			 * @see mshop/index/manager/text/insert/ansi
			 * @see mshop/index/manager/text/search/ansi
			 * @see mshop/index/manager/text/text/ansi
			 */
			$stmt = $this->getCachedStatement( $conn, 'mshop/index/manager/text/insert' );

			foreach( $items as $item ) {
				$this->saveTexts( $stmt, $item );
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		foreach( $this->getSubManagers() as $submanager ) {
			$submanager->rebuild( $items );
		}

		return $this;
	}


	/**
	 * Removes the products from the product index.
	 *
	 * @param array|string $ids Product ID or list of IDs
	 * @return \Aimeos\MShop\Index\Manager\Iface Manager object for chaining method calls
	 */
	public function remove( $ids ) : \Aimeos\MShop\Index\Manager\Iface
	{
		parent::remove( $ids )->delete( $ids );
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
		/** mshop/index/manager/text/search/mysql
		 * Retrieves the records matched by the given criteria in the database
		 *
		 * @see mshop/index/manager/text/search/ansi
		 */

		/** mshop/index/manager/text/search/ansi
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
		 * @see mshop/index/manager/text/aggregate/ansi
		 * @see mshop/index/manager/text/cleanup/ansi
		 * @see mshop/index/manager/text/count/ansi
		 * @see mshop/index/manager/text/insert/ansi
		 * @see mshop/index/manager/text/optimize/ansi
		 * @see mshop/index/manager/text/text/ansi
		 */
		$cfgPathSearch = 'mshop/index/manager/text/search';

		/** mshop/index/manager/text/count/mysql
		 * Counts the number of records matched by the given criteria in the database
		 *
		 * @see mshop/index/manager/text/count/ansi
		 */

		/** mshop/index/manager/text/count/ansi
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
		 * @see mshop/index/manager/text/aggregate/ansi
		 * @see mshop/index/manager/text/cleanup/ansi
		 * @see mshop/index/manager/text/insert/ansi
		 * @see mshop/index/manager/text/optimize/ansi
		 * @see mshop/index/manager/text/search/ansi
		 * @see mshop/index/manager/text/text/ansi
		 */
		$cfgPathCount = 'mshop/index/manager/text/count';

		return $this->searchItemsIndexBase( $search, $ref, $total, $cfgPathSearch, $cfgPathCount );
	}


	/**
	 * Returns the search function for searching by relevance
	 *
	 * @return \Closure Relevance search function
	 */
	protected function getFunctionRelevance()
	{
		return function( $source, array $params ) {

			if( isset( $params[1] ) ) {
				$params[1] = mb_strtolower( $params[1] );
			}

			return $params;
		};
	}


	/**
	 * Returns the language IDs available for the current site
	 *
	 * @return string[] List of ISO language codes
	 */
	protected function getLanguageIds() : array
	{
		if( !isset( $this->languageIds ) )
		{
			$list = [];
			$manager = \Aimeos\MShop::create( $this->getContext(), 'locale' );
			$items = $manager->search( $manager->filter()->slice( 0, 10000 ) );

			foreach( $items as $item ) {
				$list[$item->getLanguageId()] = null;
			}

			$this->languageIds = array_keys( $list );
		}

		return $this->languageIds;
	}


	/**
	 * Saves the text items referenced indirectly by products
	 *
	 * @param \Aimeos\MW\DB\Statement\Iface $stmt Prepared SQL statement with place holders
	 * @param \Aimeos\MShop\Product\Item\Iface $item Product item containing associated text items
	 */
	protected function saveTexts( \Aimeos\MW\DB\Statement\Iface $stmt, \Aimeos\MShop\Product\Item\Iface $item )
	{
		$texts = [];

		foreach( $item->getRefItems( 'text', 'url', 'default' ) as $text ) {
			$texts[$text->getLanguageId()]['url'] = \Aimeos\MW\Str::slug( $text->getContent() );
		}

		foreach( $item->getRefItems( 'text', 'name', 'default' ) as $text ) {
			$texts[$text->getLanguageId()]['name'] = $text->getContent();
		}

		/** mshop/index/manager/text/types
		 * List of text types that should be added to the product index
		 *
		 * By default, all available texts of a product are indexed. This setting
		 * allows you to name only those text types that should be added. All
		 * others will be left out so products won't be found if users search
		 * for words that are part of those skipped texts. This is most useful
		 * for avoiding product matches due to texts that should be internal only.
		 *
		 * @param array|string|null Type name or list of type names, null for all
		 * @category Developer
		 * @since 2019.04
		 */
		$types = $this->getContext()->getConfig()->get( 'mshop/index/manager/text/types' );
		$products = ( $item->getType() === 'select' ? $item->getRefItems( 'product', null, 'default' ) : [] );
		$products[] = $item;

		foreach( $products as $product )
		{
			foreach( $this->getLanguageIds() as $langId )
			{
				$texts[$langId]['content'][] = $product->getCode();

				foreach( $product->getCatalogItems() as $catItem ) {
					$texts[$langId]['content'][] = $catItem->getName();
				}

				foreach( $product->getSupplierItems() as $supItem ) {
					$texts[$langId]['content'][] = $supItem->getName();
				}
			}

			foreach( $product->getRefItems( 'text', $types ) as $text ) {
				$texts[$text->getLanguageId()]['content'][] = $text->getContent();
			}
		}

		$this->saveTextMap( $stmt, $item, $texts );
	}


	/**
	 * Saves the mapped texts for the given item
	 *
	 * @param \Aimeos\MW\DB\Statement\Iface $stmt Prepared SQL statement with place holders
	 * @param \Aimeos\MShop\Product\Item\Iface $item Product item containing associated text items
	 * @param array $map Associative list of text types as keys and content as value
	 */
	protected function saveTextMap( \Aimeos\MW\DB\Statement\Iface $stmt, \Aimeos\MShop\Product\Item\Iface $item, array $texts )
	{
		$date = date( 'Y-m-d H:i:s' );
		$siteid = $this->getContext()->getLocale()->getSiteId();

		foreach( $texts as $langId => $map )
		{
			if( $langId == '' ) {
				continue;
			}

			$url = $map['url'] ?? $item->getName( 'url', $langId );

			if( isset( $texts[''] ) ) {
				$map['content'] = array_merge( $map['content'], $texts['']['content'] );
			}

			if( !isset( $map['name'] ) )
			{
				if( isset( $texts['']['name'] ) ) {
					$map['name'] = $texts['']['name'];
				} else {
					$map['content'][] = $map['name'] = $item->getLabel();
				}
			}

			$content = ' ' . join( ' ', $map['content'] ); // extra space for SQL POSITION() > 0
			$this->saveText( $stmt, $item->getId(), $siteid, $langId, $url, $map['name'], $content, $date );
		}
	}


	/**
	 * Saves the text record with given set of parameters.
	 *
	 * @param \Aimeos\MW\DB\Statement\Iface $stmt Prepared SQL statement with place holders
	 * @param string $id ID of the product item
	 * @param string $siteid Site ID
	 * @param string $lang Two letter ISO language code
	 * @param string $url Product name in URL
	 * @param string $name Name of the product
	 * @param string $content Text content to store
	 * @param string $date Current timestamp in "YYYY-MM-DD HH:mm:ss" format
	 */
	protected function saveText( \Aimeos\MW\DB\Statement\Iface $stmt, string $id, string $siteid, string $lang,
		string $url, string $name, string $content, string $date )
	{
		$stmt->bind( 1, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
		$stmt->bind( 2, $lang );
		$stmt->bind( 3, $url );
		$stmt->bind( 4, $name );
		$stmt->bind( 5, mb_strtolower( $content ) ); // for case insensitive searches
		$stmt->bind( 6, $date ); //mtime
		$stmt->bind( 7, $siteid );

		try {
			$stmt->execute()->finish();
		} catch( \Aimeos\MW\DB\Exception $e ) { ; } // Ignore duplicates
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

			/** mshop/index/manager/text/submanagers
			 * A list of sub-manager names used for indexing associated items to texts
			 *
			 * All items referenced by a product (e.g. texts, prices, media,
			 * etc.) are added to the product index via specialized index
			 * managers. You can add the name of new sub-managers to add more
			 * data to the index or remove existing ones if you don't want to
			 * index that data at all.
			 *
			 * This option configures the sub-managers that cares about
			 * indexing data associated to product texts.
			 *
			 * @param string List of index sub-manager names
			 * @since 2014.09
			 * @category User
			 * @category Developer
			 * @see mshop/index/manager/submanagers
			 */
			foreach( $config->get( 'mshop/index/manager/text/submanagers', [] ) as $domain )
			{
				$name = $config->get( 'mshop/index/manager/text/' . $domain . '/name' );
				$this->subManagers[$domain] = $this->getObject()->getSubManager( $domain, $name );
			}

			return $this->subManagers;
		}

		return $this->subManagers;
	}
}
