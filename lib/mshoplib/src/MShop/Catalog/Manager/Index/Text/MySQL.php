<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Catalog
 */


/**
 * MySQL based catalog index text for searching in product tables.
 *
 * @package MShop
 * @subpackage Catalog
 */
class MShop_Catalog_Manager_Index_Text_MySQL
	extends MShop_Catalog_Manager_Index_Text_Default
	implements MShop_Catalog_Manager_Index_Interface
{
	private $_searchConfig = array(
		'catalog.index.text.id' => array(
			'code'=>'catalog.index.text.id',
			'internalcode'=>'mcatinte."textid"',
			'internaldeps'=>array( 'LEFT JOIN "mshop_catalog_index_text" AS mcatinte USE INDEX ("idx_mscatinte_value", "idx_mscatinte_p_s_lt_la_ty_do_va") ON mcatinte."prodid" = mpro."id"' ),
			'label'=>'Product index text ID',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
			'public' => false,
		),
		'catalog.index.text.relevance' => array(
			'code' => 'catalog.index.text.relevance()',
			'internalcode' => ':site AND mcatinte."listtype" = $1 AND ( mcatinte."langid" = $2 OR mcatinte."langid" IS NULL ) AND MATCH( mcatinte."value" ) AGAINST( $3 IN BOOLEAN MODE )',
			'label' => 'Product texts, parameter(<list type code>,<language ID>,<search term>)',
			'type' => 'float',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_FLOAT,
			'public' => false,
		),
		'sort:catalog.index.text.relevance' => array(
			'code' => 'sort:catalog.index.text.relevance()',
			'internalcode' => 'MATCH( mcatinte."value" ) AGAINST( $3 IN BOOLEAN MODE )',
			'label' => 'Product texts, parameter(<list type code>,<language ID>,<search term>)',
			'type' => 'float',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_FLOAT,
			'public' => false,
		),
	);


	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );

		$site = $context->getLocale()->getSitePath();

		$this->_replaceSiteMarker( $this->_searchConfig['catalog.index.text.relevance'], 'mcatinte."siteid"', $site );
	}


	/**
	 * Returns a list of objects describing the available criterias for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = parent::getSearchAttributes( $withsub );

		foreach( $this->_searchConfig as $key => $fields ) {
			$list[$key] = new MW_Common_Criteria_Attribute_Default( $fields );
		}

		return $list;
	}


	/**
	 * Creates a search object and optionally sets base criteria.
	 *
	 * @param boolean $default Add default criteria
	 * @return MW_Common_Criteria_Interface Criteria object
	 */
	public function createSearch( $default = false )
	{
		$object = new MW_Common_Criteria_MySQL( new MW_DB_Connection_None() );

		if( $default === true ) {
			$object->setConditions( parent::createSearch( $default )->getConditions() );
		}

		return $object;
	}
}