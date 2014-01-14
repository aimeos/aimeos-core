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