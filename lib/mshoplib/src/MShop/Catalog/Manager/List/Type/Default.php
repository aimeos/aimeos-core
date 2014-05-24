<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Catalog
 */


/**
 * Default catalog list type manager for creating and handling catalog list type items.
 * @package MShop
 * @subpackage Catalog
 */
class MShop_Catalog_Manager_List_Type_Default
	extends MShop_Common_Manager_Type_Abstract
	implements MShop_Catalog_Manager_List_Type_Interface
{
	private $_searchConfig = array(
		'catalog.list.type.id' => array(
			'code'=>'catalog.list.type.id',
			'internalcode'=>'mcatlity."id"',
			'internaldeps'=>array('LEFT JOIN "mshop_catalog_list_type" as mcatlity ON ( mcatli."typeid" = mcatlity."id" )'),
			'label'=>'Catalog list type ID',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'catalog.list.type.siteid' => array(
			'code'=>'catalog.list.type.siteid',
			'internalcode'=>'mcatlity."siteid"',
			'label'=>'Catalog list type site ID',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'catalog.list.type.code' => array(
			'code'=>'catalog.list.type.code',
			'internalcode'=>'mcatlity."code"',
			'label'=>'Catalog list type code',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'catalog.list.type.domain' => array(
			'code'=>'catalog.list.type.domain',
			'internalcode'=>'mcatlity."domain"',
			'label'=>'Catalog list type domain',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'catalog.list.type.label' => array(
			'code' => 'catalog.list.type.label',
			'internalcode' => 'mcatlity."label"',
			'label' => 'Catalog list type label',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'catalog.list.type.status' => array(
			'code' => 'catalog.list.type.status',
			'internalcode' => 'mcatlity."status"',
			'label' => 'Catalog list type status',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'catalog.list.type.ctime'=> array(
			'label' => 'Catalog list type creation time',
			'code' => 'catalog.list.type.ctime',
			'internalcode' => 'mcatlity."ctime"',
			'type' => 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'catalog.list.type.mtime'=> array(
			'label' => 'Catalog list type modification time',
			'code' => 'catalog.list.type.mtime',
			'internalcode' => 'mcatlity."mtime"',
			'type' => 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'catalog.list.type.editor'=> array(
			'code'=>'catalog.list.type.editor',
			'internalcode'=>'mcatlity."editor"',
			'label'=>'Catalog list type editor',
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
		$this->_setResourceName( 'db-catalog' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param array $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'classes/catalog/manager/list/type/submanagers';
		foreach( $this->_getContext()->getConfig()->get( $path, array() ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->_cleanup( $siteids, 'mshop/catalog/manager/list/type/default/item/delete' );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also catalogs of sub-managers if true
	 * @return array List of catalog items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = parent::getSearchAttributes();

		if( $withsub === true )
		{
			$context = $this->_getContext();

			$path = 'classes/catalog/manager/list/type/submanagers';
			foreach( $context->getConfig()->get($path, array() ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for catalog list type extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g types, lists etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		/** classes/catalog/manager/list/type/name
		 * Class name of the used catalog list type manager implementation
		 *
		 * Each default catalog list type manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Catalog_Manager_List_Type_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Catalog_Manager_List_Type_Mytype
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/catalog/manager/list/type/name = Mytype
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

		/** mshop/catalog/manager/list/type/decorators/excludes
		 * Excludes decorators added by the "common" option from the catalog list type manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the catalog list type manager.
		 *
		 *  mshop/catalog/manager/list/type/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "mshop/common/manager/decorators/default" for the catalog list type manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/catalog/manager/list/type/decorators/global
		 * @see mshop/catalog/manager/list/type/decorators/local
		 */

		/** mshop/catalog/manager/list/type/decorators/global
		 * Adds a list of globally available decorators only to the catalog list type manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the catalog list type manager.
		 *
		 *  mshop/catalog/manager/list/type/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the catalog controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/catalog/manager/list/type/decorators/excludes
		 * @see mshop/catalog/manager/list/type/decorators/local
		 */

		/** mshop/catalog/manager/list/type/decorators/local
		 * Adds a list of local decorators only to the catalog list type manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the catalog list type manager.
		 *
		 *  mshop/catalog/manager/list/type/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the catalog
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/catalog/manager/list/type/decorators/excludes
		 * @see mshop/catalog/manager/list/type/decorators/global
		 */

		return $this->_getSubManager( 'catalog', 'list/type/' . $manager, $name );
	}


	/**
	 * Returns the config path for retrieving the configuration values.
	 *
	 * @return string Configuration path
	 */
	protected function _getConfigPath()
	{
		return 'mshop/catalog/manager/list/type/default/item/';
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