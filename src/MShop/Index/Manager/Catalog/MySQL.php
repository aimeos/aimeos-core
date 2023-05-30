<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2023
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
{
	private array $searchConfig = array(
		'index.catalog.id' => array(
			'code' => 'index.catalog.id',
			'internalcode' => 'mindca."catid"',
			'internaldeps'=>array( 'LEFT JOIN "mshop_index_catalog" AS mindca USE INDEX ("idx_msindca_s_ca_lt_po", "unq_msindca_p_s_cid_lt_po") ON mindca."prodid" = mpro."id"' ),
			'label' => 'Product index category ID',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_INT,
		),
	);


	/**
	 * Returns a list of objects describing the available criterias for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of search attriubte items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		$list = parent::getSearchAttributes( $withsub );

		foreach( $this->searchConfig as $key => $fields ) {
			$list[$key] = new \Aimeos\Base\Criteria\Attribute\Standard( $fields );
		}

		return $list;
	}
}
