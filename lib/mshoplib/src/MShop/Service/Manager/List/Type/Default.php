<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Service
 */


/**
 * Default service list type manager for creating and handling service list type items.
 * @package MShop
 * @subpackage Service
 */
class MShop_Service_Manager_List_Type_Default
	extends MShop_Common_Manager_Type_Abstract
	implements MShop_Service_Manager_List_Type_Interface
{
	private $_searchConfig = array(
		'service.list.type.id' => array(
			'code' => 'service.list.type.id',
			'internalcode' => 'mserlity."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_service_list_type" AS mserlity ON ( mserli."typeid" = mserlity."id" )' ),
			'label' => 'Service list type id',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'service.list.type.siteid' => array(
			'code' => 'service.list.type.siteid',
			'internalcode' => 'mserlity."siteid"',
			'label' => 'Service list type site id',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'service.list.type.code' => array(
			'code' => 'service.list.type.code',
			'internalcode' => 'mserlity."code"',
			'label' => 'Service list type code',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.list.type.domain' => array(
			'code' => 'service.list.type.domain',
			'internalcode' => 'mserlity."domain"',
			'label' => 'Service list type domain',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.list.type.label' => array(
			'code' => 'service.list.type.label',
			'internalcode' => 'mserlity."label"',
			'label' => 'Service list type label',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.list.type.status' => array(
			'code' => 'service.list.type.status',
			'internalcode' => 'mserlity."status"',
			'label' => 'Service list type status',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'service.list.type.ctime'=> array(
			'code'=>'service.list.type.ctime',
			'internalcode'=>'mserlity."ctime"',
			'label'=>'Service list type create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.list.type.mtime'=> array(
			'code'=>'service.list.type.mtime',
			'internalcode'=>'mserlity."mtime"',
			'label'=>'Service list type modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.list.type.editor'=> array(
			'code'=>'service.list.type.editor',
			'internalcode'=>'mserlity."editor"',
			'label'=>'Service list type editor',
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

			$path = 'classes/service/manager/list/type/submanagers';
			foreach( $context->getConfig()->get($path, array() ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for service list type extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g types, lists etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'service', 'list/type/' . $manager, $name );
	}


	/**
	 * Gets the config path for configuration.
	 */
	protected function _getConfigPath()
	{
		return 'mshop/service/manager/list/type/default/item/';
	}


	/**
	 * Gets the searchConfig for search.
	 */
	protected function _getSearchConfig()
	{
		return $this->_searchConfig;
	}
}