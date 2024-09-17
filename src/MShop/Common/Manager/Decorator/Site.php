<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2024
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\Decorator;


/**
 * Provides a decorator for fetching site items
 *
 * @package MShop
 * @subpackage Common
 */
class Site
	extends \Aimeos\MShop\Common\Manager\Decorator\Base
{
	/**
	 * Merges the data from the given map and the referenced items
	 *
	 * @param array $entries Associative list of ID as key and the associative list of property key/value pairs as values
	 * @param array $ref List of referenced items to fetch and add to the entries
	 * @return array Associative list of ID as key and the updated entries as value
	 */
	public function searchRefs( array $entries, array $ref ) : array
	{
		$entries = $this->getManager()->searchRefs( $entries, $ref );

		if( $this->hasRef( $ref, 'locale/site' ) && !empty( $entries ) )
		{
			$manager = \Aimeos\MShop::create( $this->context(), 'locale/site' );
			$key = join( '.', $this->getManager()->type() ) . '.siteid';
			$siteIds = array_column( $entries, $key );

			$filter = $manager->filter( true )->add( ['locale.site.siteid' => $siteIds] )->slice( 0, 0x7fffffff );
			$siteItems = $manager->search( $filter )->col( null, 'locale.site.siteid' );

			foreach( $entries as $id => $entry ) {
				$entries[$id]['.locale/site'] = $siteItems[$entry[$key]] ?? null;
			}
		}

		return $entries;
	}
}
