<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Customer
 */


/**
 * Default customer list type manager for creating and handling customer list type items.
 * @package MShop
 * @subpackage Customer
 */
class MShop_Customer_Manager_List_Type_Default
	extends MShop_Common_Manager_Type_Default
	implements MShop_Customer_Manager_List_Type_Interface
{
	private $_searchConfig = array(
		'customer.list.type.id' => array(
			'code'=>'customer.list.type.id',
			'internalcode'=>'mcuslity."id"',
			'internaldeps'=>array('LEFT JOIN "mshop_customer_list_type" AS mcuslity ON ( mcusli."typeid" = mcuslity."id" )'),
			'label'=>'Customer list type Id',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'customer.list.type.siteid' => array(
			'code'=>'customer.list.type.siteid',
			'internalcode'=>'mcuslity."siteid"',
			'label'=>'Customer list type site Id',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'customer.list.type.code' => array(
			'code'=>'customer.list.type.code',
			'internalcode'=>'mcuslity."code"',
			'label'=>'Customer list type code',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.list.type.domain' => array(
			'code'=>'customer.list.type.domain',
			'internalcode'=>'mcuslity."domain"',
			'label'=>'Customer list type domain',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.list.type.label' => array(
			'code'=>'customer.list.type.label',
			'internalcode'=>'mcuslity."label"',
			'label'=>'Customer list type label',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'customer.list.type.status' => array(
			'code'=>'customer.list.type.status',
			'internalcode'=>'mcuslity."status"',
			'label'=>'Customer list type status',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'customer.list.type.ctime'=> array(
			'code'=>'customer.list.type.ctime',
			'internalcode'=>'mcuslity."ctime"',
			'label'=>'Customer list type create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR
		),
		'customer.list.type.mtime'=> array(
			'code'=>'customer.list.type.mtime',
			'internalcode'=>'mcuslity."mtime"',
			'label'=>'Customer list type modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR
		),
		'customer.list.type.editor'=> array(
			'code'=>'customer.list.type.editor',
			'internalcode'=>'mcuslity."editor"',
			'label'=>'Customer list type editor',
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
		$confpath = 'mshop/customer/manager/list/type/default/item/';
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

			$path = 'classes/customer/manager/list/type/submanagers';
			foreach( $context->getConfig()->get($path, array() ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}
}