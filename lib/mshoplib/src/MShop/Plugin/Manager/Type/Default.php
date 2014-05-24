<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Plugin
 */


/**
 * Default plugin type manager for creating and handling plugin type items.
 * @package MShop
 * @subpackage Plugin
 */
class MShop_Plugin_Manager_Type_Default
	extends MShop_Common_Manager_Type_Abstract
	implements MShop_Plugin_Manager_Type_Interface
{
	private $_searchConfig = array(
		'plugin.type.id'=> array(
			'code'=>'plugin.type.id',
			'internalcode'=>'mpluty."id"',
			'internaldeps'=> array( 'LEFT JOIN "mshop_plugin_type" AS mpluty ON ( mpluty."id" = mplu."typeid" )' ),
			'label'=>'Plugin type ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'plugin.type.siteid'=> array(
			'code'=>'plugin.type.siteid',
			'internalcode'=>'mpluty."siteid"',
			'label'=>'Plugin type site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'plugin.type.code' => array(
			'code'=>'plugin.type.code',
			'internalcode'=>'mpluty."code"',
			'label'=>'Plugin type code',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'plugin.type.domain' => array(
			'code'=>'plugin.type.domain',
			'internalcode'=>'mpluty."domain"',
			'label'=>'Plugin type domain',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'plugin.type.label' => array(
			'code'=>'plugin.type.label',
			'internalcode'=>'mpluty."label"',
			'label'=>'Plugin type label',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'plugin.type.status' => array(
			'code'=>'plugin.type.status',
			'internalcode'=>'mpluty."status"',
			'label'=>'Plugin type status',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'plugin.type.mtime'=> array(
			'code'=>'plugin.type.mtime',
			'internalcode'=>'mpluty."mtime"',
			'label'=>'Plugin type modification date',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'plugin.type.ctime'=> array(
			'code'=>'plugin.type.ctime',
			'internalcode'=>'mpluty."ctime"',
			'label'=>'Plugin type creation date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'plugin.type.editor'=> array(
			'code'=>'plugin.type.editor',
			'internalcode'=>'mpluty."editor"',
			'label'=>'Plugin type editor',
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
		$this->_setResourceName( 'db-plugin' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param array $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'classes/plugin/manager/type/submanagers';
		foreach( $this->_getContext()->getConfig()->get( $path, array() ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->_cleanup( $siteids, 'mshop/plugin/manager/type/default/item/delete' );
	}


	/**
	 * Returns the attributes that can be used for searching.
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

			$path = 'classes/plugin/manager/type/submanagers';
			foreach( $context->getConfig()->get($path, array() ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for plugin type extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g types, lists etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		/** classes/plugin/manager/type/name
		 * Class name of the used plugin type manager implementation
		 *
		 * Each default plugin type manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Plugin_Manager_Type_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Plugin_Manager_Type_Mytype
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/plugin/manager/type/name = Mytype
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
		 * @since 2014.03
		 * @category Developer
		 */

		/** mshop/plugin/manager/type/decorators/excludes
		 * Excludes decorators added by the "common" option from the plugin type manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the plugin type manager.
		 *
		 *  mshop/plugin/manager/type/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "mshop/common/manager/decorators/default" for the plugin type manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/plugin/manager/type/decorators/global
		 * @see mshop/plugin/manager/type/decorators/local
		 */

		/** mshop/plugin/manager/type/decorators/global
		 * Adds a list of globally available decorators only to the plugin type manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the plugin type manager.
		 *
		 *  mshop/plugin/manager/type/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the plugin controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/plugin/manager/type/decorators/excludes
		 * @see mshop/plugin/manager/type/decorators/local
		 */

		/** mshop/plugin/manager/type/decorators/local
		 * Adds a list of local decorators only to the plugin type manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the plugin type manager.
		 *
		 *  mshop/plugin/manager/type/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the plugin
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/plugin/manager/type/decorators/excludes
		 * @see mshop/plugin/manager/type/decorators/global
		 */

		return $this->_getSubManager( 'plugin', 'type/' . $manager, $name );
	}


	/**
	 * Returns the config path for retrieving the configuration values.
	 *
	 * @return string Configuration path
	 */
	protected function _getConfigPath()
	{
		return 'mshop/plugin/manager/type/default/item/';
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