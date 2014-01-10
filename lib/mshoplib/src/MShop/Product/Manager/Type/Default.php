<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Product
 */


/**
 * Default product type manager for creating and handling product type items.
 * @package MShop
 * @subpackage Product
 */
class MShop_Product_Manager_Type_Default
	extends MShop_Common_Manager_Type_Abstract
	implements MShop_Product_Manager_Type_Interface
{
	private $_searchConfig = array(
		'product.type.id' => array(
			'code'=>'product.type.id',
			'internalcode'=>'mproty."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_product_type" AS mproty ON ( mpro."typeid" = mproty."id" )' ),
			'label'=>'Product type ID',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.type.siteid' => array(
			'code'=>'product.type.siteid',
			'internalcode'=>'mproty."siteid"',
			'label'=>'Product type site ID',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.type.code' => array(
			'code'=>'product.type.code',
			'internalcode'=>'mproty."code"',
			'label'=>'Product type code',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.type.domain' => array(
			'code'=>'product.type.domain',
			'internalcode'=>'mproty."domain"',
			'label'=>'Product type domain',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.type.label' => array(
			'code'=>'product.type.label',
			'internalcode'=>'mproty."label"',
			'label'=>'Product type label',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.type.status' => array(
			'code'=>'product.type.status',
			'internalcode'=>'mproty."status"',
			'label'=>'Product type status',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'product.type.ctime'=> array(
			'code'=>'product.type.ctime',
			'internalcode'=>'mproty."ctime"',
			'label'=>'Product type create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.type.mtime'=> array(
			'code'=>'product.type.mtime',
			'internalcode'=>'mproty."mtime"',
			'label'=>'Product type modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.type.editor'=> array(
			'code'=>'product.type.editor',
			'internalcode'=>'mproty."editor"',
			'label'=>'Product type editor',
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

			$path = 'classes/product/manager/type/submanagers';
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
		return $this->_getSubManager( 'product', 'type/' . $manager, $name );
	}


	/**
	 * Gets the config path for configuration.
	 */
	protected function _getConfigPath()
	{
		return 'mshop/product/manager/type/default/item/';
	}


	/**
	 * Gets the searchConfig for search.
	 */
	protected function _getSearchConfig()
	{
		return $this->_searchConfig;
	}
}