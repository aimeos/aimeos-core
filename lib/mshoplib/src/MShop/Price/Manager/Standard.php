<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Price
 */


namespace Aimeos\MShop\Price\Manager;


/**
 * Default implementation of a price manager.
 *
 * @package MShop
 * @subpackage Price
 */
class Standard
	extends \Aimeos\MShop\Price\Manager\Base
	implements \Aimeos\MShop\Price\Manager\Iface
{
	private $searchConfig = array(
		'price.id' => array(
			'code' => 'price.id',
			'internalcode' => 'mpri."id"',
			'label' => 'Price ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'price.siteid' => array(
			'code' => 'price.siteid',
			'internalcode' => 'mpri."siteid"',
			'label' => 'Price site ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'price.typeid' => array(
			'label' => 'Price type ID',
			'code' => 'price.typeid',
			'internalcode' => 'mpri."typeid"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'price.currencyid' => array(
			'code' => 'price.currencyid',
			'internalcode' => 'mpri."currencyid"',
			'label' => 'Price currency code',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'price.domain' => array(
			'code' => 'price.domain',
			'internalcode' => 'mpri."domain"',
			'label' => 'Price domain',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'price.label' => array(
			'code' => 'price.label',
			'internalcode' => 'mpri."label"',
			'label' => 'Price label',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'price.quantity' => array(
			'code' => 'price.quantity',
			'internalcode' => 'mpri."quantity"',
			'label' => 'Price quantity',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'price.value' => array(
			'code' => 'price.value',
			'internalcode' => 'mpri."value"',
			'label' => 'Price regular value',
			'type' => 'decimal',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'price.costs' => array(
			'code' => 'price.costs',
			'internalcode' => 'mpri."costs"',
			'label' => 'Price shipping costs',
			'type' => 'decimal',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'price.rebate' => array(
			'code' => 'price.rebate',
			'internalcode' => 'mpri."rebate"',
			'label' => 'Price rebate amount',
			'type' => 'decimal',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'price.taxrate' => array(
			'code' => 'price.taxrate',
			'internalcode' => 'mpri."taxrate"',
			'label' => 'Price tax in percent',
			'type' => 'decimal',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'price.status' => array(
			'code' => 'price.status',
			'internalcode' => 'mpri."status"',
			'label' => 'Price status',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'price.mtime'=> array(
			'code'=>'price.mtime',
			'internalcode'=>'mpri."mtime"',
			'label'=>'Price modification date',
			'type'=> 'datetime',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'price.ctime'=> array(
			'code'=>'price.ctime',
			'internalcode'=>'mpri."ctime"',
			'label'=>'Price creation date/time',
			'type'=> 'datetime',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'price.editor'=> array(
			'code'=>'price.editor',
			'internalcode'=>'mpri."editor"',
			'label'=>'Price editor',
			'type'=> 'string',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
	);

	private $taxflag;


	/**
	 * Initializes the object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );
		$this->setResourceName( 'db-price' );

		/** mshop/price/taxflag
		 * Configuration setting if prices are inclusive or exclusive tax
		 *
		 * In Aimeos, prices can be entered either completely with or without tax. The
		 * default is that prices contains tax. You must specifiy the tax rate for each
		 * prices to prevent wrong calculations.
		 *
		 * @param boolean True if gross prices are used, false for net prices
		 * @category Developer
		 * @category User
		 * @since 2016.02
		 */
		$this->taxflag = $context->getConfig()->get( 'mshop/price/taxflag', true );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param array $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'mshop/price/manager/submanagers';
		foreach( $this->getContext()->getConfig()->get( $path, array( 'type', 'lists' ) ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->cleanupBase( $siteids, 'mshop/price/manager/standard/delete' );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param boolean $withsub Return also the resource type of sub-managers if true
	 * @return array Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( $withsub = true )
	{
		$path = 'mshop/price/manager/submanagers';

		return $this->getResourceTypeBase( 'price', $path, array( 'type', 'lists' ), $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** mshop/price/manager/submanagers
		 * List of manager names that can be instantiated by the price manager
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
		$path = 'mshop/price/manager/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, array( 'type', 'lists' ), $withsub );
	}


	/**
	 * Instantiates a new price item object.
	 *
	 * @return \Aimeos\MShop\Price\Item\Iface
	 */
	public function createItem()
	{
		$locale = $this->getContext()->getLocale();
		$values = array( 'price.siteid' => $locale->getSiteId() );

		if( $locale->getCurrencyId() !== null ) {
			$values['price.currencyid'] = $locale->getCurrencyId();
		}

		return $this->createItemBase( $values );
	}


	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		/** mshop/price/manager/standard/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/price/manager/standard/delete/ansi
		 */

		/** mshop/price/manager/standard/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the price database.
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
		 * @see mshop/price/manager/standard/insert/ansi
		 * @see mshop/price/manager/standard/update/ansi
		 * @see mshop/price/manager/standard/newid/ansi
		 * @see mshop/price/manager/standard/search/ansi
		 * @see mshop/price/manager/standard/count/ansi
		 */
		$path = 'mshop/price/manager/standard/delete';
		$this->deleteItemsBase( $ids, $path );
	}


	/**
	 * Returns the price item object specificed by its ID.
	 *
	 * @param integer $id Unique price ID referencing an existing price
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MShop\Price\Item\Iface $item Returns the price item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = [], $default = false )
	{
		return $this->getItemBase( 'price.id', $id, $ref, $default );
	}


	/**
	 * Saves a price item object.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $item Price item object
	 * @param boolean $fetch True if the new ID should be returned in the item
	 *
	 * @throws \Aimeos\MShop\Price\Exception If price couldn't be saved
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Iface $item, $fetch = true )
	{
		$iface = '\\Aimeos\\MShop\\Price\\Item\\Iface';
		if( !( $item instanceof $iface ) ) {
			throw new \Aimeos\MShop\Price\Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

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
				/** mshop/price/manager/standard/insert/mysql
				 * Inserts a new price record into the database table
				 *
				 * @see mshop/price/manager/standard/insert/ansi
				 */

				/** mshop/price/manager/standard/insert/ansi
				 * Inserts a new price record into the database table
				 *
				 * Items with no ID yet (i.e. the ID is NULL) will be created in
				 * the database and the newly created ID retrieved afterwards
				 * using the "newid" SQL statement.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the price item to the statement before they are
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
				 * @see mshop/price/manager/standard/update/ansi
				 * @see mshop/price/manager/standard/newid/ansi
				 * @see mshop/price/manager/standard/delete/ansi
				 * @see mshop/price/manager/standard/search/ansi
				 * @see mshop/price/manager/standard/count/ansi
				 */
				$path = 'mshop/price/manager/standard/insert';
			}
			else
			{
				/** mshop/price/manager/standard/update/mysql
				 * Updates an existing price record in the database
				 *
				 * @see mshop/price/manager/standard/update/ansi
				 */

				/** mshop/price/manager/standard/update/ansi
				 * Updates an existing price record in the database
				 *
				 * Items which already have an ID (i.e. the ID is not NULL) will
				 * be updated in the database.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the price item to the statement before they are
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
				 * @see mshop/price/manager/standard/insert/ansi
				 * @see mshop/price/manager/standard/newid/ansi
				 * @see mshop/price/manager/standard/delete/ansi
				 * @see mshop/price/manager/standard/search/ansi
				 * @see mshop/price/manager/standard/count/ansi
				 */
				$path = 'mshop/price/manager/standard/update';
			}

			$stmt = $this->getCachedStatement( $conn, $path );

			$stmt->bind( 1, $context->getLocale()->getSiteId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 2, $item->getTypeId() );
			$stmt->bind( 3, $item->getCurrencyId() );
			$stmt->bind( 4, $item->getDomain() );
			$stmt->bind( 5, $item->getLabel() );
			$stmt->bind( 6, $item->getQuantity(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 7, $item->getValue() );
			$stmt->bind( 8, $item->getCosts() );
			$stmt->bind( 9, $item->getRebate() );
			$stmt->bind( 10, $item->getTaxRate() );
			$stmt->bind( 11, $item->getStatus(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 12, $date ); //mtime
			$stmt->bind( 13, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind( 14, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$item->setId( $id );
			} else {
				$stmt->bind( 14, $date ); //ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** mshop/price/manager/standard/newid/mysql
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * @see mshop/price/manager/standard/newid/ansi
				 */

				/** mshop/price/manager/standard/newid/ansi
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
				 *  SELECT currval('seq_mpri_id')
				 * For SQL Server:
				 *  SELECT SCOPE_IDENTITY()
				 * For Oracle:
				 *  SELECT "seq_mpri_id".CURRVAL FROM DUAL
				 *
				 * There's no way to retrive the new ID by a SQL statements that
				 * fits for most database servers as they implement their own
				 * specific way.
				 *
				 * @param string SQL statement for retrieving the last inserted record ID
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/price/manager/standard/insert/ansi
				 * @see mshop/price/manager/standard/update/ansi
				 * @see mshop/price/manager/standard/delete/ansi
				 * @see mshop/price/manager/standard/search/ansi
				 * @see mshop/price/manager/standard/count/ansi
				 */
				$path = 'mshop/price/manager/standard/newid';
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
	 * Returns the item objects matched by the given search criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param integer|null &$total Number of items that are available in total
	 * @return array List of items implementing \Aimeos\MShop\Price\Item\Iface
	 */
	public function searchItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], &$total = null )
	{
		$map = $typeIds = [];
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$required = array( 'price' );
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;

			/** mshop/price/manager/standard/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see mshop/price/manager/standard/search/ansi
			 */

			/** mshop/price/manager/standard/search/ansi
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * Fetches the records matched by the given criteria from the price
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
			 * @see mshop/price/manager/standard/insert/ansi
			 * @see mshop/price/manager/standard/update/ansi
			 * @see mshop/price/manager/standard/newid/ansi
			 * @see mshop/price/manager/standard/delete/ansi
			 * @see mshop/price/manager/standard/count/ansi
			 */
			$cfgPathSearch = 'mshop/price/manager/standard/search';

			/** mshop/price/manager/standard/count/mysql
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * @see mshop/price/manager/standard/count/ansi
			 */

			/** mshop/price/manager/standard/count/ansi
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * Counts all records matched by the given criteria from the price
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
			 * @see mshop/price/manager/standard/insert/ansi
			 * @see mshop/price/manager/standard/update/ansi
			 * @see mshop/price/manager/standard/newid/ansi
			 * @see mshop/price/manager/standard/delete/ansi
			 * @see mshop/price/manager/standard/search/ansi
			 */
			$cfgPathCount = 'mshop/price/manager/standard/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false )
			{
				$map[$row['price.id']] = $row;
				$typeIds[$row['price.typeid']] = null;
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		if( !empty( $typeIds ) )
		{
			$typeManager = $this->getSubManager( 'type' );
			$typeSearch = $typeManager->createSearch();
			$typeSearch->setConditions( $typeSearch->compare( '==', 'price.type.id', array_keys( $typeIds ) ) );
			$typeSearch->setSlice( 0, $search->getSliceSize() );
			$typeItems = $typeManager->searchItems( $typeSearch );

			foreach( $map as $id => $row )
			{
				if( isset( $typeItems[$row['price.typeid']] ) )
				{
					$map[$id]['price.type'] = $typeItems[$row['price.typeid']]->getCode();
					$map[$id]['price.typename'] = $typeItems[$row['price.typeid']]->getName();
				}
			}
		}

		return $this->buildItems( $map, $ref, 'price' );
	}


	/**
	 * creates a search object and sets base criteria
	 *
	 * @param boolean $default Prepopulate object with default criterias
	 * @return \Aimeos\MW\Criteria\Iface
	 */
	public function createSearch( $default = false )
	{
		if( $default === true )
		{
			$object = $this->createSearchBase( 'price' );
			$currencyid = $this->getContext()->getLocale()->getCurrencyId();

			if( $currencyid !== null )
			{
				$expr = array(
					$object->compare( '==', 'price.currencyid', $currencyid ),
					$object->getConditions(),
				);

				$object->setConditions( $object->combine( '&&', $expr ) );
			}

			return $object;
		}

		return parent::createSearch();
	}


	/**
	 * Returns a new manager for price extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g type, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->getSubManagerBase( 'price', $manager, $name );
	}


	/**
	 * Creates a new price item
	 *
	 * @param array $values List of attributes for price item
	 * @param array $listItems List of items implementing \Aimeos\MShop\Common\Item\Lists\Iface
	 * @param array $refItems List of items implementing \Aimeos\MShop\Common\Item\Iface
	 * @return \Aimeos\MShop\Price\Item\Iface New price item
	 */
	protected function createItemBase( array $values = [], array $listItems = [], array $refItems = [] )
	{
		if( !isset( $values['price.taxflag'] ) ) {
			$values['price.taxflag'] = $this->taxflag;
		}

		return new \Aimeos\MShop\Price\Item\Standard( $values, $listItems, $refItems );
	}
}
