<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Price
 */


/**
 * Default price list manager for creating and handling price list items.
 * @package MShop
 * @subpackage Price
 */
class MShop_Price_Manager_List_Default
	extends MShop_Common_Manager_List_Abstract
	implements MShop_Price_Manager_List_Interface
{
	private $_searchConfig = array(
		'price.list.id' => array(
			'code' => 'price.list.id',
			'internalcode' => 'mprili."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_price_list" AS mprili ON ( mpri."id" = mprili."parentid" )' ),
			'label' => 'Price list ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'price.list.siteid' => array(
			'code' => 'price.list.siteid',
			'internalcode' => 'mprili."siteid"',
			'label' => 'Price list site ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'price.list.parentid' => array(
			'code' => 'price.list.parentid',
			'internalcode' => 'mprili."parentid"',
			'label' => 'Price list price ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'price.list.domain' => array(
			'code' => 'price.list.domain',
			'internalcode' => 'mprili."domain"',
			'label' => 'Price list domain',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'price.list.typeid' => array(
			'code' => 'price.list.typeid',
			'internalcode' => 'mprili."typeid"',
			'label' => 'Price list type ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'price.list.refid' => array(
			'code' => 'price.list.refid',
			'internalcode' => 'mprili."refid"',
			'label' => 'Price list reference ID',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'price.list.datestart' => array(
			'code' => 'price.list.datestart',
			'internalcode' => 'mprili."start"',
			'label' => 'Price list start date',
			'type' => 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'price.list.dateend' => array(
			'code' => 'price.list.dateend',
			'internalcode' => 'mprili."end"',
			'label' => 'Price list end date',
			'type' => 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'price.list.config' => array(
			'code' => 'price.list.config',
			'internalcode' => 'mprili."config"',
			'label' => 'Price list config',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'price.list.position' => array(
			'code' => 'price.list.position',
			'internalcode' => 'mprili."pos"',
			'label' => 'Price list position',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'price.list.status' => array(
			'code' => 'price.list.status',
			'internalcode' => 'mprili."status"',
			'label' => 'Price list status',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'price.list.ctime' => array(
			'code' => 'price.list.ctime',
			'internalcode' => 'mprili."ctime"',
			'label' => 'Price list create date/time',
			'type' => 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'price.list.mtime' => array(
			'code' => 'price.list.mtime',
			'internalcode' => 'mprili."mtime"',
			'label' => 'Price list modification date/time',
			'type' => 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'price.list.editor' => array(
			'code' => 'price.list.editor',
			'internalcode' => 'mprili."editor"',
			'label' => 'Price list editor',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
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

			$path = 'classes/price/manager/list/submanagers';
			foreach( $context->getConfig()->get( $path, array( 'type' ) ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for price list extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g types, lists etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'price', 'list/' . $manager, $name );
	}


	/**
	 * Returns the config path for retrieving the configuration values.
	 *
	 * @return string Configuration path
	 */
	protected function _getConfigPath()
	{
		return 'mshop/price/manager/list/default/item/';
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