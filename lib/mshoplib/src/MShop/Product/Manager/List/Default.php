<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Product
 */


/**
 * Default product list manager for creating and handling product list items.
 * @package MShop
 * @subpackage Product
 */
class MShop_Product_Manager_List_Default
	extends MShop_Common_Manager_List_Abstract
	implements MShop_Product_Manager_List_Interface
{
	private $_searchConfig = array(
		'product.list.id'=> array(
			'code'=>'product.list.id',
			'internalcode'=>'mproli."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_product_list" AS mproli ON ( mpro."id" = mproli."parentid" )' ),
			'label'=>'Product list ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.list.siteid'=> array(
			'code'=>'product.list.siteid',
			'internalcode'=>'mproli."siteid"',
			'label'=>'Product list site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.list.parentid'=> array(
			'code'=>'product.list.parentid',
			'internalcode'=>'mproli."parentid"',
			'label'=>'Product list parent ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.list.domain'=> array(
			'code'=>'product.list.domain',
			'internalcode'=>'mproli."domain"',
			'label'=>'Product list domain',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.list.typeid' => array(
			'code'=>'product.list.typeid',
			'internalcode'=>'mproli."typeid"',
			'label'=>'Product list type ID',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.list.refid'=> array(
			'code'=>'product.list.refid',
			'internalcode'=>'mproli."refid"',
			'label'=>'Product list reference ID',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.list.datestart' => array(
			'code'=>'product.list.datestart',
			'internalcode'=>'mproli."start"',
			'label'=>'Product list start date/time',
			'type'=> 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.list.dateend' => array(
			'code'=>'product.list.dateend',
			'internalcode'=>'mproli."end"',
			'label'=>'Product list end date/time',
			'type'=> 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.list.config' => array(
			'code'=>'product.list.config',
			'internalcode'=>'mproli."config"',
			'label'=>'Product list config',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.list.position' => array(
			'code'=>'product.list.position',
			'internalcode'=>'mproli."pos"',
			'label'=>'Product list position',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'product.list.status' => array(
			'code'=>'product.list.status',
			'internalcode'=>'mproli."status"',
			'label'=>'Product list status',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'product.list.ctime'=> array(
			'code'=>'product.list.ctime',
			'internalcode'=>'mproli."ctime"',
			'label'=>'Product list create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.list.mtime'=> array(
			'code'=>'product.list.mtime',
			'internalcode'=>'mproli."mtime"',
			'label'=>'Product list modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.list.editor'=> array(
			'code'=>'product.list.editor',
			'internalcode'=>'mproli."editor"',
			'label'=>'Product list editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);


	/**
	 * Returns the list attributes that can be used for searching.
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

			$path = 'classes/product/manager/list/submanagers';
			foreach( $context->getConfig()->get( $path, array( 'type' ) ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for product list extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g types, lists etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'product', 'list/' . $manager, $name );
	}


	/**
	 * Returns the config path for retrieving the configuration values.
	 *
	 * @return string Configuration path
	 */
	protected function _getConfigPath()
	{
		return 'mshop/product/manager/list/default/item/';
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


	/**
	 * Returns the name of the requested resource or the name of the default resource.
	 *
	 * @param string $name Name of the requested resource
	 * @return string Name of the resource
	 */
	protected function _getResourceName( $name = 'db-product' )
	{
		return parent::_getResourceName( $name );
	}
}