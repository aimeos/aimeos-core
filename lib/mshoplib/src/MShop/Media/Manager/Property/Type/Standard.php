<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2020
 * @package MShop
 * @subpackage Media
 */


namespace Aimeos\MShop\Media\Manager\Property\Type;


/**
 * Default media property type manager for creating and handling media property type items.
 * @package MShop
 * @subpackage Media
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\Type\Base
	implements \Aimeos\MShop\Media\Manager\Property\Type\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private $searchConfig = array(
		'media.property.type.id' => array(
			'code' => 'media.property.type.id',
			'internalcode' => 'mmedprty."id"',
			'label' => 'Property type ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'media.property.type.siteid' => array(
			'code' => 'media.property.type.siteid',
			'internalcode' => 'mmedprty."siteid"',
			'label' => 'Property type site ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'media.property.type.label' => array(
			'code' => 'media.property.type.label',
			'internalcode' => 'mmedprty."label"',
			'label' => 'Property type label',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'media.property.type.code' => array(
			'code' => 'media.property.type.code',
			'internalcode' => 'mmedprty."code"',
			'label' => 'Property type code',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'media.property.type.domain' => array(
			'code' => 'media.property.type.domain',
			'internalcode' => 'mmedprty."domain"',
			'label' => 'Property type domain',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'media.property.type.position' => array(
			'code' => 'media.property.type.position',
			'internalcode' => 'mmedprty."pos"',
			'label' => 'Property type position',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'media.property.type.status' => array(
			'code' => 'media.property.type.status',
			'internalcode' => 'mmedprty."status"',
			'label' => 'Property type status',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'media.property.type.ctime' => array(
			'code' => 'media.property.type.ctime',
			'internalcode' => 'mmedprty."ctime"',
			'label' => 'Property type create date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'media.property.type.mtime' => array(
			'code' => 'media.property.type.mtime',
			'internalcode' => 'mmedprty."mtime"',
			'label' => 'Property type modify date',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'media.property.type.editor' => array(
			'code' => 'media.property.type.editor',
			'internalcode' => 'mmedprty."editor"',
			'label' => 'Property type editor',
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
		$this->setResourceName( 'db-media' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param string[] $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Media\Manager\Property\Type\Iface Manager object for chaining method calls
	 */
	public function clear( array $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$path = 'mshop/media/manager/property/type/submanagers';
		foreach( $this->getContext()->getConfig()->get( $path, [] ) as $domain ) {
			$this->getObject()->getSubManager( $domain )->clear( $siteids );
		}

		return $this->clearBase( $siteids, 'mshop/media/manager/property/type/standard/delete' );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		$path = 'mshop/media/manager/property/type/submanagers';
		return $this->getResourceTypeBase( 'media/property/type', $path, [], $withsub );
	}


	/**
	 * Returns the medias that can be used for searching.
	 *
	 * @param bool $withsub Return also medias of sub-managers if true
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		/** mshop/media/manager/property/type/submanagers
		 * List of manager names that can be instantiated by the media property type manager
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
		 * @since 2018.01
		 * @category Developer
		 */
		$path = 'mshop/media/manager/property/type/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, [], $withsub );
	}


	/**
	 * Returns a new manager for media type extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g types, lists etc.
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/media/manager/property/type/name
		 * Class name of the used media property type manager implementation
		 *
		 * Each default media property type manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\MShop\Media\Manager\Lists\Type\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\MShop\Media\Manager\Lists\Type\Mytype
		 *
		 * then you have to set the this configuration option:
		 *
		 *  mshop/media/manager/property/type/name = Mytype
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyType"!
		 *
		 * @param string Last part of the class name
		 * @since 2018.01
		 * @category Developer
		 */

		/** mshop/media/manager/property/type/decorators/excludes
		 * Excludes decorators added by the "common" option from the media property type manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the media property type manager.
		 *
		 *  mshop/media/manager/property/type/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
		 * "mshop/common/manager/decorators/default" for the media property type manager.
		 *
		 * @param array List of decorator names
		 * @since 2018.01
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/media/manager/property/type/decorators/global
		 * @see mshop/media/manager/property/type/decorators/local
		 */

		/** mshop/media/manager/property/type/decorators/global
		 * Adds a list of globally available decorators only to the media property type manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the media property
		 * type manager.
		 *
		 *  mshop/media/manager/property/type/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the media
		 * property type manager.
		 *
		 * @param array List of decorator names
		 * @since 2018.01
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/media/manager/property/type/decorators/excludes
		 * @see mshop/media/manager/property/type/decorators/local
		 */

		/** mshop/media/manager/property/type/decorators/local
		 * Adds a list of local decorators only to the media property type manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\MShop\Media\Manager\Property\Type\Decorator\*") around the
		 * media property type manager.
		 *
		 *  mshop/media/manager/property/type/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\MShop\Media\Manager\Property\Type\Decorator\Decorator2" only
		 * to the media property type manager.
		 *
		 * @param array List of decorator names
		 * @since 2018.01
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/media/manager/property/type/decorators/excludes
		 * @see mshop/media/manager/property/type/decorators/global
		 */

		return $this->getSubManagerBase( 'media', 'property/type/' . $manager, $name );
	}


	/**
	 * Returns the config path for retrieving the configuration values.
	 *
	 * @return string Configuration path
	 */
	protected function getConfigPath() : string
	{
		/** mshop/media/manager/property/type/standard/insert/mysql
		 * Inserts a new media property type record into the database table
		 *
		 * @see mshop/media/manager/property/type/standard/insert/ansi
		 */

		/** mshop/media/manager/property/type/standard/insert/ansi
		 * Inserts a new media property type record into the database table
		 *
		 * Items with no ID yet (i.e. the ID is NULL) will be created in
		 * the database and the newly created ID retrieved afterwards
		 * using the "newid" SQL statement.
		 *
		 * The SQL statement must be a string suitable for being used as
		 * prepared statement. It must include question marks for binding
		 * the values from the media type item to the statement before they are
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
		 * @since 2018.01
		 * @category Developer
		 * @see mshop/media/manager/property/type/standard/update/ansi
		 * @see mshop/media/manager/property/type/standard/newid/ansi
		 * @see mshop/media/manager/property/type/standard/delete/ansi
		 * @see mshop/media/manager/property/type/standard/search/ansi
		 * @see mshop/media/manager/property/type/standard/count/ansi
		 */

		/** mshop/media/manager/property/type/standard/update/mysql
		 * Updates an existing media property type record in the database
		 *
		 * @see mshop/media/manager/property/type/standard/update/ansi
		 */

		/** mshop/media/manager/property/type/standard/update/ansi
		 * Updates an existing media property type record in the database
		 *
		 * Items which already have an ID (i.e. the ID is not NULL) will
		 * be updated in the database.
		 *
		 * The SQL statement must be a string suitable for being used as
		 * prepared statement. It must include question marks for binding
		 * the values from the media type item to the statement before they are
		 * sent to the database server. The order of the columns must
		 * correspond to the order in the saveItems() method, so the
		 * correct values are bound to the columns.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for updating records
		 * @since 2018.01
		 * @category Developer
		 * @see mshop/media/manager/property/type/standard/insert/ansi
		 * @see mshop/media/manager/property/type/standard/newid/ansi
		 * @see mshop/media/manager/property/type/standard/delete/ansi
		 * @see mshop/media/manager/property/type/standard/search/ansi
		 * @see mshop/media/manager/property/type/standard/count/ansi
		 */

		/** mshop/media/manager/property/type/standard/newid/mysql
		 * Retrieves the ID generated by the database when inserting a new record
		 *
		 * @see mshop/media/manager/property/type/standard/newid/ansi
		 */

		/** mshop/media/manager/property/type/standard/newid/ansi
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
		 *  SELECT currval('seq_mmedprty_id')
		 * For SQL Server:
		 *  SELECT SCOPE_IDENTITY()
		 * For Oracle:
		 *  SELECT "seq_mmedprty_id".CURRVAL FROM DUAL
		 *
		 * There's no way to retrive the new ID by a SQL statements that
		 * fits for most database servers as they implement their own
		 * specific way.
		 *
		 * @param string SQL statement for retrieving the last inserted record ID
		 * @since 2018.01
		 * @category Developer
		 * @see mshop/media/manager/property/type/standard/insert/ansi
		 * @see mshop/media/manager/property/type/standard/update/ansi
		 * @see mshop/media/manager/property/type/standard/delete/ansi
		 * @see mshop/media/manager/property/type/standard/search/ansi
		 * @see mshop/media/manager/property/type/standard/count/ansi
		 */

		/** mshop/media/manager/property/type/standard/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/media/manager/property/type/standard/delete/ansi
		 */

		/** mshop/media/manager/property/type/standard/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the media database.
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
		 * @since 2018.01
		 * @category Developer
		 * @see mshop/media/manager/property/type/standard/insert/ansi
		 * @see mshop/media/manager/property/type/standard/update/ansi
		 * @see mshop/media/manager/property/type/standard/newid/ansi
		 * @see mshop/media/manager/property/type/standard/search/ansi
		 * @see mshop/media/manager/property/type/standard/count/ansi
		 */

		/** mshop/media/manager/property/type/standard/search/mysql
		 * Retrieves the records matched by the given criteria in the database
		 *
		 * @see mshop/media/manager/property/type/standard/search/ansi
		 */

		/** mshop/media/manager/property/type/standard/search/ansi
		 * Retrieves the records matched by the given criteria in the database
		 *
		 * Fetches the records matched by the given criteria from the media
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
		 * @since 2018.01
		 * @category Developer
		 * @see mshop/media/manager/property/type/standard/insert/ansi
		 * @see mshop/media/manager/property/type/standard/update/ansi
		 * @see mshop/media/manager/property/type/standard/newid/ansi
		 * @see mshop/media/manager/property/type/standard/delete/ansi
		 * @see mshop/media/manager/property/type/standard/count/ansi
		 */

		/** mshop/media/manager/property/type/standard/count/mysql
		 * Counts the number of records matched by the given criteria in the database
		 *
		 * @see mshop/media/manager/property/type/standard/count/ansi
		 */

		/** mshop/media/manager/property/type/standard/count/ansi
		 * Counts the number of records matched by the given criteria in the database
		 *
		 * Counts all records matched by the given criteria from the media
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
		 * @since 2018.01
		 * @category Developer
		 * @see mshop/media/manager/property/type/standard/insert/ansi
		 * @see mshop/media/manager/property/type/standard/update/ansi
		 * @see mshop/media/manager/property/type/standard/newid/ansi
		 * @see mshop/media/manager/property/type/standard/delete/ansi
		 * @see mshop/media/manager/property/type/standard/search/ansi
		 */

		return 'mshop/media/manager/property/type/standard/';
	}


	/**
	 * Returns the search configuration for searching items.
	 *
	 * @return array Associative list of search keys and search definitions
	 */
	protected function getSearchConfig() : array
	{
		return $this->searchConfig;
	}
}
