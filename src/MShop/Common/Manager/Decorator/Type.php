<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2024
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\Decorator;


/**
 * Provides a decorator for fetching type items
 *
 * @package MShop
 * @subpackage Common
 */
class Type
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

		$type = $this->getManager()->type();
		$path = join( '/', $type );

		if( $this->hasRef( $ref, $path . '/type' ) && !empty( $entries ) )
		{
			$dkey = join( '.', $type ) . '.domain';
			$key = join( '.', $type ) . '.type';
			$domain = current( $type );
			$code = $key . '.code';

			if( !empty( $values = array_column( $entries, $key ) ) )
			{
				$manager = \Aimeos\MShop::create( $this->context(), $path . '/type' );
				$filter = $manager->filter( true )->slice( 0, 0x7fffffff )->add( [$code => $values] );
				$typeItems = $manager->search( $filter )->groupBy( $code );

				foreach( $entries as $id => $entry )
				{
					foreach( $typeItems[$entry[$key]] ?? [] as $typeItem )
					{
						if( ( $entry[$dkey] ?? $domain ) === $typeItem->getDomain() ) {
							$entries[$id]['.type'] = $typeItem;
						}
					}
				}
			}
		}

		return $entries;
	}
}
