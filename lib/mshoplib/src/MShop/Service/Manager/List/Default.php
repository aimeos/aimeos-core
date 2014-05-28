<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Service
 */


/**
 * Default service list manager for creating and handling service list items.
 * @package MShop
 * @subpackage Service
 */
class MShop_Service_Manager_List_Default
	extends MShop_Common_Manager_List_Abstract
	implements MShop_Service_Manager_List_Interface
{
	private $_searchConfig = array(
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
		'service.list.status' => array(
			'code' => 'service.list.status',
			'internalcode' => 'mserli."status"',
			'label' => 'Service list status',
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
		$path = 'classes/service/manager/list/submanagers';
		foreach( $this->_getContext()->getConfig()->get( $path, array( 'type' ) ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->_cleanup( $siteids, 'mshop/service/manager/list/default/item/delete' );
	}


	/**
	 * Returns the list attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = parent::getSearchAttributes();

		if( $withsub === true )
		{
			$context = $this->_getContext();

			$path = 'classes/service/manager/list/submanagers';
			foreach( $context->getConfig()->get( $path, array( 'type' ) ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for service list extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g types, lists etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		/** classes/service/manager/list/name
		 * Class name of the used service list manager implementation
		 *
		 * Each default service list manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Service_Manager_List_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Service_Manager_List_Mylist
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/service/manager/list/name = Mylist
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyList"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */

		/** mshop/service/manager/list/decorators/excludes
		 * Excludes decorators added by the "common" option from the service list manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the service list manager.
		 *
		 *  mshop/service/manager/list/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "mshop/common/manager/decorators/default" for the service list manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/service/manager/list/decorators/global
		 * @see mshop/service/manager/list/decorators/local
		 */

		/** mshop/service/manager/list/decorators/global
		 * Adds a list of globally available decorators only to the service list manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the service list manager.
		 *
		 *  mshop/service/manager/list/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the service controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/service/manager/list/decorators/excludes
		 * @see mshop/service/manager/list/decorators/local
		 */

		/** mshop/service/manager/list/decorators/local
		 * Adds a list of local decorators only to the service list manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the service list manager.
		 *
		 *  mshop/service/manager/list/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the service
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/service/manager/list/decorators/excludes
		 * @see mshop/service/manager/list/decorators/global
		 */

		return $this->_getSubManager( 'service', 'list/' . $manager, $name );
	}


	/**
	 * Returns the config path for retrieving the configuration values.
	 *
	 * @return string Configuration path
	 */
	protected function _getConfigPath()
	{
		return 'mshop/service/manager/list/default/item/';
	}


	/**
	 * Returns the search configuration for searching items.
	 *
	 * @return array Associative list of search keys and search definitions
	 */
	protected function _getSearchConfig()
	{
		return $this->_searchConfig;
	}
}