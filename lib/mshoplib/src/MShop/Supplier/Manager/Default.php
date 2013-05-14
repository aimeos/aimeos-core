<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Supplier
 * @version $Id: Default.php 14854 2012-01-13 12:54:14Z doleiynyk $
 */


/**
 * Class MShop_Supplier_Manager_Default.
 * @package MShop
 * @subpackage Supplier
 */
class MShop_Supplier_Manager_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Supplier_Manager_Interface
{
	private $_searchConfig = array(
		'supplier.id' => array(
			'code' => 'supplier.id',
			'internalcode' => 'msup."id"',
			'label' => 'Supplier ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'supplier.siteid' => array(
			'code' => 'supplier.siteid',
			'internalcode' => 'msup."siteid"',
			'label' => 'Supplier site ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'supplier.code' => array(
			'code' => 'supplier.code',
			'internalcode' => 'msup."code"',
			'label' => 'Supplier code',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.label' => array(
			'code' => 'supplier.label',
			'internalcode' => 'msup."label"',
			'label' => 'Supplier label',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.status'=> array(
			'code' => 'supplier.status',
			'internalcode' => 'msup."status"',
			'label' => 'Supplier status',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'supplier.ctime'=> array(
			'code'=>'supplier.ctime',
			'internalcode'=>'msup."ctime"',
			'label'=>'Supplier create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.mtime'=> array(
			'code'=>'supplier.mtime',
			'internalcode'=>'msup."mtime"',
			'label'=>'Supplier modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.editor'=> array(
			'code'=>'supplier.editor',
			'internalcode'=>'msup."editor"',
			'label'=>'Supplier editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);

	private $_addressSearchConfig = array(
		'supplier.address.id' => array(
			'code' => 'supplier.address.id',
			'internalcode' => 'msupad."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_supplier_address" AS msupad ON msupad."refid" = msup."id"' ),
			'label' => 'Supplier address ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'supplier.address.siteid' => array(
			'code' => 'supplier.address.siteid',
			'internalcode' => 'msupad."siteid"',
			'label' => 'Supplier address site ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'supplier.address.refid' => array(
			'code' => 'supplier.address.refid',
			'internalcode' => 'msupad."refid"',
			'label' => 'Supplier address reference ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
			'public' => false,
		),
		'supplier.address.company'=> array(
			'code' => 'supplier.address.company',
			'internalcode' => 'msupad."company"',
			'label' => 'Supplier address company',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.salutation' => array(
			'code' => 'supplier.address.salutation',
			'internalcode' => 'msupad."salutation"',
			'label' => 'Supplier address salutation',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.title' => array(
			'code' => 'supplier.address.title',
			'internalcode' => 'msupad."title"',
			'label' => 'Supplier address title',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.firstname' => array(
			'code' => 'supplier.address.firstname',
			'internalcode' => 'msupad."firstname"',
			'label' => 'Supplier address firstname',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.lastname' => array(
			'code' => 'supplier.address.lastname',
			'internalcode' => 'msupad."lastname"',
			'label' => 'Supplier address lastname',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.address1' => array(
			'code' => 'supplier.address.address1',
			'internalcode' => 'msupad."address1"',
			'label' => 'Supplier address part one',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.address2' => array(
			'code' => 'supplier.address.address2',
			'internalcode' => 'msupad."address2"',
			'label' => 'Supplier address part two',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.address3' => array(
			'code' => 'supplier.address.address3',
			'internalcode' => 'msupad."address3"',
			'label' => 'Supplier address part three',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.postal' => array(
			'code' => 'supplier.address.postal',
			'internalcode' => 'msupad."postal"',
			'label' => 'Supplier address postal',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.city' => array(
			'code' => 'supplier.address.city',
			'internalcode' => 'msupad."city"',
			'label' => 'Supplier address city',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.state' => array(
			'code' => 'supplier.address.state',
			'internalcode' => 'msupad."state"',
			'label' => 'Supplier address state',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.countryid' => array(
			'code' => 'supplier.address.countryid',
			'internalcode' => 'msupad."countryid"',
			'label' => 'Supplier address country ID',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.languageid' => array(
			'code' => 'supplier.address.languageid',
			'internalcode' => 'msupad."langid"',
			'label' => 'Supplier address language ID',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.telephone' => array(
			'code' => 'supplier.address.telephone',
			'internalcode' => 'msupad."telephone"',
			'label' => 'Supplier address telephone',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.email' => array(
			'code' => 'supplier.address.email',
			'internalcode' => 'msupad."email"',
			'label' => 'Supplier address email',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.telefax' => array(
			'code' => 'supplier.address.telefax',
			'internalcode' => 'msupad."telefax"',
			'label' => 'Supplier address telefax',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.website' => array(
			'code' => 'supplier.address.website',
			'internalcode' => 'msupad."website"',
			'label' => 'Supplier address website',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.flag' => array(
			'code' => 'supplier.address.flag',
			'internalcode' => 'msupad."flag"',
			'label' => 'Supplier address flag',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'supplier.address.position' => array(
			'code' => 'supplier.address.position',
			'internalcode' => 'msupad."pos"',
			'label' => 'Supplier address position',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'supplier.address.ctime'=> array(
			'code'=>'supplier.address.ctime',
			'internalcode'=>'msupad."ctime"',
			'label'=>'Supplier address create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.mtime'=> array(
			'code'=>'supplier.address.mtime',
			'internalcode'=>'msupad."mtime"',
			'label'=>'Supplier address modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.address.editor'=> array(
			'code'=>'supplier.address.editor',
			'internalcode'=>'msupad."editor"',
			'label'=>'Supplier address editor',
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
			$path = 'classes/supplier/manager/submanagers';
			foreach( $this->_getContext()->getConfig()->get( $path, array( 'address' ) ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Instantiates a new supplier item object.
	 *
	 * @return MShop_Supplier_Item_Interface
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
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( 'mshop/supplier/manager/default/item/delete', 'mshop/supplier/manager/default/item/delete' ) );
	}


	/**
	 * Returns the supplier item object specificed by its ID.
	 *
	 * @param integer $id Unique supplier ID referencing an existing supplier
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'supplier.id', $id, $ref );
	}


	/**
	 * Saves a supplier item object.
	 *
	 * @param MShop_Supplier_Item_Interface $item Supplier item object
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Supplier_Item_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Supplier_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if( !$item->isModified() ) { return; }

		$context = $this->_getContext();
		$config = $context->getConfig();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$id = $item->getId();

			$path = 'mshop/supplier/manager/default/item/';
			$path .= ( $id === null ) ? 'insert' : 'update';

			$stmt = $this->_getCachedStatement( $conn, $path );
			$stmt->bind( 1, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $item->getCode() );
			$stmt->bind( 3, $item->getLabel() );
			$stmt->bind( 4, $item->getStatus(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 5, date('Y-m-d H:i:s', time()) );// mtime
			$stmt->bind( 6, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind(7, $id, MW_DB_Statement_Abstract::PARAM_INT);
			} else {
				$stmt->bind(7, date('Y-m-d H:i:s', time()) );// ctime
			}

			$result = $stmt->execute()->finish();

			if( $fetch === true )
			{
				if( $id === null ) {
					$path = 'mshop/supplier/manager/default/item/newid';
					$item->setId( $this->_newId( $conn, $config->get($path, $path) ) );
				} else {
					$item->setId($id);
				}
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
	 * @param integer &$total Number of items that are available in total
	 * @return array List of items implementing MShop_Supplier_Item_Interface
	 * @throws MShop_Supplier_Exception If creating items failed
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
			$cfgPathSearch = 'mshop/supplier/manager/default/item/search';
			$cfgPathCount =  'mshop/supplier/manager/default/item/count';
			$required = array( 'supplier' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );
			while( ( $row = $results->fetch() ) !== false ) {
				$items[$row['id']] = $this->_createItem( $row );
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
	 * Creates a new manager for supplier
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return MShop_Common_Manager_Address_Interface Returns a address manager
	 * @throws MShop_Supplier_Exception If creating manager failed
	 */
	public function getSubManager( $manager, $name = null )
	{
		switch( $manager )
		{
			case 'address':
				return $this->_createAddressManager(
					'supplier',
					$name,
					'mshop/supplier/manager/address/default/item',
					$this->_addressSearchConfig
				);
			default:
				return $this->_getSubManager( 'supplier', $manager, $name );
		}
	}


	/**
	 * creates a search object and sets base criteria
	 *
	 * @param boolean $default
	 * @return MW_Common_Criteria_Interface
	 */
	public function createSearch($default = false)
	{
		if ($default) {
			return parent::_createSearch('supplier');
		}

		return parent::createSearch();
	}


	/**
	 * Creates a new supplier item.
	 *
	 * @param array $values List of attributes for supplier item
	 * @return MShop_Supplier_Item_Interface New supplier item
	 */
	protected function _createItem( array $values = array() )
	{
		return new MShop_Supplier_Item_Default( $values );
	}
}
