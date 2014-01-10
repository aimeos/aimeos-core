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
		return $this->_getSubManager( 'attribute', 'list/' . $manager, $name );
	}


	/**
	 * Gets the config path for configuration.
	 */
	protected function _getConfigPath()
	{
		return 'mshop/attribute/manager/list/default/item/';
	}


	/**
	 * Gets the searchConfig for search.
	 */
	protected function _getSearchConfig()
	{
		return $this->_searchConfig;
	}
}