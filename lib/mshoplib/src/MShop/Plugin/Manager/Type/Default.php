<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Plugin
 */


/**
 * Default plugin type manager for creating and handling plugin type items.
 * @package MShop
 * @subpackage Plugin
 */
class MShop_Plugin_Manager_Type_Default
	extends MShop_Common_Manager_Type_Abstract
	implements MShop_Plugin_Manager_Type_Interface
{
	private $_searchConfig = array(
		'plugin.type.id'=> array(
			'code'=>'plugin.type.id',
			'internalcode'=>'mpluty."id"',
			'internaldeps'=> array( 'LEFT JOIN "mshop_plugin_type" AS mpluty ON ( mpluty."id" = mplu."typeid" )' ),
			'label'=>'Plugin type ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'plugin.type.siteid'=> array(
			'code'=>'plugin.type.siteid',
			'internalcode'=>'mpluty."siteid"',
			'label'=>'Plugin type site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'plugin.type.code' => array(
			'code'=>'plugin.type.code',
			'internalcode'=>'mpluty."code"',
			'label'=>'Plugin type code',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'plugin.type.domain' => array(
			'code'=>'plugin.type.domain',
			'internalcode'=>'mpluty."domain"',
			'label'=>'Plugin type domain',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'plugin.type.label' => array(
			'code'=>'plugin.type.label',
			'internalcode'=>'mpluty."label"',
			'label'=>'Plugin type label',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'plugin.type.status' => array(
			'code'=>'plugin.type.status',
			'internalcode'=>'mpluty."status"',
			'label'=>'Plugin type status',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'plugin.type.mtime'=> array(
			'code'=>'plugin.type.mtime',
			'internalcode'=>'mpluty."mtime"',
			'label'=>'Plugin type modification date',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'plugin.type.ctime'=> array(
			'code'=>'plugin.type.ctime',
			'internalcode'=>'mpluty."ctime"',
			'label'=>'Plugin type creation date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'plugin.type.editor'=> array(
			'code'=>'plugin.type.editor',
			'internalcode'=>'mpluty."editor"',
			'label'=>'Plugin type editor',
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

			$path = 'classes/plugin/manager/type/submanagers';
			foreach( $context->getConfig()->get($path, array() ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for plugin type extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g types, lists etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'plugin', 'type/' . $manager, $name );
	}


	/**
	 * Returns the config path for retrieving the configuration values.
	 *
	 * @return string Configuration path
	 */
	protected function _getConfigPath()
	{
		return 'mshop/plugin/manager/type/default/item/';
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