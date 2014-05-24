<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Attribute
 */


/**
 * Default attribute type manager for creating and handling attribute type items.
 * @package MShop
 * @subpackage Attribute
 */
class MShop_Attribute_Manager_Type_Default
	extends MShop_Common_Manager_Type_Abstract
	implements MShop_Attribute_Manager_Type_Interface
{
	private $_searchConfig = array(
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
		$path = 'classes/attribute/manager/type/submanagers';
		foreach( $this->_getContext()->getConfig()->get( $path, array() ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->_cleanup( $siteids, 'mshop/attribute/manager/type/default/item/delete' );
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

			$path = 'classes/attribute/manager/type/submanagers';
			foreach( $context->getConfig()->get($path, array() ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for attribute type extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g types, lists etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		/** classes/attribute/manager/type/name
		 * Class name of the used attribute type manager implementation
		 *
		 * Each default attribute type manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Attribute_Manager_Type_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Attribute_Manager_Type_Mytype
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/attribute/manager/type/name = Mytype
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

		/** mshop/attribute/manager/type/decorators/excludes
		 * Excludes decorators added by the "common" option from the attribute type manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the attribute type manager.
		 *
		 *  mshop/attribute/manager/type/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "mshop/common/manager/decorators/default" for the attribute type manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/attribute/manager/type/decorators/global
		 * @see mshop/attribute/manager/type/decorators/local
		 */

		/** mshop/attribute/manager/type/decorators/global
		 * Adds a list of globally available decorators only to the attribute type manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the attribute type manager.
		 *
		 *  mshop/attribute/manager/type/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the attribute controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/attribute/manager/type/decorators/excludes
		 * @see mshop/attribute/manager/type/decorators/local
		 */

		/** mshop/attribute/manager/type/decorators/local
		 * Adds a list of local decorators only to the attribute type manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the attribute type manager.
		 *
		 *  mshop/attribute/manager/type/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the attribute
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/attribute/manager/type/decorators/excludes
		 * @see mshop/attribute/manager/type/decorators/global
		 */

		return $this->_getSubManager( 'attribute', 'type/' . $manager, $name );
	}


	/**
	 * Returns the config path for retrieving the configuration values.
	 *
	 * @return string Configuration path
	 */
	protected function _getConfigPath()
	{
		return 'mshop/attribute/manager/type/default/item/';
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