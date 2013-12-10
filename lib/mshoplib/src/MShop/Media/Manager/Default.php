<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Media
 */


/**
 * Default media manager implementation.
 *
 * @package MShop
 * @subpackage Media
 */
class MShop_Media_Manager_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Media_Manager_Interface
{
	private $_searchConfig = array(
		'media.id' => array(
			'label' => 'Media ID',
			'code' => 'media.id',
			'internalcode' => 'mmed."id"',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'media.siteid' => array(
			'label' => 'Media site ID',
			'code' => 'media.siteid',
			'internalcode' => 'mmed."siteid"',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'media.typeid' => array(
			'label' => 'Media type ID',
			'code' => 'media.typeid',
			'internalcode' => 'mmed."typeid"',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'media.languageid' => array(
			'label' => 'Media language code',
			'code' => 'media.languageid',
			'internalcode' => 'mmed."langid"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.domain' => array(
			'label' => 'Media domain',
			'code' => 'media.domain',
			'internalcode' => 'mmed."domain"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.label' => array(
			'label' => 'Media label',
			'code' => 'media.label',
			'internalcode' => 'mmed."label"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.mimetype' => array(
			'label' => 'Media mimetype',
			'code' => 'media.mimetype',
			'internalcode' => 'mmed."mimetype"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.url' => array(
			'label' => 'Media URL',
			'code' => 'media.url',
			'internalcode' => 'mmed."link"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.preview' => array(
			'label' => 'Media preview URL',
			'code' => 'media.preview',
			'internalcode' => 'mmed."preview"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.status' => array(
			'label' => 'Media status',
			'code' => 'media.status',
			'internalcode' => 'mmed."status"',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'media.ctime'=> array(
			'code'=>'media.ctime',
			'internalcode'=>'mmed."ctime"',
			'label'=>'Media create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.mtime'=> array(
			'code'=>'media.mtime',
			'internalcode'=>'mmed."mtime"',
			'label'=>'Media modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.editor'=> array(
			'code'=>'media.editor',
			'internalcode'=>'mmed."editor"',
			'label'=>'Media editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);


	private $_typeSearchConfig = array(
		'media.type.id' => array(
			'label' => 'Media type ID',
			'code' => 'media.type.id',
			'internalcode' => 'mmedty."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_media_type" AS mmedty ON ( mmed."typeid" = mmedty."id" )' ),
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'media.type.siteid' => array(
			'label' => 'Media type site ID',
			'code' => 'media.type.siteid',
			'internalcode' => 'mmedty."siteid"',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'media.type.code' => array(
			'label' => 'Media type code',
			'code' => 'media.type.code',
			'internalcode' => 'mmedty."code"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.type.domain' => array(
			'label' => 'Media type domain',
			'code' => 'media.type.domain',
			'internalcode' => 'mmedty."domain"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.type.label' => array(
			'label' => 'Media type label',
			'code' => 'media.type.label',
			'internalcode' => 'mmedty."label"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.type.status' => array(
			'label' => 'Media type status',
			'code' => 'media.type.status',
			'internalcode' => 'mmedty."status"',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'media.type.ctime'=> array(
			'code'=>'media.type.ctime',
			'internalcode'=>'mmedty."ctime"',
			'label'=>'Media type create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.type.mtime'=> array(
			'code'=>'media.type.mtime',
			'internalcode'=>'mmedty."mtime"',
			'label'=>'Media type modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.type.editor'=> array(
			'code'=>'media.type.editor',
			'internalcode'=>'mmedty."editor"',
			'label'=>'Media type editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);

	private $_listSearchConfig = array(
		'media.list.id'=> array(
			'code'=>'media.list.id',
			'internalcode'=>'mmedli."id"',
			'internaldeps'=> array( 'LEFT JOIN "mshop_media_list" AS mmedli ON ( mmed."id" = mmedli."parentid" )' ),
			'label'=>'Media list ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'media.list.siteid'=> array(
			'code'=>'media.list.siteid',
			'internalcode'=>'mmedli."siteid"',
			'label'=>'Media list site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'media.list.parentid'=> array(
			'code'=>'media.list.parentid',
			'internalcode'=>'mmedli."parentid"',
			'label'=>'Media list media ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'media.list.domain'=> array(
			'code'=>'media.list.domain',
			'internalcode'=>'mmedli."domain"',
			'label'=>'Media list domain',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.list.typeid'=> array(
			'code'=>'media.list.typeid',
			'internalcode'=>'mmedli."typeid"',
			'label'=>'Media list type ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'media.list.refid'=> array(
			'code'=>'media.list.refid',
			'internalcode'=>'mmedli."refid"',
			'label'=>'Media list reference ID',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.list.datestart' => array(
			'code'=>'media.list.datestart',
			'internalcode'=>'mmedli."start"',
			'label'=>'Media list start date',
			'type'=> 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.list.dateend' => array(
			'code'=>'media.list.dateend',
			'internalcode'=>'mmedli."end"',
			'label'=>'Media list end date',
			'type'=> 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.list.config' => array(
			'code'=>'media.list.config',
			'internalcode'=>'mmedli."config"',
			'label'=>'Media list config',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.list.position' => array(
			'code'=>'media.list.position',
			'internalcode'=>'mmedli."pos"',
			'label'=>'Media list position',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'media.list.ctime'=> array(
			'code'=>'media.list.ctime',
			'internalcode'=>'mmedli."ctime"',
			'label'=>'Media list create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.list.mtime'=> array(
			'code'=>'media.list.mtime',
			'internalcode'=>'mmedli."mtime"',
			'label'=>'Media list modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.list.editor'=> array(
			'code'=>'media.list.editor',
			'internalcode'=>'mmedli."editor"',
			'label'=>'Media list editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);

	private $_listTypeSearchConfig = array(
		'media.list.type.id' => array(
			'code'=>'media.list.type.id',
			'internalcode'=>'mmedlity."id"',
			'internaldeps'=> array( 'LEFT JOIN "mshop_media_list_type" AS mmedlity ON ( mmedli."typeid" = mmedlity."id" )' ),
			'label'=>'Media list type Id',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'media.list.type.siteid' => array(
			'code'=>'media.list.type.siteid',
			'internalcode'=>'mmedlity."siteid"',
			'label'=>'Media list type site Id',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'media.list.type.code' => array(
			'code'=>'media.list.type.code',
			'internalcode'=>'mmedlity."code"',
			'label'=>'Media list type code',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.list.type.domain' => array(
			'code'=>'media.list.type.domain',
			'internalcode'=>'mmedlity."domain"',
			'label'=>'Media list type domain',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.list.type.label' => array(
			'label' => 'Media list type label',
			'code' => 'media.list.type.label',
			'internalcode' => 'mmedlity."label"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.list.type.status' => array(
			'label' => 'Media list type status',
			'code' => 'media.list.type.status',
			'internalcode' => 'mmedlity."status"',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'media.list.type.ctime'=> array(
			'code'=>'media.list.type.ctime',
			'internalcode'=>'mmedlity."ctime"',
			'label'=>'Media list type create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.list.type.mtime'=> array(
			'code'=>'media.list.type.mtime',
			'internalcode'=>'mmedlity."mtime"',
			'label'=>'Media list type modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.list.type.editor'=> array(
			'code'=>'media.list.type.editor',
			'internalcode'=>'mmedlity."editor"',
			'label'=>'Media list type editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = array();

		foreach ( $this->_searchConfig as $key => $fields ) {
			$list[$key] = new MW_Common_Criteria_Attribute_Default($fields);
		}

		if( $withsub === true )
		{
			$config = $this->_getContext()->getConfig();

			foreach( $config->get( 'classes/media/manager/submanagers', array( 'type', 'list' ) ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Creates a new media object.
	 *
	 * @return MShop_Media_Item_Interface New media object
	 */
	public function createItem()
	{
		$values = array('siteid' => $this->_getContext()->getLocale()->getSiteId());
		return $this->_createItem($values);
	}


	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		$path = 'mshop/media/manager/default/item/delete';
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( $path, $path ) );
	}


	/**
	 * Returns an item for the given ID.
	 *
	 * @param integer $id ID of the item that should be retrieved
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Media_Item_Interface Returns the media item of the given id
	 * @throws MShop_Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'media.id', $id, $ref );
	}


	/**
	 * Adds a new item to the storage or updates an existing one.
	 *
	 * @param MShop_Media_Item_Interface $item New item that should be saved to the storage
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Media_Item_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Media_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if( !$item->isModified() ) { return; }

		$context = $this->_getContext();
		$config = $context->getConfig();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$id = $item->getId();

			$path = 'mshop/media/manager/default/item/';
			$path .= ( $id === null ) ? 'insert' : 'update';

			$stmt = $this->_getCachedStatement( $conn, $path );

			$stmt->bind( 1, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $item->getLanguageId() );
			$stmt->bind( 3, $item->getTypeId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 4, $item->getLabel() );
			$stmt->bind( 5, $item->getMimeType() );
			$stmt->bind( 6, $item->getUrl() );
			$stmt->bind( 7, $item->getStatus(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 8, $item->getDomain());
			$stmt->bind( 9, $item->getPreview() );
			$stmt->bind(10, date( 'Y-m-d H:i:s', time() ) ); // mtime
			$stmt->bind(11, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind(12, $id, MW_DB_Statement_Abstract::PARAM_INT);
				$item->setId($id); //is not modified anymore
			} else {
				$stmt->bind(12, date( 'Y-m-d H:i:s', time() ) ); // ctime
			}

			$result = $stmt->execute()->finish();

			if( $id === null && $fetch === true ) {
				$path = 'mshop/media/manager/default/item/newid';
				$item->setId( $this->_newId( $conn, $config->get($path, $path) ) );
			}

			$dbm->release($conn);
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}
	}


	/**
	 * Returns the item objects matched by the given search criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search criteria object
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @param integer &$total Number of items that are available in total
	 * @return array List of items implementing MShop_Media_Item_Interface
	 * @throws MShop_Media_Exception If creating items failed
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$map = $typeIds = array();
		$dbm = $this->_getContext()->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$level = MShop_Locale_Manager_Abstract::SITE_ALL;
			$cfgPathSearch = 'mshop/media/manager/default/item/search';
			$cfgPathCount =  'mshop/media/manager/default/item/count';
			$required = array( 'media' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false )
			{
				$map[ $row['id'] ] = $row;
				$typeIds[ $row['typeid'] ] = null;
			}

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}

		if( !empty( $typeIds ) )
		{
			$typeManager = $this->getSubManager( 'type' );
			$typeSearch = $typeManager->createSearch();
			$typeSearch->setConditions( $typeSearch->compare( '==', 'media.type.id', array_keys( $typeIds ) ) );
			$typeSearch->setSlice( 0, $search->getSliceSize() );
			$typeItems = $typeManager->searchItems( $typeSearch );

			foreach( $map as $id => $row )
			{
				if( isset( $typeItems[ $row['typeid'] ] ) ) {
					$map[$id]['type'] = $typeItems[ $row['typeid'] ]->getCode();
				}
			}
		}

		return $this->_buildItems( $map, $ref, 'media' );
	}


	/**
	 * creates a search object and sets base criteria
	 *
	 * @param boolean $default
	 * @return MW_Common_Criteria_Interface
	 */
	public function createSearch($default = false)
	{
		if( $default === true )
		{
			$object = $this->_createSearch( 'media' );

			$temp[] = $object->compare( '==', 'media.languageid', $this->_getContext()->getLocale()->getLanguageId() );
			$temp[] = $object->compare( '==', 'media.languageid', null );

			$expr[] = $object->combine( '||', $temp );
			$expr[] = $object->getConditions();

			$object->setConditions( $object->combine( '&&', $expr ) );

			return $object;
		}

		return parent::createSearch();
	}


	/**
	 * Returns a new manager for product extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g stock, tags, locations, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		switch( $manager )
		{
			case 'list':
				$typeManager = $this->_getTypeManager( 'media', 'list/type', null, $this->_listTypeSearchConfig);
				return $this->_getListManager( 'media', $manager, $name, $this->_listSearchConfig, $typeManager );
			case 'type':
				return $this->_getTypeManager( 'media', $manager, $name, $this->_typeSearchConfig );
			default:
				return $this->_getSubManager( 'media', $manager, $name );
		}
	}


	/**
	 * Creates a new media item instance.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @param array $listitems List of items implementing MShop_Common_Item_List_Interface
	 * @param array $refItems List of items reference to this item
	 * @return MShop_Media_Item_Interface New product item
	 */
	protected function _createItem( array $values = array(), array $listItems = array(), array $refItems = array() )
	{
		return new MShop_Media_Item_Default( $values, $listItems, $refItems );
	}
}
