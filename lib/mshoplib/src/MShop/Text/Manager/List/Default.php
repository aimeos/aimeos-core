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
		return $this->_getSubManager( 'text', 'list/' . $manager, $name );
	}


	/**
	 * Gets the config path for configuration.
	 */
	protected function _getConfigPath()
	{
		return 'mshop/text/manager/list/default/item/';
	}


	/**
	 * Gets the searchConfig for search.
	 */
	protected function _getSearchConfig()
	{
		return $this->_searchConfig;
	}
}