<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Price
 */


/**
 * Default price type manager for creating and handling price type items.
 * @package MShop
 * @subpackage Price
 */
class MShop_Price_Manager_Type_Default
	extends MShop_Common_Manager_Type_Abstract
	implements MShop_Price_Manager_Type_Interface
{
	private $_searchConfig = array(
		'price.type.id' => array(
			'code'=>'price.type.id',
			'internalcode'=>'mprity."id"',
			'internaldeps'=> array( 'LEFT JOIN "mshop_price_type" AS mprity ON mpri.typeid = mprity.id' ),
			'label'=>'Price type ID',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'price.type.siteid' => array(
			'code'=>'price.type.siteid',
			'internalcode'=>'mprity."siteid"',
			'label'=>'Price type site ID',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'price.type.code' => array(
			'code'=>'price.type.code',
			'internalcode'=>'mprity."code"',
			'label'=>'Price type code',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'price.type.domain' => array(
			'code'=>'price.type.domain',
			'internalcode'=>'mprity."domain"',
			'label'=>'Price type domain',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'price.type.label' => array(
			'code'=>'price.type.label',
			'internalcode'=>'mprity."label"',
			'label'=>'Price type label',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'price.type.status' => array(
			'code'=>'price.type.status',
			'internalcode'=>'mprity."status"',
			'label'=>'Price type status',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'price.type.mtime'=> array(
			'code'=>'price.type.mtime',
			'internalcode'=>'mprity."mtime"',
			'label'=>'Price type modification date',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'price.type.ctime'=> array(
			'code'=>'price.type.ctime',
			'internalcode'=>'mprity."ctime"',
			'label'=>'Price type creation date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'price.type.editor'=> array(
			'code'=>'price.type.editor',
			'internalcode'=>'mprity."editor"',
			'label'=>'Price type editor',
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

			$path = 'classes/price/manager/type/submanagers';
			foreach( $context->getConfig()->get($path, array() ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for price type extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g types, lists etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'price', 'type/' . $manager, $name );
	}


	/**
	 * Returns the config path for retrieving the configuration values.
	 *
	 * @return string Configuration path
	 */
	protected function _getConfigPath()
	{
		return 'mshop/price/manager/type/default/item/';
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