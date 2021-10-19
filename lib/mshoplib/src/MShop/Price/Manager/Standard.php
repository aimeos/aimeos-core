<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	implements \Aimeos\MShop\Price\Manager\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
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
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'price.type' => array(
			'label' => 'Price type ID',
			'code' => 'price.type',
			'internalcode' => 'mpri."type"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
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
			'type' => 'float',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT,
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
			'label' => 'Price tax rates as JSON encoded string',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'price.status' => array(
			'code' => 'price.status',
			'internalcode' => 'mpri."status"',
			'label' => 'Price status',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'price.mtime' => array(
			'code' => 'price.mtime',
			'internalcode' => 'mpri."mtime"',
			'label' => 'Price modify date',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'price.ctime' => array(
			'code' => 'price.ctime',
			'internalcode' => 'mpri."ctime"',
			'label' => 'Price create date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'price.editor' => array(
			'code' => 'price.editor',
			'internalcode' => 'mpri."editor"',
			'label' => 'Price editor',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'price:has' => array(
			'code' => 'price:has()',
			'internalcode' => ':site AND :key AND mprili."id"',
			'internaldeps' => ['LEFT JOIN "mshop_price_list" AS mprili ON ( mprili."parentid" = mpri."id" )'],
			'label' => 'Price has list item, parameter(<domain>[,<list type>[,<reference ID>)]]',
			'type' => 'null',
			'internaltype' => 'null',
			'public' => false,
		),
		'price:prop' => array(
			'code' => 'price:prop()',
			'internalcode' => ':site AND :key AND mpripr."id"',
			'internaldeps' => ['LEFT JOIN "mshop_price_property" AS mpripr ON ( mpripr."parentid" = mpri."id" )'],
			'label' => 'Price has property item, parameter(<property type>[,<language code>[,<property value>]])',
			'type' => 'null',
			'internaltype' => 'null',
			'public' => false,
		),
	);

	private $currencyId;
	private $precision;
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
		$this->currencyId = $context->getLocale()->getCurrencyId();

		/** mshop/price/taxflag
		 * Configuration setting if prices are inclusive or exclusive tax
		 *
		 * In Aimeos, prices can be entered either completely with or without tax. The
		 * default is that prices contains tax. You must specifiy the tax rate for each
		 * prices to prevent wrong calculations.
		 *
		 * @param bool True if gross prices are used, false for net prices
		 * @category Developer
		 * @category User
		 * @since 2016.02
		 */
		$this->taxflag = $context->getConfig()->get( 'mshop/price/taxflag', true );

		/** mshop/price/precision
		 * Number of decimal digits prices contain
		 *
		 * Sets the number of decimal digits price values will contain. Internally,
		 * prices are calculated as double values with high precision but these
		 * values will be rounded after calculation to the configured number of digits.
		 *
		 * @param int Positive number of digits
		 * @category Developer
		 * @since 2019.04
		 */
		$this->precision = $context->getConfig()->get( 'mshop/price/precision', 2 );

		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
		$level = $context->getConfig()->get( 'mshop/price/manager/sitemode', $level );


		$this->searchConfig['price:has']['function'] = function( &$source, array $params ) use ( $level ) {

			$keys = [];

			foreach( (array) ( $params[1] ?? '' ) as $type ) {
				foreach( (array) ( $params[2] ?? '' ) as $id ) {
					$keys[] = $params[0] . '|' . ( $type ? $type . '|' : '' ) . $id;
				}
			}

			$sitestr = $this->getSiteString( 'mprili."siteid"', $level );
			$keystr = $this->toExpression( 'mprili."key"', $keys, ( $params[2] ?? null ) ? '==' : '=~' );
			$source = str_replace( [':site', ':key'], [$sitestr, $keystr], $source );

			return $params;
		};


		$this->searchConfig['price:prop']['function'] = function( &$source, array $params ) use ( $level ) {

			$keys = [];
			$langs = array_key_exists( 1, $params ) ? ( $params[1] ?? 'null' ) : '';

			foreach( (array) $langs as $lang ) {
				foreach( (array) ( $params[2] ?? '' ) as $id ) {
					$keys[] = $params[0] . '|' . ( $lang === null ? 'null|' : ( $lang ? $lang . '|' : '' ) ) . ( $id != '' ? md5( $id ) : '' );
				}
			}

			$sitestr = $this->getSiteString( 'mpripr."siteid"', $level );
			$keystr = $this->toExpression( 'mpripr."key"', $keys, ( $params[2] ?? null ) ? '==' : '=~' );
			$source = str_replace( [':site', ':key'], [$sitestr, $keystr], $source );

			return $params;
		};
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Price\Manager\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$path = 'mshop/price/manager/submanagers';
		foreach( $this->getContext()->getConfig()->get( $path, ['type', 'property', 'lists'] ) as $domain ) {
			$this->getObject()->getSubManager( $domain )->clear( $siteids );
		}

		return $this->clearBase( $siteids, 'mshop/price/manager/delete' );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Price\Item\Iface New price item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$locale = $this->getContext()->getLocale();
		$values['price.siteid'] = $locale->getSiteId();

		if( !isset( $values['price.currencyid'] ) && $locale->getCurrencyId() !== null ) {
			$values['price.currencyid'] = $locale->getCurrencyId();
		}

		return $this->createItemBase( $values );
	}



	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		$path = 'mshop/price/manager/submanagers';
		return $this->getResourceTypeBase( 'price', $path, ['property', 'lists'], $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
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

		return $this->getSearchAttributesBase( $this->searchConfig, $path, [], $withsub );
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Price\Manager\Iface Manager object for chaining method calls
	 */
	public function delete( $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/price/manager/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/price/manager/delete/ansi
		 */

		/** mshop/price/manager/delete/ansi
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
		 * @see mshop/price/manager/insert/ansi
		 * @see mshop/price/manager/update/ansi
		 * @see mshop/price/manager/newid/ansi
		 * @see mshop/price/manager/search/ansi
		 * @see mshop/price/manager/count/ansi
		 */
		$path = 'mshop/price/manager/delete';

		return $this->deleteItemsBase( $itemIds, $path )->deleteRefItems( $itemIds );
	}


	/**
	 * Returns the price item object specificed by its ID.
	 *
	 * @param string $id Unique price ID referencing an existing price
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Price\Item\Iface $item Returns the price item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function get( string $id, array $ref = [], ?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->getItemBase( 'price.id', $id, $ref, $default );
	}


	/**
	 * Saves a price item object.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $item Price item object
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Price\Item\Iface Updated item including the generated ID
	 * @throws \Aimeos\MShop\Price\Exception If price couldn't be saved
	 */
	public function saveItem( \Aimeos\MShop\Price\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Price\Item\Iface
	{
		if( !$item->isModified() )
		{
			$item = $this->savePropertyItems( $item, 'price', $fetch );
			return $this->saveListItems( $item, 'price', $fetch );
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
				/** mshop/price/manager/insert/mysql
				 * Inserts a new price record into the database table
				 *
				 * @see mshop/price/manager/insert/ansi
				 */

				/** mshop/price/manager/insert/ansi
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
				 * order in the save() method, so the correct values are
				 * bound to the columns.
				 *
				 * The SQL statement should conform to the ANSI standard to be
				 * compatible with most relational database systems. This also
				 * includes using double quotes for table and column names.
				 *
				 * @param string SQL statement for inserting records
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/price/manager/update/ansi
				 * @see mshop/price/manager/newid/ansi
				 * @see mshop/price/manager/delete/ansi
				 * @see mshop/price/manager/search/ansi
				 * @see mshop/price/manager/count/ansi
				 */
				$path = 'mshop/price/manager/insert';
				$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ) );
			}
			else
			{
				/** mshop/price/manager/update/mysql
				 * Updates an existing price record in the database
				 *
				 * @see mshop/price/manager/update/ansi
				 */

				/** mshop/price/manager/update/ansi
				 * Updates an existing price record in the database
				 *
				 * Items which already have an ID (i.e. the ID is not NULL) will
				 * be updated in the database.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the price item to the statement before they are
				 * sent to the database server. The order of the columns must
				 * correspond to the order in the save() method, so the
				 * correct values are bound to the columns.
				 *
				 * The SQL statement should conform to the ANSI standard to be
				 * compatible with most relational database systems. This also
				 * includes using double quotes for table and column names.
				 *
				 * @param string SQL statement for updating records
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/price/manager/insert/ansi
				 * @see mshop/price/manager/newid/ansi
				 * @see mshop/price/manager/delete/ansi
				 * @see mshop/price/manager/search/ansi
				 * @see mshop/price/manager/count/ansi
				 */
				$path = 'mshop/price/manager/update';
				$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ), false );
			}

			$idx = 1;
			$stmt = $this->getCachedStatement( $conn, $path, $sql );

			foreach( $columns as $name => $entry ) {
				$stmt->bind( $idx++, $item->get( $name ), $entry->getInternalType() );
			}

			$stmt->bind( $idx++, $item->getType() );
			$stmt->bind( $idx++, $item->getCurrencyId() );
			$stmt->bind( $idx++, $item->getDomain() );
			$stmt->bind( $idx++, $item->getLabel() );
			$stmt->bind( $idx++, $item->getQuantity(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $item->getValue() );
			$stmt->bind( $idx++, $item->getCosts() );
			$stmt->bind( $idx++, $item->getRebate() );
			$stmt->bind( $idx++, json_encode( $item->getTaxrates(), JSON_FORCE_OBJECT ) );
			$stmt->bind( $idx++, $item->getStatus(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $date ); //mtime
			$stmt->bind( $idx++, $context->getEditor() );
			$stmt->bind( $idx++, $context->getLocale()->getSiteId() );

			if( $id !== null ) {
				$stmt->bind( $idx++, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			} else {
				$stmt->bind( $idx++, $date ); //ctime
			}

			$stmt->execute()->finish();

			if( $id === null )
			{
				/** mshop/price/manager/newid/mysql
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * @see mshop/price/manager/newid/ansi
				 */

				/** mshop/price/manager/newid/ansi
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
				 * @see mshop/price/manager/insert/ansi
				 * @see mshop/price/manager/update/ansi
				 * @see mshop/price/manager/delete/ansi
				 * @see mshop/price/manager/search/ansi
				 * @see mshop/price/manager/count/ansi
				 */
				$path = 'mshop/price/manager/newid';
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

		$item = $this->savePropertyItems( $item, 'price', $fetch );
		return $this->saveListItems( $item, 'price', $fetch );
	}


	/**
	 * Returns the item objects matched by the given search criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Price\Item\Iface with ids as keys
	 */
	public function search( \Aimeos\MW\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$map = [];
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$required = array( 'price' );

			/** mshop/price/manager/sitemode
			 * Mode how items from levels below or above in the site tree are handled
			 *
			 * By default, only items from the current site are fetched from the
			 * storage. If the ai-sites extension is installed, you can create a
			 * tree of sites. Then, this setting allows you to define for the
			 * whole price domain if items from parent sites are inherited,
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
			$level = $context->getConfig()->get( 'mshop/price/manager/sitemode', $level );

			/** mshop/price/manager/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see mshop/price/manager/search/ansi
			 */

			/** mshop/price/manager/search/ansi
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
			 * @see mshop/price/manager/insert/ansi
			 * @see mshop/price/manager/update/ansi
			 * @see mshop/price/manager/newid/ansi
			 * @see mshop/price/manager/delete/ansi
			 * @see mshop/price/manager/count/ansi
			 */
			$cfgPathSearch = 'mshop/price/manager/search';

			/** mshop/price/manager/count/mysql
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * @see mshop/price/manager/count/ansi
			 */

			/** mshop/price/manager/count/ansi
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
			 * @see mshop/price/manager/insert/ansi
			 * @see mshop/price/manager/update/ansi
			 * @see mshop/price/manager/newid/ansi
			 * @see mshop/price/manager/delete/ansi
			 * @see mshop/price/manager/search/ansi
			 */
			$cfgPathCount = 'mshop/price/manager/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== null )
			{
				if( ( $row['price.taxrates'] = json_decode( $config = $row['price.taxrates'], true ) ) === null )
				{
					$msg = sprintf( 'Invalid JSON as result of search for ID "%2$s" in "%1$s": %3$s', 'mshop_price.taxrates', $row['price.id'], $config );
					$this->getContext()->getLogger()->log( $msg, \Aimeos\MW\Logger\Base::WARN, 'core/price' );
				}
				$map[$row['price.id']] = $row;
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		$propItems = []; $name = 'price/property';
		if( isset( $ref[$name] ) || in_array( $name, $ref, true ) )
		{
			$propTypes = isset( $ref[$name] ) && is_array( $ref[$name] ) ? $ref[$name] : null;
			$propItems = $this->getPropertyItems( array_keys( $map ), 'price', $propTypes );
		}

		return $this->buildItems( $map, $ref, 'price', $propItems );
	}


	/**
	 * Creates a filter object.
	 *
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @param bool $site TRUE for adding site criteria to limit items by the site of related items
	 * @return \Aimeos\MW\Criteria\Iface Returns the filter object
	 */
	public function filter( ?bool $default = false, bool $site = false ) : \Aimeos\MW\Criteria\Iface
	{
		if( $default !== false )
		{
			$object = $this->filterBase( 'price', $default );

			if( $currencyid = $this->getContext()->getLocale()->getCurrencyId() ) {
				$object->add( ['price.currencyid' => $currencyid] );
			}

			return $object;
		}

		return parent::filter();
	}


	/**
	 * Returns a new manager for price extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g type, etc.
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->getSubManagerBase( 'price', $manager, $name );
	}


	/**
	 * Creates a new price item
	 *
	 * @param array $values List of attributes for price item
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 * @param \Aimeos\MShop\Common\Item\Property\Iface[] $propItems List of property items
	 * @return \Aimeos\MShop\Price\Item\Iface New price item
	 */
	protected function createItemBase( array $values = [], array $listItems = [], array $refItems = [],
		array $propItems = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values['.currencyid'] = $this->currencyId;
		$values['.precision'] = $this->precision;

		if( !isset( $values['price.taxflag'] ) ) {
			$values['price.taxflag'] = $this->taxflag;
		}

		return new \Aimeos\MShop\Price\Item\Standard( $values, $listItems, $refItems, $propItems );
	}
}
