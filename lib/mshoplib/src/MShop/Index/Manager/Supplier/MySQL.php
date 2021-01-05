<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
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
	private $searchConfig = array(
		'index.supplier.id' => array(
			'code' => 'index.supplier.id',
			'internalcode' => 'mindsu."supid"',
			'internaldeps'=> array( 'LEFT JOIN "mshop_index_supplier" AS mindsu USE INDEX ("idx_msindsup_sid_supid_lt_po", "unq_msindsup_p_sid_supid_lt_po") ON mindsu."prodid" = mpro."id"' ),
			'label' => 'Product index supplier ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
	);


	/**
	 * Returns a list of objects describing the available criterias for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] List of search attriubte items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		$list = parent::getSearchAttributes( $withsub );

		foreach( $this->searchConfig as $key => $fields ) {
			$list[$key] = new \Aimeos\MW\Criteria\Attribute\Standard( $fields );
		}

		return $list;
	}
}
