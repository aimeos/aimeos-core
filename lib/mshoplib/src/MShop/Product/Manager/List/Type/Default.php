<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Product
 */


/**
 * Default product list type manager for creating and handling product list type items.
 * @package MShop
 * @subpackage Product
 */
class MShop_Product_Manager_List_Type_Default
	extends MShop_Common_Manager_Type_Abstract
	implements MShop_Product_Manager_List_Type_Interface
{
	private $_searchConfig = array(
		'product.list.type.id' => array(
			'code'=>'product.list.type.id',
			'internalcode'=>'mprolity."id"',
			'internaldeps'=>array( 'LEFT JOIN "mshop_product_list_type" AS mprolity ON ( mproli."typeid" = mprolity."id" )' ),
			'label'=>'Product list type ID',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.list.type.siteid' => array(
			'code'=>'product.list.type.siteid',
			'internalcode'=>'mprolity."siteid"',
			'label'=>'Product list type site ID',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.list.type.code' => array(
			'code'=>'product.list.type.code',
			'internalcode'=>'mprolity."code"',
			'label'=>'Product list type code',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.list.type.domain' => array(
			'code'=>'product.list.type.domain',
			'internalcode'=>'mprolity."domain"',
			'label'=>'Product list type domain',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.list.type.label' => array(
			'code'=>'product.list.type.label',
			'internalcode'=>'mprolity."label"',
			'label'=>'Product list type label',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.list.type.status' => array(
			'code'=>'product.list.type.status',
			'internalcode'=>'mprolity."status"',
			'label'=>'Product list type status',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'product.list.type.ctime'=> array(
			'code'=>'product.list.type.ctime',
			'internalcode'=>'mprolity."ctime"',
			'label'=>'Product list type create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.list.type.mtime'=> array(
			'code'=>'product.list.type.mtime',
			'internalcode'=>'mprolity."mtime"',
			'label'=>'Product list type modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.list.type.editor'=> array(
			'code'=>'product.list.type.editor',
			'internalcode'=>'mprolity."editor"',
			'label'=>'Product list type editor',
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

			$path = 'classes/product/manager/list/type/submanagers';
			foreach( $context->getConfig()->get($path, array() ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for product list type extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g types, lists etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'product', 'list/type/' . $manager, $name );
	}


	/**
	 * Returns the config path for retrieving the configuration values.
	 *
	 * @return string Configuration path
	 */
	protected function _getConfigPath()
	{
		return 'mshop/product/manager/list/type/default/item/';
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