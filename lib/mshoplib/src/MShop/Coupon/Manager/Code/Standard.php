<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Coupon
 */


namespace Aimeos\MShop\Coupon\Manager\Code;


/**
 * Default coupon manager interface for creating and handling coupons.
 *
 * @package MShop
 * @subpackage Coupon
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Coupon\Manager\Code\Iface
{
	private $searchConfig = array(
		'coupon.code.id'=> array(
			'code'=>'coupon.code.id',
			'internalcode'=>'mcouco."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_coupon_code" AS mcouco ON (mcou."id"=mcouco."parentid")' ),
			'label'=>'Coupon code ID',
			'type'=> 'integer',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'coupon.code.siteid'=> array(
			'code'=>'coupon.code.siteid',
			'internalcode'=>'mcouco."siteid"',
			'label'=>'Coupon code site ID',
			'type'=> 'integer',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'coupon.code.parentid'=> array(
			'code'=>'coupon.code.parentid',
			'internalcode'=>'mcouco."parentid"',
			'label'=>'Coupon ID',
			'type'=> 'integer',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'coupon.code.code'=> array(
			'code'=>'coupon.code.code',
			'internalcode'=>'mcouco."code"',
			'label'=>'Coupon code value',
			'type'=> 'string',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'coupon.code.count'=> array(
			'code'=>'coupon.code.count',
			'internalcode'=>'mcouco."count"',
			'label'=>'Coupon code quantity',
			'type'=> 'string',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'coupon.code.datestart'=> array(
			'code'=>'coupon.code.datestart',
			'internalcode'=>'mcouco."start"',
			'label'=>'Coupon code start date/time',
			'type'=> 'datetime',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'coupon.code.dateend'=> array(
			'code'=>'coupon.code.dateend',
			'internalcode'=>'mcouco."end"',
			'label'=>'Coupon code end date/time',
			'type'=> 'datetime',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'coupon.code.ctime'=> array(
			'code'=>'coupon.code.ctime',
			'internalcode'=>'mcouco."ctime"',
			'label'=>'Coupon code create date/time',
			'type'=> 'datetime',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'coupon.code.mtime'=> array(
			'code'=>'coupon.code.mtime',
			'internalcode'=>'mcouco."mtime"',
			'label'=>'Coupon code modification date/time',
			'type'=> 'datetime',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'coupon.code.editor'=> array(
			'code'=>'coupon.code.editor',
			'internalcode'=>'mcouco."editor"',
			'label'=>'Coupon code editor',
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
		$path = 'mshop/coupon/manager/code/submanagers';
		foreach( $this->getContext()->getConfig()->get( $path, [] ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->cleanupBase( $siteids, 'mshop/coupon/manager/code/standard/delete' );
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
		/** mshop/coupon/manager/code/name
		 * Class name of the used coupon code manager implementation
		 *
		 * Each default coupon code manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\MShop\Coupon\Manager\Address\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\MShop\Coupon\Manager\Address\Mycode
		 *
		 * then you have to set the this configuration option:
		 *
		 *  mshop/coupon/manager/code/name = Mycode
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyAddress"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */

		/** mshop/coupon/manager/code/decorators/excludes
		 * Excludes decorators added by the "common" option from the coupon code manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the coupon code manager.
		 *
		 *  mshop/coupon/manager/code/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
		 * "mshop/common/manager/decorators/default" for the coupon code manager.
		 *
		 * @param array Address of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/coupon/manager/code/decorators/global
		 * @see mshop/coupon/manager/code/decorators/local
		 */

		/** mshop/coupon/manager/code/decorators/global
		 * Adds a list of globally available decorators only to the coupon code manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the coupon code manager.
		 *
		 *  mshop/coupon/manager/code/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the coupon controller.
		 *
		 * @param array Address of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/coupon/manager/code/decorators/excludes
		 * @see mshop/coupon/manager/code/decorators/local
		 */

		/** mshop/coupon/manager/code/decorators/local
		 * Adds a list of local decorators only to the coupon code manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the coupon code manager.
		 *
		 *  mshop/coupon/manager/code/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator2" only to the coupon
		 * controller.
		 *
		 * @param array Address of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/coupon/manager/code/decorators/excludes
		 * @see mshop/coupon/manager/code/decorators/global
		 */

		return $this->getSubManagerBase( 'coupon', 'code/' . $manager, $name );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param boolean $withsub Return also the resource type of sub-managers if true
	 * @return array Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( $withsub = true )
	{
		$path = 'mshop/coupon/manager/code/submanagers';

		return $this->getResourceTypeBase( 'coupon/code', $path, [], $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** mshop/coupon/manager/code/submanagers
		 * List of manager names that can be instantiated by the coupon code manager
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
		$path = 'mshop/coupon/manager/code/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, [], $withsub );
	}


	/**
	 * Creates a new empty coupon code instance
	 *
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface Emtpy coupon code object
	 */
	public function createItem()
	{
		$values = array( 'coupon.code.siteid'=> $this->getContext()->getLocale()->getSiteId() );
		return $this->createItemBase( $values );
	}


	/**
	 * Returns the item specified by its code and domain/type if necessary
	 *
	 * @param string $code Code of the item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param string|null $domain Domain of the item if necessary to identify the item uniquely
	 * @param string|null $type Type code of the item if necessary to identify the item uniquely
	 * @return \Aimeos\MShop\Common\Item\Iface Item object
	 */
	public function findItem( $code, array $ref = [], $domain = null, $type = null )
	{
		return $this->findItemBase( array( 'coupon.code.code' => $code ), $ref );
	}


	/**
	 * Returns the coupon code object specified by its ID.
	 *
	 * @param integer $id Unique ID of the coupon code in the storage
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code object
	 * @throws \Aimeos\MShop\Coupon\Exception If coupon couldn't be found
	 */
	public function getItem( $id, array $ref = [], $default = false )
	{
		return $this->getItemBase( 'coupon.code.id', $id, $ref, $default );
	}


	/**
	 * Saves a modified code object to the storage.
	 *
	 * @param \Aimeos\MShop\Coupon\Item\Code\Iface $item Coupon code object
	 * @param boolean $fetch True if the new ID should be returned in the item
	 * @throws \Aimeos\MShop\Coupon\Exception If coupon couldn't be saved
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Iface $item, $fetch = true )
	{
		$iface = '\\Aimeos\\MShop\\Coupon\\Item\\Code\\Iface';
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
				/** mshop/coupon/manager/code/standard/insert/mysql
				 * Inserts a new coupon code record into the database table
				 *
				 * @see mshop/coupon/manager/code/standard/insert/ansi
				 */

				/** mshop/coupon/manager/code/standard/insert/ansi
				 * Inserts a new coupon code record into the database table
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
				 * @see mshop/coupon/manager/code/standard/update/ansi
				 * @see mshop/coupon/manager/code/standard/newid/ansi
				 * @see mshop/coupon/manager/code/standard/delete/ansi
				 * @see mshop/coupon/manager/code/standard/search/ansi
				 * @see mshop/coupon/manager/code/standard/count/ansi
				 * @see mshop/coupon/manager/code/standard/counter/ansi
				 */
				$path = 'mshop/coupon/manager/code/standard/insert';
			}
			else
			{
				/** mshop/coupon/manager/code/standard/update/mysql
				 * Updates an existing coupon code record in the database
				 *
				 * @see mshop/coupon/manager/code/standard/update/ansi
				 */

				/** mshop/coupon/manager/code/standard/update/ansi
				 * Updates an existing coupon code record in the database
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
				 * @see mshop/coupon/manager/code/standard/insert/ansi
				 * @see mshop/coupon/manager/code/standard/newid/ansi
				 * @see mshop/coupon/manager/code/standard/delete/ansi
				 * @see mshop/coupon/manager/code/standard/search/ansi
				 * @see mshop/coupon/manager/code/standard/count/ansi
				 * @see mshop/coupon/manager/code/standard/counter/ansi
				 */
				$path = 'mshop/coupon/manager/code/standard/update';
			}

			$stmt = $this->getCachedStatement( $conn, $path );

			$stmt->bind( 1, $context->getLocale()->getSiteId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 2, $item->getParentId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 3, $item->getCode() );
			$stmt->bind( 4, $item->getCount(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 5, $item->getDateStart() );
			$stmt->bind( 6, $item->getDateEnd() );
			$stmt->bind( 7, $date ); // mtime
			$stmt->bind( 8, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind( 9, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$item->setId( $id );
			} else {
				$stmt->bind( 9, $date ); // ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** mshop/coupon/manager/code/standard/newid/mysql
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * @see mshop/coupon/manager/code/standard/newid/ansi
				 */

				/** mshop/coupon/manager/code/standard/newid/ansi
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
				 *  SELECT currval('seq_mcouco_id')
				 * For SQL Server:
				 *  SELECT SCOPE_IDENTITY()
				 * For Oracle:
				 *  SELECT "seq_mcouco_id".CURRVAL FROM DUAL
				 *
				 * There's no way to retrive the new ID by a SQL statements that
				 * fits for most database servers as they implement their own
				 * specific way.
				 *
				 * @param string SQL statement for retrieving the last inserted record ID
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/coupon/manager/code/standard/insert/ansi
				 * @see mshop/coupon/manager/code/standard/update/ansi
				 * @see mshop/coupon/manager/code/standard/delete/ansi
				 * @see mshop/coupon/manager/code/standard/search/ansi
				 * @see mshop/coupon/manager/code/standard/count/ansi
				 * @see mshop/coupon/manager/code/standard/counter/ansi
				 */
				$path = 'mshop/coupon/manager/code/standard/newid';
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
		/** mshop/coupon/manager/code/standard/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/coupon/manager/code/standard/delete/ansi
		 */

		/** mshop/coupon/manager/code/standard/delete/ansi
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
		 * @see mshop/coupon/manager/code/standard/insert/ansi
		 * @see mshop/coupon/manager/code/standard/update/ansi
		 * @see mshop/coupon/manager/code/standard/newid/ansi
		 * @see mshop/coupon/manager/code/standard/search/ansi
		 * @see mshop/coupon/manager/code/standard/count/ansi
		 * @see mshop/coupon/manager/code/standard/counter/ansi
		 */
		$path = 'mshop/coupon/manager/code/standard/delete';
		$this->deleteItemsBase( $ids, $path );
	}


	/**
	 * Searchs for coupon items based on the given criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param integer|null &$total Number of items that are available in total
	 * @return array List of code items implementing \Aimeos\MShop\Coupon\Item\Code\Iface's
	 */
	public function searchItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], &$total = null )
	{
		$dbm = $this->getContext()->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );
		$items = [];

		try
		{
			$required = array( 'coupon.code' );
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_PATH;

			/** mshop/coupon/manager/code/standard/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see mshop/coupon/manager/code/standard/search/ansi
			 */

			/** mshop/coupon/manager/code/standard/search/ansi
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
			 * @see mshop/coupon/manager/code/standard/insert/ansi
			 * @see mshop/coupon/manager/code/standard/update/ansi
			 * @see mshop/coupon/manager/code/standard/newid/ansi
			 * @see mshop/coupon/manager/code/standard/delete/ansi
			 * @see mshop/coupon/manager/code/standard/count/ansi
			 * @see mshop/coupon/manager/code/standard/counter/ansi
			 */
			$cfgPathSearch = 'mshop/coupon/manager/code/standard/search';

			/** mshop/coupon/manager/code/standard/count/mysql
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * @see mshop/coupon/manager/code/standard/count/ansi
			 */

			/** mshop/coupon/manager/code/standard/count/ansi
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
			 * @see mshop/coupon/manager/code/standard/insert/ansi
			 * @see mshop/coupon/manager/code/standard/update/ansi
			 * @see mshop/coupon/manager/code/standard/newid/ansi
			 * @see mshop/coupon/manager/code/standard/delete/ansi
			 * @see mshop/coupon/manager/code/standard/search/ansi
			 * @see mshop/coupon/manager/code/standard/counter/ansi
			 */
			$cfgPathCount = 'mshop/coupon/manager/code/standard/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			try
			{
				while( ( $row = $results->fetch() ) !== false ) {
					$items[$row['coupon.code.id']] = $this->createItemBase( $row );
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
	 * Decreases the counter of the coupon code.
	 *
	 * @param string $code Unique code of a coupon
	 * @param integer $amount Amount the coupon count should be decreased
	 */
	public function decrease( $code, $amount )
	{
		$this->increase( $code, -$amount );
	}



	/**
	 * Increases the counter of the coupon code.
	 *
	 * @param string $code Unique code of a coupon
	 * @param integer $amount Amount the coupon count should be increased
	 */
	public function increase( $code, $amount )
	{
		$context = $this->getContext();

		$search = $this->createSearch();
		$search->setConditions( $search->compare( '==', 'coupon.code.siteid', $context->getLocale()->getSitePath() ) );

		$types = array( 'coupon.code.siteid' => $this->searchConfig['coupon.code.siteid']['internaltype'] );
		$translations = array( 'coupon.code.siteid' => 'siteid' );
		$conditions = $search->getConditionString( $types, $translations );

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			/** mshop/coupon/manager/code/standard/counter/mysql
			 * Increases or decreases the counter of the coupon code record matched by the given code
			 *
			 * @see mshop/coupon/manager/code/standard/counter/ansi
			 */

			/** mshop/coupon/manager/code/standard/counter/ansi
			 * Increases or decreases the counter of the coupon code record matched by the given code
			 *
			 * A counter is associated to each coupon code and it's decreased
			 * each time by one if a code used in an paid order was redeemed
			 * successfully. Shop owners can also use the coupon code counter to
			 * use the same code more often by setting it to an arbitrary value.
			 * In this case, the code can be redeemed until the counter reaches
			 * zero.
			 *
			 * The coupon codes must be from one of the sites that are configured
			 * via the context item. If the current site is part of a tree of
			 * sites, the statement can increase or decrease codes from the
			 * current site and all parent sites if the code is inherited by one
			 * of the parent sites.
			 *
			 * Each time the code is updated, the modification time is set to
			 * the current timestamp and the editor field is updated.
			 *
			 * @param string SQL statement for increasing/decreasing the coupon code count
			 * @since 2014.03
			 * @category Developer
			 * @see mshop/coupon/manager/code/standard/insert/ansi
			 * @see mshop/coupon/manager/code/standard/update/ansi
			 * @see mshop/coupon/manager/code/standard/newid/ansi
			 * @see mshop/coupon/manager/code/standard/delete/ansi
			 * @see mshop/coupon/manager/code/standard/search/ansi
			 * @see mshop/coupon/manager/code/standard/count/ansi
			 */
			$path = 'mshop/coupon/manager/code/standard/counter';
			$stmt = $conn->create( str_replace( ':cond', $conditions, $this->getSqlConfig( $path ) ) );

			$stmt->bind( 1, $amount, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 2, date( 'Y-m-d H:i:s' ) ); // mtime
			$stmt->bind( 3, $context->getEditor() );
			$stmt->bind( 4, $code );

			$stmt->execute()->finish();
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		$dbm->release( $conn, $dbname );
	}


	/**
	 * Creates a new code instance
	 *
	 * @param array $values Associative list of item key/value pairs
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface Emtpy coupon code object
	 */
	protected function createItemBase( array $values = [] )
	{
		return new \Aimeos\MShop\Coupon\Item\Code\Standard( $values );
	}


	/**
	 * creates a search object and sets base criteria
	 *
	 * @param boolean $default
	 * @return \Aimeos\MW\Criteria\Iface
	 */
	public function createSearch( $default = false )
	{
		$dbm = $this->getContext()->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		$object = new \Aimeos\MW\Criteria\SQL( $conn );

		$dbm->release( $conn, $dbname );

		if( $default === true )
		{
			$curDate = date( 'Y-m-d H:i:00', time() );

			$expr = array(
				$object->compare( '>', 'coupon.code.count', 0 )
			);

			$temp = [];
			$temp[] = $object->compare( '==', 'coupon.code.datestart', null );
			$temp[] = $object->compare( '<=', 'coupon.code.datestart', $curDate );
			$expr[] = $object->combine( '||', $temp );

			$temp = [];
			$temp[] = $object->compare( '==', 'coupon.code.dateend', null );
			$temp[] = $object->compare( '>=', 'coupon.code.dateend', $curDate );
			$expr[] = $object->combine( '||', $temp );

			$object->setConditions( $object->combine( '&&', $expr ) );
		}

		return $object;
	}
}