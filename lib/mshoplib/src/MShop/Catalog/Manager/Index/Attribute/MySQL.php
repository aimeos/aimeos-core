<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Catalog
 */


/**
 * MySQL based catalog index attribute for searching in product tables.
 *
 * @package MShop
 * @subpackage Catalog
 */
class MShop_Catalog_Manager_Index_Attribute_MySQL
	extends MShop_Catalog_Manager_Index_Attribute_Default
	implements MShop_Catalog_Manager_Index_Iface
{
	private $searchConfig = array(
		'catalog.index.attribute.id' => array(
			'code'=>'catalog.index.attribute.id',
			'internalcode'=>'mcatinat."attrid"',
			'internaldeps'=>array( 'LEFT JOIN "mshop_catalog_index_attribute" AS mcatinat USE INDEX ("idx_mscatinat_s_at_lt", "idx_mscatinat_p_s_at_lt") ON mcatinat."prodid" = mpro."id"' ),
			'label'=>'Product index attribute ID',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Base::PARAM_INT,
			'public' => false,
		)
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
			$list[$key] = new MW_Common_Criteria_Attribute_Default( $fields );
		}

		return $list;
	}
}