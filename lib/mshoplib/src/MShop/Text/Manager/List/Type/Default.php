<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Text
 */


/**
 * Default text list type manager for creating and handling text list type items.
 * @package MShop
 * @subpackage Text
 */
class MShop_Text_Manager_List_Type_Default
	extends MShop_Common_Manager_Type_Default
	implements MShop_Text_Manager_List_Type_Interface
{
	private $_searchConfig = array(
		'text.list.type.id' => array(
			'code'=>'text.list.type.id',
			'internalcode'=>'mtexlity."id"',
			'internaldeps'=> array( 'LEFT JOIN "mshop_text_list_type" AS mtexlity ON mtexlity."id" = mtexli."typeid"' ),
			'label'=>'Text list type Id',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'text.list.type.siteid' => array(
			'code'=>'text.list.type.siteid',
			'internalcode'=>'mtexlity."siteid"',
			'label'=>'Text list type site Id',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'text.list.type.code' => array(
			'code'=>'text.list.type.code',
			'internalcode'=>'mtexlity."code"',
			'label'=>'Text list type code',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.list.type.domain' => array(
			'code'=>'text.list.type.domain',
			'internalcode'=>'mtexlity."domain"',
			'label'=>'Text list type domain',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.list.type.label' => array(
			'code'=>'text.list.type.label',
			'internalcode'=>'mtexlity."label"',
			'label'=>'Text list type label',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.list.type.status' => array(
			'code'=>'text.list.type.status',
			'internalcode'=>'mtexlity."status"',
			'label'=>'Text list type status',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'text.list.type.ctime'=> array(
			'code'=>'text.list.type.ctime',
			'internalcode'=>'mtexlity."ctime"',
			'label'=>'Text list type create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.list.type.mtime'=> array(
			'code'=>'text.list.type.mtime',
			'internalcode'=>'mtexlity."mtime"',
			'label'=>'Text list type modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.list.type.editor'=> array(
			'code'=>'text.list.type.editor',
			'internalcode'=>'mtexlity."editor"',
			'label'=>'Text list type editor',
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
		$confpath = 'mshop/text/manager/list/type/default/item/';
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

			$path = 'classes/text/manager/list/type/submanagers';
			foreach( $context->getConfig()->get($path, array() ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}
}