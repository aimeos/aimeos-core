<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package MShop
 * @subpackage Media
 */


namespace Aimeos\MShop\Media\Manager;


/**
 * Default media manager implementation.
 *
 * @package MShop
 * @subpackage Media
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Media\Manager\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	/** mshop/media/manager/name
	 * Class name of the used media manager implementation
	 *
	 * Each default manager can be replace by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the manager factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\MShop\Media\Manager\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\MShop\Media\Manager\Mymanager
	 *
	 * then you have to set the this configuration option:
	 *
	 *  mshop/media/manager/name = Mymanager
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

	/** mshop/media/manager/decorators/excludes
	 * Excludes decorators added by the "common" option from the media manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to remove a decorator added via
	 * "mshop/common/manager/decorators/default" before they are wrapped
	 * around the media manager.
	 *
	 *  mshop/media/manager/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
	 * "mshop/common/manager/decorators/default" for the media manager.
	 *
	 * @param array List of decorator names
	 * @since 2014.03
	 * @category Developer
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/media/manager/decorators/global
	 * @see mshop/media/manager/decorators/local
	 */

	/** mshop/media/manager/decorators/global
	 * Adds a list of globally available decorators only to the media manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap global decorators
	 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the media manager.
	 *
	 *  mshop/media/manager/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the media
	 * manager.
	 *
	 * @param array List of decorator names
	 * @since 2014.03
	 * @category Developer
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/media/manager/decorators/excludes
	 * @see mshop/media/manager/decorators/local
	 */

	/** mshop/media/manager/decorators/local
	 * Adds a list of local decorators only to the media manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\MShop\Media\Manager\Decorator\*") around the media manager.
	 *
	 *  mshop/media/manager/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\MShop\Media\Manager\Decorator\Decorator2" only to the media
	 * manager.
	 *
	 * @param array List of decorator names
	 * @since 2014.03
	 * @category Developer
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/media/manager/decorators/excludes
	 * @see mshop/media/manager/decorators/global
	 */


	use \Aimeos\MShop\Common\Manager\ListsRef\Traits;
	use \Aimeos\MShop\Common\Manager\PropertyRef\Traits;


	private array $searchConfig = array(
		'media.id' => array(
			'label' => 'ID',
			'code' => 'media.id',
			'internalcode' => 'mmed."id"',
			'type' => 'integer',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_INT,
		),
		'media.siteid' => array(
			'label' => 'Site ID',
			'code' => 'media.siteid',
			'internalcode' => 'mmed."siteid"',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'media.type' => array(
			'label' => 'Type',
			'code' => 'media.type',
			'internalcode' => 'mmed."type"',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'media.label' => array(
			'label' => 'Label',
			'code' => 'media.label',
			'internalcode' => 'mmed."label"',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'media.domain' => array(
			'label' => 'Domain',
			'code' => 'media.domain',
			'internalcode' => 'mmed."domain"',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'media.languageid' => array(
			'label' => 'Language code',
			'code' => 'media.languageid',
			'internalcode' => 'mmed."langid"',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'media.mimetype' => array(
			'label' => 'Mime type',
			'code' => 'media.mimetype',
			'internalcode' => 'mmed."mimetype"',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'media.url' => array(
			'label' => 'URL',
			'code' => 'media.url',
			'internalcode' => 'mmed."link"',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'media.preview' => array(
			'label' => 'Preview URLs as JSON encoded string',
			'code' => 'media.preview',
			'internalcode' => 'mmed."preview"',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'media.previews' => array(
			'label' => 'Preview URLs as JSON encoded string',
			'code' => 'media.previews',
			'internalcode' => 'mmed."previews"',
			'type' => 'json',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'media.filesystem' => array(
			'label' => 'File sytem name',
			'code' => 'media.filesystem',
			'internalcode' => 'mmed."fsname"',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'media.status' => array(
			'label' => 'Status',
			'code' => 'media.status',
			'internalcode' => 'mmed."status"',
			'type' => 'integer',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_INT,
		),
		'media.ctime' => array(
			'code' => 'media.ctime',
			'internalcode' => 'mmed."ctime"',
			'label' => 'Create date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'media.mtime' => array(
			'code' => 'media.mtime',
			'internalcode' => 'mmed."mtime"',
			'label' => 'Modify date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'media.editor' => array(
			'code' => 'media.editor',
			'internalcode' => 'mmed."editor"',
			'label' => 'Editor',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'media:has' => array(
			'code' => 'media:has()',
			'internalcode' => ':site AND :key AND mmedli."id"',
			'internaldeps' => ['LEFT JOIN "mshop_media_list" AS mmedli ON ( mmedli."parentid" = mmed."id" )'],
			'label' => 'Media has list item, parameter(<domain>[,<list type>[,<reference ID>)]]',
			'type' => 'null',
			'internaltype' => 'null',
			'public' => false,
		),
		'media:prop' => array(
			'code' => 'media:prop()',
			'internalcode' => ':site AND :key AND mmedpr."id"',
			'internaldeps' => ['LEFT JOIN "mshop_media_property" AS mmedpr ON ( mmedpr."parentid" = mmed."id" )'],
			'label' => 'Media has property item, parameter(<property type>[,<language code>[,<property value>]])',
			'type' => 'null',
			'internaltype' => 'null',
			'public' => false,
		),
	);

	private ?string $languageId;


	/**
	 * Initializes the object.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 */
	public function __construct( \Aimeos\MShop\ContextIface $context )
	{
		parent::__construct( $context );

		/** mshop/media/manager/resource
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
		$this->setResourceName( $context->config()->get( 'mshop/media/manager/resource', 'db-media' ) );
		$this->languageId = $context->locale()->getLanguageId();

		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
		$level = $context->config()->get( 'mshop/media/manager/sitemode', $level );


		$this->searchConfig['media:has']['function'] = function( &$source, array $params ) use ( $level ) {

			$keys = [];

			foreach( (array) ( $params[1] ?? '' ) as $type ) {
				foreach( (array) ( $params[2] ?? '' ) as $id ) {
					$keys[] = $params[0] . '|' . ( $type ? $type . '|' : '' ) . $id;
				}
			}

			$sitestr = $this->siteString( 'mmedli."siteid"', $level );
			$keystr = $this->toExpression( 'mmedli."key"', $keys, ( $params[2] ?? null ) ? '==' : '=~' );
			$source = str_replace( [':site', ':key'], [$sitestr, $keystr], $source );

			return $params;
		};


		$this->searchConfig['media:prop']['function'] = function( &$source, array $params ) use ( $level ) {

			$keys = [];
			$langs = array_key_exists( 1, $params ) ? ( $params[1] ?? 'null' ) : '';

			foreach( (array) $langs as $lang ) {
				foreach( (array) ( $params[2] ?? '' ) as $val ) {
					$keys[] = substr( $params[0] . '|' . ( $lang === null ? 'null|' : ( $lang ? $lang . '|' : '' ) ) . $val, 0, 255 );
				}
			}

			$sitestr = $this->siteString( 'mmedpr."siteid"', $level );
			$keystr = $this->toExpression( 'mmedpr."key"', $keys, ( $params[2] ?? null ) ? '==' : '=~' );
			$source = str_replace( [':site', ':key'], [$sitestr, $keystr], $source );

			return $params;
		};
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Media\Manager\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$path = 'mshop/media/manager/submanagers';
		$default = ['lists', 'property', 'type'];

		foreach( $this->context()->config()->get( $path, $default ) as $domain ) {
			$this->object()->getSubManager( $domain )->clear( $siteids );
		}

		return $this->clearBase( $siteids, 'mshop/media/manager/delete' );
	}


	/**
	 * Copies the media item and the referenced files
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $item Media item whose files should be copied
	 * @return \Aimeos\MShop\Media\Item\Iface Copied media item with new files
	 */
	public function copy( \Aimeos\MShop\Media\Item\Iface $item ) : \Aimeos\MShop\Media\Item\Iface
	{
		$item = ( clone $item )->setId( null );

		$path = $item->getUrl();
		$previews = $item->getPreviews();
		$fsname = $item->getFileSystem();
		$fs = $this->context()->fs( $fsname );

		if( $fs->has( $path ) )
		{
			$newPath = $this->getPath( substr( basename( $path ), 9 ), 'files', $item->getMimeType() );
			$fs->copy( $path, $newPath );
			$item->setUrl( $newPath );
		}

		if( $fsname !== 'fs-mimeicon' && empty( $previews ) ) {
			return $this->scale( $item, true );
		}

		foreach( $previews as $size => $preview )
		{
			if( $fsname !== 'fs-mimeicon' && $fs->has( $preview ) )
			{
				$newPath = $this->getPath( substr( basename( $preview ), 9 ), 'preview', pathinfo( $preview, PATHINFO_EXTENSION ) );
				$fs->copy( $preview, $newPath );
				$previews[$size] = $newPath;
			}
		}

		return $item->setPreviews( $previews );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Media\Item\Iface New media item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values['media.siteid'] = $values['media.siteid'] ?? $this->context()->locale()->getSiteId();
		return $this->createItemBase( $values );
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $items List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Media\Manager\Iface Manager object for chaining method calls
	 */
	public function delete( $items ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/media/manager/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/media/manager/delete/ansi
		 */

		/** mshop/media/manager/delete/ansi
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
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/media/manager/insert/ansi
		 * @see mshop/media/manager/update/ansi
		 * @see mshop/media/manager/newid/ansi
		 * @see mshop/media/manager/search/ansi
		 * @see mshop/media/manager/count/ansi
		 */
		$cfgpath = 'mshop/media/manager/delete';

		$fs = $this->context()->fs( 'fs-media' );

		foreach( map( $items ) as $item )
		{
			if( $item instanceof \Aimeos\MShop\Media\Item\Iface && $item->getFileSystem() === 'fs-media' )
			{
				if( ( $path = $item->getUrl() ) && $fs->has( $path ) ) {
					$fs->rm( $path );
				}

				$this->deletePreviews( $item, $item->getPreviews() );
			}
		}

		return $this->deleteItemsBase( $items, $cfgpath )->deleteRefItems( $items );
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
		if( $default !== false )
		{
			$object = $this->filterBase( 'media', $default );
			$langid = $this->context()->locale()->getLanguageId();

			if( $langid !== null )
			{
				$temp = array(
					$object->compare( '==', 'media.languageid', $langid ),
					$object->compare( '==', 'media.languageid', null ),
				);

				$expr = array(
					$object->getConditions(),
					$object->or( $temp ),
				);

				$object->setConditions( $object->and( $expr ) );
			}

			return $object;
		}

		return parent::filter();
	}


	/**
	 * Returns an item for the given ID.
	 *
	 * @param string $id ID of the item that should be retrieved
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Media\Item\Iface Returns the media item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function get( string $id, array $ref = [], ?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->getItemBase( 'media.id', $id, $ref, $default );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		$path = 'mshop/media/manager/submanagers';
		$default = ['lists', 'property'];

		return $this->getResourceTypeBase( 'media', $path, $default, $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		/** mshop/media/manager/submanagers
		 * List of manager names that can be instantiated by the media manager
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
		$path = 'mshop/media/manager/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, [], $withsub );
	}


	/**
	 * Returns a new manager for media extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g stock, tags, locations, etc.
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->getSubManagerBase( 'media', $manager, $name );
	}


	/**
	 * Adds a new item to the storage or updates an existing one.
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $item New item that should be saved to the storage
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Media\Item\Iface $item Updated item including the generated ID
	 */
	protected function saveItem( \Aimeos\MShop\Media\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Media\Item\Iface
	{
		if( !$item->isModified() )
		{
			$item = $this->savePropertyItems( $item, 'media', $fetch );
			return $this->saveListItems( $item, 'media', $fetch );
		}

		$context = $this->context();
		$conn = $context->db( $this->getResourceName() );

		$id = $item->getId();
		$date = date( 'Y-m-d H:i:s' );
		$columns = $this->object()->getSaveAttributes();

		if( $id === null )
		{
			/** mshop/media/manager/insert/mysql
			 * Inserts a new media record into the database table
			 *
			 * @see mshop/media/manager/insert/ansi
			 */

			/** mshop/media/manager/insert/ansi
			 * Inserts a new media record into the database table
			 *
			 * Items with no ID yet (i.e. the ID is NULL) will be created in
			 * the database and the newly created ID retrieved afterwards
			 * using the "newid" SQL statement.
			 *
			 * The SQL statement must be a string suitable for being used as
			 * prepared statement. It must include question marks for binding
			 * the values from the media item to the statement before they are
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
			 * @see mshop/media/manager/update/ansi
			 * @see mshop/media/manager/newid/ansi
			 * @see mshop/media/manager/delete/ansi
			 * @see mshop/media/manager/search/ansi
			 * @see mshop/media/manager/count/ansi
			 */
			$path = 'mshop/media/manager/insert';
			$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ) );
		}
		else
		{
			/** mshop/media/manager/update/mysql
			 * Updates an existing media record in the database
			 *
			 * @see mshop/media/manager/update/ansi
			 */

			/** mshop/media/manager/update/ansi
			 * Updates an existing media record in the database
			 *
			 * Items which already have an ID (i.e. the ID is not NULL) will
			 * be updated in the database.
			 *
			 * The SQL statement must be a string suitable for being used as
			 * prepared statement. It must include question marks for binding
			 * the values from the media item to the statement before they are
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
			 * @see mshop/media/manager/insert/ansi
			 * @see mshop/media/manager/newid/ansi
			 * @see mshop/media/manager/delete/ansi
			 * @see mshop/media/manager/search/ansi
			 * @see mshop/media/manager/count/ansi
			 */
			$path = 'mshop/media/manager/update';
			$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ), false );
		}

		$idx = 1;
		$stmt = $this->getCachedStatement( $conn, $path, $sql );

		foreach( $columns as $name => $entry ) {
			$stmt->bind( $idx++, $item->get( $name ), $entry->getInternalType() );
		}

		$stmt->bind( $idx++, $item->getLanguageId() );
		$stmt->bind( $idx++, $item->getType() );
		$stmt->bind( $idx++, $item->getLabel() );
		$stmt->bind( $idx++, $item->getMimeType() );
		$stmt->bind( $idx++, $item->getUrl() );
		$stmt->bind( $idx++, $item->getStatus(), \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		$stmt->bind( $idx++, $item->getFileSystem() );
		$stmt->bind( $idx++, $item->getDomain() );
		$stmt->bind( $idx++, json_encode( $item->getPreviews(), JSON_FORCE_OBJECT ) );
		$stmt->bind( $idx++, $date ); // mtime
		$stmt->bind( $idx++, $context->editor() );

		if( $id !== null ) {
			$stmt->bind( $idx++, $context->locale()->getSiteId() . '%' );
			$stmt->bind( $idx++, $id, \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		} else {
			$stmt->bind( $idx++, $this->siteId( $item->getSiteId(), \Aimeos\MShop\Locale\Manager\Base::SITE_SUBTREE ) );
			$stmt->bind( $idx++, $date ); // ctime
		}

		$stmt->execute()->finish();

		if( $id === null )
		{
			/** mshop/media/manager/newid/mysql
			 * Retrieves the ID generated by the database when inserting a new record
			 *
			 * @see mshop/media/manager/newid/ansi
			 */

			/** mshop/media/manager/newid/ansi
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
			 *  SELECT currval('seq_mmed_id')
			 * For SQL Server:
			 *  SELECT SCOPE_IDENTITY()
			 * For Oracle:
			 *  SELECT "seq_mmed_id".CURRVAL FROM DUAL
			 *
			 * There's no way to retrive the new ID by a SQL statements that
			 * fits for most database servers as they implement their own
			 * specific way.
			 *
			 * @param string SQL statement for retrieving the last inserted record ID
			 * @since 2014.03
			 * @category Developer
			 * @see mshop/media/manager/insert/ansi
			 * @see mshop/media/manager/update/ansi
			 * @see mshop/media/manager/delete/ansi
			 * @see mshop/media/manager/search/ansi
			 * @see mshop/media/manager/count/ansi
			 */
			$path = 'mshop/media/manager/newid';
			$id = $this->newId( $conn, $path );
		}

		$item->setId( $id );

		$item = $this->savePropertyItems( $item, 'media', $fetch );
		return $this->saveListItems( $item, 'media', $fetch );
	}


	/**
	 * Rescales the original file to preview files referenced by the media item
	 *
	 * The height/width configuration for scaling
	 * - mshop/media/<files|preview>/maxheight
	 * - mshop/media/<files|preview>/maxwidth
	 * - mshop/media/<files|preview>/force-size
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $item Media item whose files should be scaled
	 * @param bool $force True to enforce creating new preview images
	 * @return \Aimeos\MShop\Media\Item\Iface Rescaled media item
	 */
	public function scale( \Aimeos\MShop\Media\Item\Iface $item, bool $force = false ) : \Aimeos\MShop\Media\Item\Iface
	{
		$url = $item->getUrl();

		if( empty( $url ) || \Aimeos\Base\Str::starts( $url, 'data:' ) ) {
			return $item->setPreview( $url );
		}

		$context = $this->context();
		$fs = $context->fs( $item->getFileSystem() );
		$is = ( $fs instanceof \Aimeos\Base\Filesystem\MetaIface ? true : false );

		if( !$force && !empty( $item->getPreviews() ) && preg_match( '#^[a-zA-Z]{2,6}://#', $url ) !== 1
			&& ( $is && date( 'Y-m-d H:i:s', $fs->time( $url ) ) < $item->getTimeModified() || $fs->has( $url ) )
		) {
			return $item;
		}

		$media = $this->getFile( $url );

		if( $media instanceof \Aimeos\MW\Media\Image\Iface )
		{
			$previews = [];
			$old = $item->getPreviews();
			$domain = $item->getDomain();

			foreach( $this->createPreviews( $media, $domain, $item->getType() ) as $width => $mediaFile )
			{
				$mime = $this->getMime( $mediaFile );
				$path = $old[$width] ?? $this->getPath( $url, $mime, $domain ?: '-' );

				$this->store( $mediaFile->save( null, $mime ), $path, $fs );
				$previews[$width] = $path;
				unset( $old[$width] );
			}

			$item = $this->deletePreviews( $item, $old );
			$item->setPreviews( $previews );

			$this->call( 'scaled', $item, $media );
		}

		return $item;
	}


	/**
	 * Returns the item objects matched by the given search criteria.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Media\Item\Iface with ids as keys
	 */
	public function search( \Aimeos\Base\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$map = [];
		$context = $this->context();
		$conn = $context->db( $this->getResourceName() );

			$required = array( 'media' );

			/** mshop/media/manager/sitemode
			 * Mode how items from levels below or above in the site tree are handled
			 *
			 * By default, only items from the current site are fetched from the
			 * storage. If the ai-sites extension is installed, you can create a
			 * tree of sites. Then, this setting allows you to define for the
			 * whole media domain if items from parent sites are inherited,
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
			$level = $context->config()->get( 'mshop/media/manager/sitemode', $level );

			/** mshop/media/manager/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see mshop/media/manager/search/ansi
			 */

			/** mshop/media/manager/search/ansi
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
			 * @since 2014.03
			 * @category Developer
			 * @see mshop/media/manager/insert/ansi
			 * @see mshop/media/manager/update/ansi
			 * @see mshop/media/manager/newid/ansi
			 * @see mshop/media/manager/delete/ansi
			 * @see mshop/media/manager/count/ansi
			 */
			$cfgPathSearch = 'mshop/media/manager/search';

			/** mshop/media/manager/count/mysql
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * @see mshop/media/manager/count/ansi
			 */

			/** mshop/media/manager/count/ansi
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
			 * @since 2014.03
			 * @category Developer
			 * @see mshop/media/manager/insert/ansi
			 * @see mshop/media/manager/update/ansi
			 * @see mshop/media/manager/newid/ansi
			 * @see mshop/media/manager/delete/ansi
			 * @see mshop/media/manager/search/ansi
			 */
			$cfgPathCount = 'mshop/media/manager/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== null )
			{
				if( ( $row['media.previews'] = json_decode( $config = $row['media.previews'], true ) ) === null )
				{
					$msg = sprintf( 'Invalid JSON as result of search for ID "%2$s" in "%1$s": %3$s', 'mshop_media.previews', $row['media.id'], $config );
					$this->context()->logger()->warning( $msg, 'core/media' );
					$row['media.previews'] = [];
				}
				$map[$row['media.id']] = $row;
			}

		$propItems = []; $name = 'media/property';
		if( isset( $ref[$name] ) || in_array( $name, $ref, true ) )
		{
			$propTypes = isset( $ref[$name] ) && is_array( $ref[$name] ) ? $ref[$name] : null;
			$propItems = $this->getPropertyItems( array_keys( $map ), 'media', $propTypes );
		}

		return $this->buildItems( $map, $ref, 'media', $propItems );
	}


	/**
	 * Creates a new media item instance.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of items referenced
	 * @param \Aimeos\MShop\Common\Item\Property\Iface[] $propItems List of property items
	 * @return \Aimeos\MShop\Media\Item\Iface New media item
	 */
	protected function createItemBase( array $values = [], array $listItems = [], array $refItems = [],
		array $propItems = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values['.languageid'] = $this->languageId;

		return new \Aimeos\MShop\Media\Item\Standard( $values, $listItems, $refItems, $propItems );
	}


	/**
	 * Creates scaled images according to the configuration settings
	 *
	 * @param \Aimeos\MW\Media\Image\Iface $media Media object
	 * @param string $domain Domain the item is from, e.g. product, catalog, etc.
	 * @param string $type Type of the item within the given domain, e.g. default, stage, etc.
	 * @return \Aimeos\MW\Media\Image\Iface[] Associative list of image width as keys and scaled media object as values
	 */
	protected function createPreviews( \Aimeos\MW\Media\Image\Iface $media, string $domain, string $type ) : array
	{
		$list = [];
		$config = $this->context()->config();

		/** controller/common/media/previews
		 * Scaling options for preview images
		 *
		 * For responsive images, several preview images of different sizes are
		 * generated. This setting controls how many preview images are generated,
		 * what's their maximum width and height and if the given width/height is
		 * enforced by cropping images that doesn't fit.
		 *
		 * The setting must consist of a list image size definitions like:
		 *
		 *  [
		 *    ['maxwidth' => 240, 'maxheight' => 320, 'force-size' => true],
		 *    ['maxwidth' => 720, 'maxheight' => 960, 'force-size' => false],
		 *    ['maxwidth' => 2160, 'maxheight' => 2880, 'force-size' => false],
		 *  ]
		 *
		 * "maxwidth" sets the maximum allowed width of the image whereas
		 * "maxheight" does the same for the maximum allowed height. If both
		 * values are given, the image is scaled proportionally so it fits into
		 * the box defined by both values. In case the image has different
		 * proportions than the specified ones and "force-size" is false, the
		 * image is resized to fit entirely into the specified box. One side of
		 * the image will be shorter than it would be possible by the specified
		 * box.
		 *
		 * If "force-size" is true, scaled images that doesn't fit into the
		 * given maximum width/height are centered and then cropped. By default,
		 * images aren't cropped.
		 *
		 * The values for "maxwidth" and "maxheight" can also be null or not
		 * used. In that case, the width or height or both is unbound. If none
		 * of the values are given, the image won't be scaled at all. If only
		 * one value is set, the image will be scaled exactly to the given width
		 * or height and the other side is scaled proportionally.
		 *
		 * You can also define different preview sizes for different domains (e.g.
		 * for catalog images) and for different types (e.g. catalog stage images).
		 * Use configuration settings like
		 *
		 *  controller/common/media/<domain>/previews
		 *  controller/common/media/<domain>/<type>/previews
		 *
		 * for example:
		 *
		 *  controller/common/media/catalog/previews => [
		 *    ['maxwidth' => 240, 'maxheight' => 320, 'force-size' => true],
		 *  ]
		 *  controller/common/media/catalog/previews => [
		 *    ['maxwidth' => 400, 'maxheight' => 300, 'force-size' => false]
		 *  ]
		 *  controller/common/media/catalog/stage/previews => [
		 *    ['maxwidth' => 360, 'maxheight' => 320, 'force-size' => true],
		 *    ['maxwidth' => 720, 'maxheight' => 480, 'force-size' => true]
		 *  ]
		 *
		 * These settings will create two preview images for catalog stage images,
		 * one with a different size for all other catalog images and all images
		 * from other domains will be sized to 240x320px. The available domains
		 * which can have images are:
		 *
		 * * attribute
		 * * catalog
		 * * product
		 * * service
		 * * supplier
		 *
		 * There are a few image types included per domain ("default" is always
		 * available). You can also add your own types in the admin backend and
		 * extend the frontend to display them where you need them.
		 *
		 * @param array List of image size definitions
		 * @category Developer
		 * @category User
		 * @since 2019.07
		 */
		$previews = $config->get( 'controller/common/media/previews', [] );
		$previews = $config->get( 'controller/common/media/' . $domain . '/previews', $previews );
		$previews = $config->get( 'controller/common/media/' . $domain . '/' . $type . '/previews', $previews );

		foreach( $previews as $entry )
		{
			$force = $entry['force-size'] ?? 0;
			$maxwidth = $entry['maxwidth'] ?? null;
			$maxheight = $entry['maxheight'] ?? null;

			if( $this->call( 'filterPreviews', $media, $domain, $type, $maxwidth, $maxheight, $force ) )
			{
				$file = $media->scale( $maxwidth, $maxheight, $force );
				$width = $file->getWidth();
				$list[$width] = $file;
			}
		}

		return $list;
	}


	/**
	 * Removes the previes images from the storage
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $item Media item which will contains the image URLs afterwards
	 * @param array List of preview paths to remove
	 * @return \Aimeos\MShop\Media\Item\Iface Media item with preview images removed
	 */
	protected function deletePreviews( \Aimeos\MShop\Media\Item\Iface $item, array $paths ) : \Aimeos\MShop\Media\Item\Iface
	{
		if( !empty( $paths = $this->call( 'removePreviews', $item, $paths ) ) )
		{
			$fs = $this->context()->fs( $item->getFileSystem() );

			foreach( $paths as $preview )
			{
				if( $preview && $fs->has( $preview ) ) {
					$fs->rm( $preview );
				}
			}
		}

		return $item;
	}


	/**
	 * Tests if the preview image should be created
	 *
	 * @param \Aimeos\MW\Media\Image\Iface $media Media object
	 * @param string $domain Domain the item is from, e.g. product, catalog, etc.
	 * @param string $type Type of the item within the given domain, e.g. default, stage, etc.
	 * @param int|null $width New width of the image or null for automatic calculation
	 * @param int|null $height New height of the image or null for automatic calculation
	 * @param int $fit "0" keeps image ratio, "1" adds padding while "2" crops image to enforce image size
	 */
	protected function filterPreviews( \Aimeos\MW\Media\Image\Iface $media, string $domain, string $type,
		?int $maxwidth, ?int $maxheight, int $force ) : bool
	{
		return true;
	}


	/**
	 * Returns the file content of the file or URL
	 *
	 * @param string $path Path to the file or URL
	 * @return string File content
	 * @throws \Aimeos\MShop\Media\Exception If no file is found
	 */
	protected function getContent( string $path ) : string
	{
		if( $path )
		{
			if( preg_match( '#^[a-zA-Z]{1,10}://#', $path ) === 1 )
			{
				if( ( $content = @file_get_contents( $path ) ) === false ) {
					throw new \Aimeos\MShop\Media\Exception( sprintf( 'Downloading file "%1$s" failed', $path ) );
				}

				return $content;
			}

			$fs = $this->context()->fs( 'fs-media' );

			if( $fs->has( $path ) ) {
				return $fs->read( $path );
			}
		}

		throw new \Aimeos\MShop\Media\Exception( sprintf( 'File "%1$s" not found', $path ) );
	}


	/**
	 * Returns the media object for the given file name
	 *
	 * @param string $file Path to the file
	 * @return \Aimeos\MW\Media\Iface Media object
	 */
	protected function getFile( string $filepath ) : \Aimeos\MW\Media\Iface
	{
		/** controller/common/media/options
		 * Options used for processing the uploaded media files
		 *
		 * When uploading a file, a preview image for that file is generated if
		 * possible (especially for images). You can configure certain options
		 * for the generated images, namely the implementation of the scaling
		 * algorithm and the quality of the resulting images with
		 *
		 *  array(
		 *  	'image' => array(
		 *  		'name' => 'Imagick',
		 *  		'quality' => 75,
		 * 			'background' => '#f8f8f8' // only if "force-size" is true
		 *  	)
		 *  )
		 *
		 * @param array Multi-dimendional list of configuration options
		 * @since 2016.01
		 * @category Developer
		 * @category User
		 */
		$options = $this->context()->config()->get( 'controller/common/media/options', [] );

		return \Aimeos\MW\Media\Factory::get( $this->getContent( $filepath ), $options );
	}


	/**
	 * Creates a new file path from the given arguments
	 *
	 * @param string $filepath Original file name, can contain the path as well
	 * @param string $mimetype Mime type
	 * @param string $domain data domain
	 * @return string New file name including the file path
	 */
	protected function getPath( string $filepath, string $mimetype, string $domain ) : string
	{
		$context = $this->context();

		/** controller/common/media/extensions
		 * Available files extensions for mime types of uploaded files
		 *
		 * Uploaded files should have the right file extension (e.g. ".jpg" for
		 * JPEG images) so files are recognized correctly if downloaded by users.
		 * The extension of the uploaded file can't be trusted and only its mime
		 * type can be determined automatically. This configuration setting
		 * provides the file extensions for the configured mime types. You can
		 * add more mime type / file extension combinations if required.
		 *
		 * @param array Associative list of mime types as keys and file extensions as values
		 * @since 2018.04
		 * @category Developer
		 */
		$list = $context->config()->get( 'controller/common/media/extensions', [] );

		$filename = basename( $filepath );
		$filename = \Aimeos\Base\Str::slug( substr( $filename, 0, strrpos( $filename, '.' ) ?: null ) );
		$filename = substr( md5( $filename . getmypid() . microtime( true ) ), -8 ) . '_' . $filename;

		$ext = isset( $list[$mimetype] ) ? '.' . $list[$mimetype] : '';
		$siteid = $context->locale()->getSiteId();

		// the "d" after {siteid} is the required extension for Windows (no dots at the end allowed)
		return "{$siteid}d/{$domain}/{$filename[0]}/{$filename[1]}/{$filename}{$ext}";
	}


	/**
	 * Returns the mime type for the new image
	 *
	 * @param \Aimeos\MW\Media\Iface $media Media object
	 * @return string New mime type
	 * @throws \Aimeos\Controller\Common\Exception If no mime types are configured
	 */
	protected function getMime( \Aimeos\MW\Media\Iface $media ) : string
	{
		$mimetype = $media->getMimetype();
		$config = $this->context()->config();

		/** controller/common/media/files/allowedtypes
		 * A list of image mime types that are allowed for uploaded image files
		 *
		 * The list of allowed image types must be explicitly configured for the
		 * uploaded image files. Trying to upload and store an image file not
		 * available in the list of allowed mime types will result in an exception.
		 *
		 * @param array List of image mime types
		 * @since 2016.01
		 * @category Developer
		 * @category User
		 */

		/** controller/common/media/preview/allowedtypes
		 * A list of image mime types that are allowed for preview image files
		 *
		 * The list of allowed image types must be explicitly configured for the
		 * preview image files. Trying to create a preview image whose mime type
		 * is not available in the list of allowed mime types will result in an
		 * exception.
		 *
		 * @param array List of image mime types
		 * @since 2016.01
		 * @category Developer
		 * @category User
		 */
		$default = [
			'image/webp', 'image/jpeg', 'image/png', 'image/gif', 'image/svg+xml',
			'application/epub+zip', 'application/pdf', 'application/zip',
			'video/mp4', 'video/webm',
			'audio/mpeg', 'audio/ogg', 'audio/weba'
		];
		$allowed = $config->get( 'controller/common/media/preview/allowedtypes', $default );

		if( in_array( $mimetype, ['image/jpeg', 'image/png'] )
			&& !empty( $supported = $media::supports( $allowed ) ) && ( $imgtype = reset( $supported ) ) !== false
		) {
			return $imgtype;
		}

		return $mimetype;
	}


	/**
	 * Returns the preview images to be deleted
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $item Media item with new preview URLs
	 * @param array List of preview paths to remove
	 * @return iterable List of preview URLs to remove
	 */
	protected function removePreviews( \Aimeos\MShop\Media\Item\Iface $item, array $paths ) : iterable
	{
		$previews = $item->getPreviews();

		// don't delete first (smallest) image because it may be referenced in past orders
		if( $item->getDomain() === 'product' && in_array( key( $previews ), $paths ) ) {
			return array_slice( $paths, 1 );
		}

		return $paths;
	}


	/**
	 * Called after the image has been scaled
	 * Can be used to update the media item with image information.
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $item Media item with new preview URLs
	 * @param \Aimeos\MW\Media\Image\Iface $media Media object
	 */
	protected function scaled( \Aimeos\MShop\Media\Item\Iface $item, \Aimeos\MW\Media\Image\Iface $media )
	{
	}


	/**
	 * Stores the file content
	 *
	 * @param string $content File content
	 * @param string $filepath Path of the new file
	 * @param \Aimeos\Base\Filesystem\Iface $fs File system object
	 * @return \Aimeos\Controller\Common\Media\Iface Self object for fluent interface
	 */
	protected function store( string $content, string $filepath, \Aimeos\Base\Filesystem\Iface $fs ) : Iface
	{
		$fs->write( $filepath, $content );
		return $this;
	}
}
