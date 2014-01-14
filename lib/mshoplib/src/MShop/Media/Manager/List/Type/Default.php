<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Media
 */


/**
 * Default media list type manager for creating and handling media list type items.
 * @package MShop
 * @subpackage Media
 */
class MShop_Media_Manager_List_Type_Default
	extends MShop_Common_Manager_Type_Abstract
	implements MShop_Media_Manager_List_Type_Interface
{
	private $_searchConfig = array(
		'media.list.type.id' => array(
			'code'=>'media.list.type.id',
			'internalcode'=>'mmedlity."id"',
			'internaldeps'=> array( 'LEFT JOIN "mshop_media_list_type" AS mmedlity ON ( mmedli."typeid" = mmedlity."id" )' ),
			'label'=>'Media list type Id',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'media.list.type.siteid' => array(
			'code'=>'media.list.type.siteid',
			'internalcode'=>'mmedlity."siteid"',
			'label'=>'Media list type site Id',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'media.list.type.code' => array(
			'code'=>'media.list.type.code',
			'internalcode'=>'mmedlity."code"',
			'label'=>'Media list type code',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.list.type.domain' => array(
			'code'=>'media.list.type.domain',
			'internalcode'=>'mmedlity."domain"',
			'label'=>'Media list type domain',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.list.type.label' => array(
			'label' => 'Media list type label',
			'code' => 'media.list.type.label',
			'internalcode' => 'mmedlity."label"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.list.type.status' => array(
			'label' => 'Media list type status',
			'code' => 'media.list.type.status',
			'internalcode' => 'mmedlity."status"',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'media.list.type.ctime'=> array(
			'code'=>'media.list.type.ctime',
			'internalcode'=>'mmedlity."ctime"',
			'label'=>'Media list type create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.list.type.mtime'=> array(
			'code'=>'media.list.type.mtime',
			'internalcode'=>'mmedlity."mtime"',
			'label'=>'Media list type modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'media.list.type.editor'=> array(
			'code'=>'media.list.type.editor',
			'internalcode'=>'mmedlity."editor"',
			'label'=>'Media list type editor',
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

			$path = 'classes/media/manager/list/type/submanagers';
			foreach( $context->getConfig()->get($path, array() ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for media list type extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g types, lists etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'media', 'list/type/' . $manager, $name );
	}



	/**
	 * Returns the config path for retrieving the configuration values.
	 *
	 * @return string Configuration path
	 */
	protected function _getConfigPath()
	{
		return 'mshop/media/manager/list/type/default/item/';
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