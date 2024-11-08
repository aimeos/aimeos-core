<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2024
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
	/** mshop/locale/manager/name
	 * Class name of the used locale manager implementation
	 *
	 * Each default manager can be replace by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the manager factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\MShop\Locale\Manager\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\MShop\Locale\Manager\Mymanager
	 *
	 * then you have to set the this configuration option:
	 *
	 *  mshop/locale/manager/name = Mymanager
	 *
	 * The value is the last part of your own class name and it's case sensitive,
	 * so take care that the configuration value is exactly named like the last
	 * part of the class name.
	 *
	 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
	 * characters are possible! You should always start the last part of the class
	 * name with an upper case character and continue only with lower case characters
	 * or numbers. Avoid chamel case names like "MyManager"!
	 *
	 * @param string Last part of the class name
	 * @since 2014.03
	 */

	/** mshop/locale/manager/decorators/excludes
	 * Excludes decorators added by the "common" option from the locale manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to remove a decorator added via
	 * "mshop/common/manager/decorators/default" before they are wrapped
	 * around the locale manager.
	 *
	 *  mshop/locale/manager/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
	 * "mshop/common/manager/decorators/default" for the locale manager.
	 *
	 * @param array List of decorator names
	 * @since 2014.03
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/locale/manager/decorators/global
	 * @see mshop/locale/manager/decorators/local
	 */

	/** mshop/locale/manager/decorators/global
	 * Adds a list of globally available decorators only to the locale manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap global decorators
	 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the locale manager.
	 *
	 *  mshop/locale/manager/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the locale
	 * manager.
	 *
	 * @param array List of decorator names
	 * @since 2014.03
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/locale/manager/decorators/excludes
	 * @see mshop/locale/manager/decorators/local
	 */

	/** mshop/locale/manager/decorators/local
	 * Adds a list of local decorators only to the locale manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\MShop\Locale\Manager\Decorator\*") around the locale manager.
	 *
	 *  mshop/locale/manager/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\MShop\Locale\Manager\Decorator\Decorator2" only to the locale
	 * manager.
	 *
	 * @param array List of decorator names
	 * @since 2014.03
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/locale/manager/decorators/excludes
	 * @see mshop/locale/manager/decorators/global
	 */

	/** mshop/locale/manager/resource
	 * Name of the database connection resource to use
	 *
	 * You can configure a different database connection for each data domain
	 * and if no such connection name exists, the "db" connection will be used.
	 * It's also possible to use the same database connection for different
	 * data domains by configuring the same connection name using this setting.
	 *
	 * @param string Database connection name
	 * @since 2023.04
	 */

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
	 * @see mshop/locale/manager/insert/ansi
	 * @see mshop/locale/manager/update/ansi
	 * @see mshop/locale/manager/newid/ansi
	 * @see mshop/locale/manager/search/ansi
	 * @see mshop/locale/manager/count/ansi
	 */


	private array $searchConfig = [
		'locale.id' => [
			'label' => 'ID',
			'internalcode' => 'mloc."id"',
			'type' => 'int',
			'public' => false,
		],
		'locale.siteid' => [
			'label' => 'Site ID',
			'internalcode' => 'mloc."siteid"',
			'public' => false,
		],
		'locale.languageid' => [
			'label' => 'Language ID',
			'internalcode' => 'mloc."langid"',
		],
		'locale.currencyid' => [
			'label' => 'Currency ID',
			'internalcode' => 'mloc."currencyid"',
		],
		'locale.status' => [
			'label' => 'Status',
			'internalcode' => 'mloc."status"',
			'type' => 'int',
		],
		'locale.position' => [
			'label' => 'Position',
			'internalcode' => 'mloc."pos"',
			'type' => 'int',
		],
		'locale.ctime' => [
			'label' => 'Create date/time',
			'internalcode' => 'mloc."ctime"',
			'type' => 'datetime',
			'public' => false,
		],
		'locale.mtime' => [
			'label' => 'Modify date/time',
			'internalcode' => 'mloc."mtime"',
			'type' => 'datetime',
			'public' => false,
		],
		'locale.editor' => [
			'label' => 'Editor',
			'internalcode' => 'mloc."editor"',
			'public' => false,
		],
	];


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
	public function bootstrap( string $site, string $lang = '', string $currency = '', bool $active = true, ?int $level = null,
		bool $bare = false ) : \Aimeos\MShop\Locale\Item\Iface
	{
		$siteItem = $this->object()->getSubManager( 'site' )->find( $site );

		// allow enabled sites and sites under review
		if( $active && $siteItem->getStatus() < 1 && $siteItem->getStatus() !== -1 ) {
			throw new \Aimeos\MShop\Locale\Exception( 'Site not found' );
		}

		$siteId = $siteItem->getSiteId();
		$sites = [Base::SITE_ONE => $siteId];

		return $this->bootstrapBase( $site, $lang, $currency, $active, $siteItem, $siteId, $sites, $bare );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Locale\Item\Iface New locale item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		try {
			$values['locale.siteid'] = $values['locale.siteid'] ?? $this->context()->locale()->getSiteId();
		} catch( \Exception $e ) {} // if no locale item is available

		return $this->createItemBase( $values );
	}


	/**
	 * Creates a filter object.
	 *
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @param bool $site TRUE for adding site criteria to limit items by the site of related items
	 * @return \Aimeos\Base\Criteria\Iface Returns the filter object
	 */
	public function filter( ?bool $default = false, bool $site = false ) : \Aimeos\Base\Criteria\Iface
	{
		return $this->filterBase( 'locale', $default );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of search attribute items
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
		 */
		$path = 'mshop/locale/manager/submanagers';
		$default = ['language', 'currency', 'site'];

		return $this->getSearchAttributesBase( $this->searchConfig, $path, $default, $withsub );
	}


	/**
	 * Searches for all items matching the given critera.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $search Criteria object with conditions, sortations, etc.
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Locale\Item\Iface with ids as keys
	 */
	public function search( \Aimeos\Base\Criteria\Iface $search, array $ref = [], ?int &$total = null ) : \Aimeos\Map
	{
		$items = [];
		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_PATH;
		$search = (clone $search)->add( $this->siteCondition( 'locale.siteid', $level ) );

		foreach( $this->searchEntries( $search, $ref, $total ) as $row )
		{
			if( $item = $this->applyFilter( $this->createItemBase( $row ) ) ) {
				$items[$row['locale.id']] = $item;
			}
		}

		return map( $items );
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

		$msg = $this->context()->translate( 'mshop', 'Locale item for site "%1$s" not found' );
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
		$search = $this->object()->filter( $active );

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
		$search = $this->object()->filter( $active );

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

		// Try to find first item where site and language matches
		foreach( $result as $row )
		{
			if( $row['locale.siteid'] === $siteId && $row['locale.languageid'] === $lang ) {
				return $this->createItemBase( $row, $siteItem, $sites );
			}
		}

		$short = strlen( $lang ) > 2 ? substr( $lang, 0, 2 ) : null;

		// Try to find first item where site and language without country matches
		if( $short )
		{
			foreach( $result as $row )
			{
				if( $row['locale.siteid'] === $siteId && $row['locale.languageid'] === $short ) {
					return $this->createItemBase( $row, $siteItem, $sites );
				}
			}
		}

		// Try to find first item where language matches
		foreach( $result as $row )
		{
			if( $row['locale.languageid'] === $lang )
			{
				$row['locale.siteid'] = $siteId;
				return $this->createItemBase( $row, $siteItem, $sites );
			}
		}

		// Try to find first item where language without country matches
		if( $short )
		{
			foreach( $result as $row )
			{
				if( $row['locale.siteid'] === $siteId && $row['locale.languageid'] === $short ) {
					return $this->createItemBase( $row, $siteItem, $sites );
				}
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
	protected function createItemBase( array $values = [], ?\Aimeos\MShop\Locale\Item\Site\Iface $site = null,
		array $sites = [] ) : \Aimeos\MShop\Locale\Item\Iface
	{
		return new \Aimeos\MShop\Locale\Item\Standard( $values, $site, $sites );
	}


	/**
	 * Returns the site coditions for the search request
	 *
	 * @param string[] $keys Sorted list of criteria keys
	 * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes Associative list of search keys and criteria attribute items as values
	 * @param int $sitelevel Site level constant from \Aimeos\MShop\Locale\Manager\Base
	 * @return \Aimeos\Base\Criteria\Expression\Iface[] List of search conditions
	 */
	protected function getSiteConditions( array $keys, array $attributes, int $sitelevel ) : array
	{
		return [];
	}


	/**
	 * Returns the prefix for the item properties and search keys.
	 *
	 * @return string Prefix for the item properties and search keys
	 */
	protected function prefix() : string
	{
		return 'locale.';
	}


	/**
	 * Adds or updates an item object.
	 *
	 * @param \Aimeos\MShop\Locale\Item\Iface $item Item object whose data should be saved
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Locale\Item\Iface $item Updated item including the generated ID
	 */
	protected function saveBase( \Aimeos\MShop\Common\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Common\Item\Iface
	{
		if( !$item->isModified() ) {
			return $item;
		}

		$context = $this->context();
		$conn = $context->db( $this->getResourceName() );

		$id = $item->getId();
		$columns = $this->object()->getSaveAttributes();

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
		$siteIds = explode( '.', trim( $item->getSiteId(), '.' ) );

		foreach( $columns as $name => $entry ) {
			$stmt->bind( $idx++, $item->get( $name ), \Aimeos\Base\Criteria\SQL::type( $entry->getType() ) );
		}

		$stmt->bind( $idx++, $item->getLanguageId() );
		$stmt->bind( $idx++, $item->getCurrencyId() );
		$stmt->bind( $idx++, $item->getPosition(), \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		$stmt->bind( $idx++, $item->getStatus(), \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		$stmt->bind( $idx++, $context->datetime() ); // mtime
		$stmt->bind( $idx++, $context->editor() );
		$stmt->bind( $idx++, end( $siteIds ), \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		$stmt->bind( $idx++, $context->locale()->getSiteId() );

		if( $id !== null ) {
			$stmt->bind( $idx++, $id, \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		} else {
			$stmt->bind( $idx++, $context->datetime() ); // ctime
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

		return $item;
	}


	/**
	 * Searches for all items matching the given critera.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $search Criteria object with conditions, sortations, etc.
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int &$total Number of items that are available in total
	 * @return array Associative list of key/value pairs
	 */
	protected function searchEntries( \Aimeos\Base\Criteria\Iface $search, array $ref = [], ?int &$total = null ) : array
	{
		$map = [];
		$context = $this->context();
		$conn = $context->db( $this->getResourceName() );

		$required = ['locale'];

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
		 * replaces the ":order" placeholder. Columns of
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
		 * @see mshop/locale/manager/insert/ansi
		 * @see mshop/locale/manager/update/ansi
		 * @see mshop/locale/manager/newid/ansi
		 * @see mshop/locale/manager/delete/ansi
		 * @see mshop/locale/manager/count/ansi
		 */
		$cfgPathSearch = 'mshop/locale/manager/search';

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
		 * @see mshop/locale/manager/insert/ansi
		 * @see mshop/locale/manager/update/ansi
		 * @see mshop/locale/manager/newid/ansi
		 * @see mshop/locale/manager/delete/ansi
		 * @see mshop/locale/manager/search/ansi
		 */
		$cfgPathCount = 'mshop/locale/manager/count';

		$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total );

		while( $row = $results->fetch() ) {
			$map[$row['locale.id']] = $row;
		}

		return $map;
	}
}
