<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Product
 */


/**
 * Default product tag type manager for creating and handling product tag type items.
 * @package MShop
 * @subpackage Product
 */
class MShop_Product_Manager_Tag_Type_Default
	extends MShop_Common_Manager_Type_Abstract
	implements MShop_Product_Manager_Tag_Type_Interface
{
	private $_searchConfig = array(
		'product.tag.type.id' => array(
			'code' => 'product.tag.type.id',
			'internalcode' => 'mprotaty."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_product_tag_type" AS mprotaty ON ( mprota."typeid" = mprotaty."id" )' ),
			'label' => 'Product tag type ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.tag.type.siteid' => array(
			'code' => 'product.tag.type.siteid',
			'internalcode' => 'mprotaty."siteid"',
			'label' => 'Product tag type site ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.tag.type.code' => array(
			'code' => 'product.tag.type.code',
			'internalcode' => 'mprotaty."code"',
			'label' => 'Product tag type code',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.tag.type.domain' => array(
			'code' => 'product.tag.type.domain',
			'internalcode' => 'mprotaty."domain"',
			'label' => 'Product tag type domain',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.tag.type.label' => array(
			'code' => 'product.tag.type.label',
			'internalcode' => 'mprotaty."label"',
			'label' => 'Product tag type label',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.tag.type.status' => array(
			'code' => 'product.tag.type.status',
			'internalcode' => 'mprotaty."status"',
			'label' => 'Product tag type status',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'product.tag.type.mtime'=> array(
			'code'=>'product.tag.type.mtime',
			'internalcode'=>'mprotaty."mtime"',
			'label'=>'Product tag type modification date',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.tag.type.ctime'=> array(
			'code'=>'product.tag.type.ctime',
			'internalcode'=>'mprotaty."ctime"',
			'label'=>'Product tag type creation date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.tag.type.editor'=> array(
			'code'=>'product.tag.type.editor',
			'internalcode'=>'mprotaty."editor"',
			'label'=>'Product tag type editor',
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
		$this->_setResourceName( 'db-product' );
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

			$path = 'classes/product/manager/tag/type/submanagers';
			foreach( $context->getConfig()->get($path, array() ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for product type extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g types, lists etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'product', 'tag/type/' . $manager, $name );
	}


	/**
	 * Returns the config path for retrieving the configuration values.
	 *
	 * @return string Configuration path
	 */
	protected function _getConfigPath()
	{
		return 'mshop/product/manager/tag/type/default/item/';
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