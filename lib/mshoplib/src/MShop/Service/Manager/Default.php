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


	/**
	 * Initializes the object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );
		$this->_setResourceName( 'db-service' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param array $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'classes/service/manager/submanagers';
		foreach( $this->_getContext()->getConfig()->get( $path, array( 'type', 'list' ) ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->_cleanup( $siteids, 'mshop/service/manager/default/item/delete' );
	}


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

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();

			$path = 'mshop/service/manager/default/item/';
			$path .= ( $id === null ) ? 'insert' : 'update';

			$stmt = $this->_getCachedStatement( $conn, $path );
			$stmt->bind(1, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT);
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
					$item->setId( $this->_newId($conn, $context->getConfig()->get( $path, $path ) ) );
				} else {
					$item->setId($id);
				}
			}

			$dbm->release( $conn, $dbname );
		}
		catch ( Exception $e )
		{
			$dbm->release( $conn, $dbname );
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
		$context = $this->_getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

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

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
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

		if( ( $provider = array_shift( $names ) ) === null ) {
			throw new MShop_Service_Exception( sprintf( 'Provider in "%1$s" not available', $item->getProvider() ) );
		}

		if ( ctype_alnum( $provider ) === false ) {
			throw new MShop_Service_Exception( sprintf( 'Invalid characters in provider name "%1$s"', $provider ) );
		}

		$interface = 'MShop_Service_Provider_Factory_Interface';
		$classname = 'MShop_Service_Provider_' . $domain . '_' . $provider;

		if ( class_exists( $classname ) === false ) {
			throw new MShop_Service_Exception(sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$context = $this->_getContext();
		$provider = new $classname( $context, $item );

		if ( ( $provider instanceof $interface ) === false )
		{
			$msg = sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $interface );
			throw new MShop_Service_Exception($msg);
		}

		$config = $context->getConfig();
		$decorators = $config->get( 'mshop/service/provider/' . $item->getType() . '/decorators', array() );

		$provider = $this->_addServiceDecorators( $item, $provider, $names );
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
		/** classes/service/manager/name
		 * Class name of the used service manager implementation
		 *
		 * Each default service manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Service_Manager_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Service_Manager_Myservice
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/service/manager/name = Myservice
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyService"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */

		/** mshop/service/manager/decorators/excludes
		 * Excludes decorators added by the "common" option from the service manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the service manager.
		 *
		 *  mshop/service/manager/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "mshop/common/manager/decorators/default" for the service manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/service/manager/decorators/global
		 * @see mshop/service/manager/decorators/local
		 */

		/** mshop/service/manager/decorators/global
		 * Adds a list of globally available decorators only to the service manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the service manager.
		 *
		 *  mshop/service/manager/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the service controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/service/manager/decorators/excludes
		 * @see mshop/service/manager/decorators/local
		 */

		/** mshop/service/manager/decorators/local
		 * Adds a list of local decorators only to the service manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the service manager.
		 *
		 *  mshop/service/manager/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the service
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/service/manager/decorators/excludes
		 * @see mshop/service/manager/decorators/global
		 */

		return $this->_getSubManager('service', $manager, $name);
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
