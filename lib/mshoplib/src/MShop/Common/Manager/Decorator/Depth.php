<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\Decorator;


/**
 * Provides a decorator for limiting recursive searches
 *
 * @package MShop
 * @subpackage Common
 */
class Depth
	extends \Aimeos\MShop\Common\Manager\Decorator\Base
{
	private $level = 0;


	/**
	 * Searches for all items matching the given critera.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Criteria object with conditions, sortations, etc.
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @param integer &$total Number of items that are available in total
	 * @return array List of items implementing \Aimeos\MShop\Common\Item\Iface
	 */
	public function searchItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], &$total = null )
	{
		$items = [];

		try
		{
			$max = $this->getContext()->getConfig()->get( 'mshop/common/manager/maxdepth', 2 );

			if( $this->level++ < $max ) {
				$items = $this->getManager()->searchItems( $search, $ref, $total );
			}

			$this->level--;
		}
		catch( \Exception $e )
		{
			$this->level--;
			throw $e;
		}

		return $items;
	}
}