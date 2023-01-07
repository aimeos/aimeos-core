<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\Decorator;


/**
 * Provides a decorator for lazy loading
 *
 * @package MShop
 * @subpackage Common
 */
class Lazy
	extends \Aimeos\MShop\Common\Manager\Decorator\Base
{
	/**
	 * Searches for all items matching the given critera.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $search Criteria object with conditions, sortations, etc.
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items \Aimeos\MShop\Common\Item\Iface with ids as keys
	 */
	public function search( \Aimeos\Base\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		if( $total === null )
		{
			$search = clone $search;

			return map( function() use ( $search, $ref, &$total ) {
				return $this->getManager()->search( $search, $ref, $total )->all();
			} );
		}

		return $this->getManager()->search( $search, $ref, $total );
	}
}
