<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Index
 */


namespace Aimeos\MShop\Index\Manager\Price;


/**
 * MySQL based index price for searching in product tables.
 *
 * @package MShop
 * @subpackage Index
 */
class MySQL
	extends \Aimeos\MShop\Index\Manager\Price\Standard
{
	private $searchConfig = array(
		'index.price.id' => array(
			'code' => 'index.price.id',
			'internalcode' => 'mindpr."priceid"',
			'internaldeps'=>array( 'LEFT JOIN "mshop_index_price" AS mindpr USE INDEX ("unq_msindpr_pid_sid_cid", "idx_msindpr_sid_cid_val") ON mindpr."prodid" = mpro."id"' ),
			'label' => 'Product index price ID',
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
