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
	extends MShop_Common_Manager_Type_Abstract
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
	 * Initializes the object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );
		$this->_setResourceName( 'db-text' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param array $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'classes/text/manager/type/submanagers';
		foreach( $this->_getContext()->getConfig()->get( $path, array() ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->_cleanup( $siteids, 'mshop/text/manager/type/default/item/delete' );
	}


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

			$path = 'classes/text/manager/type/submanagers';
			foreach( $context->getConfig()->get($path, array() ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for text type extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g types, lists etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'text', 'type/' . $manager, $name );
	}


	/**
	 * Returns the config path for retrieving the configuration values.
	 *
	 * @return string Configuration path
	 */
	protected function _getConfigPath()
	{
		return 'mshop/text/manager/type/default/item/';
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