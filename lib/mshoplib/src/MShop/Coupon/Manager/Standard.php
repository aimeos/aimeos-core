<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Coupon
 */


namespace Aimeos\MShop\Coupon\Manager;


/**
 * Default coupon manager interface for creating and handling coupons.
 *
 * @package MShop
 * @subpackage Coupon
 */
class Standard
	extends \Aimeos\MShop\Coupon\Manager\Base
	implements \Aimeos\MShop\Coupon\Manager\Iface
{
	private $searchConfig = array(
		'coupon.id'=> array(
			'code'=>'coupon.id',
			'internalcode'=>'mcou."id"',
			'label'=>'Coupon ID',
			'type'=> 'integer',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'coupon.siteid'=> array(
			'code'=>'coupon.siteid',
			'internalcode'=>'mcou."siteid"',
			'label'=>'Coupon site ID',
			'type'=> 'integer',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'coupon.label'=> array(
			'code'=>'coupon.label',
			'internalcode'=>'mcou."label"',
			'label'=>'Coupon label',
			'type'=> 'string',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'coupon.provider'=> array(
			'code'=>'coupon.provider',
			'internalcode'=>'mcou."provider"',
			'label'=>'Coupon method',
			'type'=> 'string',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'coupon.config'=> array(
			'code'=>'coupon.config',
			'internalcode'=>'mcou."config"',
			'label'=>'Coupon config',
			'type'=> 'string',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'coupon.datestart'=> array(
			'code'=>'coupon.datestart',
			'internalcode'=>'mcou."start"',
			'label'=>'Coupon start date/time',
			'type'=> 'datetime',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'coupon.dateend'=> array(
			'code'=>'coupon.dateend',
			'internalcode'=>'mcou."end"',
			'label'=>'Coupon end date/time',
			'type'=> 'datetime',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'coupon.status'=> array(
			'code'=>'coupon.status',
			'internalcode'=>'mcou."status"',
			'label'=>'Coupon status',
			'type'=> 'integer',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'coupon.ctime'=> array(
			'code'=>'coupon.ctime',
			'internalcode'=>'mcou."ctime"',
			'label'=>'Coupon create date/time',
			'type'=> 'datetime',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'coupon.mtime'=> array(
			'code'=>'coupon.mtime',
			'internalcode'=>'mcou."mtime"',
			'label'=>'Coupon modification date/time',
			'type'=> 'datetime',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'coupon.editor'=> array(
			'code'=>'coupon.editor',
			'internalcode'=>'mcou."editor"',
			'label'=>'Coupon editor',
			'type'=> 'string',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
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
		$this->setResourceName( 'db-coupon' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param array $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'mshop/coupon/manager/submanagers';
		foreach( $this->getContext()->getConfig()->get( $path, array( 'code' ) ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->cleanupBase( $siteids, 'mshop/coupon/manager/standard/delete' );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param boolean $withsub Return also the resource type of sub-managers if true
	 * @return array Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( $withsub = true )
	{
		$path = 'mshop/coupon/manager/submanagers';

		return $this->getResourceTypeBase( 'coupon', $path, array( 'code' ), $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** mshop/coupon/manager/submanagers
		 * List of manager names that can be instantiated by the coupon manager
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
		$path = 'mshop/coupon/manager/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, array( 'code' ), $withsub );
	}


	/**
	 * Creates a new empty coupon item instance
	 *
	 * @return \Aimeos\MShop\Coupon\Item\Iface Creates a blank coupon item
	 */
	public function createItem()
	{
		$values = array( 'coupon.siteid'=> $this->getContext()->getLocale()->getSiteId() );
		return $this->createItemBase( $values );
	}


	/**
	 * Returns the coupons item specified by its ID.
	 *
	 * @param string $id Unique ID of the coupon item in the storage
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MShop\Coupon\Item\Iface Returns the coupon item of the given ID
	 * @throws \Aimeos\MShop\Exception If coupon couldn't be found
	 */
	public function getItem( $id, array $ref = [], $default = false )
	{
		return $this->getItemBase( 'coupon.id', $id, $ref, $default );
	}


	/**
	 * Saves a coupon item to the storage.
	 *
	 * @param \Aimeos\MShop\Coupon\Item\Iface $item Coupon implementing the coupon interface
	 * @param boolean $fetch True if the new ID should be returned in the item
	 * @throws \Aimeos\MShop\Coupon\Exception If coupon couldn't be saved
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Iface $item, $fetch = true )
	{
		$iface = '\\Aimeos\\MShop\\Coupon\\Item\\Iface';
		if( !( $item instanceof $iface ) ) {
			throw new \Aimeos\MShop\Coupon\Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if( !$item->isModified() ) { return; }

		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();
			$date = date( 'Y-m-d H:i:s' );

			if( $id === null )
			{
				/** mshop/coupon/manager/standard/insert/mysql
				 * Inserts a new coupon record into the database table
				 *
				 * @see mshop/coupon/manager/standard/insert/ansi
				 */

				/** mshop/coupon/manager/standard/insert/ansi
				 * Inserts a new coupon record into the database table
				 *
				 * Items with no ID yet (i.e. the ID is NULL) will be created in
				 * the database and the newly created ID retrieved afterwards
				 * using the "newid" SQL statement.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the coupon item to the statement before they are
				 * sent to the database server. The number of question marks must
				 * be the same as the number of columns listed in the INSERT
				 * statement. The order of the columns must correspond to the
				 * order in the saveItems() method, so the correct values are
				 * bound to the columns.
				 *
				 * The SQL statement should conform to the ANSI standard to be
				 * compatible with most relational database systems. This also
				 * includes using double quotes for table and column names.
				 *
				 * @param string SQL statement for inserting records
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/coupon/manager/standard/update/ansi
				 * @see mshop/coupon/manager/standard/newid/ansi
				 * @see mshop/coupon/manager/standard/delete/ansi
				 * @see mshop/coupon/manager/standard/search/ansi
				 * @see mshop/coupon/manager/standard/count/ansi
				 */
				$path = 'mshop/coupon/manager/standard/insert';
			}
			else
			{
				/** mshop/coupon/manager/standard/update/mysql
				 * Updates an existing coupon record in the database
				 *
				 * @see mshop/coupon/manager/standard/update/ansi
				 */

				/** mshop/coupon/manager/standard/update/ansi
				 * Updates an existing coupon record in the database
				 *
				 * Items which already have an ID (i.e. the ID is not NULL) will
				 * be updated in the database.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the coupon item to the statement before they are
				 * sent to the database server. The order of the columns must
				 * correspond to the order in the saveItems() method, so the
				 * correct values are bound to the columns.
				 *
				 * The SQL statement should conform to the ANSI standard to be
				 * compatible with most relational database systems. This also
				 * includes using double quotes for table and column names.
				 *
				 * @param string SQL statement for updating records
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/coupon/manager/standard/insert/ansi
				 * @see mshop/coupon/manager/standard/newid/ansi
				 * @see mshop/coupon/manager/standard/delete/ansi
				 * @see mshop/coupon/manager/standard/search/ansi
				 * @see mshop/coupon/manager/standard/count/ansi
				 */
				$path = 'mshop/coupon/manager/standard/update';
			}

			$stmt = $this->getCachedStatement( $conn, $path );

			$stmt->bind( 1, $context->getLocale()->getSiteId() );
			$stmt->bind( 2, $item->getLabel() );
			$stmt->bind( 3, $item->getProvider() );
			$stmt->bind( 4, json_encode( $item->getConfig() ) );
			$stmt->bind( 5, $item->getDateStart() );
			$stmt->bind( 6, $item->getDateEnd() );
			$stmt->bind( 7, $item->getStatus(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 8, $date ); // mtime
			$stmt->bind( 9, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind( 10, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$item->setId( $id );
			} else {
				$stmt->bind( 10, $date ); // ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** mshop/coupon/manager/standard/newid/mysql
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * @see mshop/coupon/manager/standard/newid/ansi
				 */

				/** mshop/coupon/manager/standard/newid/ansi
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
				 *  SELECT currval('seq_mcou_id')
				 * For SQL Server:
				 *  SELECT SCOPE_IDENTITY()
				 * For Oracle:
				 *  SELECT "seq_mcou_id".CURRVAL FROM DUAL
				 *
				 * There's no way to retrive the new ID by a SQL statements that
				 * fits for most database servers as they implement their own
				 * specific way.
				 *
				 * @param string SQL statement for retrieving the last inserted record ID
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/coupon/manager/standard/insert/ansi
				 * @see mshop/coupon/manager/standard/update/ansi
				 * @see mshop/coupon/manager/standard/delete/ansi
				 * @see mshop/coupon/manager/standard/search/ansi
				 * @see mshop/coupon/manager/standard/count/ansi
				 */
				$path = 'mshop/coupon/manager/standard/newid';
				$item->setId( $this->newId( $conn, $path ) );
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}
	}


	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		/** mshop/coupon/manager/standard/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/coupon/manager/standard/delete/ansi
		 */

		/** mshop/coupon/manager/standard/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the coupon database.
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
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/coupon/manager/standard/insert/ansi
		 * @see mshop/coupon/manager/standard/update/ansi
		 * @see mshop/coupon/manager/standard/newid/ansi
		 * @see mshop/coupon/manager/standard/search/ansi
		 * @see mshop/coupon/manager/standard/count/ansi
		 */
		$path = 'mshop/coupon/manager/standard/delete';
		$this->deleteItemsBase( $ids, $path );
	}


	/**
	 * Searchs for coupon items based on the given criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param integer|null &$total Number of items that are available in total
	 * @return array Returns a list of coupon items implementing \Aimeos\MShop\Coupon\Item\Iface
	 */
	public function searchItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], &$total = null )
	{
		$dbm = $this->getContext()->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );
		$items = [];

		try
		{
			$required = array( 'coupon' );
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_PATH;

			/** mshop/coupon/manager/standard/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see mshop/coupon/manager/standard/search/ansi
			 */

			/** mshop/coupon/manager/standard/search/ansi
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * Fetches the records matched by the given criteria from the coupon
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
			 * @see mshop/coupon/manager/standard/insert/ansi
			 * @see mshop/coupon/manager/standard/update/ansi
			 * @see mshop/coupon/manager/standard/newid/ansi
			 * @see mshop/coupon/manager/standard/delete/ansi
			 * @see mshop/coupon/manager/standard/count/ansi
			 */
			$cfgPathSearch = 'mshop/coupon/manager/standard/search';

			/** mshop/coupon/manager/standard/count/mysql
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * @see mshop/coupon/manager/standard/count/ansi
			 */

			/** mshop/coupon/manager/standard/count/ansi
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * Counts all records matched by the given criteria from the coupon
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
			 * @see mshop/coupon/manager/standard/insert/ansi
			 * @see mshop/coupon/manager/standard/update/ansi
			 * @see mshop/coupon/manager/standard/newid/ansi
			 * @see mshop/coupon/manager/standard/delete/ansi
			 * @see mshop/coupon/manager/standard/search/ansi
			 */
			$cfgPathCount = 'mshop/coupon/manager/standard/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			try
			{
				while( ( $row = $results->fetch() ) !== false )
				{
					$config = $row['coupon.config'];

					if( ( $row['coupon.config'] = json_decode( $row['coupon.config'], true ) ) === null )
					{
						$msg = sprintf( 'Invalid JSON as result of search for ID "%2$s" in "%1$s": %3$s', 'mshop_locale.config', $row['id'], $config );
						$this->getContext()->getLogger()->log( $msg, \Aimeos\MW\Logger\Base::WARN );
					}

					$items[$row['coupon.id']] = $this->createItemBase( $row );
				}
			}
			catch( \Exception $e )
			{
				$results->finish();
				throw $e;
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $items;
	}


	/**
	 * Returns a new sub manager of the given type and name.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Lists\Iface List manager
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->getSubManagerBase( 'coupon', $manager, $name );
	}


	/**
	 * Returns the coupon model which belongs to the given code.
	 *
	 * @param \Aimeos\MShop\Coupon\Item\Iface $item Coupon item interface
	 * @param string $code Coupon code
	 * @return \Aimeos\MShop\Coupon\Provider\Iface Returns a coupon provider instance
	 * @throws \Aimeos\MShop\Coupon\Exception If coupon couldn't be found
	 */
	public function getProvider( \Aimeos\MShop\Coupon\Item\Iface $item, $code )
	{
		$names = explode( ',', $item->getProvider() );

		if( ( $providername = array_shift( $names ) ) === null ) {
			throw new \Aimeos\MShop\Coupon\Exception( sprintf( 'Provider in "%1$s" not available', $item->getProvider() ) );
		}

		if( ctype_alnum( $providername ) === false ) {
			throw new \Aimeos\MShop\Coupon\Exception( sprintf( 'Invalid characters in provider name "%1$s"', $providername ) );
		}

		$interface = '\\Aimeos\\MShop\\Coupon\\Provider\\Factory\\Iface';
		$classname = '\\Aimeos\\MShop\\Coupon\\Provider\\' . $providername;

		if( class_exists( $classname ) === false ) {
			throw new \Aimeos\MShop\Coupon\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$context = $this->getContext();
		$provider = new $classname( $context, $item, $code );

		if( ( $provider instanceof $interface ) === false )
		{
			$msg = sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $interface );
			throw new \Aimeos\MShop\Coupon\Exception( $msg );
		}

		/** mshop/coupon/provider/decorators
		 * Adds a list of decorators to all coupon provider objects automatcally
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap decorators
		 * ("\Aimeos\MShop\Coupon\Provider\Decorator\*") around the coupon provider.
		 *
		 *  mshop/coupon/provider/decorators = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Coupon\Provider\Decorator\Decorator1" to all coupon provider
		 * objects.
		 *
		 * @param array List of decorator names
		 * @since 2014.05
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/account/favorite/decorators/excludes
		 * @see client/html/account/favorite/decorators/local
		 */
		$decorators = $context->getConfig()->get( 'mshop/coupon/provider/decorators', [] );

		$object = $this->addCouponDecorators( $item, $code, $provider, $names );
		$object = $this->addCouponDecorators( $item, $code, $object, $decorators );
		$object->setObject( $object );

		return $object;
	}


	/**
	 * Creates a search object and sets base criteria
	 *
	 * @param boolean $default
	 * @return \Aimeos\MW\Criteria\Iface
	 */
	public function createSearch( $default = false )
	{
		if( $default === true )
		{
			$object = $this->createSearchBase( 'coupon' );
			$curDate = date( 'Y-m-d H:i:00', time() );

			$expr = [];
			$expr[] = $object->getConditions();

			$temp = [];
			$temp[] = $object->compare( '==', 'coupon.datestart', null );
			$temp[] = $object->compare( '<=', 'coupon.datestart', $curDate );
			$expr[] = $object->combine( '||', $temp );

			$temp = [];
			$temp[] = $object->compare( '==', 'coupon.dateend', null );
			$temp[] = $object->compare( '>=', 'coupon.dateend', $curDate );
			$expr[] = $object->combine( '||', $temp );

			$object->setConditions( $object->combine( '&&', $expr ) );

			return $object;
		}

		return parent::createSearch();
	}


	/**
	 * Creates a new coupon item instance
	 *
	 * @param array $values Values of the coupon item from the storage
	 * @return \Aimeos\MShop\Coupon\Item\Standard Returns a new created coupon item instance
	 */
	protected function createItemBase( array $values = [] )
	{
		return new \Aimeos\MShop\Coupon\Item\Standard( $values );
	}
}