<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Catalog
 */


namespace Aimeos\MShop\Catalog\Manager\Index\Attribute;


/**
 * MySQL based catalog index attribute for searching in product tables.
 *
 * @package MShop
 * @subpackage Catalog
 */
class MySQL
	extends \Aimeos\MShop\Catalog\Manager\Index\Attribute\Standard
	implements \Aimeos\MShop\Catalog\Manager\Index\Iface
{
	private $searchConfig = array(
		'catalog.index.attribute.id' => array(
			'code'=>'catalog.index.attribute.id',
			'internalcode'=>'mcatinat."attrid"',
			'internaldeps'=>array( 'LEFT JOIN "mshop_catalog_index_attribute" AS mcatinat USE INDEX ("idx_mscatinat_s_at_lt", "idx_mscatinat_p_s_at_lt") ON mcatinat."prodid" = mpro."id"' ),
			'label'=>'Product index attribute ID',
			'type'=> 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		)
	);


	/**
	 * Returns a list of objects describing the available criterias for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of items implementing \Aimeos\MW\Common\Criteria\Attribute\Iface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = parent::getSearchAttributes( $withsub );

		foreach( $this->searchConfig as $key => $fields ) {
			$list[$key] = new \Aimeos\MW\Common\Criteria\Attribute\Standard( $fields );
		}

		return $list;
	}
}