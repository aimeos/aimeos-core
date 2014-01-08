<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Service
 */


/**
 * Default service type manager for creating and handling service type items.
 * @package MShop
 * @subpackage Service
 */
class MShop_Service_Manager_Type_Default
	extends MShop_Common_Manager_Type_Default
	implements MShop_Service_Manager_Type_Interface
{
	private $_searchConfig = array(
		'service.type.id' => array(
			'code' => 'service.type.id',
			'internalcode' => 'mserty."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_service_type" AS mserty ON ( mser."typeid" = mserty."id" )' ),
			'label' => 'Service type ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'service.type.siteid' => array(
			'code' => 'service.type.siteid',
			'internalcode' => 'mserty."siteid"',
			'label' => 'Service type site ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'service.type.code' => array(
			'code' => 'service.type.code',
			'internalcode' => 'mserty."code"',
			'label' => 'Service type code',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.type.domain' => array(
			'code' => 'service.type.domain',
			'internalcode' => 'mserty."domain"',
			'label' => 'Service type domain',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.type.label' => array(
			'code' => 'service.type.label',
			'internalcode' => 'mserty."label"',
			'label' => 'Service type label',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.type.status' => array(
			'code' => 'service.type.status',
			'internalcode' => 'mserty."status"',
			'label' => 'Service type status',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'service.type.ctime'=> array(
			'code'=>'service.type.ctime',
			'internalcode'=>'mserty."ctime"',
			'label'=>'Service type create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.type.mtime'=> array(
			'code'=>'service.type.mtime',
			'internalcode'=>'mserty."mtime"',
			'label'=>'Service type modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'service.type.editor'=> array(
			'code'=>'service.type.editor',
			'internalcode'=>'mserty."editor"',
			'label'=>'Service type editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);


	/**
	 * Creates the type manager using the given context object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 * @param array $config Associative list of SQL statements
	 * @param array $searchConfig Associative list of search configuration
	 *
	 * @throws MShop_Common_Exception if no configuration is available
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		$config = $context->getConfig();
		$confpath = 'mshop/service/manager/type/default/item/';
		$conf = array(
			'insert' => $config->get( $confpath . 'insert' ),
			'update' => $config->get( $confpath . 'update' ),
			'delete' => $config->get( $confpath . 'delete' ),
			'search' => $config->get( $confpath . 'search' ),
			'count' => $config->get( $confpath . 'count' ),
			'newid' => $config->get( $confpath . 'newid' ),
		);


		parent::__construct( $context, $conf, $this->_searchConfig );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = array();

		foreach( $this->_searchConfig as $key => $fields ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default( $fields );
		}

		if( $withsub === true )
		{
			$context = $this->_getContext();

			$path = 'classes/service/manager/type/submanagers';
			foreach( $context->getConfig()->get($path, array() ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}
}