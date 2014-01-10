<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Attribute
 */


/**
 * Default attribute list type manager for creating and handling attribute list type items.
 * @package MShop
 * @subpackage Attribute
 */
class MShop_Attribute_Manager_List_Type_Default
	extends MShop_Common_Manager_Type_Abstract
	implements MShop_Attribute_Manager_List_Type_Interface
{
	private $_searchConfig = array(
		'attribute.list.type.id' => array(
			'code'=>'attribute.list.type.id',
			'internalcode'=>'mattlity."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_attribute_list_type" AS mattlity ON ( mattli."typeid" = mattlity."id" )' ),
			'label'=>'Attribute list type Id',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'attribute.list.type.siteid' => array(
			'code'=>'attribute.list.type.siteid',
			'internalcode'=>'mattlity."siteid"',
			'label'=>'Attribute list type site Id',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'attribute.list.type.code' => array(
			'code'=>'attribute.list.type.code',
			'internalcode'=>'mattlity."code"',
			'label'=>'Attribute list type code',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.list.type.domain' => array(
			'code'=>'attribute.list.type.domain',
			'internalcode'=>'mattlity."domain"',
			'label'=>'Attribute list type domain',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.list.type.label' => array(
			'code' => 'attribute.list.type.label',
			'internalcode' => 'mattlity."label"',
			'label' => 'Attribute list type label',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.list.type.status' => array(
			'code' => 'attribute.list.type.status',
			'internalcode' => 'mattlity."status"',
			'label' => 'Attribute list type status',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'attribute.list.type.ctime'=> array(
			'code'=>'attribute.list.type.ctime',
			'internalcode'=>'mattlity."ctime"',
			'label'=>'Attribute list type create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.list.type.mtime'=> array(
			'code'=>'attribute.list.type.mtime',
			'internalcode'=>'mattlity."mtime"',
			'label'=>'Attribute list type modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'attribute.list.type.editor'=> array(
			'code'=>'attribute.list.type.editor',
			'internalcode'=>'mattlity."editor"',
			'label'=>'Attribute list type editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);


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

			$path = 'classes/attribute/manager/list/type/submanagers';
			foreach( $context->getConfig()->get($path, array() ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for attribute list type extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g types, lists etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'attribute', 'list/type/' . $manager, $name );
	}


	/**
	 * Gets the config path for configuration.
	 */
	protected function _getConfigPath()
	{
		return 'mshop/attribute/manager/list/type/default/item/';
	}


	/**
	 * Gets the searchConfig for search.
	 */
	protected function _getSearchConfig()
	{
		return $this->_searchConfig;
	}
}