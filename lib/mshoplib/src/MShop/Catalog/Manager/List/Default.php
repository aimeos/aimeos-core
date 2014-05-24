<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Catalog
 */


/**
 * Default catalog list manager for creating and handling catalog list items.
 * @package MShop
 * @subpackage Catalog
 */
class MShop_Catalog_Manager_List_Default
	extends MShop_Common_Manager_List_Abstract
	implements MShop_Catalog_Manager_List_Interface
{
	private $_searchConfig = array(
		'catalog.list.id'=> array(
			'code'=>'catalog.list.id',
			'internalcode'=>'mcatli."id"',
			'internaldeps'=> array( 'LEFT JOIN "mshop_catalog_list" AS mcatli ON ( mcat."id" = mcatli."parentid" )' ),
			'label'=>'Catalog list ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'catalog.list.siteid'=> array(
			'code'=>'catalog.list.siteid',
			'internalcode'=>'mcatli."siteid"',
			'label'=>'Catalog list site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'catalog.list.parentid'=> array(
			'code'=>'catalog.list.parentid',
			'internalcode'=>'mcatli."parentid"',
			'label'=>'Catalog list parent ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'catalog.list.domain'=> array(
			'code'=>'catalog.list.domain',
			'internalcode'=>'mcatli."domain"',
			'label'=>'Catalog list Domain',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'catalog.list.typeid'=> array(
			'code'=>'catalog.list.typeid',
			'internalcode'=>'mcatli."typeid"',
			'label'=>'Catalog list type ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'catalog.list.refid'=> array(
			'code'=>'catalog.list.refid',
			'internalcode'=>'mcatli."refid"',
			'label'=>'Catalog list reference ID',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'catalog.list.datestart' => array(
			'code'=>'catalog.list.datestart',
			'internalcode'=>'mcatli."start"',
			'label'=>'Catalog list start date',
			'type'=> 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'catalog.list.dateend' => array(
			'code'=>'catalog.list.dateend',
			'internalcode'=>'mcatli."end"',
			'label'=>'Catalog list end date',
			'type'=> 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'catalog.list.config' => array(
			'code'=>'catalog.list.config',
			'internalcode'=>'mcatli."config"',
			'label'=>'Catalog list config',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'catalog.list.position' => array(
			'code'=>'catalog.list.position',
			'internalcode'=>'mcatli."pos"',
			'label'=>'Catalog list position',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'catalog.list.status' => array(
			'code'=>'catalog.list.status',
			'internalcode'=>'mcatli."status"',
			'label'=>'Catalog list status',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'catalog.list.ctime'=> array(
			'label' => 'Catalog list creation time',
			'code' => 'catalog.list.ctime',
			'internalcode' => 'mcatli."ctime"',
			'type' => 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'catalog.list.mtime'=> array(
			'label' => 'Catalog list modification time',
			'code' => 'catalog.list.mtime',
			'internalcode' => 'mcatli."mtime"',
			'type' => 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'catalog.list.editor'=> array(
			'code'=>'catalog.list.editor',
			'internalcode'=>'mcatli."editor"',
			'label'=>'Catalog list editor',
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
		$path = 'classes/catalog/manager/list/submanagers';
		foreach( $this->_getContext()->getConfig()->get( $path, array( 'type' ) ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->_cleanup( $siteids, 'mshop/catalog/manager/list/default/item/delete' );
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

			$path = 'classes/catalog/manager/list/submanagers';
			foreach( $context->getConfig()->get( $path, array( 'type' ) ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for catalog list extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g types, lists etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		/** classes/catalog/manager/list/name
		 * Class name of the used catalog list manager implementation
		 *
		 * Each default catalog list manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Catalog_Manager_List_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Catalog_Manager_List_Mylist
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/catalog/manager/list/name = Mylist
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

		/** mshop/catalog/manager/list/decorators/excludes
		 * Excludes decorators added by the "common" option from the catalog list manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the catalog list manager.
		 *
		 *  mshop/catalog/manager/list/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "mshop/common/manager/decorators/default" for the catalog list manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/catalog/manager/list/decorators/global
		 * @see mshop/catalog/manager/list/decorators/local
		 */

		/** mshop/catalog/manager/list/decorators/global
		 * Adds a list of globally available decorators only to the catalog list manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the catalog list manager.
		 *
		 *  mshop/catalog/manager/list/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the catalog controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/catalog/manager/list/decorators/excludes
		 * @see mshop/catalog/manager/list/decorators/local
		 */

		/** mshop/catalog/manager/list/decorators/local
		 * Adds a list of local decorators only to the catalog list manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the catalog list manager.
		 *
		 *  mshop/catalog/manager/list/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the catalog
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/catalog/manager/list/decorators/excludes
		 * @see mshop/catalog/manager/list/decorators/global
		 */

		return $this->_getSubManager( 'catalog', 'list/' . $manager, $name );
	}


	/**
	 * Returns the config path for retrieving the configuration values.
	 *
	 * @return string Configuration path
	 */
	protected function _getConfigPath()
	{
		return 'mshop/catalog/manager/list/default/item/';
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