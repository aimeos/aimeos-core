<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Media
 */


/**
 * Default media list manager for creating and handling media list items.
 * @package MShop
 * @subpackage Media
 */
class MShop_Media_Manager_List_Default
	extends MShop_Common_Manager_List_Abstract
	implements MShop_Media_Manager_List_Interface
{
	private $_searchConfig = array(
		'media.list.id'=> array(
			'code'=>'media.list.id',
			'internalcode'=>'mmedli."id"',
			'internaldeps'=> array( 'LEFT JOIN "mshop_media_list" AS mmedli ON ( mmed."id" = mmedli."parentid" )' ),
			'label'=>'Media list ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'media.list.siteid'=> array(
			'code'=>'media.list.siteid',
			'internalcode'=>'mmedli."siteid"',
			'label'=>'Media list site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'media.list.parentid'=> array(
			'code'=>'media.list.parentid',
			'internalcode'=>'mmedli."parentid"',
			'label'=>'Media list media ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'media.list.domain'=> array(
			'code'=>'media.list.domain',
			'internalcode'=>'mmedli."domain"',
			'label'=>'Media list domain',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.list.typeid'=> array(
			'code'=>'media.list.typeid',
			'internalcode'=>'mmedli."typeid"',
			'label'=>'Media list type ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'media.list.refid'=> array(
			'code'=>'media.list.refid',
			'internalcode'=>'mmedli."refid"',
			'label'=>'Media list reference ID',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.list.datestart' => array(
			'code'=>'media.list.datestart',
			'internalcode'=>'mmedli."start"',
			'label'=>'Media list start date',
			'type'=> 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.list.dateend' => array(
			'code'=>'media.list.dateend',
			'internalcode'=>'mmedli."end"',
			'label'=>'Media list end date',
			'type'=> 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.list.config' => array(
			'code'=>'media.list.config',
			'internalcode'=>'mmedli."config"',
			'label'=>'Media list config',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.list.position' => array(
			'code'=>'media.list.position',
			'internalcode'=>'mmedli."pos"',
			'label'=>'Media list position',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'media.list.status' => array(
			'code'=>'media.list.status',
			'internalcode'=>'mmedli."status"',
			'label'=>'Media list status',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'media.list.ctime'=> array(
			'code'=>'media.list.ctime',
			'internalcode'=>'mmedli."ctime"',
			'label'=>'Media list create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.list.mtime'=> array(
			'code'=>'media.list.mtime',
			'internalcode'=>'mmedli."mtime"',
			'label'=>'Media list modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.list.editor'=> array(
			'code'=>'media.list.editor',
			'internalcode'=>'mmedli."editor"',
			'label'=>'Media list editor',
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
		$this->_setResourceName( 'db-media' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param array $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'classes/media/manager/list/submanagers';
		foreach( $this->_getContext()->getConfig()->get( $path, array( 'type' ) ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->_cleanup( $siteids, 'mshop/media/manager/list/default/item/delete' );
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

			$path = 'classes/media/manager/list/submanagers';
			foreach( $context->getConfig()->get( $path, array( 'type' ) ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for media list extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g types, lists etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		/** classes/media/manager/list/name
		 * Class name of the used media list manager implementation
		 *
		 * Each default media list manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Media_Manager_List_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Media_Manager_List_Mylist
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/media/manager/list/name = Mylist
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

		/** mshop/media/manager/list/decorators/excludes
		 * Excludes decorators added by the "common" option from the media list manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the media list manager.
		 *
		 *  mshop/media/manager/list/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "mshop/common/manager/decorators/default" for the media list manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/media/manager/list/decorators/global
		 * @see mshop/media/manager/list/decorators/local
		 */

		/** mshop/media/manager/list/decorators/global
		 * Adds a list of globally available decorators only to the media list manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the media list manager.
		 *
		 *  mshop/media/manager/list/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the media controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/media/manager/list/decorators/excludes
		 * @see mshop/media/manager/list/decorators/local
		 */

		/** mshop/media/manager/list/decorators/local
		 * Adds a list of local decorators only to the media list manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the media list manager.
		 *
		 *  mshop/media/manager/list/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the media
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/media/manager/list/decorators/excludes
		 * @see mshop/media/manager/list/decorators/global
		 */

		return $this->_getSubManager( 'media', 'list/' . $manager, $name );
	}


	/**
	 * Returns the config path for retrieving the configuration values.
	 *
	 * @return string Configuration path
	 */
	protected function _getConfigPath()
	{
		return 'mshop/media/manager/list/default/item/';
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