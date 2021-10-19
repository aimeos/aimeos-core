<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Locale
 */


namespace Aimeos\MShop\Locale\Manager;


/**
 * Default locale manager implementation.
 *
 * @package MShop
 * @subpackage Locale
 */
class Standard
	extends \Aimeos\MShop\Locale\Manager\Base
	implements \Aimeos\MShop\Locale\Manager\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private $searchConfig = array(
		'locale.id' => array(
			'code' => 'locale.id',
			'internalcode' => 'mloc."id"',
			'label' => 'ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'locale.siteid' => array(
			'code' => 'locale.siteid',
			'internalcode' => 'mloc."siteid"',
			'label' => 'Site ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'locale.languageid' => array(
			'code' => 'locale.languageid',
			'internalcode' => 'mloc."langid"',
			'label' => 'Language ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'locale.currencyid' => array(
			'code' => 'locale.currencyid',
			'internalcode' => 'mloc."currencyid"',
			'label' => 'Currency ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'locale.status' => array(
			'code' => 'locale.status',
			'internalcode' => 'mloc."status"',
			'label' => 'Status',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'locale.position' => array(
			'code' => 'locale.position',
			'internalcode' => 'mloc."pos"',
			'label' => 'Position',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'locale.ctime' => array(
			'code' => 'locale.ctime',
			'internalcode' => 'mloc."ctime"',
			'label' => 'Create date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'locale.mtime' => array(
			'code' => 'locale.mtime',
			'internalcode' => 'mloc."mtime"',
			'label' => 'Modify date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'locale.editor' => array(
			'code' => 'locale.editor',
			'internalcode' => 'mloc."editor"',
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
		$this->setResourceName( 'db-locale' );
	}


	/**
	 * Returns the locale item for the given site code, language code and currency code.
	 *
	 * @param string $site Site code
	 * @param string $lang Language code (optional)
	 * @param string $currency Currency code (optional)
	 * @param bool $active Flag to get only active items (optional)
	 * @param int|null $level Constant from abstract class which site ID levels should be available (optional),
	 * 	based on config or value for SITE_PATH if null
	 * @param bool $bare Allow locale items with sites only
	 * @return \Aimeos\MShop\Locale\Item\Iface Locale item for the given parameters
	 * @throws \Aimeos\MShop\Locale\Exception If no locale item is found
	 */
	public function bootstrap( string $site, string $lang = '', string $currency = '', bool $active = true, int $level = null,
		bool $bare = false ) : \Aimeos\MShop\Locale\Item\Iface
	{
		$siteItem = $this->getObject()->getSubManager( 'site' )->find( $site );

		if( $active && $siteItem->getStatus() < 1 ) {
			throw new \Aimeos\MShop\Locale\Exception( 'Site not found' );
		}

		$siteId = $siteItem->getSiteId();
		$sites = [Base::SITE_ONE => $siteId];

		return $this->bootstrapBase( $site, $lang, $currency, $active, $siteItem, $siteId, $sites, $bare );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Locale\Manager\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->clearBase( $siteids, 'mshop/locale/manager/delete' );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Locale\Item\Iface New locale item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		try
		{
			$values['locale.siteid'] = $this->getContext()->getLocale()->getSiteId();
			return $this->createItemBase( $values );
		}
		catch( \Exception $e )
		{
			return $this->createItemBase( $values );
		}
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
		return $this->filterBase( 'locale', $default );
	}


	/**
	 * Returns the item specified by its ID.
	 *
	 * @param string $id Unique ID of the locale item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Locale\Item\Iface Returns the locale item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function get( string $id, array $ref = [], ?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->getItemBase( 'locale.id', $id, $ref, $default );
	}


	/**
	 * Searches for all items matching the given critera.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Criteria object with conditions, sortations, etc.
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Locale\Item\Iface with ids as keys
	 */
	public function search( \Aimeos\MW\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$items = [];
		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_PATH;

		$search = clone $search;
		$expr = array(
			$this->getSiteCondition( $search, 'locale.siteid', $level ),
			$search->getConditions(),
		);
		$search->setConditions( $search->and( $expr ) );

		foreach( $this->searchEntries( $search, $ref, $total ) as $row )
		{
			if( $item = $this->applyFilter( $this->createItemBase( $row ) ) ) {
				$items[$row['locale.id']] = $item;
			}
		}

		return map( $items );
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Locale\Manager\Iface Manager object for chaining method calls
	 */
	public function delete( $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/locale/manager/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/locale/manager/delete/ansi
		 */

		/** mshop/locale/manager/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the locale database.
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
		 * @see mshop/locale/manager/insert/ansi
		 * @see mshop/locale/manager/update/ansi
		 * @see mshop/locale/manager/newid/ansi
		 * @see mshop/locale/manager/search/ansi
		 * @see mshop/locale/manager/count/ansi
		 */
		$path = 'mshop/locale/manager/delete';

		return $this->deleteItemsBase( $itemIds, $path );
	}


	/**
	 * Adds or updates an item object.
	 *
	 * @param \Aimeos\MShop\Locale\Item\Iface $item Item object whose data should be saved
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Locale\Item\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Locale\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Locale\Item\Iface
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
				/** mshop/locale/manager/insert/mysql
				 * Inserts a new locale record into the database table
				 *
				 * @see mshop/locale/manager/insert/ansi
				 */

				/** mshop/locale/manager/insert/ansi
				 * Inserts a new locale record into the database table
				 *
				 * Items with no ID yet (i.e. the ID is NULL) will be created in
				 * the database and the newly created ID retrieved afterwards
				 * using the "newid" SQL statement.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the locale item to the statement before they are
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
				 * @see mshop/locale/manager/update/ansi
				 * @see mshop/locale/manager/newid/ansi
				 * @see mshop/locale/manager/delete/ansi
				 * @see mshop/locale/manager/search/ansi
				 * @see mshop/locale/manager/count/ansi
				 */
				$path = 'mshop/locale/manager/insert';
				$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ) );
			}
			else
			{
				/** mshop/locale/manager/update/mysql
				 * Updates an existing locale record in the database
				 *
				 * @see mshop/locale/manager/update/ansi
				 */

				/** mshop/locale/manager/update/ansi
				 * Updates an existing locale record in the database
				 *
				 * Items which already have an ID (i.e. the ID is not NULL) will
				 * be updated in the database.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the locale item to the statement before they are
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
				 * @see mshop/locale/manager/insert/ansi
				 * @see mshop/locale/manager/newid/ansi
				 * @see mshop/locale/manager/delete/ansi
				 * @see mshop/locale/manager/search/ansi
				 * @see mshop/locale/manager/count/ansi
				 */
				$path = 'mshop/locale/manager/update';
				$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ), false );
			}

			$idx = 1;
			$stmt = $this->getCachedStatement( $conn, $path, $sql );

			foreach( $columns as $name => $entry ) {
				$stmt->bind( $idx++, $item->get( $name ), $entry->getInternalType() );
			}

			$stmt->bind( $idx++, $item->getLanguageId() );
			$stmt->bind( $idx++, $item->getCurrencyId() );
			$stmt->bind( $idx++, $item->getPosition(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $item->getStatus(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $date ); // mtime
			$stmt->bind( $idx++, $context->getEditor() );
			$stmt->bind( $idx++, $item->getSiteId() );

			if( $id !== null ) {
				$stmt->bind( $idx++, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			} else {
				$stmt->bind( $idx++, $date ); // ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** mshop/locale/manager/newid/mysql
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * @see mshop/locale/manager/newid/ansi
				 */

				/** mshop/locale/manager/newid/ansi
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
				 *  SELECT currval('seq_mloc_id')
				 * For SQL Server:
				 *  SELECT SCOPE_IDENTITY()
				 * For Oracle:
				 *  SELECT "seq_mloc_id".CURRVAL FROM DUAL
				 *
				 * There's no way to retrive the new ID by a SQL statements that
				 * fits for most database servers as they implement their own
				 * specific way.
				 *
				 * @param string SQL statement for retrieving the last inserted record ID
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/locale/manager/insert/ansi
				 * @see mshop/locale/manager/update/ansi
				 * @see mshop/locale/manager/delete/ansi
				 * @see mshop/locale/manager/search/ansi
				 * @see mshop/locale/manager/count/ansi
				 */
				$path = 'mshop/locale/manager/newid';
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
	 * Returns a new manager for locale extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g site, language, currency.
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->getSubManagerBase( 'locale', $manager, $name );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		$path = 'mshop/locale/manager/submanagers';
		return $this->getResourceTypeBase( 'locale', $path, array( 'currency', 'language', 'site' ), $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		/** mshop/locale/manager/submanagers
		 * List of manager names that can be instantiated by the locale manager
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
		$path = 'mshop/locale/manager/submanagers';
		$default = array( 'language', 'currency', 'site' );

		return $this->getSearchAttributesBase( $this->searchConfig, $path, $default, $withsub );
	}


	/**
	 * Returns the locale item for the given site code, language code and currency code.
	 *
	 * If the locale item is inherited from a parent site, the site ID of this locale item
	 * is changed to the site ID of the actual site. This ensures that items assigned to
	 * the same site as the site item are still used.
	 *
	 * @param string $site Site code
	 * @param string $lang Language code
	 * @param string $currency Currency code
	 * @param bool $active Flag to get only active items
	 * @param \Aimeos\MShop\Locale\Item\Site\Iface $siteItem Site item
	 * @param string $siteId Site ID
	 * @param array $sites Associative list of site constant as key and sites as values
	 * @param bool $bare Allow locale items with sites only
	 * @return \Aimeos\MShop\Locale\Item\Iface Locale item for the given parameters
	 * @throws \Aimeos\MShop\Locale\Exception If no locale item is found
	 */
	protected function bootstrapBase( string $site, string $lang, string $currency, bool $active,
		\Aimeos\MShop\Locale\Item\Site\Iface $siteItem, string $siteId, array $sites, bool $bare ) : \Aimeos\MShop\Locale\Item\Iface
	{
		if( $result = $this->bootstrapMatch( $siteId, $lang, $currency, $active, $siteItem, $sites ) ) {
			return $result;
		}

		if( $result = $this->bootstrapClosest( $siteId, $lang, $active, $siteItem, $sites ) ) {
			return $result;
		}

		if( $bare === true ) {
			return $this->createItemBase( ['locale.siteid' => $siteId], $siteItem, $sites );
		}

		$msg = $this->getContext()->translate( 'mshop', 'Locale item for site "%1$s" not found' );
		throw new \Aimeos\MShop\Locale\Exception( sprintf( $msg, $site ) );
	}


	/**
	 * Returns the matching locale item for the given site code, language code and currency code.
	 *
	 * If the locale item is inherited from a parent site, the site ID of this locale item
	 * is changed to the site ID of the actual site. This ensures that items assigned to
	 * the same site as the site item are still used.
	 *
	 * @param string $siteId Site ID
	 * @param string $lang Language code
	 * @param string $currency Currency code
	 * @param bool $active Flag to get only active items
	 * @param \Aimeos\MShop\Locale\Item\Site\Iface $siteItem Site item
	 * @param array $sites Associative list of site constant as key and sites as values
	 * @return \Aimeos\MShop\Locale\Item\Iface|null Locale item for the given parameters or null if no item was found
	 */
	private function bootstrapMatch( string $siteId, string $lang, string $currency, bool $active,
		\Aimeos\MShop\Locale\Item\Site\Iface $siteItem, array $sites ) : ?\Aimeos\MShop\Locale\Item\Iface
	{
		// Try to find exact match
		$search = $this->getObject()->filter( $active );

		$expr = array( $search->compare( '==', 'locale.siteid', $sites[Base::SITE_PATH] ?? $sites[Base::SITE_ONE] ) );

		if( !empty( $lang ) )
		{
			$langIds = strlen( $lang ) > 2 ? [$lang, substr( $lang, 0, 2 )] : [$lang];
			$expr[] = $search->compare( '==', 'locale.languageid', $langIds );
		}

		if( !empty( $currency ) ) {
			$expr[] = $search->compare( '==', 'locale.currencyid', $currency );
		}

		$expr[] = $search->getConditions();


		if( $active === true )
		{
			$expr[] = $search->compare( '>', 'locale.currency.status', 0 );
			$expr[] = $search->compare( '>', 'locale.language.status', 0 );
			$expr[] = $search->compare( '>', 'locale.site.status', 0 );
		}

		$search->setConditions( $search->and( $expr ) );
		$search->setSortations( array( $search->sort( '+', 'locale.position' ) ) );
		$result = $this->searchEntries( $search );

		// Try to find first item where site matches
		foreach( $result as $row )
		{
			if( $row['locale.siteid'] === $siteId ) {
				return $this->createItemBase( $row, $siteItem, $sites );
			}
		}

		if( ( $row = reset( $result ) ) !== false )
		{
			$row['locale.siteid'] = $siteId;
			return $this->createItemBase( $row, $siteItem, $sites );
		}

		return null;
	}


	/**
	 * Returns the locale item for the given site code, language code and currency code.
	 *
	 * If the locale item is inherited from a parent site, the site ID of this locale item
	 * is changed to the site ID of the actual site. This ensures that items assigned to
	 * the same site as the site item are still used.
	 *
	 * @param string $siteId Site ID
	 * @param string $lang Language code
	 * @param bool $active Flag to get only active items
	 * @param \Aimeos\MShop\Locale\Item\Site\Iface $siteItem Site item
	 * @param array $sites Associative list of site constant as key and sites as values
	 * @return \Aimeos\MShop\Locale\Item\Iface|null Locale item for the given parameters or null if no item was found
	 */
	private function bootstrapClosest( string $siteId, string $lang, bool $active,
		\Aimeos\MShop\Locale\Item\Site\Iface $siteItem, array $sites ) : ?\Aimeos\MShop\Locale\Item\Iface
	{
		// Try to find the best matching locale
		$search = $this->getObject()->filter( $active );

		$expr = array(
			$search->compare( '==', 'locale.siteid', $sites[Base::SITE_PATH] ?? $sites[Base::SITE_ONE] ),
			$search->getConditions()
		);

		if( $active === true )
		{
			$expr[] = $search->compare( '>', 'locale.currency.status', 0 );
			$expr[] = $search->compare( '>', 'locale.language.status', 0 );
			$expr[] = $search->compare( '>', 'locale.site.status', 0 );
		}

		$search->setConditions( $search->and( $expr ) );
		$search->setSortations( array( $search->sort( '+', 'locale.position' ) ) );
		$result = $this->searchEntries( $search );

		$langIds = strlen( $lang ) > 2 ? [$lang, substr( $lang, 0, 2 )] : [$lang];

		// Try to find first item where site and language matches
		foreach( $result as $row )
		{
			if( $row['locale.siteid'] === $siteId && in_array( $row['locale.languageid'], $langIds, true ) ) {
				return $this->createItemBase( $row, $siteItem, $sites );
			}
		}

		// Try to find first item where language matches
		foreach( $result as $row )
		{
			if( in_array( $row['locale.languageid'], $langIds, true ) )
			{
				$row['locale.siteid'] = $siteId;
				return $this->createItemBase( $row, $siteItem, $sites );
			}
		}

		// Try to find first item where site matches
		foreach( $result as $row )
		{
			if( $row['locale.siteid'] === $siteId ) {
				return $this->createItemBase( $row, $siteItem, $sites );
			}
		}

		// Return first item (no other match found)
		if( ( $row = reset( $result ) ) !== false )
		{
			$row['locale.siteid'] = $siteId;
			return $this->createItemBase( $row, $siteItem, $sites );
		}

		return null;
	}


	/**
	 * Instances a new locale item object.
	 *
	 * @param array $values Parameter to initialise the item
	 * @param \Aimeos\MShop\Locale\Item\Site\Iface|null $site Site item
	 * @param array $sites Associative list of site constant as key and sites as values
	 * @return \Aimeos\MShop\Locale\Item\Iface Locale item
	 */
	protected function createItemBase( array $values = [], \Aimeos\MShop\Locale\Item\Site\Iface $site = null,
		array $sites = [] ) : \Aimeos\MShop\Locale\Item\Iface
	{
		return new \Aimeos\MShop\Locale\Item\Standard( $values, $site, $sites );
	}


	/**
	 * Returns the search results for the given SQL statement.
	 *
	 * @param \Aimeos\MW\DB\Connection\Iface $conn Database connection
	 * @param string $sql SQL statement
	 * @return \Aimeos\MW\DB\Result\Iface Search result object
	 */
	protected function getSearchResults( \Aimeos\MW\DB\Connection\Iface $conn, string $sql ) : \Aimeos\MW\DB\Result\Iface
	{
		$time = microtime( true );

		$stmt = $conn->create( $sql );
		$result = $stmt->execute();

		$msg = 'Time: ' . ( microtime( true ) - $time ) * 1000 . "ms\n"
			. 'Class: ' . get_class( $this ) . "\n"
			. str_replace( ["\t", "\n\n"], ['', "\n"], trim( (string) $stmt ) );

		$this->getContext()->getLogger()->log( $msg, \Aimeos\MW\Logger\Base::DEBUG, 'core/sql' );

		return $result;
	}


	/**
	 * Searches for all items matching the given critera.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Criteria object with conditions, sortations, etc.
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int &$total Number of items that are available in total
	 * @return array Associative list of key/value pairs
	 */
	protected function searchEntries( \Aimeos\MW\Criteria\Iface $search, array $ref = [], int &$total = null ) : array
	{
		$map = [];
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$attributes = $this->getObject()->getSearchAttributes();
			$translations = $this->getSearchTranslations( $attributes );
			$types = $this->getSearchTypes( $attributes );
			$columns = $this->getObject()->getSaveAttributes();
			$sortcols = $search->translate( $search->getSortations(), $translations );

			if( empty( $search->getSortations() ) && ( $attribute = reset( $attributes ) ) !== false ) {
				$search = ( clone $search )->setSortations( [$search->sort( '+', $attribute->getCode() )] );
			}

			$colstring = '';
			foreach( $columns as $name => $entry ) {
				$colstring .= $entry->getInternalCode() . ', ';
			}

			$find = array( ':columns', ':cond', ':order', ':group', ':start', ':size' );
			$replace = array(
				$colstring . ( $sortcols ? join( ', ', $sortcols ) . ', ' : '' ),
				$search->getConditionSource( $types, $translations ),
				$search->getSortationSource( $types, $translations ),
				implode( ', ', $search->translate( $search->getSortations(), $translations ) ) . ', ',
				$search->getOffset(),
				$search->getLimit(),
			);

			/** mshop/locale/manager/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see mshop/locale/manager/search/ansi
			 */

			/** mshop/locale/manager/search/ansi
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * Fetches the records matched by the given criteria from the locale
			 * database. The records must be from one of the sites that are
			 * configured via the context item. If the current site is part of
			 * a tree of sites, the SELECT statement can retrieve all records
			 * from the current site and the complete sub-tree of sites.
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
			 * @see mshop/locale/manager/insert/ansi
			 * @see mshop/locale/manager/update/ansi
			 * @see mshop/locale/manager/newid/ansi
			 * @see mshop/locale/manager/delete/ansi
			 * @see mshop/locale/manager/count/ansi
			 */
			$path = 'mshop/locale/manager/search';

			$sql = $this->getSqlConfig( $path );
			$results = $this->getSearchResults( $conn, str_replace( $find, $replace, $sql ) );

			try
			{
				while( ( $row = $results->fetch() ) !== null ) {
					$map[$row['locale.id']] = $row;
				}
			}
			catch( \Exception $e )
			{
				$results->finish();
				throw $e;
			}

			if( $total !== null )
			{
				/** mshop/locale/manager/count/mysql
				 * Counts the number of records matched by the given criteria in the database
				 *
				 * @see mshop/locale/manager/count/ansi
				 */

				/** mshop/locale/manager/count/ansi
				 * Counts the number of records matched by the given criteria in the database
				 *
				 * Counts all records matched by the given criteria from the locale
				 * database. The records must be from one of the sites that are
				 * configured via the context item. If the current site is part of
				 * a tree of sites, the statement can count all records from the
				 * current site and the complete sub-tree of sites.
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
				 * @see mshop/locale/manager/insert/ansi
				 * @see mshop/locale/manager/update/ansi
				 * @see mshop/locale/manager/newid/ansi
				 * @see mshop/locale/manager/delete/ansi
				 * @see mshop/locale/manager/search/ansi
				 */
				$path = 'mshop/locale/manager/count';

				$sql = $this->getSqlConfig( $path );
				$results = $this->getSearchResults( $conn, str_replace( $find, $replace, $sql ) );

				$row = $results->fetch();
				$results->finish();

				if( $row === null )
				{
					$msg = $this->getContext()->translate( 'mshop', 'Total results value not found' );
					throw new \Aimeos\MShop\Locale\Exception( $msg );
				}

				$total = $row['count'];
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $map;
	}
}
