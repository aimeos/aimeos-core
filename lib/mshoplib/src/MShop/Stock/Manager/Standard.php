<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Stock
 */


namespace Aimeos\MShop\Stock\Manager;


/**
 * Default stock manager implementation.
 *
 * @package MShop
 * @subpackage Stock
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Stock\Manager\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private $searchConfig = array(
		'stock.id' => array(
			'code' => 'stock.id',
			'internalcode' => 'msto."id"',
			'label' => 'ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'stock.siteid' => array(
			'code' => 'stock.siteid',
			'internalcode' => 'msto."siteid"',
			'label' => 'site ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'stock.type' => array(
			'code' => 'stock.type',
			'internalcode' => 'msto."type"',
			'label' => 'Type',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'stock.productid' => array(
			'code' => 'stock.productid',
			'internalcode' => 'msto."prodid"',
			'label' => 'Product ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'stock.stocklevel' => array(
			'code' => 'stock.stocklevel',
			'internalcode' => 'msto."stocklevel"',
			'label' => 'Stock level',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'stock.dateback' => array(
			'code' => 'stock.dateback',
			'internalcode' => 'msto."backdate"',
			'label' => 'Back in stock date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'stock.timeframe' => array(
			'code' => 'stock.timeframe',
			'internalcode' => 'msto."timeframe"',
			'label' => 'Delivery time frame',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'stock.ctime' => array(
			'code' => 'stock.ctime',
			'internalcode' => 'msto."ctime"',
			'label' => 'Creation date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'stock.mtime' => array(
			'code' => 'stock.mtime',
			'internalcode' => 'msto."mtime"',
			'label' => 'Modify date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'stock.editor' => array(
			'code' => 'stock.editor',
			'internalcode' => 'msto."editor"',
			'label' => 'Editor',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
	);


	/**
	 * Initializes the object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );
		$this->setResourceName( 'db-stock' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Stock\Manager\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$path = 'mshop/stock/manager/submanagers';
		foreach( $this->getContext()->getConfig()->get( $path, ['type'] ) as $domain ) {
			$this->getObject()->getSubManager( $domain )->clear( $siteids );
		}

		return $this->clearBase( $siteids, 'mshop/stock/manager/delete' );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Stock\Item\Iface New stock item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values['stock.siteid'] = $this->getContext()->getLocale()->getSiteId();
		return $this->createItemBase( $values );
	}


	/**
	 * Inserts the new stock item
	 *
	 * @param \Aimeos\MShop\Stock\Item\Iface $item Stock item which should be saved
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Stock\Item\Iface Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Stock\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Stock\Item\Iface
	{
		if( !$item->isModified() ) {
			return $item;
		}

		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();
			$date = date( 'Y-m-d H:i:s' );
			$columns = $this->getObject()->getSaveAttributes();

			if( $id === null )
			{
				/** mshop/stock/manager/insert/mysql
				 * Inserts a new product stock record into the database table
				 *
				 * @see mshop/stock/manager/insert/ansi
				 */

				/** mshop/stock/manager/insert/ansi
				 * Inserts a new product stock record into the database table
				 *
				 * Items with no ID yet (i.e. the ID is NULL) will be created in
				 * the database and the newly created ID retrieved afterwards
				 * using the "newid" SQL statement.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the product stock item to the statement before they are
				 * sent to the database server. The number of question marks must
				 * be the same as the number of columns listed in the INSERT
				 * statement. The order of the columns must correspond to the
				 * order in the save() method, so the correct values are
				 * bound to the columns.
				 *
				 * The SQL statement should conform to the ANSI standard to be
				 * compatible with most relational database systems. This also
				 * includes using double quotes for table and column names.
				 *
				 * @param string SQL statement for inserting records
				 * @since 2017.01
				 * @category Developer
				 * @see mshop/stock/manager/update/ansi
				 * @see mshop/stock/manager/newid/ansi
				 * @see mshop/stock/manager/delete/ansi
				 * @see mshop/stock/manager/search/ansi
				 * @see mshop/stock/manager/count/ansi
				 * @see mshop/stock/manager/stocklevel
				 */
				$path = 'mshop/stock/manager/insert';
				$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ) );
			}
			else
			{
				/** mshop/stock/manager/update/mysql
				 * Updates an existing product stock record in the database
				 *
				 * @see mshop/stock/manager/update/ansi
				 */

				/** mshop/stock/manager/update/ansi
				 * Updates an existing product stock record in the database
				 *
				 * Items which already have an ID (i.e. the ID is not NULL) will
				 * be updated in the database.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the product stock item to the statement before they are
				 * sent to the database server. The order of the columns must
				 * correspond to the order in the save() method, so the
				 * correct values are bound to the columns.
				 *
				 * The SQL statement should conform to the ANSI standard to be
				 * compatible with most relational database systems. This also
				 * includes using double quotes for table and column names.
				 *
				 * @param string SQL statement for updating records
				 * @since 2017.01
				 * @category Developer
				 * @see mshop/stock/manager/insert/ansi
				 * @see mshop/stock/manager/newid/ansi
				 * @see mshop/stock/manager/delete/ansi
				 * @see mshop/stock/manager/search/ansi
				 * @see mshop/stock/manager/count/ansi
				 * @see mshop/stock/manager/stocklevel
				 */
				$path = 'mshop/stock/manager/update';
				$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ), false );
			}

			$idx = 1;
			$stmt = $this->getCachedStatement( $conn, $path, $sql );

			foreach( $columns as $name => $entry ) {
				$stmt->bind( $idx++, $item->get( $name ), $entry->getInternalType() );
			}

			$stmt->bind( $idx++, $item->getProductId() );
			$stmt->bind( $idx++, $item->getType() );
			$stmt->bind( $idx++, $item->getStockLevel(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $item->getDateBack() );
			$stmt->bind( $idx++, $item->getTimeFrame() );
			$stmt->bind( $idx++, $date ); //mtime
			$stmt->bind( $idx++, $context->getEditor() );
			$stmt->bind( $idx++, $context->getLocale()->getSiteId() );

			if( $id !== null ) {
				$stmt->bind( $idx++, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			} else {
				$stmt->bind( $idx++, $date ); //ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** mshop/stock/manager/newid/mysql
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * @see mshop/stock/manager/newid/ansi
				 */

				/** mshop/stock/manager/newid/ansi
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * As soon as a new record is inserted into the database table,
				 * the database server generates a new and unique identifier for
				 * that record. This ID can be used for retrieving, updating and
				 * deleting that specific record from the table again.
				 *
				 * For MySQL:
				 *  SELECT LAST_INSERT_ID()
				 * For PostgreSQL:
				 *  SELECT currval('seq_msto_id')
				 * For SQL Server:
				 *  SELECT SCOPE_IDENTITY()
				 * For Oracle:
				 *  SELECT "seq_msto_id".CURRVAL FROM DUAL
				 *
				 * There's no way to retrive the new ID by a SQL statements that
				 * fits for most database servers as they implement their own
				 * specific way.
				 *
				 * @param string SQL statement for retrieving the last inserted record ID
				 * @since 2017.01
				 * @category Developer
				 * @see mshop/stock/manager/insert/ansi
				 * @see mshop/stock/manager/update/ansi
				 * @see mshop/stock/manager/delete/ansi
				 * @see mshop/stock/manager/search/ansi
				 * @see mshop/stock/manager/count/ansi
				 * @see mshop/stock/manager/stocklevel
				 */
				$path = 'mshop/stock/manager/newid';
				$id = $this->newId( $conn, $path );
			}

			$item->setId( $id );

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $item;
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Stock\Manager\Iface Manager object for chaining method calls
	 */
	public function delete( $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/stock/manager/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/stock/manager/delete/ansi
		 */

		/** mshop/stock/manager/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the product database.
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
		 * @param string SQL statement for deleting items
		 * @since 2017.01
		 * @category Developer
		 * @see mshop/stock/manager/insert/ansi
		 * @see mshop/stock/manager/update/ansi
		 * @see mshop/stock/manager/newid/ansi
		 * @see mshop/stock/manager/search/ansi
		 * @see mshop/stock/manager/count/ansi
		 * @see mshop/stock/manager/stocklevel
		 */
		$path = 'mshop/stock/manager/delete';

		return $this->deleteItemsBase( $itemIds, $path );
	}


	/**
	 * Creates a stock item object for the given item id.
	 *
	 * @param string $id Id of the stock item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Stock\Item\Iface Returns the product stock item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function get( string $id, array $ref = [], ?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->getItemBase( 'stock.id', $id, $ref, $default );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		$path = 'mshop/stock/manager/submanagers';
		return $this->getResourceTypeBase( 'stock', $path, [], $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		/** mshop/stock/manager/submanagers
		 * List of manager names that can be instantiated by the product stock manager
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
		 * @since 2017.01
		 * @category Developer
		 */
		$path = 'mshop/stock/manager/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, [], $withsub );
	}


	/**
	 * Search for stock items based on the given critera.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Stock\Item\Iface with ids as keys
	 */
	public function search( \Aimeos\MW\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$items = [];
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$required = array( 'stock' );

			/** mshop/stock/manager/sitemode
			 * Mode how items from levels below or above in the site tree are handled
			 *
			 * By default, only items from the current site are fetched from the
			 * storage. If the ai-sites extension is installed, you can create a
			 * tree of sites. Then, this setting allows you to define for the
			 * whole stock domain if items from parent sites are inherited,
			 * sites from child sites are aggregated or both.
			 *
			 * Available constants for the site mode are:
			 * * 0 = only items from the current site
			 * * 1 = inherit items from parent sites
			 * * 2 = aggregate items from child sites
			 * * 3 = inherit and aggregate items at the same time
			 *
			 * You also need to set the mode in the locale manager
			 * (mshop/locale/manager/sitelevel) to one of the constants.
			 * If you set it to the same value, it will work as described but you
			 * can also use different modes. For example, if inheritance and
			 * aggregation is configured the locale manager but only inheritance
			 * in the domain manager because aggregating items makes no sense in
			 * this domain, then items wil be only inherited. Thus, you have full
			 * control over inheritance and aggregation in each domain.
			 *
			 * @param int Constant from Aimeos\MShop\Locale\Manager\Base class
			 * @category Developer
			 * @since 2018.01
			 * @see mshop/locale/manager/sitelevel
			 */
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
			$level = $context->getConfig()->get( 'mshop/stock/manager/sitemode', $level );

			/** mshop/stock/manager/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see mshop/stock/manager/search/ansi
			 */

			/** mshop/stock/manager/search/ansi
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * Fetches the records matched by the given criteria from the product
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
			 * @since 2017.01
			 * @category Developer
			 * @see mshop/stock/manager/insert/ansi
			 * @see mshop/stock/manager/update/ansi
			 * @see mshop/stock/manager/newid/ansi
			 * @see mshop/stock/manager/delete/ansi
			 * @see mshop/stock/manager/count/ansi
			 * @see mshop/stock/manager/stocklevel
			 */
			$cfgPathSearch = 'mshop/stock/manager/search';

			/** mshop/stock/manager/count/mysql
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * @see mshop/stock/manager/count/ansi
			 */

			/** mshop/stock/manager/count/ansi
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * Counts all records matched by the given criteria from the product
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
			 * @since 2017.01
			 * @category Developer
			 * @see mshop/stock/manager/insert/ansi
			 * @see mshop/stock/manager/update/ansi
			 * @see mshop/stock/manager/newid/ansi
			 * @see mshop/stock/manager/delete/ansi
			 * @see mshop/stock/manager/search/ansi
			 * @see mshop/stock/manager/stocklevel
			 */
			$cfgPathCount = 'mshop/stock/manager/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== null )
			{
				if( $item = $this->applyFilter( $this->createItemBase( $row ) ) ) {
					$items[$row['stock.id']] = $item;
				}
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return map( $items );
	}


	/**
	 * Returns a new manager for stock extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g base, etc.
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/stock/manager/name
		 * Class name of the used product stock manager implementation
		 *
		 * Each default product stock manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\MShop\Stock\Manager\Stock\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\MShop\Stock\Manager\Stock\Mystock
		 *
		 * then you have to set the this configuration option:
		 *
		 *  mshop/stock/manager/name = Mystock
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyStock"!
		 *
		 * @param string Last part of the class name
		 * @since 2017.01
		 * @category Developer
		 */

		/** mshop/stock/manager/decorators/excludes
		 * Excludes decorators added by the "common" option from the product stock manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the product stock manager.
		 *
		 *  mshop/stock/manager/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
		 * "mshop/common/manager/decorators/default" for the product stock manager.
		 *
		 * @param array List of decorator names
		 * @since 2017.01
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/stock/manager/decorators/global
		 * @see mshop/stock/manager/decorators/local
		 */

		/** mshop/stock/manager/decorators/global
		 * Adds a list of globally available decorators only to the product stock manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the product stock manager.
		 *
		 *  mshop/stock/manager/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the product controller.
		 *
		 * @param array List of decorator names
		 * @since 2017.01
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/stock/manager/decorators/excludes
		 * @see mshop/stock/manager/decorators/local
		 */

		/** mshop/stock/manager/decorators/local
		 * Adds a list of local decorators only to the product stock manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the product stock manager.
		 *
		 *  mshop/stock/manager/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator2" only to the product
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2017.01
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/stock/manager/decorators/excludes
		 * @see mshop/stock/manager/decorators/global
		 */

		return $this->getSubManagerBase( 'stock', $manager, $name );
	}


	/**
	 * Decreases the stock level for the given product codes/quantity pairs and type
	 *
	 * @param array $pairs Associative list of product codes as keys and quantities as values
	 * @param string $type Unique code of the stock type
	 * @return \Aimeos\MShop\Stock\Manager\Iface Manager object for chaining method calls
	 */
	public function decrease( iterable $pairs, string $type = 'default' ) : \Aimeos\MShop\Stock\Manager\Iface
	{
		$context = $this->getContext();
		$translations = ['stock.siteid' => '"siteid"'];
		$types = ['stock.siteid' => $this->searchConfig['stock.siteid']['internaltype']];

		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
		$level = $context->getConfig()->get( 'mshop/stock/manager/sitemode', $level );

		$search = $this->getObject()->filter();
		$search->setConditions( $this->getSiteCondition( $search, 'stock.siteid', $level ) );
		$conditions = $search->getConditionSource( $types, $translations );

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			/** mshop/stock/manager/stocklevel/mysql
			 * Increases or decreases the stock level for the given product and type code
			 *
			 * @see mshop/stock/manager/stocklevel/ansi
			 */

			/** mshop/stock/manager/stocklevel/ansi
			 * Increases or decreases the stock level for the given product and type code
			 *
			 * The stock level is decreased for the ordered products each time
			 * an order is placed by a customer successfully. Also, updates
			 * from external sources like ERP systems can increase the stock
			 * level of a product if no absolute values are set via save()
			 * instead.
			 *
			 * The stock level must be from one of the sites that are configured
			 * via the context item. If the current site is part of a tree of
			 * sites, the statement can increase or decrease stock levels from
			 * the current site and all parent sites if the stock level is
			 * inherited by one of the parent sites.
			 *
			 * Each time the stock level is updated, the modify date/time is
			 * set to the current timestamp and the editor field is updated.
			 *
			 * @param string SQL statement for increasing/decreasing the stock level
			 * @since 2017.01
			 * @category Developer
			 * @see mshop/stock/manager/insert/ansi
			 * @see mshop/stock/manager/update/ansi
			 * @see mshop/stock/manager/newid/ansi
			 * @see mshop/stock/manager/delete/ansi
			 * @see mshop/stock/manager/search/ansi
			 * @see mshop/stock/manager/count/ansi
			 */
			$path = 'mshop/stock/manager/stocklevel';

			foreach( $pairs as $prodid => $qty )
			{
				$stmt = $conn->create( str_replace( ':cond', $conditions, $this->getSqlConfig( $path ) ) );

				$stmt->bind( 1, $qty, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$stmt->bind( 2, date( 'Y-m-d H:i:s' ) ); //mtime
				$stmt->bind( 3, $context->getEditor() );
				$stmt->bind( 4, $prodid );
				$stmt->bind( 5, $type );

				$stmt->execute()->finish();
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $this;
	}


	/**
	 * Increases the stock level for the given product codes/quantity pairs and type
	 *
	 * @param array $pairs Associative list of product codes as keys and quantities as values
	 * @param string $type Unique code of the type
	 * @return \Aimeos\MShop\Stock\Manager\Iface Manager object for chaining method calls
	 */
	public function increase( iterable $pairs, string $type = 'default' ) : \Aimeos\MShop\Stock\Manager\Iface
	{
		foreach( $pairs as $prodid => $qty ) {
			$pairs[$prodid] = -$qty;
		}

		return $this->getObject()->decrease( $pairs, $type );
	}


	/**
	 * Creates new stock item object.
	 *
	 * @param array $values Possible optional array keys can be given: id, parentid, siteid, type, stocklevel, backdate
	 * @return \Aimeos\MShop\Stock\Item\Standard New stock item object
	 */
	protected function createItemBase( array $values = [] ) : \Aimeos\MShop\Stock\Item\Iface
	{
		return new \Aimeos\MShop\Stock\Item\Standard( $values );
	}
}
