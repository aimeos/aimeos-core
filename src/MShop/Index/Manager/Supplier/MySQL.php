<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2023
 * @package MShop
 * @subpackage Index
 */


namespace Aimeos\MShop\Index\Manager\Supplier;


/**
 * MySQL based index supplier for searching in product tables.
 *
 * @package MShop
 * @subpackage Index
 */
class MySQL
	extends \Aimeos\MShop\Index\Manager\Supplier\Standard
{
	private array $searchConfig = array(
		'index.supplier.id' => array(
			'code' => 'index.supplier.id',
			'internalcode' => 'mindsu."supid"',
			'internaldeps'=> array( 'LEFT JOIN "mshop_index_supplier" AS mindsu USE INDEX ("idx_msindsup_sid_supid_lt_po", "unq_msindsu_p_s_lt_si_po_la_lo") ON mindsu."prodid" = mpro."id"' ),
			'label' => 'Product index supplier ID',
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
