<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Text
 */


/**
 * Default text list manager for creating and handling text list items.
 * @package MShop
 * @subpackage Text
 */
class MShop_Text_Manager_List_Default
	extends MShop_Common_Manager_List_Abstract
	implements MShop_Text_Manager_List_Interface
{
	private $_searchConfig = array(
		'text.list.id'=> array(
			'code'=>'text.list.id',
			'internalcode'=>'mtexli."id"',
			'internaldeps'=> array( 'LEFT JOIN "mshop_text_list" AS mtexli ON mtexli."parentid" = mtex."id"' ),
			'label'=>'Text list ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'text.list.siteid'=> array(
			'code'=>'text.list.siteid',
			'internalcode'=>'mtexli."siteid"',
			'label'=>'Text list site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'text.list.parentid'=> array(
			'code'=>'text.list.parentid',
			'internalcode'=>'mtexli."parentid"',
			'label'=>'Text list parent Id',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'text.list.domain'=> array(
			'code'=>'text.list.domain',
			'internalcode'=>'mtexli."domain"',
			'label'=>'Text list domain',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.list.typeid'=> array(
			'code'=>'text.list.typeid',
			'internalcode'=>'mtexli."typeid"',
			'label'=>'Text list type ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'text.list.refid'=> array(
			'code'=>'text.list.refid',
			'internalcode'=>'mtexli."refid"',
			'label'=>'Text list reference ID',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.list.datestart' => array(
			'code'=>'text.list.datestart',
			'internalcode'=>'mtexli."start"',
			'label'=>'Text list start date',
			'type'=> 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.list.dateend' => array(
			'code'=>'text.list.dateend',
			'internalcode'=>'mtexli."end"',
			'label'=>'Text list end date',
			'type'=> 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.list.config' => array(
			'code'=>'text.list.config',
			'internalcode'=>'mtexli."config"',
			'label'=>'Text list config',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.list.position' => array(
			'code'=>'text.list.position',
			'internalcode'=>'mtexli."pos"',
			'label'=>'Text list position',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'text.list.status' => array(
			'code'=>'text.list.status',
			'internalcode'=>'mtexli."status"',
			'label'=>'Text list status',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'text.list.ctime'=> array(
			'code'=>'text.list.ctime',
			'internalcode'=>'mtexli."ctime"',
			'label'=>'Text list create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.list.mtime'=> array(
			'code'=>'text.list.mtime',
			'internalcode'=>'mtexli."mtime"',
			'label'=>'Text list modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.list.editor'=> array(
			'code'=>'text.list.editor',
			'internalcode'=>'mtexli."editor"',
			'label'=>'Text list editor',
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
		$this->_setResourceName( 'db-text' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param array $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'classes/text/manager/list/submanagers';
		foreach( $this->_getContext()->getConfig()->get( $path, array( 'type' ) ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->_cleanup( $siteids, 'mshop/text/manager/list/default/item/delete' );
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

			$path = 'classes/text/manager/list/submanagers';
			foreach( $context->getConfig()->get( $path, array( 'type' ) ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for text list extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g types, lists etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		/** classes/text/manager/list/name
		 * Class name of the used text list manager implementation
		 *
		 * Each default text list manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Text_Manager_List_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Text_Manager_List_Mylist
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/text/manager/list/name = Mylist
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

		/** mshop/text/manager/list/decorators/excludes
		 * Excludes decorators added by the "common" option from the text list manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the text list manager.
		 *
		 *  mshop/text/manager/list/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "mshop/common/manager/decorators/default" for the text list manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/text/manager/list/decorators/global
		 * @see mshop/text/manager/list/decorators/local
		 */

		/** mshop/text/manager/list/decorators/global
		 * Adds a list of globally available decorators only to the text list manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the text list manager.
		 *
		 *  mshop/text/manager/list/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the text controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/text/manager/list/decorators/excludes
		 * @see mshop/text/manager/list/decorators/local
		 */

		/** mshop/text/manager/list/decorators/local
		 * Adds a list of local decorators only to the text list manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the text list manager.
		 *
		 *  mshop/text/manager/list/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the text
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/text/manager/list/decorators/excludes
		 * @see mshop/text/manager/list/decorators/global
		 */

		return $this->_getSubManager( 'text', 'list/' . $manager, $name );
	}


	/**
	 * Returns the config path for retrieving the configuration values.
	 *
	 * @return string Configuration path
	 */
	protected function _getConfigPath()
	{
		return 'mshop/text/manager/list/default/item/';
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