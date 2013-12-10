<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Service
 */


/**
 * Delivery and payment service manager.
 *
 * @package MShop
 * @subpackage Service
 */
class MShop_Service_Manager_Default
	extends MShop_Service_Manager_Abstract
	implements MShop_Service_Manager_Interface
{
	private $_searchConfig = array(
		'service.id' => array(
			'code' => 'service.id',
			'internalcode' => 'mser."id"',
			'label' => 'Service ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'service.siteid' => array(
			'code' => 'service.siteid',
			'internalcode' => 'mser."siteid"',
			'label' => 'Service site ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'service.typeid' => array(
			'code' => 'service.typeid',
			'internalcode' => 'mser."typeid"',
			'label' => 'Service type ID',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
			'public' => false,
		),
		'service.code' => array(
			'code' => 'service.code',
			'internalcode' => 'mser."code"',
			'label' => 'Service code',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.position' => array(
			'code' => 'service.position',
			'internalcode' => 'mser."pos"',
			'label' => 'Service position',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.provider' => array(
			'code' => 'service.provider',
			'internalcode' => 'mser."provider"',
			'label' => 'Service provider',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.config' => array(
			'code' => 'service.config',
			'internalcode' => 'mser."config"',
			'label' => 'Service config',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.status' => array(
			'code' => 'service.status',
			'internalcode' => 'mser."status"',
			'label' => 'Service status',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'service.label' => array(
			'code' => 'service.label',
			'internalcode' => 'mser."label"',
			'label' => 'Service label',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.ctime'=> array(
			'code'=>'service.ctime',
			'internalcode'=>'mser."ctime"',
			'label'=>'Service create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.mtime'=> array(
			'code'=>'service.mtime',
			'internalcode'=>'mser."mtime"',
			'label'=>'Service modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.editor'=> array(
			'code'=>'service.editor',
			'internalcode'=>'mser."editor"',
			'label'=>'Service editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);

	private $_listSearchConfig = array(
		'service.list.id' => array(
			'code' => 'service.list.id',
			'internalcode' => 'mserli."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_service_list" AS mserli ON ( mser."id" = mserli."parentid" )' ),
			'label' => 'Service list ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'service.list.siteid' => array(
			'code' => 'service.list.siteid',
			'internalcode' => 'mserli."siteid"',
			'label' => 'Service list site ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'service.list.parentid' => array(
			'code' => 'service.list.parentid',
			'internalcode' => 'mserli."parentid"',
			'label' => 'Service list parent ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'service.list.domain' => array(
			'code' => 'service.list.domain',
			'internalcode' => 'mserli."domain"',
			'label' => 'Service list domain',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.list.typeid' => array(
			'code' => 'service.list.typeid',
			'internalcode' => 'mserli."typeid"',
			'label' => 'Service list type ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'service.list.refid' => array(
			'code' => 'service.list.refid',
			'internalcode' => 'mserli."refid"',
			'label' => 'Service list reference ID',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.list.datestart' => array(
			'code' => 'service.list.datestart',
			'internalcode' => 'mserli."start"',
			'label' => 'Service list start date',
			'type' => 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.list.dateend' => array(
			'code' => 'service.list.dateend',
			'internalcode' => 'mserli."end"',
			'label' => 'Service list end date',
			'type' => 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.list.config' => array(
			'code' => 'service.list.config',
			'internalcode' => 'mserli."config"',
			'label' => 'Service list config',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.list.position' => array(
			'code' => 'service.list.position',
			'internalcode' => 'mserli."pos"',
			'label' => 'Service list position',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'service.list.ctime'=> array(
			'code'=>'service.list.ctime',
			'internalcode'=>'mserli."ctime"',
			'label'=>'Service list create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.list.mtime'=> array(
			'code'=>'service.list.mtime',
			'internalcode'=>'mserli."mtime"',
			'label'=>'Service list modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.list.editor'=> array(
			'code'=>'service.list.editor',
			'internalcode'=>'mserli."editor"',
			'label'=>'Service list editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);

	private $_listTypeSearchConfig = array(
		'service.list.type.id' => array(
			'code' => 'service.list.type.id',
			'internalcode' => 'mserlity."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_service_list_type" AS mserlity ON ( mserli."typeid" = mserlity."id" )' ),
			'label' => 'Service list type id',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'service.list.type.siteid' => array(
			'code' => 'service.list.type.siteid',
			'internalcode' => 'mserlity."siteid"',
			'label' => 'Service list type site id',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'service.list.type.code' => array(
			'code' => 'service.list.type.code',
			'internalcode' => 'mserlity."code"',
			'label' => 'Service list type code',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.list.type.domain' => array(
			'code' => 'service.list.type.domain',
			'internalcode' => 'mserlity."domain"',
			'label' => 'Service list type domain',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.list.type.label' => array(
			'code' => 'service.list.type.label',
			'internalcode' => 'mserlity."label"',
			'label' => 'Service list type label',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.list.type.status' => array(
			'code' => 'service.list.type.status',
			'internalcode' => 'mserlity."status"',
			'label' => 'Service list type status',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'service.list.type.ctime'=> array(
			'code'=>'service.list.type.ctime',
			'internalcode'=>'mserlity."ctime"',
			'label'=>'Service list type create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.list.type.mtime'=> array(
			'code'=>'service.list.type.mtime',
			'internalcode'=>'mserlity."mtime"',
			'label'=>'Service list type modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.list.type.editor'=> array(
			'code'=>'service.list.type.editor',
			'internalcode'=>'mserlity."editor"',
			'label'=>'Service list type editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);

	private $_typeSearchConfig = array(
		'service.type.id' => array(
			'code' => 'service.type.id',
			'internalcode' => 'mserty."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_service_type" AS mserty ON ( mser."typeid" = mserty."id" )' ),
			'label' => 'Service type ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'service.type.siteid' => array(
			'code' => 'service.type.siteid',
			'internalcode' => 'mserty."siteid"',
			'label' => 'Service type site ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'service.type.code' => array(
			'code' => 'service.type.code',
			'internalcode' => 'mserty."code"',
			'label' => 'Service type code',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.type.domain' => array(
			'code' => 'service.type.domain',
			'internalcode' => 'mserty."domain"',
			'label' => 'Service type domain',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.type.label' => array(
			'code' => 'service.type.label',
			'internalcode' => 'mserty."label"',
			'label' => 'Service type label',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.type.status' => array(
			'code' => 'service.type.status',
			'internalcode' => 'mserty."status"',
			'label' => 'Service type status',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'service.type.ctime'=> array(
			'code'=>'service.type.ctime',
			'internalcode'=>'mserty."ctime"',
			'label'=>'Service type create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.type.mtime'=> array(
			'code'=>'service.type.mtime',
			'internalcode'=>'mserty."mtime"',
			'label'=>'Service type modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.type.editor'=> array(
			'code'=>'service.type.editor',
			'internalcode'=>'mserty."editor"',
			'label'=>'Service type editor',
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
		$list = array( );

		foreach( $this->_searchConfig as $key => $fields ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default($fields);
		}

		if ( $withsub === true ) {

			$config = $this->_getContext()->getConfig();

			foreach ( $config->get('classes/service/manager/submanagers', array( 'type', 'list' )) as $domain ) {
				$list = array_merge($list, $this->getSubManager($domain)->getSearchAttributes());
			}
		}

		return $list;
	}


	/**
	 * Instanciates a new service item depending on the kind of service manager.
	 *
	 * @return MShop_Service_Item_Interface Service item
	 */
	public function createItem()
	{
		$values = array( 'siteid' => $this->_getContext()->getLocale()->getSiteId() );
		return $this->_createItem($values);
	}


	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		$path = 'mshop/service/manager/default/item/delete';
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( $path, $path ) );
	}


	/**
	 * Returns the service item specified by the given id.
	 *
	 * @param int $id Unique ID of the service item
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Service_Item_Interface Returns the service item of the given id
	 * @throws MShop_Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'service.id', $id, $ref );
	}


	/**
	 * Adds a new or updates an existing service item in the storage.
	 *
	 * @param MShop_Service_Item_Interface $service New or existing service item that should be saved to the storage
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Service_Item_Interface';
		if ( !( $item instanceof $iface ) ) {
			throw new MShop_Service_Exception(sprintf('Object is not of required type "%1$s"', $iface));
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

			$path = 'mshop/service/manager/default/item/';
			$path .= ( $id === null ) ? 'insert' : 'update';

			$stmt = $this->_getCachedStatement( $conn, $path );
			$stmt->bind(1, $locale->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT);
			$stmt->bind(2, $item->getPosition(), MW_DB_Statement_Abstract::PARAM_INT);
			$stmt->bind(3, $item->getTypeId());
			$stmt->bind(4, $item->getCode());
			$stmt->bind(5, $item->getLabel());
			$stmt->bind(6, $item->getProvider());
			$stmt->bind(7, json_encode($item->getConfig()));
			$stmt->bind(8, $item->getStatus(), MW_DB_Statement_Abstract::PARAM_INT);
			$stmt->bind(9, date('Y-m-d H:i:s', time()) );// mtime
			$stmt->bind(10, $context->getEditor() );

			if ( $id !== null ) {
				$stmt->bind(11, $id, MW_DB_Statement_Abstract::PARAM_INT);
			} else {
				$stmt->bind(11, date('Y-m-d H:i:s', time()) );// ctime
			}

			$stmt->execute()->finish();

			if( $fetch === true )
			{
				if ( $id === null ) {
					$path = 'mshop/service/manager/default/item/newid';
					$item->setId( $this->_newId($conn, $config->get($path, $path) ) );
				} else {
					$item->setId($id);
				}
			}

			$dbm->release($conn);
		}
		catch ( Exception $e )
		{
			$dbm->release($conn);
			throw $e;
		}
	}


	/**
	 * Searches for service items based on the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search criteria object
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @param integer &$total Number of items that are available in total
	 * @return array List of service items implementing MShop_Service_Item_Interface
	 *
	 * @throws MShop_Service_Exception if creating items failed
	 * @throws MW_Common_Exception If a failure in the search object occurred
	 * @throws MW_DB_Exception If errors regarding database access occured
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$map = $typeIds = array();
		$dbm = $this->_getContext()->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$level = MShop_Locale_Manager_Abstract::SITE_PATH;
			$cfgPathSearch = 'mshop/service/manager/default/item/search';
			$cfgPathCount =  'mshop/service/manager/default/item/count';
			$required = array( 'service' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false )
			{
				$config = $row['config'];

				if ( ( $row['config'] = json_decode( $row['config'], true ) ) === null )
				{
					$msg = sprintf( 'Invalid JSON as result of search for ID "%2$s" in "%1$s": %3$s', 'mshop_service.config', $row['id'], $config );
					$this->_getContext()->getLogger()->log( $msg, MW_Logger_Abstract::WARN );
				}

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
			$typeSearch->setConditions( $typeSearch->compare( '==', 'service.type.id', array_keys( $typeIds ) ) );
			$typeSearch->setSlice( 0, $search->getSliceSize() );
			$typeItems = $typeManager->searchItems( $typeSearch );

			foreach( $map as $id => $row )
			{
				if( isset( $typeItems[ $row['typeid'] ] ) ) {
					$map[$id]['type'] = $typeItems[ $row['typeid'] ]->getCode();
				}
			}
		}

		return $this->_buildItems( $map, $ref, 'service' );
	}


	/**
	 * Returns the service provider which is responsible for the service item.
	 *
	 * @param MShop_Service_Item_Interface $item Delivery or payment service item object
	 * @return MShop_Service_Provider_Interface Returns a service provider implementing MShop_Service_Provider_Interface
	 * @throws MShop_Service_Exception If provider couldn't be found
	 */
	public function getProvider( MShop_Service_Item_Interface $item )
	{
		$domain = ucwords( $item->getType() );
		$names = explode( ',', $item->getProvider() );

		if ( ctype_alnum( $domain ) === false ) {
			throw new MShop_Service_Exception( sprintf( 'Invalid characters in domain name "%1$s"', $domain ) );
		}

		if( ( $provider = array_shift( $names ) ) === null )
		{
			$msg = sprintf( 'Provider in "%1$s" not available', $item->getProvider() );
			throw new MShop_Service_Exception( $msg );
		}

		if ( ctype_alnum( $provider ) === false ) {
			throw new MShop_Service_Exception( sprintf( 'Invalid characters in provider name "%1$s"', $provider ) );
		}

		$interface = 'MShop_Service_Provider_Factory_Interface';
		$classname = 'MShop_Service_Provider_' . $domain . '_' . $provider;

		if ( class_exists( $classname ) === false ) {
			throw new MShop_Service_Exception(sprintf('Class "%1$s" not available', $classname));
		}

		$context = $this->_getContext();
		$provider = new $classname($context, $item);

		if ( ( $provider instanceof $interface ) === false ) {
			$msg = sprintf('Class "%1$s" does not implement interface "%2$s"', $classname, $interface);
			throw new MShop_Service_Exception($msg);
		}


		$provider = $this->_addServiceDecorators( $item, $provider, $names );

		$config = $context->getConfig();
		$decorators = $config->get( 'mshop/service/provider/' . $item->getType() . '/decorators', array() );

		return $this->_addServiceDecorators( $item, $provider, $decorators );
	}


	/**
	 * Returns a new sub manager specified by its name.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return MShop_Common_Manager_List_Interface List manager
	 */
	public function getSubManager( $manager, $name = null )
	{
		switch ( $manager ) {
			case 'list':
				$typeManager = $this->_getTypeManager('service', 'list/type', null, $this->_listTypeSearchConfig);
				return $this->_getListManager('service', $manager, $name, $this->_listSearchConfig, $typeManager);
			case 'type':
				return $this->_getTypeManager('service', $manager, $name, $this->_typeSearchConfig);
			default:
				return $this->_getSubManager('service', $manager, $name);
		}
	}


	/**
	 * creates a search object and sets base criteria
	 *
	 * @param boolean $default Prepopulate object with default criterias
	 * @return MW_Common_Criteria_Interface
	 */
	public function createSearch( $default = false )
	{
		if ( $default === true ) {
			return parent::_createSearch('service');
		}

		return parent::createSearch();
	}


	/**
	 * Creates a new service item initialized with the given values.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @param array $listitems List of items implementing MShop_Common_Item_List_Interface
	 * @param array $textItems List of items implementing MShop_Text_Item_Interface
	 * @return MShop_Service_Item_Interface New service item
	 */
	protected function _createItem( array $values = array( ), array $listitems = array( ), array $textItems = array( ) )
	{
		return new MShop_Service_Item_Default($values, $listitems, $textItems);
	}
}
