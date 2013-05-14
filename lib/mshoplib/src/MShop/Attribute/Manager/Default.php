<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Attribute
 * @version $Id: Default.php 14854 2012-01-13 12:54:14Z doleiynyk $
 */


/**
 * Default attribute manager for creating and handling attributes.
 * @package MShop
 * @subpackage Attribute
 */
class MShop_Attribute_Manager_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Attribute_Manager_Interface
{
	private $_searchConfig = array(
		'attribute.id'=> array(
			'code'=>'attribute.id',
			'internalcode'=>'matt."id"',
			'label'=>'Attribute ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'attribute.siteid'=> array(
			'code'=>'attribute.siteid',
			'internalcode'=>'matt."siteid"',
			'label'=>'Attribute site',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'attribute.typeid'=> array(
			'code'=>'attribute.typeid',
			'internalcode'=>'matt."typeid"',
			'label'=>'Attribute type',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'attribute.domain'=> array(
			'code'=>'attribute.domain',
			'internalcode'=>'matt."domain"',
			'label'=>'Attribute domain',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.code'=> array(
			'code'=>'attribute.code',
			'internalcode'=>'matt."code"',
			'label'=>'Attribute code',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.position'=> array(
			'code'=>'attribute.position',
			'internalcode'=>'matt."pos"',
			'label'=>'Attribute position',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'attribute.label'=> array(
			'code'=>'attribute.label',
			'internalcode'=>'matt."label"',
			'label'=>'Attribute label',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.status'=> array(
			'code'=>'attribute.status',
			'internalcode'=>'matt."status"',
			'label'=>'Attribute status',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'attribute.ctime'=> array(
			'code'=>'attribute.ctime',
			'internalcode'=>'matt."ctime"',
			'label'=>'Attribute create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.mtime'=> array(
			'code'=>'attribute.mtime',
			'internalcode'=>'matt."mtime"',
			'label'=>'Attribute modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.editor'=> array(
			'code'=>'attribute.editor',
			'internalcode'=>'matt."editor"',
			'label'=>'Attribute editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);

	private $_typeSearchConfig = array(
		'attribute.type.id' => array(
			'label' => 'Attribute type ID',
			'code' => 'attribute.type.id',
			'internalcode' => 'mattty."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_attribute_type" AS mattty ON ( matt."typeid" = mattty."id" )' ),
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'attribute.type.siteid' => array(
			'code' => 'attribute.type.siteid',
			'internalcode' => 'mattty."siteid"',
			'label' => 'Attribute type site ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'attribute.type.code' => array(
			'label' => 'Attribute type code',
			'code' => 'attribute.type.code',
			'internalcode' => 'mattty."code"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.type.domain' => array(
			'label' => 'Attribute type domain',
			'code' => 'attribute.type.domain',
			'internalcode' => 'mattty."domain"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.type.label' => array(
			'code' => 'attribute.type.label',
			'internalcode' => 'mattty."label"',
			'label' => 'Attribute type label',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.type.status' => array(
			'code' => 'attribute.type.status',
			'internalcode' => 'mattty."status"',
			'label' => 'Attribute type status',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'attribute.type.ctime'=> array(
			'code'=>'attribute.type.ctime',
			'internalcode'=>'mattty."ctime"',
			'label'=>'Attribute type create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.type.mtime'=> array(
			'code'=>'attribute.type.mtime',
			'internalcode'=>'mattty."mtime"',
			'label'=>'Attribute type modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.type.editor'=> array(
			'code'=>'attribute.type.editor',
			'internalcode'=>'mattty."editor"',
			'label'=>'Attribute type editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);

	private $_listSearchConfig = array(
		'attribute.list.id'=> array(
			'code'=>'attribute.list.id',
			'internalcode'=>'mattli."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_attribute_list" AS mattli ON ( matt."id" = mattli."parentid" )' ),
			'label'=>'List ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'attribute.list.parentid'=> array(
			'code'=>'attribute.list.parentid',
			'internalcode'=>'mattli."parentid"',
			'label'=>'Attribute ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'attribute.list.siteid' => array(
			'code' => 'attribute.list.siteid',
			'internalcode' => 'mattli."siteid"',
			'label' => 'Attribute list site ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'attribute.list.domain'=> array(
			'code'=>'attribute.list.domain',
			'internalcode'=>'mattli."domain"',
			'label'=>'Attribute list domain',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.list.typeid'=> array(
			'code'=>'attribute.list.typeid',
			'internalcode'=>'mattli."typeid"',
			'label'=>'Attribute list typeID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'attribute.list.refid'=> array(
			'code'=>'attribute.list.refid',
			'internalcode'=>'mattli."refid"',
			'label'=>'Attribute list reference ID',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.list.datestart' => array(
			'code'=>'attribute.list.datestart',
			'internalcode'=>'mattli."start"',
			'label'=>'Attribute list start date',
			'type'=> 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.list.dateend' => array(
			'code'=>'attribute.list.dateend',
			'internalcode'=>'mattli."end"',
			'label'=>'Attribute list end date',
			'type'=> 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.list.position' => array(
			'code'=>'attribute.list.position',
			'internalcode'=>'mattli."pos"',
			'label'=>'Attribute list position',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'attribute.list.ctime'=> array(
			'code'=>'attribute.list.ctime',
			'internalcode'=>'mattli."ctime"',
			'label'=>'Attribute list create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.list.mtime'=> array(
			'code'=>'attribute.list.mtime',
			'internalcode'=>'mattli."mtime"',
			'label'=>'Attribute list modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.list.editor'=> array(
			'code'=>'attribute.list.editor',
			'internalcode'=>'mattli."editor"',
			'label'=>'Attribute list editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);

	private $_listTypeSearchConfig = array(
		'attribute.list.type.id' => array(
			'code'=>'attribute.list.type.id',
			'internalcode'=>'mattlity."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_attribute_list_type" AS mattlity ON ( mattli."typeid" = mattlity."id" )' ),
			'label'=>'Attribute list type Id',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'attribute.list.type.siteid' => array(
			'code'=>'attribute.list.type.siteid',
			'internalcode'=>'mattlity."siteid"',
			'label'=>'Attribute list type site Id',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'attribute.list.type.code' => array(
			'code'=>'attribute.list.type.code',
			'internalcode'=>'mattlity."code"',
			'label'=>'Attribute list type code',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.list.type.domain' => array(
			'code'=>'attribute.list.type.domain',
			'internalcode'=>'mattlity."domain"',
			'label'=>'Attribute list type domain',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.list.type.label' => array(
			'code' => 'attribute.list.type.label',
			'internalcode' => 'mattlity."label"',
			'label' => 'Attribute list type label',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.list.type.status' => array(
			'code' => 'attribute.list.type.status',
			'internalcode' => 'mattlity."status"',
			'label' => 'Attribute list type status',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'attribute.list.type.ctime'=> array(
			'code'=>'attribute.list.type.ctime',
			'internalcode'=>'mattlity."ctime"',
			'label'=>'Attribute list type create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.list.type.mtime'=> array(
			'code'=>'attribute.list.type.mtime',
			'internalcode'=>'mattlity."mtime"',
			'label'=>'Attribute list type modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.list.type.editor'=> array(
			'code'=>'attribute.list.type.editor',
			'internalcode'=>'mattlity."editor"',
			'label'=>'Attribute list type editor',
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

		foreach( $this->_searchConfig as $key => $fields ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default( $fields );
		}

		if( $withsub === true )
		{
			$context = $this->_getContext();

			$path = 'classes/attribute/manager/submanagers';
			foreach( $context->getConfig()->get($path, array( 'type', 'list' ) ) as $domain ) {
					$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Creates a new empty attribute item instance.
	 *
	 * @return MShop_Attribute_Item_Interface Creates a blank Attribute item
	 */
	public function createItem()
	{
		$values = array('siteid' => $this->_getContext()->getLocale()->getSiteId());
		return $this->_createItem($values);
	}


	/**
	 * Returns the attributes item specified by its ID.
	 *
	 * @param integer $attributeId Unique ID of the attribute item in the storage
	 * @return MShop_Attribute_Item_Interface Returns the attribute item of the given id
	 * @throws MShop_Attribute_Exception If attribute couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'attribute.id', $id, $ref );
	}


	/**
	 * Saves an attribute item to the storage.
	 *
	 * @param MShop_Attribute_Item_Interface $attribute Attribute implementing the Attribute interface
	 * @param boolean $fetch True if the new ID should be returned in the item
	 * @throws MShop_Attribute_Exception If Attribute couldn't be saved
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Attribute_Item_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Attribute_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if( $item->isModified() === false ) { return; }

		$context = $this->_getContext();
		$config = $context->getConfig();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$id = $item->getId();

			$path = 'mshop/attribute/manager/default/item/';
			$path .= ( $id === null ) ? 'insert' : 'update';

			$stmt = $this->_getCachedStatement( $conn, $path );
			$stmt->bind( 1, $context->getLocale()->getSiteId() );
			$stmt->bind( 2, $item->getTypeId() );
			$stmt->bind( 3, $item->getDomain() );
			$stmt->bind( 4, $item->getCode() );
			$stmt->bind( 5, $item->getStatus(), MW_DB_Statement_Abstract::PARAM_INT);
			$stmt->bind( 6, $item->getPosition(), MW_DB_Statement_Abstract::PARAM_INT);
			$stmt->bind( 7, $item->getLabel() );
			$stmt->bind( 8, date('Y-m-d H:i:s', time()) );
			$stmt->bind( 9, $context->getEditor() );

			if ( $item->getId() !== null ) {
				$stmt->bind(10, $item->getId(), MW_DB_Statement_Abstract::PARAM_INT);
			} else {
				$stmt->bind( 10, date('Y-m-d H:i:s', time()) );
			}

			$result = $stmt->execute()->finish();

			if ( $fetch === true )
			{
				if ( $id === null ) {
					$path = 'mshop/attribute/manager/default/item/newid';
					$item->setId( $this->_newId( $conn, $config->get( $path, $path ) ) );
				} else {
					$item->setId($id);
				}
			}

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}
	}


	/**
	 * Deletes an attribute for the storage.
	 *
	 * @param integer $attributeId Unique ID of the attribute in the storage
	 */
	public function deleteItem( $attributeId )
	{
		$dbm = $this->_getContext()->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$stmt = $this->_getCachedStatement($conn, 'mshop/attribute/manager/default/item/delete');
			$stmt->bind( 1, $attributeId, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->execute()->finish();

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}
	}


	/**
	 * Searches for attribute items based on the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object containing the conditions
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @param integer &$total Number of items that are available in total
	 * @return array List of attribute items implementing MShop_Attribute_Item_Interface
	 *
	 * @throws MW_DB_Exception On failures with the db object
	 * @throws MShop_Common_Exception On failures with the MW_Common_Criteria_ object
	 * @throws MShop_Attribute_Exception On failures with the Attribute items
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$map = $typeIds = array();
		$dbm = $this->_getContext()->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$level = MShop_Locale_Manager_Abstract::SITE_ALL;
			$cfgPathSearch = 'mshop/attribute/manager/default/item/search';
			$cfgPathCount =  'mshop/attribute/manager/default/item/count';
			$required = array( 'attribute' );

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
			$start = $search->getSliceStart();
			$size = $search->getSliceSize();
			$typeManager = $this->getSubManager( 'type' );
			$search = $typeManager->createSearch();
			$search->setConditions( $search->compare( '==', 'attribute.type.id', array_unique( $typeIds ) ) );
			$search->setSlice( $start, $size );
			$typeItems = $typeManager->searchItems( $search );

			foreach( $map as $id => $row )
			{
				if( isset( $typeItems[ $row['typeid'] ] ) ) {
					$map[$id]['type'] = $typeItems[ $row['typeid'] ]->getCode();
				}
			}
		}

		return $this->_buildItems( $map, $ref, 'attribute' );
	}


	/**
	 * creates a search object and sets base criteria
	 *
	 * @param boolean $default
	 * @return MW_Common_Criteria_Interface
	 */
	public function createSearch($default = false)
	{
		if( $default === true ) {
			return parent::_createSearch('attribute');
		}

		return parent::createSearch();
	}


	/**
	 * Returns a new manager for attribute extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g Type, List's etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		switch( $manager )
		{
			case 'list':
				$typeManager = $this->_getTypeManager( 'attribute', 'list/type', null, $this->_listTypeSearchConfig );
				return $this->_getListManager( 'attribute', $manager, $name, $this->_listSearchConfig, $typeManager );
			case 'type':
				return $this->_getTypeManager( 'attribute', $manager, $name, $this->_typeSearchConfig );
			default:
				return $this->_getSubManager( 'attribute', $manager, $name );
		}
	}


	/**
	 * Creates a new attribute item instance.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @param array $listitems List of items implementing MShop_Common_Item_List_Interface
	 * @param array $textItems List of items implementing MShop_Text_Item_Interface
	 * @return MShop_Attribute_Item_Interface New product item
	 */
	protected function _createItem( array $values = array(), array $listItems = array(), array $refItems = array() )
	{
		return new MShop_Attribute_Item_Default( $values, $listItems, $refItems );
	}

}
