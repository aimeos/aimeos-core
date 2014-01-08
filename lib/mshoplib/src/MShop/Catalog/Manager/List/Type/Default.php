<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Catalog
 */


/**
 * Default catalog list type manager for creating and handling catalog list type items.
 * @package MShop
 * @subpackage Catalog
 */
class MShop_Catalog_Manager_List_Type_Default
	extends MShop_Common_Manager_Type_Default
	implements MShop_Catalog_Manager_List_Type_Interface
{
	private $_searchConfig = array(
		'catalog.list.type.id' => array(
			'code'=>'catalog.list.type.id',
			'internalcode'=>'mcatlity."id"',
			'internaldeps'=>array('LEFT JOIN "mshop_catalog_list_type" as mcatlity ON ( mcatli."typeid" = mcatlity."id" )'),
			'label'=>'Catalog list type ID',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'catalog.list.type.siteid' => array(
			'code'=>'catalog.list.type.siteid',
			'internalcode'=>'mcatlity."siteid"',
			'label'=>'Catalog list type site ID',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'catalog.list.type.code' => array(
			'code'=>'catalog.list.type.code',
			'internalcode'=>'mcatlity."code"',
			'label'=>'Catalog list type code',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'catalog.list.type.domain' => array(
			'code'=>'catalog.list.type.domain',
			'internalcode'=>'mcatlity."domain"',
			'label'=>'Catalog list type domain',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'catalog.list.type.label' => array(
			'code' => 'catalog.list.type.label',
			'internalcode' => 'mcatlity."label"',
			'label' => 'Catalog list type label',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'catalog.list.type.status' => array(
			'code' => 'catalog.list.type.status',
			'internalcode' => 'mcatlity."status"',
			'label' => 'Catalog list type status',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'catalog.list.type.ctime'=> array(
			'label' => 'Catalog list type creation time',
			'code' => 'catalog.list.type.ctime',
			'internalcode' => 'mcatlity."ctime"',
			'type' => 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'catalog.list.type.mtime'=> array(
			'label' => 'Catalog list type modification time',
			'code' => 'catalog.list.type.mtime',
			'internalcode' => 'mcatlity."mtime"',
			'type' => 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'catalog.list.type.editor'=> array(
			'code'=>'catalog.list.type.editor',
			'internalcode'=>'mcatlity."editor"',
			'label'=>'Catalog list type editor',
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
		$confpath = 'mshop/catalog/manager/list/type/default/item/';
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
	 * @param boolean $withsub Return also catalogs of sub-managers if true
	 * @return array List of catalog items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = parent::getSearchAttributes();

		if( $withsub === true )
		{
			$context = $this->_getContext();

			$path = 'classes/catalog/manager/list/type/submanagers';
			foreach( $context->getConfig()->get($path, array() ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for catalog list type extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g types, lists etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'catalog', 'list/type/' . $manager, $name );
	}
}