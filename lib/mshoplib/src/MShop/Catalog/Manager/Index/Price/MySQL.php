<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Catalog
 */


/**
 * MySQL based catalog index price for searching in product tables.
 *
 * @package MShop
 * @subpackage Catalog
 */
class MShop_Catalog_Manager_Index_Price_MySQL
	extends MShop_Catalog_Manager_Index_Price_Standard
	implements MShop_Catalog_Manager_Index_Iface
{
	private $searchConfig = array(
		'catalog.index.price.id' => array(
			'code'=>'catalog.index.price.id',
			'internalcode'=>'mcatinpr."priceid"',
			'internaldeps'=>array( 'LEFT JOIN "mshop_catalog_index_price" AS mcatinpr USE INDEX ("idx_mscatinpr_s_lt_cu_ty_va", "idx_mscatinpr_p_s_lt_cu_ty_va") ON mcatinpr."prodid" = mpro."id"' ),
			'label'=>'Product index price ID',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Base::PARAM_INT,
			'public' => false,
		),
	);


	/**
	 * Returns a list of objects describing the available criterias for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of items implementing MW_Common_Criteria_Attribute_Iface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = parent::getSearchAttributes( $withsub );

		foreach( $this->searchConfig as $key => $fields ) {
			$list[$key] = new MW_Common_Criteria_Attribute_Standard( $fields );
		}

		return $list;
	}
}