<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Text
 * @version $Id: Default.php 14750 2012-01-09 12:09:16Z nsendetzky $
 */


/**
 * Default text manager implementation
 *
 * @package MShop
 * @subpackage Text
 */
class MShop_Text_Manager_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Text_Manager_Interface
{
	private $_searchConfig = array(
		'text.id'=> array(
			'code'=>'text.id',
			'internalcode'=>'mtex."id"',
			'label'=>'Text ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'text.siteid'=> array(
			'code'=>'text.siteid',
			'internalcode'=>'mtex."siteid"',
			'label'=>'Text site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'text.languageid' => array(
			'code'=>'text.languageid',
			'internalcode'=>'mtex."langid"',
			'label'=>'Text language code',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.typeid' => array(
			'code'=>'text.typeid',
			'internalcode'=>'mtex."typeid"',
			'label'=>'Text type ID',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'text.label' => array(
			'code'=>'text.label',
			'internalcode'=>'mtex."label"',
			'label'=>'Text label',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.domain' => array(
			'code'=>'text.domain',
			'internalcode'=>'mtex."domain"',
			'label'=>'Text domain',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.content' => array(
			'code'=>'text.content',
			'internalcode'=>'mtex."content"',
			'label'=>'Text content',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.status' => array(
			'code'=>'text.status',
			'internalcode'=>'mtex."status"',
			'label'=>'Text status',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'text.ctime'=> array(
			'code'=>'text.ctime',
			'internalcode'=>'mtex."ctime"',
			'label'=>'Text create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.mtime'=> array(
			'code'=>'text.mtime',
			'internalcode'=>'mtex."mtime"',
			'label'=>'Text modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.editor'=> array(
			'code'=>'text.editor',
			'internalcode'=>'mtex."editor"',
			'label'=>'Text editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);

	private $_typeSearchConfig = array(
		'text.type.id'=> array(
			'code'=>'text.type.id',
			'internalcode'=>'mtexty."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_text_type" AS mtexty ON ( mtex."typeid" = mtexty."id" )' ),
			'label'=>'Text type ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'text.type.siteid'=> array(
			'code'=>'text.type.siteid',
			'internalcode'=>'mtexty."siteid"',
			'label'=>'Text type site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'text.type.code' => array(
			'code'=>'text.type.code',
			'internalcode'=>'mtexty."code"',
			'label'=>'Text type code',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.type.domain' => array(
			'code'=>'text.type.domain',
			'internalcode'=>'mtexty."domain"',
			'label'=>'Text type domain',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.type.label' => array(
			'code'=>'text.type.label',
			'internalcode'=>'mtexty."label"',
			'label'=>'Text type label',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.type.status' => array(
			'code'=>'text.type.status',
			'internalcode'=>'mtexty."status"',
			'label'=>'Text type status',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'text.type.ctime'=> array(
			'code'=>'text.type.ctime',
			'internalcode'=>'mtexty."ctime"',
			'label'=>'Text type create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.type.mtime'=> array(
			'code'=>'text.type.mtime',
			'internalcode'=>'mtexty."mtime"',
			'label'=>'Text type modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.type.editor'=> array(
			'code'=>'text.type.editor',
			'internalcode'=>'mtexty."editor"',
			'label'=>'Text type editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);


	private $_listSearchConfig = array(
		'text.list.id'=> array(
			'code'=>'text.list.id',
			'internalcode'=>'mtexli."id"',
			'internaldeps'=> array( 'LEFT JOIN "mshop_text_list" AS mtexli ON mtexli."parentid" = mtex."id"' ),
			'label'=>'Text list ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'text.list.siteid'=> array(
			'code'=>'text.list.siteid',
			'internalcode'=>'mtexli."siteid"',
			'label'=>'Text list site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'text.list.parentid'=> array(
			'code'=>'text.list.parentid',
			'internalcode'=>'mtexli."parentid"',
			'label'=>'Text list parent Id',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'text.list.domain'=> array(
			'code'=>'text.list.domain',
			'internalcode'=>'mtexli."domain"',
			'label'=>'Text list domain',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.list.typeid'=> array(
			'code'=>'text.list.typeid',
			'internalcode'=>'mtexli."typeid"',
			'label'=>'Text list type ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'text.list.refid'=> array(
			'code'=>'text.list.refid',
			'internalcode'=>'mtexli."refid"',
			'label'=>'Text list reference ID',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.list.datestart' => array(
			'code'=>'text.list.datestart',
			'internalcode'=>'mtexli."start"',
			'label'=>'Text list start date',
			'type'=> 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.list.dateend' => array(
			'code'=>'text.list.dateend',
			'internalcode'=>'mtexli."end"',
			'label'=>'Text list end date',
			'type'=> 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.list.position' => array(
			'code'=>'text.list.position',
			'internalcode'=>'mtexli."pos"',
			'label'=>'Text list position',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'text.list.ctime'=> array(
			'code'=>'text.list.ctime',
			'internalcode'=>'mtexli."ctime"',
			'label'=>'Text list create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.list.mtime'=> array(
			'code'=>'text.list.mtime',
			'internalcode'=>'mtexli."mtime"',
			'label'=>'Text list modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.list.editor'=> array(
			'code'=>'text.list.editor',
			'internalcode'=>'mtexli."editor"',
			'label'=>'Text list editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);


	private $_listTypeSearchConfig = array(
		'text.list.type.id' => array(
			'code'=>'text.list.type.id',
			'internalcode'=>'mtexlity."id"',
			'internaldeps'=> array( 'LEFT JOIN "mshop_text_list_type" AS mtexlity ON mtexlity."id" = mtexli."typeid"' ),
			'label'=>'Text list type Id',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'text.list.type.siteid' => array(
			'code'=>'text.list.type.siteid',
			'internalcode'=>'mtexlity."siteid"',
			'label'=>'Text list type site Id',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'text.list.type.code' => array(
			'code'=>'text.list.type.code',
			'internalcode'=>'mtexlity."code"',
			'label'=>'Text list type code',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.list.type.domain' => array(
			'code'=>'text.list.type.domain',
			'internalcode'=>'mtexlity."domain"',
			'label'=>'Text list type domain',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.list.type.label' => array(
			'code'=>'text.list.type.label',
			'internalcode'=>'mtexlity."label"',
			'label'=>'Text list type label',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.list.type.status' => array(
			'code'=>'text.list.type.status',
			'internalcode'=>'mtexlity."status"',
			'label'=>'Text list type status',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'text.list.type.ctime'=> array(
			'code'=>'text.list.type.ctime',
			'internalcode'=>'mtexlity."ctime"',
			'label'=>'Text list type create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.list.type.mtime'=> array(
			'code'=>'text.list.type.mtime',
			'internalcode'=>'mtexlity."mtime"',
			'label'=>'Text list type modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.list.type.editor'=> array(
			'code'=>'text.list.type.editor',
			'internalcode'=>'mtexlity."editor"',
			'label'=>'Text list type editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);


	/**
	 * Creates new text item object.
	 *
	 * @return MShop_Text_Item_Interface New text item object
	 */
	public function createItem()
	{
		$values = array('siteid' => $this->_getContext()->getLocale()->getSiteId());
		return $this->_createItem($values);
	}


	/**
	 * Updates or adds a text item object.
	 * This method doesn't update the type string that belongs to the type ID
	 *
	 * @param MShop_Text_Item_Interface $item Text item which should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Text_Item_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Text_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if( !$item->isModified() ) { return; }

		$context = $this->_getContext();
		$config = $context->getConfig();
		$locale = $context->getLocale();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$id = $item->getId();

			$path = 'mshop/text/manager/default/item/';
			$path .= ( $id === null ) ? 'insert' : 'update';

			$stmt = $this->_getCachedStatement( $conn, $path );
			$stmt->bind( 1, $locale->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $item->getLanguageId() );
			$stmt->bind( 3, $item->getTypeId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 4, $item->getDomain() );
			$stmt->bind( 5, $item->getLabel() );
			$stmt->bind( 6, $item->getContent() );
			$stmt->bind( 7, $item->getStatus(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 8, date('Y-m-d H:i:s', time()) );// mtime
			$stmt->bind( 9, $context->getEditor() );

			if ( $id !== null ) {
				$stmt->bind(10, $id, MW_DB_Statement_Abstract::PARAM_INT);
			} else {
				$stmt->bind(10, date('Y-m-d H:i:s', time()) );// ctime
			}

			$result = $stmt->execute()->finish();

			if ( $id === null && $fetch === true ) {
				$path = 'mshop/text/manager/default/item/newid';
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
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		$path = 'mshop/text/manager/default/item/delete';
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( $path, $path ) );
	}


	/**
	 * Returns the text item object specified by the given ID.
	 *
	 * @param integer $id Id of text item
	 * @return MShop_Text_Item_Interface Text item
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'text.id', $id, $ref );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = array();

		foreach( $this->_searchConfig as $key => $fields ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default( $fields );
		}

		if( $withsub === true )
		{
			$config = $this->_getContext()->getConfig();

			foreach( $config->get( 'classes/text/manager/submanagers', array( 'type', 'list' ) ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Searches for all text items matching the given critera.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object with search conditions
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @param integer &$total Number of items that are available in total
	 * @return array List of text items implementing MShop_Text_Item_Interface
	 * @throws MShop_Service_Exception if creating items failed
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$map = $typeIds = array();
		$dbm = $this->_getContext()->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$level = MShop_Locale_Manager_Abstract::SITE_ALL;
			$cfgPathSearch = 'mshop/text/manager/default/item/search';
			$cfgPathCount =  'mshop/text/manager/default/item/count';
			$required = array( 'text' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false )
			{
				$map[ $row['id'] ] = $row;
				$typeIds[] = $row['typeid'];
			}

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}

		if( count( $typeIds ) > 0 )
		{
			$typeManager = $this->getSubManager( 'type' );
			$search = $typeManager->createSearch();
			$search->setConditions( $search->compare( '==', 'text.type.id', array_unique( $typeIds ) ) );
			$typeItems = $typeManager->searchItems( $search );

			foreach( $map as $id => $row )
			{
				if( isset( $typeItems[ $row['typeid'] ] ) ) {
					$map[$id]['type'] = $typeItems[ $row['typeid'] ]->getCode();
				}
			}
		}

		return $this->_buildItems( $map, $ref, 'text' );
	}


	/**
	 * Returns a new manager for text extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g types, lists etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		switch( $manager )
		{
			case 'list':
				$typeManager = $this->_getTypeManager( 'text', 'list/type', null, $this->_listTypeSearchConfig );
				return $this->_getListManager( 'text', $manager, $name, $this->_listSearchConfig, $typeManager );
			case 'type':
				return $this->_getTypeManager( 'text', $manager, $name, $this->_typeSearchConfig );
			default:
				return $this->_getSubManager( 'text', $manager, $name );
		}
	}


	/**
	 * Creates a search object.
	 *
	 * @param boolean $default If base criteria should be added
	 * @return MW_Common_Criteria_Interface Search criteria object
	 */
	public function createSearch( $default = false )
	{
		if( $default === true )
		{
			$object = $this->_createSearch( 'text' );
			$langid = $this->_getContext()->getLocale()->getLanguageId();

			if( $langid !== '' )
			{
				$temp[] = $object->compare( '==', 'text.languageid', $langid );
				$temp[] = $object->compare( '==', 'text.languageid', null );

				$expr[] = $object->getConditions();
				$expr[] = $object->combine( '||', $temp );

				$object->setConditions( $object->combine( '&&', $expr ) );
			}

			return $object;
		}

		return parent::createSearch();
	}


	/**
	 * Creates a new text item instance.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @param array $listitems List of items implementing MShop_Common_Item_List_Interface
	 * @param array $refItems List of items implementing MShop_Text_Item_Interface
	 * @return MShop_Text_Item_Interface New product item
	 */
	protected function _createItem( array $values = array(), array $listItems = array(), array $refItems = array() )
	{
		return new MShop_Text_Item_Default( $values, $listItems, $refItems );
	}
}
