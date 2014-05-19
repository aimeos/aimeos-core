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