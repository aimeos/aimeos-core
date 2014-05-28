<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Attribute
 */


/**
 * Default attribute list manager for creating and handling attribute list items.
 * @package MShop
 * @subpackage Attribute
 */
class MShop_Attribute_Manager_List_Default
	extends MShop_Common_Manager_List_Abstract
	implements MShop_Attribute_Manager_List_Interface
{
	private $_searchConfig = array(
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
		'attribute.list.config' => array(
			'code'=>'attribute.list.config',
			'internalcode'=>'mattli."config"',
			'label'=>'Attribute list config',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.list.position' => array(
			'code'=>'attribute.list.position',
			'internalcode'=>'mattli."pos"',
			'label'=>'Attribute list position',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'attribute.list.status' => array(
			'code'=>'attribute.list.status',
			'internalcode'=>'mattli."status"',
			'label'=>'Attribute list status',
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


	/**
	 * Initializes the object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );
		$this->_setResourceName( 'db-attribute' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param array $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'classes/attribute/manager/list/submanagers';
		foreach( $this->_getContext()->getConfig()->get( $path, array( 'type' ) ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->_cleanup( $siteids, 'mshop/attribute/manager/list/default/item/delete' );
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

			$path = 'classes/attribute/manager/list/submanagers';
			foreach( $context->getConfig()->get( $path, array( 'type' ) ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for attribute list extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g types, lists etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		/** classes/attribute/manager/list/name
		 * Class name of the used attribute list manager implementation
		 *
		 * Each default attribute list manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Attribute_Manager_List_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Attribute_Manager_List_Mylist
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/attribute/manager/list/name = Mylist
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

		/** mshop/attribute/manager/list/decorators/excludes
		 * Excludes decorators added by the "common" option from the attribute list manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the attribute list manager.
		 *
		 *  mshop/attribute/manager/list/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "mshop/common/manager/decorators/default" for the attribute list manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/attribute/manager/list/decorators/global
		 * @see mshop/attribute/manager/list/decorators/local
		 */

		/** mshop/attribute/manager/list/decorators/global
		 * Adds a list of globally available decorators only to the attribute list manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the attribute list manager.
		 *
		 *  mshop/attribute/manager/list/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the attribute controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/attribute/manager/list/decorators/excludes
		 * @see mshop/attribute/manager/list/decorators/local
		 */

		/** mshop/attribute/manager/list/decorators/local
		 * Adds a list of local decorators only to the attribute list manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the attribute list manager.
		 *
		 *  mshop/attribute/manager/list/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the attribute
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/attribute/manager/list/decorators/excludes
		 * @see mshop/attribute/manager/list/decorators/global
		 */

		return $this->_getSubManager( 'attribute', 'list/' . $manager, $name );
	}


	/**
	 * Returns the config path for retrieving the configuration values.
	 *
	 * @return string Configuration path
	 */
	protected function _getConfigPath()
	{
		return 'mshop/attribute/manager/list/default/item/';
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