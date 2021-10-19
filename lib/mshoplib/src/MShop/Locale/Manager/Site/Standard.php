<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Locale
 */


namespace Aimeos\MShop\Locale\Manager\Site;


/**
 * Default implementation for managing sites.
 *
 * @package MShop
 * @subpackage Locale
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Locale\Manager\Site\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private $cache = [];

	private $searchConfig = array(
		'locale.site.id' => array(
			'code' => 'locale.site.id',
			'internalcode' => 'mlocsi."id"',
			'internaldeps' => ['LEFT JOIN "mshop_locale_site" AS mlocsi ON (mloc."siteid" = mlocsi."siteid")'],
			'label' => 'Site ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'locale.site.siteid' => array(
			'code' => 'locale.site.siteid',
			'internalcode' => 'mlocsi."siteid"',
			'label' => 'Unique site ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'locale.site.label' => array(
			'code' => 'locale.site.label',
			'internalcode' => 'mlocsi."label"',
			'label' => 'Site label',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'locale.site.code' => array(
			'code' => 'locale.site.code',
			'internalcode' => 'mlocsi."code"',
			'label' => 'Site code',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'locale.site.status' => array(
			'code' => 'locale.site.status',
			'internalcode' => 'mlocsi."status"',
			'label' => 'Site status',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'locale.site.level' => array(
			'code' => 'locale.site.level',
			'internalcode' => 'mlocsi."level"',
			'label' => 'Site tree level',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'locale.site.config' => array(
			'code' => 'locale.site.config',
			'internalcode' => 'mlocsi."config"',
			'label' => 'Site config',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'locale.site.icon' => array(
			'code' => 'locale.site.icon',
			'internalcode' => 'mlocsi."icon"',
			'label' => 'Site icon',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'locale.site.logo' => array(
			'code' => 'locale.site.logo',
			'internalcode' => 'mlocsi."logo"',
			'label' => 'Site logo',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'locale.site.supplierid' => array(
			'code' => 'locale.site.supplierid',
			'internalcode' => 'mlocsi."supplierid"',
			'label' => 'Site-related supplier ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'locale.site.theme' => array(
			'code' => 'locale.site.theme',
			'internalcode' => 'mlocsi."theme"',
			'label' => 'Site theme',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'locale.site.ctime' => array(
			'code' => 'locale.site.ctime',
			'internalcode' => 'mlocsi."ctime"',
			'label' => 'Site create date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'locale.site.mtime' => array(
			'code' => 'locale.site.mtime',
			'internalcode' => 'mlocsi."mtime"',
			'label' => 'Site modify date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'locale.site.editor' => array(
			'code' => 'locale.site.editor',
			'internalcode' => 'mlocsi."editor"',
			'label' => 'Site editor',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'parentid' => array(
			'code' => 'locale.site.parentid',
			'internalcode' => 'mlocsi."parentid"',
			'label' => 'Parent site ID',
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
	 * @return \Aimeos\MShop\Locale\Manager\Site\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		if( empty( $siteids ) ) {
			return $this;
		}

		$context = $this->getContext();
		$config = $context->getConfig();

		/** mshop/locale/manager/site/cleanup/shop/domains
		 * List of madmin domains names whose items referring to the same site should be deleted as well
		 *
		 * As items for each domain can be stored in a separate database, the
		 * site manager needs a list of domain names used to connect to the
		 * correct database and to remove all items that belong the the deleted
		 * site.
		 *
		 * For each domain the cleanup will be done by the corresponding MShop
		 * manager. To keep records for old sites in the database even if the
		 * site was already deleted, you can configure a new list with the
		 * domains removed you would like to keep, e.g. the "order" domain to
		 * keep all orders ever placed.
		 *
		 * @param array List of domain names in lower case
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/locale/manager/site/cleanup/admin/domains
		 */
		$path = 'mshop/locale/manager/site/cleanup/shop/domains';

		foreach( $config->get( $path, [] ) as $domain ) {
			\Aimeos\MShop::create( $context, $domain )->clear( $siteids );
		}

		/** mshop/locale/manager/site/cleanup/admin/domains
		 * List of mshop domains names whose items referring to the same site should be deleted as well
		 *
		 * As items for each domain can be stored in a separate database, the
		 * site manager needs a list of domain names used to connect to the
		 * correct database and to remove all items that belong the the deleted
		 * site.
		 *
		 * For each domain the cleanup will be done by the corresponding MAdmin
		 * manager. To keep records for old sites in the database even if the
		 * site was already deleted, you can configure a new list with the
		 * domains removed you would like to keep, e.g. the "log" domain to
		 * keep all log entries ever written.
		 *
		 * @param array List of domain names in lower case
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/locale/manager/site/cleanup/shop/domains
		 */
		$path = 'mshop/locale/manager/site/cleanup/admin/domains';

		foreach( $config->get( $path, [] ) as $domain ) {
			\Aimeos\MAdmin::create( $context, $domain )->clear( $siteids );
		}

		return $this;
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface New locale site item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->createItemBase( $values );
	}


	/**
	 * Adds a new site to the storage or updates an existing one.
	 *
	 * @param \Aimeos\MShop\Locale\Item\Site\Iface $item New site item for saving to the storage
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Locale\Item\Site\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Locale\Item\Site\Iface
	{
		if( $item->getId() === null )
		{
			$msg = $this->getContext()->translate( 'mshop', 'Newly created item can not be saved using method "save()", use "insert()" instead' );
			throw new \Aimeos\MShop\Locale\Exception( $msg );
		}

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
			$columns = $this->getObject()->getSaveAttributes();

			/** mshop/locale/manager/site/update/mysql
			 * Updates an existing site record in the database
			 *
			 * @see mshop/locale/manager/site/update/ansi
			 */

			/** mshop/locale/manager/site/update/ansi
			 * Updates an existing site record in the database
			 *
			 * The SQL statement must be a string suitable for being used as
			 * prepared statement. It must include question marks for binding
			 * the values from the site item to the statement before they are
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
			 * @see mshop/locale/manager/site/insert/ansi
			 * @see mshop/locale/manager/site/delete/ansi
			 * @see mshop/locale/manager/site/search/ansi
			 * @see mshop/locale/manager/site/count/ansi
			 * @see mshop/locale/manager/site/newid/ansi
			 */
			$path = 'mshop/locale/manager/site/update';
			$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ), false );

			$idx = 1;
			$stmt = $this->getCachedStatement( $conn, $path, $sql );

			foreach( $columns as $name => $entry ) {
				$stmt->bind( $idx++, $item->get( $name ), $entry->getInternalType() );
			}

			$stmt->bind( $idx++, $item->getSiteId() );
			$stmt->bind( $idx++, $item->getCode() );
			$stmt->bind( $idx++, $item->getLabel() );
			$stmt->bind( $idx++, json_encode( $item->getConfig(), JSON_FORCE_OBJECT ) );
			$stmt->bind( $idx++, $item->getStatus(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $item->getIcon() );
			$stmt->bind( $idx++, json_encode( $item->getLogos(), JSON_FORCE_OBJECT ) );
			$stmt->bind( $idx++, $item->getSupplierId() );
			$stmt->bind( $idx++, $item->getTheme() );
			$stmt->bind( $idx++, $context->getEditor() );
			$stmt->bind( $idx++, date( 'Y-m-d H:i:s' ) ); // mtime
			$stmt->bind( $idx++, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );

			$stmt->execute()->finish();
			$item->setId( $id ); // set Modified false

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
	 * @param \Aimeos\MShop\Common\Item\Iface|array|string $items List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Locale\Manager\Site\Iface Manager object for chaining method calls
	 */
	public function delete( $items ) : \Aimeos\MShop\Common\Manager\Iface
	{
		if( is_map( $items ) ) { $items = $items->toArray(); }
		if( !is_array( $items ) ) { $items = [$items]; }
		if( empty( $items ) ) { return $this; }


		$filter = $this->getObject()->filter()
			->add( ['locale.site.id' => $items] )
			->slice( 0, count( $items ) );

		$siteIds = $this->getObject()->search( $filter )->getSiteId()->toArray();
		$this->getObject()->clear( $siteIds );


		/** mshop/locale/manager/site/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/locale/manager/site/delete/ansi
		 */

		/** mshop/locale/manager/site/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the site records specified by the given IDs from the
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
		 * @see mshop/locale/manager/site/insert/ansi
		 * @see mshop/locale/manager/site/update/ansi
		 * @see mshop/locale/manager/site/search/ansi
		 * @see mshop/locale/manager/site/count/ansi
		 * @see mshop/locale/manager/site/newid/ansi
		 */
		$path = 'mshop/locale/manager/site/delete';

		return $this->deleteItemsBase( $items, $path, false );
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
		return $this->findBase( array( 'locale.site.code' => $code ), $ref, $default );
	}


	/**
	 * Returns the site item specified by its ID.
	 *
	 * @param string $id Unique ID of the site data in the storage
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Returns the site item of the given id
	 * @throws \Aimeos\MShop\Exception If the item couldn't be found
	 */
	public function get( string $id, array $ref = [], ?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->getItemBase( 'locale.site.id', $id, $ref, $default );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		$path = 'mshop/locale/manager/site/submanagers';

		return $this->getResourceTypeBase( 'locale/site', $path, [], $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		/** mshop/locale/manager/site/submanagers
		 * List of manager names that can be instantiated by the locale site manager
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
		$path = 'mshop/locale/manager/site/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, [], $withsub );
	}


	/**
	 * Returns a new sub manager of the given type and name.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Sub manager
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/locale/manager/site/name
		 * Class name of the used locale site manager implementation
		 *
		 * Each default locale site manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\MShop\Locale\Manager\Site\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\MShop\Locale\Manager\Site\Mysite
		 *
		 * then you have to set the this configuration option:
		 *
		 *  mshop/locale/manager/site/name = Mysite
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MySite"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */

		/** mshop/locale/manager/site/decorators/excludes
		 * Excludes decorators added by the "common" option from the locale site manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the locale site manager.
		 *
		 *  mshop/locale/manager/site/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
		 * "mshop/common/manager/decorators/default" for the locale site manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/locale/manager/site/decorators/global
		 * @see mshop/locale/manager/site/decorators/local
		 */

		/** mshop/locale/manager/site/decorators/global
		 * Adds a list of globally available decorators only to the locale site manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the locale site
		 * manager.
		 *
		 *  mshop/locale/manager/site/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the locale
		 * site manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/locale/manager/site/decorators/excludes
		 * @see mshop/locale/manager/site/decorators/local
		 */

		/** mshop/locale/manager/site/decorators/local
		 * Adds a list of local decorators only to the locale site manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\MShop\Locale\Manager\Site\Decorator\*") around the locale site
		 * manager.
		 *
		 *  mshop/locale/manager/site/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\MShop\Locale\Manager\Site\Decorator\Decorator2" only to the
		 * locale site manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/locale/manager/site/decorators/excludes
		 * @see mshop/locale/manager/site/decorators/global
		 */

		return $this->getSubManagerBase( 'locale', 'site/' . $manager, $name );
	}


	/**
	 * Searches for site items matching the given criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Locale\Item\Site\Iface with ids as keys
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

			/** mshop/locale/manager/site/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see mshop/locale/manager/site/search/ansi
			 */

			/** mshop/locale/manager/site/search/ansi
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
			 * @see mshop/locale/manager/site/insert/ansi
			 * @see mshop/locale/manager/site/update/ansi
			 * @see mshop/locale/manager/site/delete/ansi
			 * @see mshop/locale/manager/site/count/ansi
			 * @see mshop/locale/manager/site/newid/ansi
			 */
			$path = 'mshop/locale/manager/site/search';

			$sql = $this->getSqlConfig( $path );
			$results = $this->getSearchResults( $conn, str_replace( $find, $replace, $sql ) );

			try
			{
				while( ( $row = $results->fetch() ) !== null )
				{
					$id = $row['locale.site.id'];
					$logos = $row['locale.site.logo'] ?? '{}';
					$config = $row['locale.site.config'] ?? '{}';

					if( ( $row['locale.site.logo'] = json_decode( $logos, true ) ) === null )
					{
						$msg = sprintf( 'Invalid JSON as result of search for ID "%2$s" in "%1$s": %3$s', 'mshop_locale_site.logo', $id, $logos );
						$this->getContext()->getLogger()->log( $msg, \Aimeos\MW\Logger\Base::WARN, 'core/locale/site' );
					}

					if( ( $row['locale.site.config'] = json_decode( $config, true ) ) === null )
					{
						$msg = sprintf( 'Invalid JSON as result of search for ID "%2$s" in "%1$s": %3$s', 'mshop_locale_site.config', $id, $config );
						$this->getContext()->getLogger()->log( $msg, \Aimeos\MW\Logger\Base::WARN, 'core/locale/site' );
					}

					if( $item = $this->applyFilter( $this->createItemBase( $row ) ) ) {
						$items[$row['locale.site.id']] = $item;
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
	 * Creates a filter object.
	 *
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @param bool $site TRUE for adding site criteria to limit items by the site of related items
	 * @return \Aimeos\MW\Criteria\Iface Returns the filter object
	 */
	public function filter( ?bool $default = false, bool $site = false ) : \Aimeos\MW\Criteria\Iface
	{
		$search = $this->filterBase( 'locale.site', $default );

		$expr = array(
			$search->compare( '==', 'locale.site.level', 0 ),
			$search->getConditions(),
		);

		$search->setConditions( $search->and( $expr ) );

		return $search;
	}


	/**
	 * Returns a list of item IDs, that are in the path of given item ID.
	 *
	 * @param string $id ID of item to get the path for
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @return \Aimeos\Map List of IDs as keys and items implementing \Aimeos\MShop\Locale\Item\Site\Iface
	 */
	public function getPath( string $id, array $ref = [] ) : \Aimeos\Map
	{
		$item = $this->getTree( $id, $ref, \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE );
		return map( [$item->getId() => $item] );
	}


	/**
	 * Returns a node and its descendants depending on the given resource.
	 *
	 * @param string|null $id Retrieve nodes starting from the given ID
	 * @param string[] $ref List of domains (e.g. text, media, etc.) whose referenced items should be attached to the objects
	 * @param int $level One of the level constants from \Aimeos\MW\Tree\Manager\Base
	 * @param \Aimeos\MW\Criteria\Iface|null $criteria Optional criteria object with conditions
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Site node, maybe with subnodes
	 */
	public function getTree( string $id = null, array $ref = [], int $level = \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE,
		\Aimeos\MW\Criteria\Iface $criteria = null ) : \Aimeos\MShop\Locale\Item\Site\Iface
	{
		if( $id !== null )
		{
			if( count( $ref ) > 0 ) {
				return $this->getObject()->get( $id, $ref );
			}

			if( !isset( $this->cache[$id] ) ) {
				$this->cache[$id] = $this->getObject()->get( $id, $ref );
			}

			return $this->cache[$id];
		}

		$criteria = $criteria ? clone $criteria : $this->getObject()->filter();
		$criteria->add( ['locale.site.code' => 'default'] )->slice( 0, 1 );

		if( ( $item = $this->getObject()->search( $criteria, $ref )->first() ) === null )
		{
			$msg = $this->getContext()->translate( 'mshop', 'Tree root with code "%1$s" in "%2$s" not found' );
			throw new \Aimeos\MShop\Locale\Exception( sprintf( $msg, 'default', 'locale.site.code' ) );
		}

		$this->cache[$item->getId()] = $item;

		return $item;
	}


	/**
	 * Adds a new item object.
	 *
	 * @param \Aimeos\MShop\Locale\Item\Site\Iface $item Item which should be inserted
	 * @param string|null $parentId ID of the parent item where the item should be inserted into
	 * @param string|null $refId ID of the item where the item should be inserted before (null to append)
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface $item Updated item including the generated ID
	 */
	public function insert( \Aimeos\MShop\Locale\Item\Site\Iface $item, string $parentId = null, string $refId = null ) : \Aimeos\MShop\Locale\Item\Site\Iface
	{
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$date = date( 'Y-m-d H:i:s' );
			$columns = $this->getObject()->getSaveAttributes();

			/** mshop/locale/manager/site/insert/mysql
			 * Inserts a new currency record into the database table
			 *
			 * @see mshop/locale/manager/site/insert/ansi
			 */

			/** mshop/locale/manager/site/insert/ansi
			 * Inserts a new currency record into the database table
			 *
			 * The SQL statement must be a string suitable for being used as
			 * prepared statement. It must include question marks for binding
			 * the values from the site item to the statement before they are
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
			 * @see mshop/locale/manager/site/update/ansi
			 * @see mshop/locale/manager/site/delete/ansi
			 * @see mshop/locale/manager/site/search/ansi
			 * @see mshop/locale/manager/site/count/ansi
			 * @see mshop/locale/manager/site/newid/ansi
			 */
			$path = 'mshop/locale/manager/site/insert';
			$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ) );

			$idx = 1;
			$stmt = $this->getCachedStatement( $conn, $path, $sql );

			foreach( $columns as $name => $entry ) {
				$stmt->bind( $idx++, $item->get( $name ), $entry->getInternalType() );
			}

			$stmt->bind( $idx++, '' ); // site ID
			$stmt->bind( $idx++, $item->getCode() );
			$stmt->bind( $idx++, $item->getLabel() );
			$stmt->bind( $idx++, json_encode( $item->getConfig(), JSON_FORCE_OBJECT ) );
			$stmt->bind( $idx++, $item->getStatus(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $item->getIcon() );
			$stmt->bind( $idx++, json_encode( $item->getLogos(), JSON_FORCE_OBJECT ) );
			$stmt->bind( $idx++, $item->getSupplierId() );
			$stmt->bind( $idx++, $item->getTheme() );
			$stmt->bind( $idx++, $context->getEditor() );
			$stmt->bind( $idx++, $date ); // mtime
			$stmt->bind( $idx++, $date ); // ctime

			$stmt->execute()->finish();

			/** mshop/locale/manager/site/newid/mysql
			 * Retrieves the ID generated by the database when inserting a new record
			 *
			 * @see mshop/locale/manager/site/newid/ansi
			 */

			/** mshop/locale/manager/site/newid/ansi
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
			 *  SELECT currval('seq_matt_id')
			 * For SQL Server:
			 *  SELECT SCOPE_IDENTITY()
			 * For Oracle:
			 *  SELECT "seq_matt_id".CURRVAL FROM DUAL
			 *
			 * There's no way to retrive the new ID by a SQL statements that
			 * fits for most database servers as they implement their own
			 * specific way.
			 *
			 * @param string SQL statement for retrieving the last inserted record ID
			 * @since 2014.03
			 * @category Developer
			 * @see mshop/locale/manager/site/insert/ansi
			 * @see mshop/locale/manager/site/update/ansi
			 * @see mshop/locale/manager/site/delete/ansi
			 * @see mshop/locale/manager/site/search/ansi
			 * @see mshop/locale/manager/site/count/ansi
			 */
			$path = 'mshop/locale/manager/newid';
			$item->setId( $this->newId( $conn, $this->getSqlConfig( $path ) ) );

			$dbm->release( $conn, $dbname );

			// Add unique site identifier
			$item = $this->getObject()->save( $item->setSiteId( $item->getId() . '.' ) );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $item;
	}


	/**
	 * Moves an existing item to the new parent in the storage.
	 *
	 * @param string $id ID of the item that should be moved
	 * @param string|null $oldParentId ID of the old parent item which currently contains the item that should be removed
	 * @param string|null $newParentId ID of the new parent item where the item should be moved to
	 * @param string|null $refId ID of the item where the item should be inserted before (null to append)
	 * @return \Aimeos\MShop\Locale\Manager\Site\Iface Manager object for chaining method calls
	 */
	public function move( string $id, string $oldParentId = null, string $newParentId = null,
		string $refId = null ) : \Aimeos\MShop\Locale\Manager\Site\Iface
	{
		$msg = $this->getContext()->translate( 'mshop', 'Method "%1$s" for locale site manager not available' );
		throw new \Aimeos\MShop\Locale\Exception( sprintf( $msg, 'move()' ) );
	}


	/**
	 * Registers a new item filter for the given name
	 *
	 * Not used for site items but required for trees
	 *
	 * @param string $name Filter name
	 * @param \Closure $fcn Callback function
	 */
	public function registerItemFilter( string $name, \Closure $fcn ) : \Aimeos\MShop\Locale\Manager\Site\Iface
	{
		return $this;
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
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Site item object
	 */
	protected function createItemBase( array $data = [] ) : \Aimeos\MShop\Locale\Item\Site\Iface
	{
		return new \Aimeos\MShop\Locale\Item\Site\Standard( $data );
	}


	/**
	 * Returns the raw search config array.
	 *
	 * @return array List of search config arrays
	 */
	protected function getSearchConfig() : array
	{
		return $this->searchConfig;
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
		/** mshop/locale/manager/site/count/mysql
		 * Counts the number of records matched by the given criteria in the database
		 *
		 * @see mshop/locale/manager/site/count/ansi
		 */

		/** mshop/locale/manager/site/count/ansi
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
		 * @see mshop/locale/manager/site/insert/ansi
		 * @see mshop/locale/manager/site/update/ansi
		 * @see mshop/locale/manager/site/delete/ansi
		 * @see mshop/locale/manager/site/search/ansi
		 * @see mshop/locale/manager/site/newid/ansi
		 */
		$path = 'mshop/locale/manager/site/count';

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
