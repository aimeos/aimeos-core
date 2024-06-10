<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2024
 * @package MShop
 * @subpackage Attribute
 */


namespace Aimeos\MShop\Attribute\Manager;


/**
 * Default attribute manager for creating and handling attributes.
 * @package MShop
 * @subpackage Attribute
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Attribute\Manager\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	/** mshop/attribute/manager/name
	 * Class name of the used attribute manager implementation
	 *
	 * Each default manager can be replace by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the manager factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\MShop\Attribute\Manager\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\MShop\Attribute\Manager\Mymanager
	 *
	 * then you have to set the this configuration option:
	 *
	 *  mshop/attribute/manager/name = Mymanager
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
	 * @category Developer
	 */

	/** mshop/attribute/manager/decorators/excludes
	 * Excludes decorators added by the "common" option from the attribute manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to remove a decorator added via
	 * "mshop/common/manager/decorators/default" before they are wrapped
	 * around the attribute manager.
	 *
	 *  mshop/attribute/manager/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
	 * "mshop/common/manager/decorators/default" for the attribute manager.
	 *
	 * @param array List of decorator names
	 * @since 2014.03
	 * @category Developer
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/attribute/manager/decorators/global
	 * @see mshop/attribute/manager/decorators/local
	 */

	/** mshop/attribute/manager/decorators/global
	 * Adds a list of globally available decorators only to the attribute manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap global decorators
	 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the attribute manager.
	 *
	 *  mshop/attribute/manager/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the attribute controller.
	 *
	 * @param array List of decorator names
	 * @since 2014.03
	 * @category Developer
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/attribute/manager/decorators/excludes
	 * @see mshop/attribute/manager/decorators/local
	 */

	/** mshop/attribute/manager/decorators/local
	 * Adds a list of local decorators only to the attribute manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\MShop\Attribute\Manager\Decorator\*") around the attribute manager.
	 *
	 *  mshop/attribute/manager/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\MShop\Attribute\Manager\Decorator\Decorator2" only to the attribute
	 * controller.
	 *
	 * @param array List of decorator names
	 * @since 2014.03
	 * @category Developer
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/attribute/manager/decorators/excludes
	 * @see mshop/attribute/manager/decorators/global
	 */


	use \Aimeos\MShop\Common\Manager\ListsRef\Traits;
	use \Aimeos\MShop\Common\Manager\PropertyRef\Traits;


	private array $searchConfig = array(
		'attribute.id' => array(
			'code' => 'attribute.id',
			'internalcode' => 'matt."id"',
			'label' => 'ID',
			'type' => 'int',
			'public' => false,
		),
		'attribute.siteid' => array(
			'code' => 'attribute.siteid',
			'internalcode' => 'matt."siteid"',
			'label' => 'Site ID',
			'public' => false,
		),
		'attribute.key' => array(
			'code' => 'attribute.key',
			'internalcode' => 'matt."key"',
			'label' => 'Unique key',
			'public' => false,
		),
		'attribute.type' => array(
			'code' => 'attribute.type',
			'internalcode' => 'matt."type"',
			'label' => 'Type',
			'public' => false,
		),
		'attribute.label' => array(
			'code' => 'attribute.label',
			'internalcode' => 'matt."label"',
			'label' => 'Label',
		),
		'attribute.code' => array(
			'code' => 'attribute.code',
			'internalcode' => 'matt."code"',
			'label' => 'Code',
		),
		'attribute.domain' => array(
			'code' => 'attribute.domain',
			'internalcode' => 'matt."domain"',
			'label' => 'Domain',
		),
		'attribute.position' => array(
			'code' => 'attribute.position',
			'internalcode' => 'matt."pos"',
			'label' => 'Position',
			'type' => 'int',
			'public' => false,
		),
		'attribute.status' => array(
			'code' => 'attribute.status',
			'internalcode' => 'matt."status"',
			'label' => 'Status',
			'type' => 'int',
		),
		'attribute.ctime' => array(
			'code' => 'attribute.ctime',
			'internalcode' => 'matt."ctime"',
			'label' => 'Create date/time',
			'type' => 'datetime',
			'public' => false,
		),
		'attribute.mtime' => array(
			'code' => 'attribute.mtime',
			'internalcode' => 'matt."mtime"',
			'label' => 'Modification date/time',
			'type' => 'datetime',
			'public' => false,
		),
		'attribute.editor' => array(
			'code' => 'attribute.editor',
			'internalcode' => 'matt."editor"',
			'label' => 'Editor',
			'public' => false,
		),
		'attribute:has' => array(
			'code' => 'attribute:has()',
			'internalcode' => ':site AND :key AND mattli."id"',
			'internaldeps' => ['LEFT JOIN "mshop_attribute_list" AS mattli ON ( mattli."parentid" = matt."id" )'],
			'label' => 'Attribute has list item, parameter(<domain>[,<list type>[,<reference ID>)]]',
			'type' => 'null',
			'public' => false,
		),
		'attribute:prop' => array(
			'code' => 'attribute:prop()',
			'internalcode' => ':site AND :key AND mattpr."id"',
			'internaldeps' => ['LEFT JOIN "mshop_attribute_property" AS mattpr ON ( mattpr."parentid" = matt."id" )'],
			'label' => 'Attribute has property item, parameter(<property type>[,<language code>[,<property value>]])',
			'type' => 'null',
			'public' => false,
		),
	);



	/**
	 * Initializes the object.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 */
	public function __construct( \Aimeos\MShop\ContextIface $context )
	{
		parent::__construct( $context );

		/** mshop/attribute/manager/resource
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
		$this->setResourceName( $context->config()->get( 'mshop/attribute/manager/resource', 'db-attribute' ) );

		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
		$level = $context->config()->get( 'mshop/attribute/manager/sitemode', $level );


		$this->searchConfig['attribute:has']['function'] = function( &$source, array $params ) use ( $level ) {

			$keys = [];

			foreach( (array) ( $params[1] ?? '' ) as $type ) {
				foreach( (array) ( $params[2] ?? '' ) as $id ) {
					$keys[] = substr( $params[0] . '|' . ( $type ? $type . '|' : '' ) . $id, 0, 255 );
				}
			}

			$sitestr = $this->siteString( 'mattli."siteid"', $level );
			$keystr = $this->toExpression( 'mattli."key"', $keys, ( $params[2] ?? null ) ? '==' : '=~' );
			$source = str_replace( [':site', ':key'], [$sitestr, $keystr], $source );

			return $params;
		};


		$this->searchConfig['attribute:prop']['function'] = function( &$source, array $params ) use ( $level ) {

			$keys = [];
			$langs = array_key_exists( 1, $params ) ? ( $params[1] ?? 'null' ) : '';

			foreach( (array) $langs as $lang ) {
				foreach( (array) ( $params[2] ?? '' ) as $val ) {
					$keys[] = substr( $params[0] . '|' . ( $lang === null ? 'null|' : ( $lang ? $lang . '|' : '' ) ) . $val, 0, 255 );
				}
			}

			$sitestr = $this->siteString( 'mattpr."siteid"', $level );
			$keystr = $this->toExpression( 'mattpr."key"', $keys, ( $params[2] ?? null ) ? '==' : '=~' );
			$source = str_replace( [':site', ':key'], [$sitestr, $keystr], $source );

			return $params;
		};
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Attribute\Manager\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$path = 'mshop/attribute/manager/submanagers';
		$default = ['lists', 'property', 'type'];

		foreach( $this->context()->config()->get( $path, $default ) as $domain ) {
			$this->object()->getSubManager( $domain )->clear( $siteids );
		}

		return $this->clearBase( $siteids, 'mshop/attribute/manager/delete' );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		$path = 'mshop/attribute/manager/submanagers';
		$default = ['lists', 'property'];

		return $this->getResourceTypeBase( 'attribute', $path, $default, $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		/** mshop/attribute/manager/submanagers
		 * List of manager names that can be instantiated by the attribute manager
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
		$path = 'mshop/attribute/manager/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, [], $withsub );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Attribute\Item\Iface New attribute item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values['attribute.siteid'] = $values['attribute.siteid'] ?? $this->context()->locale()->getSiteId();
		return $this->createItemBase( $values );
	}


	/**
	 * Returns the item specified by its code and domain/type if necessary
	 *
	 * @param string $code Code of the item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param string|null $domain Domain of the item if necessary to identify the item uniquely
	 * @param string|null $type Type code of the item if necessary to identify the item uniquely
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item object
	 */
	public function find( string $code, array $ref = [], ?string $domain = 'product', string $type = null,
		?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$find = array(
			'attribute.code' => $code,
			'attribute.domain' => $domain,
			'attribute.type' => $type,
		);
		return $this->findBase( $find, $ref, $default );
	}


	/**
	 * Returns the attributes item specified by its ID.
	 *
	 * @param string $id Unique ID of the attribute item in the storage
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Attribute\Item\Iface Returns the attribute item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function get( string $id, array $ref = [], ?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->getItemBase( 'attribute.id', $id, $ref, $default );
	}


	/**
	 * Saves an attribute item to the storage.
	 *
	 * @param \Aimeos\MShop\Attribute\Item\Iface $item Attribute item
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Attribute\Item\Iface $item Updated item including the generated ID
	 */
	protected function saveItem( \Aimeos\MShop\Attribute\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Attribute\Item\Iface
	{
		if( !$item->isModified() )
		{
			$item = $this->savePropertyItems( $item, 'attribute', $fetch );
			return $this->saveListItems( $item, 'attribute', $fetch );
		}

		$context = $this->context();
		$conn = $context->db( $this->getResourceName() );

		$id = $item->getId();
		$columns = $this->object()->getSaveAttributes();

		if( $id === null )
		{
			/** mshop/attribute/manager/insert/mysql
			 * Inserts a new attribute record into the database table
			 *
			 * @see mshop/attribute/manager/insert/ansi
			 */

			/** mshop/attribute/manager/insert/ansi
			 * Inserts a new attribute record into the database table
			 *
			 * Items with no ID yet (i.e. the ID is NULL) will be created in
			 * the database and the newly created ID retrieved afterwards
			 * using the "newid" SQL statement.
			 *
			 * The SQL statement must be a string suitable for being used as
			 * prepared statement. It must include question marks for binding
			 * the values from the attribute item to the statement before they are
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
			 * @see mshop/attribute/manager/update/ansi
			 * @see mshop/attribute/manager/newid/ansi
			 * @see mshop/attribute/manager/delete/ansi
			 * @see mshop/attribute/manager/search/ansi
			 * @see mshop/attribute/manager/count/ansi
			 */
			$path = 'mshop/attribute/manager/insert';
			$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ) );
		}
		else
		{
			/** mshop/attribute/manager/update/mysql
			 * Updates an existing attribute record in the database
			 *
			 * @see mshop/attribute/manager/update/ansi
			 */

			/** mshop/attribute/manager/update/ansi
			 * Updates an existing attribute record in the database
			 *
			 * Items which already have an ID (i.e. the ID is not NULL) will
			 * be updated in the database.
			 *
			 * The SQL statement must be a string suitable for being used as
			 * prepared statement. It must include question marks for binding
			 * the values from the attribute item to the statement before they are
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
			 * @see mshop/attribute/manager/insert/ansi
			 * @see mshop/attribute/manager/newid/ansi
			 * @see mshop/attribute/manager/delete/ansi
			 * @see mshop/attribute/manager/search/ansi
			 * @see mshop/attribute/manager/count/ansi
			 */
			$path = 'mshop/attribute/manager/update';
			$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ), false );
		}

		$idx = 1;
		$stmt = $this->getCachedStatement( $conn, $path, $sql );

		foreach( $columns as $name => $entry ) {
			$stmt->bind( $idx++, $item->get( $name ), \Aimeos\Base\Criteria\SQL::type( $entry->getType() ) );
		}

		$stmt->bind( $idx++, $item->getKey() );
		$stmt->bind( $idx++, $item->getType() );
		$stmt->bind( $idx++, $item->getDomain() );
		$stmt->bind( $idx++, $item->getCode() );
		$stmt->bind( $idx++, $item->getStatus(), \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		$stmt->bind( $idx++, $item->getPosition(), \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		$stmt->bind( $idx++, $item->getLabel() );
		$stmt->bind( $idx++, $context->datetime() ); // mtime
		$stmt->bind( $idx++, $context->editor() );

		if( $id !== null ) {
			$stmt->bind( $idx++, $context->locale()->getSiteId() . '%' );
			$stmt->bind( $idx++, $id, \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		} else {
			$stmt->bind( $idx++, $this->siteId( $item->getSiteId(), \Aimeos\MShop\Locale\Manager\Base::SITE_SUBTREE ) );
			$stmt->bind( $idx++, $context->datetime() ); // ctime
		}

		$stmt->execute()->finish();

		if( $id === null )
		{
			/** mshop/attribute/manager/newid/mysql
			 * Retrieves the ID generated by the database when inserting a new record
			 *
			 * @see mshop/attribute/manager/newid/ansi
			 */

			/** mshop/attribute/manager/newid/ansi
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
			 * @see mshop/attribute/manager/insert/ansi
			 * @see mshop/attribute/manager/update/ansi
			 * @see mshop/attribute/manager/delete/ansi
			 * @see mshop/attribute/manager/search/ansi
			 * @see mshop/attribute/manager/count/ansi
			 */
			$path = 'mshop/attribute/manager/newid';
			$id = $this->newId( $conn, $path );
		}

		$item->setId( $id );

		$item = $this->savePropertyItems( $item, 'attribute', $fetch );
		return $this->saveListItems( $item, 'attribute', $fetch );
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Attribute\Manager\Iface Manager object for chaining method calls
	 */
	public function delete( $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/attribute/manager/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/attribute/manager/delete/ansi
		 */

		/** mshop/attribute/manager/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the attribute database.
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
		 * @see mshop/attribute/manager/insert/ansi
		 * @see mshop/attribute/manager/update/ansi
		 * @see mshop/attribute/manager/newid/ansi
		 * @see mshop/attribute/manager/search/ansi
		 * @see mshop/attribute/manager/count/ansi
		 */
		$path = 'mshop/attribute/manager/delete';

		return $this->deleteItemsBase( $itemIds, $path )->deleteRefItems( $itemIds );
	}


	/**
	 * Searches for attribute items based on the given criteria.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Attribute\Item\Iface with ids as keys
	 */
	public function search( \Aimeos\Base\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$map = [];
		$context = $this->context();
		$conn = $context->db( $this->getResourceName() );

		$required = array( 'attribute' );

		/** mshop/attribute/manager/sitemode
		 * Mode how items from levels below or above in the site tree are handled
		 *
		 * By default, only items from the current site are fetched from the
		 * storage. If the ai-sites extension is installed, you can create a
		 * tree of sites. Then, this setting allows you to define for the
		 * whole attribute domain if items from parent sites are inherited,
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
		$level = $context->config()->get( 'mshop/attribute/manager/sitemode', $level );

		/** mshop/attribute/manager/search/mysql
		 * Retrieves the records matched by the given criteria in the database
		 *
		 * @see mshop/attribute/manager/search/ansi
		 */

		/** mshop/attribute/manager/search/ansi
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
		 * @category Developer
		 * @see mshop/attribute/manager/insert/ansi
		 * @see mshop/attribute/manager/update/ansi
		 * @see mshop/attribute/manager/newid/ansi
		 * @see mshop/attribute/manager/delete/ansi
		 * @see mshop/attribute/manager/count/ansi
		 */
		$cfgPathSearch = 'mshop/attribute/manager/search';

		/** mshop/attribute/manager/count/mysql
		 * Counts the number of records matched by the given criteria in the database
		 *
		 * @see mshop/attribute/manager/count/ansi
		 */

		/** mshop/attribute/manager/count/ansi
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
		 * @see mshop/attribute/manager/insert/ansi
		 * @see mshop/attribute/manager/update/ansi
		 * @see mshop/attribute/manager/newid/ansi
		 * @see mshop/attribute/manager/delete/ansi
		 * @see mshop/attribute/manager/search/ansi
		 */
		$cfgPathCount = 'mshop/attribute/manager/count';

		$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

		while( $row = $results->fetch() ) {
			$map[$row['attribute.id']] = $row;
		}

		$propItems = []; $name = 'attribute/property';
		if( isset( $ref[$name] ) || in_array( $name, $ref, true ) )
		{
			$propTypes = isset( $ref[$name] ) && is_array( $ref[$name] ) ? $ref[$name] : null;
			$propItems = $this->getPropertyItems( array_keys( $map ), 'attribute', $propTypes );
		}

		return $this->buildItems( $map, $ref, 'attribute', $propItems );
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
		return $this->filterBase( 'attribute', $default );
	}


	/**
	 * Returns a new manager for attribute extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g Type, List's etc.
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->getSubManagerBase( 'attribute', $manager, $name );
	}


	/**
	 * Creates a new attribute item instance.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 * @param \Aimeos\MShop\Common\Item\Property\Iface[] $propertyItems List of property items
	 * @return \Aimeos\MShop\Attribute\Item\Iface New attribute item
	 */
	protected function createItemBase( array $values = [], array $listItems = [],
		array $refItems = [], array $propertyItems = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		return new \Aimeos\MShop\Attribute\Item\Standard( $values, $listItems, $refItems, $propertyItems );
	}
}
