<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Index
 */


namespace Aimeos\MShop\Index\Manager\Attribute;


/**
 * MySQL based index attribute for searching in product tables.
 *
 * @package MShop
 * @subpackage Index
 */
class MySQL
	extends \Aimeos\MShop\Index\Manager\Attribute\Standard
{
	private $searchConfig = array(
		'index.attribute.id' => array(
			'code' => 'index.attribute.id',
			'internalcode' => 'mindat."attrid"',
			'internaldeps'=>array( 'LEFT JOIN "mshop_index_attribute" AS mindat USE INDEX ("idx_msindat_s_at_lt", "unq_msindat_p_s_aid_lt") ON mindat."prodid" = mpro."id"' ),
			'label' => 'Product index attribute ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		)
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
