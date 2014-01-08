<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Text
 */


/**
 * Default text type manager for creating and handling text type items.
 * @package MShop
 * @subpackage Text
 */
class MShop_Text_Manager_Type_Default
	extends MShop_Common_Manager_Type_Default
	implements MShop_Text_Manager_Type_Interface
{
	private $_searchConfig = array(
		'text.type.id'=> array(
			'code'=>'text.type.id',
			'internalcode'=>'mtexty."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_text_type" AS mtexty ON ( mtex."typeid" = mtexty."id" )' ),
			'label'=>'Text type ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'text.type.siteid'=> array(
			'code'=>'text.type.siteid',
			'internalcode'=>'mtexty."siteid"',
			'label'=>'Text type site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'text.type.code' => array(
			'code'=>'text.type.code',
			'internalcode'=>'mtexty."code"',
			'label'=>'Text type code',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.type.domain' => array(
			'code'=>'text.type.domain',
			'internalcode'=>'mtexty."domain"',
			'label'=>'Text type domain',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.type.label' => array(
			'code'=>'text.type.label',
			'internalcode'=>'mtexty."label"',
			'label'=>'Text type label',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.type.status' => array(
			'code'=>'text.type.status',
			'internalcode'=>'mtexty."status"',
			'label'=>'Text type status',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'text.type.ctime'=> array(
			'code'=>'text.type.ctime',
			'internalcode'=>'mtexty."ctime"',
			'label'=>'Text type create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.type.mtime'=> array(
			'code'=>'text.type.mtime',
			'internalcode'=>'mtexty."mtime"',
			'label'=>'Text type modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'text.type.editor'=> array(
			'code'=>'text.type.editor',
			'internalcode'=>'mtexty."editor"',
			'label'=>'Text type editor',
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
		$confpath = 'mshop/text/manager/type/default/item/';
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

			$path = 'classes/text/manager/type/submanagers';
			foreach( $context->getConfig()->get($path, array() ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}
}