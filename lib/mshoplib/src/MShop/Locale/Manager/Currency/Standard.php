<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Locale
 */


namespace Aimeos\MShop\Locale\Manager\Currency;


/**
 * Default implementation for managing currencies.
 *
 * @package MShop
 * @subpackage Locale
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Locale\Manager\Currency\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private $searchConfig = array(
		'locale.currency.id' => array(
			'code' => 'locale.currency.id',
			'internalcode' => 'mloccu."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_locale_currency" AS mloccu ON (mloc."currencyid" = mloccu."id")' ),
			'label' => 'Currency ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'locale.currency.label' => array(
			'code' => 'locale.currency.label',
			'internalcode' => 'mloccu."label"',
			'label' => 'Currency label',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'locale.currency.code' => array(
			'code' => 'locale.currency.code',
			'internalcode' => 'mloccu."id"',
			'label' => 'Currency code',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'locale.currency.status' => array(
			'code' => 'locale.currency.status',
			'internalcode' => 'mloccu."status"',
			'label' => 'Currency status',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'locale.currency.ctime' => array(
			'code' => 'locale.currency.ctime',
			'internalcode' => 'mloccu."ctime"',
			'label' => 'Currency create date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'locale.currency.mtime' => array(
			'code' => 'locale.currency.mtime',
			'internalcode' => 'mloccu."mtime"',
			'label' => 'Currency modify date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'locale.currency.editor' => array(
			'code' => 'locale.currency.editor',
			'internalcode' => 'mloccu."editor"',
			'label' => 'Currency editor',
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
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Locale\Manager\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this;
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Locale\Item\Currency\Iface New locale currency item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		try {
			$values['locale.currency.siteid'] = $this->getContext()->getLocale()->getSiteId();
		} catch( \Exception $e ) {
			$values['locale.currency.siteid'] = null;
		}

		return $this->createItemBase( $values );
	}


	/**
	 * Saves a currency item to the storage.
	 *
	 * @param \Aimeos\MShop\Locale\Item\Currency\Iface $item Currency item to save in the storage
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Locale\Item\Currency\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Locale\Item\Currency\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Locale\Item\Currency\Iface
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
				/** mshop/locale/manager/currency/insert/mysql
				 * Inserts a new currency record into the database table
				 *
				 * @see mshop/locale/manager/currency/insert/ansi
				 */

				/** mshop/locale/manager/currency/insert/ansi
				 * Inserts a new currency record into the database table
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the currency item to the statement before they are
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
				 * @see mshop/locale/manager/currency/update/ansi
				 * @see mshop/locale/manager/currency/delete/ansi
				 * @see mshop/locale/manager/currency/search/ansi
				 * @see mshop/locale/manager/currency/count/ansi
				 */
				$path = 'mshop/locale/manager/currency/insert';
				$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ) );
			}
			else
			{
				/** mshop/locale/manager/currency/update/mysql
				 * Updates an existing currency record in the database
				 *
				 * @see mshop/locale/manager/currency/update/ansi
				 */

				/** mshop/locale/manager/currency/update/ansi
				 * Updates an existing currency record in the database
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the currency item to the statement before they are
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
				 * @see mshop/locale/manager/currency/insert/ansi
				 * @see mshop/locale/manager/currency/delete/ansi
				 * @see mshop/locale/manager/currency/search/ansi
				 * @see mshop/locale/manager/currency/count/ansi
				 */
				$path = 'mshop/locale/manager/currency/update';
				$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ), false );
			}

			$idx = 1;
			$stmt = $this->getCachedStatement( $conn, $path, $sql );

			foreach( $columns as $name => $entry ) {
				$stmt->bind( $idx++, $item->get( $name ), $entry->getInternalType() );
			}

			$stmt->bind( $idx++, $item->getLabel() );
			$stmt->bind( $idx++, $item->getStatus(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $date ); // mtime
			$stmt->bind( $idx++, $context->getEditor() );
			// bind ID but code and id are identical after saveing the stuff
			// id is the flag to detect updates or inserts!
			$stmt->bind( $idx++, $item->getCode() );

			if( $id === null ) {
				$stmt->bind( $idx++, $date ); // ctime
			}

			$stmt->execute()->finish();

			$item->setId( $item->getCode() ); // set modified flag to false

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
	 * @return \Aimeos\MShop\Locale\Manager\Currency\Iface Manager object for chaining method calls
	 */
	public function delete( $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/locale/manager/currency/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/locale/manager/currency/delete/ansi
		 */

		/** mshop/locale/manager/currency/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the language records specified by the given IDs from the
		 * locale database. The records must be from the site that is configured
		 * via the context item.
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
		 * @see mshop/locale/manager/currency/insert/ansi
		 * @see mshop/locale/manager/currency/update/ansi
		 * @see mshop/locale/manager/currency/search/ansi
		 * @see mshop/locale/manager/currency/count/ansi
		 */
		$path = 'mshop/locale/manager/currency/delete';

		return $this->deleteItemsBase( $itemIds, $path, false );
	}


	/**
	 * Returns the currency object with the given currency ID.
	 *
	 * @param string $id Currency ID indentifying the currency object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Locale\Item\Currency\Iface Returns the currency item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function get( string $id, array $ref = [], ?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->getItemBase( 'locale.currency.id', $id, $ref, $default );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		$path = 'mshop/locale/manager/currency/submanagers';
		return $this->getResourceTypeBase( 'locale/currency', $path, [], $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		/** mshop/locale/manager/currency/submanagers
		 * List of manager names that can be instantiated by the locale currency manager
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
		$path = 'mshop/locale/manager/currency/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, [], $withsub );
	}


	/**
	 * Search for currency items matching the given criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items imlementing \Aimeos\MShop\Locale\Item\Currency\Iface with ids as keys
	 */
	public function search( \Aimeos\MW\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		$items = [];

		try
		{
			$attributes = $this->getObject()->getSearchAttributes();
			$translations = $this->getSearchTranslations( $attributes );
			$types = $this->getSearchTypes( $attributes );
			$columns = $this->getObject()->getSaveAttributes();
			$sortcols = $search->translate( $search->getSortations(), $translations );

			$colstring = '';
			foreach( $columns as $name => $entry ) {
				$colstring .= $entry->getInternalCode() . ', ';
			}

			$find = array( ':columns', ':cond', ':order', ':start', ':size' );
			$replace = array(
				$colstring . ( $sortcols ? join( ', ', $sortcols ) . ', ' : '' ),
				$search->getConditionSource( $types, $translations ),
				$search->getSortationSource( $types, $translations ),
				$search->getOffset(),
				$search->getLimit(),
			);

			/** mshop/locale/manager/currency/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see mshop/locale/manager/currency/search/ansi
			 */

			/** mshop/locale/manager/currency/search/ansi
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * Fetches the records matched by the given criteria from the attribute
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
			 * @see mshop/locale/manager/currency/insert/ansi
			 * @see mshop/locale/manager/currency/update/ansi
			 * @see mshop/locale/manager/currency/delete/ansi
			 * @see mshop/locale/manager/currency/count/ansi
			 */
			$path = 'mshop/locale/manager/currency/search';

			$sql = $this->getSqlConfig( $path );
			$results = $this->getSearchResults( $conn, str_replace( $find, $replace, $sql ) );

			try
			{
				while( ( $row = $results->fetch() ) !== null )
				{
					if( $item = $this->applyFilter( $this->createItemBase( $row ) ) ) {
						$items[$row['locale.currency.id']] = $item;
					}
				}
			}
			catch( \Exception $e )
			{
				$results->finish();
				throw $e;
			}

			if( $total !== null ) {
				$total = $this->getTotal( $conn, $find, $replace );
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
	 * Returns a new sub manager of the given type and name.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface manager
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/locale/manager/currency/name
		 * Class name of the used locale currency manager implementation
		 *
		 * Each default locale currency manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\MShop\Locale\Manager\Currency\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\MShop\Locale\Manager\Currency\Mycurrency
		 *
		 * then you have to set the this configuration option:
		 *
		 *  mshop/locale/manager/currency/name = Mycurrency
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyCurrency"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */

		/** mshop/locale/manager/currency/decorators/excludes
		 * Excludes decorators added by the "common" option from the locale currency manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the locale currency manager.
		 *
		 *  mshop/locale/manager/currency/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
		 * "mshop/common/manager/decorators/default" for the locale currency manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/locale/manager/currency/decorators/global
		 * @see mshop/locale/manager/currency/decorators/local
		 */

		/** mshop/locale/manager/currency/decorators/global
		 * Adds a list of globally available decorators only to the locale currency manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the locale currency
		 * manager.
		 *
		 *  mshop/locale/manager/currency/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the locale
		 * currency manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/locale/manager/currency/decorators/excludes
		 * @see mshop/locale/manager/currency/decorators/local
		 */

		/** mshop/locale/manager/currency/decorators/local
		 * Adds a list of local decorators only to the locale currency manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\MShop\Locale\Manager\Currency\Decorator\*") around the locale
		 * currency manager.
		 *
		 *  mshop/locale/manager/currency/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\MShop\Locale\Manager\Currency\Decorator\Decorator2" only to the
		 * locale currency manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/locale/manager/currency/decorators/excludes
		 * @see mshop/locale/manager/currency/decorators/global
		 */

		return $this->getSubManagerBase( 'locale', 'currency/' . $manager, $name );
	}


	/**
	 * Returns the item specified by its code and domain/type if necessary
	 *
	 * @param string $code Code of the item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param string|null $domain Domain of the item if necessary to identify the item uniquely
	 * @param string|null $type Type code of the item if necessary to identify the item uniquely
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Common\Item\Iface Item object
	 */
	public function find( string $code, array $ref = [], string $domain = null, string $type = null,
		?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->findBase( array( 'locale.currency.id' => $code ), $ref, $default );
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
		return $this->filterBase( 'locale.currency', $default );
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
	 * Create new item object initialized with given parameters.
	 *
	 * @param array $data Associative list of item key/value pairs
	 * @return \Aimeos\MShop\Locale\Item\Currency\Iface Currency item object
	 */
	protected function createItemBase( array $data = [] ) : \Aimeos\MShop\Locale\Item\Currency\Iface
	{
		return new \Aimeos\MShop\Locale\Item\Currency\Standard( $data );
	}


	/**
	 * Returns the total number of items found for the conditions
	 *
	 * @param \Aimeos\MW\DB\Connection\Iface $conn Database connection
	 * @param string[] $find List of markers that should be replaced in the SQL statement
	 * @param string[] $replace List of replacements for the markers in the SQL statement
	 * @throws \Aimeos\MShop\Locale\Exception If no total value was found
	 * @return int Total number of found items
	 */
	protected function getTotal( \Aimeos\MW\DB\Connection\Iface $conn, array $find, array $replace ) : int
	{
		/** mshop/locale/manager/currency/count/mysql
		 * Counts the number of records matched by the given criteria in the database
		 *
		 * @see mshop/locale/manager/currency/count/ansi
		 */

		/** mshop/locale/manager/currency/count/ansi
		 * Counts the number of records matched by the given criteria in the database
		 *
		 * Counts all records matched by the given criteria from the attribute
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
		 * @see mshop/locale/manager/currency/insert/ansi
		 * @see mshop/locale/manager/currency/update/ansi
		 * @see mshop/locale/manager/currency/delete/ansi
		 * @see mshop/locale/manager/currency/search/ansi
		 */
		$path = 'mshop/locale/manager/currency/count';

		$sql = $this->getSqlConfig( $path );
		$results = $this->getSearchResults( $conn, str_replace( $find, $replace, $sql ) );

		$row = $results->fetch();
		$results->finish();

		if( $row === null ) {
			throw new \Aimeos\MShop\Locale\Exception( 'No total results value found' );
		}

		return $row['count'];
	}
}
