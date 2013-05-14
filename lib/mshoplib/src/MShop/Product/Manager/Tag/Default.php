<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Product
 * @version $Id: Default.php 14682 2012-01-04 11:30:14Z nsendetzky $
 */


/**
 * Default tag manager implementation.
 *
 * @package MShop
 * @subpackage Product
 */
class MShop_Product_Manager_Tag_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Product_Manager_Tag_Interface
{
	private $_searchConfig = array(
		'product.tag.id'=> array(
			'code'=>'product.tag.id',
			'internalcode'=>'mprota."id"',
			'label'=>'Product tag ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'product.tag.siteid'=> array(
			'code'=>'product.tag.siteid',
			'internalcode'=>'mprota."siteid"',
			'label'=>'Product tag site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.tag.typeid' => array(
			'code'=>'product.tag.typeid',
			'internalcode'=>'mprota."typeid"',
			'label'=>'Product tag type id',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.tag.languageid' => array(
			'code'=>'product.tag.languageid',
			'internalcode'=>'mprota."langid"',
			'label'=>'Product tag language id',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.tag.label' => array(
			'code'=>'product.tag.label',
			'internalcode'=>'mprota."label"',
			'label'=>'Product tag label',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.tag.mtime'=> array(
			'code'=>'product.tag.mtime',
			'internalcode'=>'mprota."mtime"',
			'label'=>'Product tag modification date',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.tag.ctime'=> array(
			'code'=>'product.tag.ctime',
			'internalcode'=>'mprota."ctime"',
			'label'=>'Product tag creation date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.tag.editor'=> array(
			'code'=>'product.tag.editor',
			'internalcode'=>'mprota."editor"',
			'label'=>'Product tag editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);

	private $_typeSearchConfig = array(
		'product.tag.type.id' => array(
			'code' => 'product.tag.type.id',
			'internalcode' => 'mprotaty."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_product_tag_type" AS mprotaty ON ( mprota."typeid" = mprotaty."id" )' ),
			'label' => 'Product tag type ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.tag.type.siteid' => array(
			'code' => 'product.tag.type.siteid',
			'internalcode' => 'mprotaty."siteid"',
			'label' => 'Product tag type site ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.tag.type.code' => array(
			'code' => 'product.tag.type.code',
			'internalcode' => 'mprotaty."code"',
			'label' => 'Product tag type code',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.tag.type.domain' => array(
			'code' => 'product.tag.type.domain',
			'internalcode' => 'mprotaty."domain"',
			'label' => 'Product tag type domain',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.tag.type.label' => array(
			'code' => 'product.tag.type.label',
			'internalcode' => 'mprotaty."label"',
			'label' => 'Product tag type label',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.tag.type.status' => array(
			'code' => 'product.tag.type.status',
			'internalcode' => 'mprotaty."status"',
			'label' => 'Product tag type status',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'product.tag.type.mtime'=> array(
			'code'=>'product.tag.type.mtime',
			'internalcode'=>'mprotaty."mtime"',
			'label'=>'Product tag type modification date',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.tag.type.ctime'=> array(
			'code'=>'product.tag.type.ctime',
			'internalcode'=>'mprotaty."ctime"',
			'label'=>'Product tag type creation date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.tag.type.editor'=> array(
			'code'=>'product.tag.type.editor',
			'internalcode'=>'mprotaty."editor"',
			'label'=>'Product tag type editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);


	/**
	 * Creates new tag item object.
	 *
	 * @return MShop_Product_Item_Tag_Interface New tag item object
	 */
	public function createItem()
	{
		$values = array('siteid' => $this->_getContext()->getLocale()->getSiteId());
		return $this->_createItem($values);
	}


	/**
	 * Inserts the new tag items for product item
	 *
	 * @param MShop_Product_Item_Tag_Interface $item Tag item which should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Product_Item_Tag_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Product_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if( !$item->isModified() ) { return; }

		$context = $this->_getContext();
		$config = $context->getConfig();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$id = $item->getId();

			$path = 'mshop/product/manager/tag/default/item/';
			$path .= ( $id === null ) ? 'insert' : 'update';

			$stmt = $conn->create( $config->get( $path, $path ) );
			$stmt->bind( 1, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $item->getLanguageId() );
			$stmt->bind( 3, $item->getTypeId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 4, $item->getLabel() );
			$stmt->bind( 5, date('Y-m-d H:i:s', time()));//mtime
			$stmt->bind( 6, $context->getEditor());

			if( $id !== null ) {
				$stmt->bind(7, $id, MW_DB_Statement_Abstract::PARAM_INT);
				$item->setId($id); //is not modified anymore
			} else {
				$stmt->bind(7, date('Y-m-d H:i:s', time()));//ctime
			}

			$result = $stmt->execute()->finish();

			if( $id === null && $fetch === true ) {
				$path = 'mshop/product/manager/tag/default/item/newid';
				$item->setId( $this->_newId($conn, $config->get($path, $path) ) );
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
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( 'mshop/product/manager/tag/default/item/delete', 'mshop/product/manager/tag/default/item/delete' ) );
	}


	/**
	 * Returns product tag item with given Id.
	 *
	 * @param Integer $id Id of product tag item
	 * @return MShop_Product_Item_Tag_Interface Product tag item
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'product.tag.id', $id, $ref );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array Returns a list of attribtes implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = array();

		foreach( $this->_searchConfig as $key => $fields ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default( $fields );
		}

		if( $withsub === true )
		{
			$path = 'classes/product/manager/tag/submanagers';
			foreach ( $this->_getContext()->getConfig()->get($path, array( 'type' )) as $domain ) {
				$list = array_merge($list, $this->getSubManager($domain)->getSearchAttributes());
			}
		}

		return $list;
	}


	/**
	 * Search for all tag items based on the given critera.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object with search conditions
	 * 		[product.tag.prodid], [product.tag.domain], [product.tag.label]
	 * @param integer &$total Number of items that are available in total
	 * @return array List of tag items implementing MShop_Product_Item_Tag_Interface
	 * @throws MShop_Product_Exception if creating items failed
	 * @see MW_Common_Criteria_SQL
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$context = $this->_getContext();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();
		$items = array();

		try
		{
			$level = MShop_Locale_Manager_Abstract::SITE_ALL;
			$cfgPathSearch = 'mshop/product/manager/tag/default/item/search';
			$cfgPathCount =  'mshop/product/manager/tag/default/item/count';
			$required = array( 'product.tag', 'product.tag.type' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );
			while( ( $row = $results->fetch() ) !== false ) {
				$items[ $row['id'] ] = $this->_createItem( $row );
			}

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}

		return $items;
	}


	/**
	 * Returns a new manager for product extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from
	 * configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g tag types, tag lists etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		switch( $manager )
		{
			case 'type':
				return $this->_getTypeManager( 'product', 'tag/type', $name, $this->_typeSearchConfig );
			default:
				return $this->_getSubManager( 'product', 'tag/' . $manager, $name );
		}
	}


	/**
	 * Creates new tag item object.
	 *
	 * @see MShop_Product_Item_Tag_Default Default tag item
	 * @param array $values Possible optional array keys can be given: id, typeid, langid, type, label
	 * @return MShop_Product_Item_Tag_Default New tag item object
	 */
	protected function _createItem( array $values = array() )
	{
		return new MShop_Product_Item_Tag_Default( $values );
	}


	/**
	 * Returns a new type manager object.
	 *
	 * @param string $name Name of the type manager implementation
	 * @return MShop_Common_Manager_Type_Interface Type manager object
	 */
	protected function _createTypeManager( $name )
	{
		$context = $this->_getContext();

		if( $name === null ) {
			$name = $context->getConfig()->get( 'classes/product/manager/tag/type/name', 'Default' );
		}

		if( empty( $name ) || ctype_alnum( $name ) === false ) {
			throw new MShop_Product_Exception( sprintf( 'Invalid characters in manager name "%1$s"', $name ) );
		}

		$classname = 'MShop_Common_Manager_Type_' . $name;
		$interface = 'MShop_Common_Manager_Type_Interface';

		if( class_exists( $classname ) === false ) {
			throw new MShop_Product_Exception( sprintf('Class "%1$s" not available', $classname ) );
		}

		$confpath = 'mshop/product/manager/tag/type/' . strtolower( $name ) . '/item';
		$config = $context->getConfig()->get( $confpath, $confpath );

		$manager = new $classname( $context, $config, $this->_typeSearchConfig );

		if( ( $manager instanceof $interface ) === false ) {
			throw new MShop_Product_Exception(
				sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $interface )
			);
		}

		return $manager;
	}
}
