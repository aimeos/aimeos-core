<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Index
 */


namespace Aimeos\MShop\Index\Manager\Catalog;


/**
 * MySQL based index catalog for searching in product tables.
 *
 * @package MShop
 * @subpackage Index
 */
class MySQL
	extends \Aimeos\MShop\Index\Manager\Catalog\Standard
	implements \Aimeos\MShop\Index\Manager\Iface
{
	private $searchConfig = array(
		'index.catalog.id' => array(
			'code'=>'index.catalog.id',
			'internalcode'=>'mindca."catid"',
			'internaldeps'=>array( 'LEFT JOIN "mshop_index_catalog" AS mindca USE INDEX ("idx_msindca_s_ca_lt_po", "unq_msindca_p_s_cid_lt_po") ON mindca."prodid" = mpro."id"' ),
			'label'=>'Product index category ID',
			'type'=> 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
	);


	/**
	 * Returns a list of objects describing the available criterias for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of items implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = parent::getSearchAttributes( $withsub );

		foreach( $this->searchConfig as $key => $fields ) {
			$list[$key] = new \Aimeos\MW\Criteria\Attribute\Standard( $fields );
		}

		return $list;
	}
}