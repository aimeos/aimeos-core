<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Index
 */


namespace Aimeos\MShop\Index\Manager\Text;


/**
 * MySQL based index text for searching in product tables.
 *
 * @package MShop
 * @subpackage Index
 */
class MySQL
	extends \Aimeos\MShop\Index\Manager\Text\Standard
{
	/**
	 * Returns the expression for searching by relevance
	 *
	 * @param string $term Quoted search term
	 * @return string SQL expression
	 */
	protected function relevanceSource( string $term ) : string
	{
		return 'MATCH( mindte."content" ) AGAINST( ' . $term . ' IN BOOLEAN MODE )';
	}


	/**
	 * Returns the expression for sorting by relevance
	 *
	 * @param string $term Quoted search term
	 * @return string SQL expression
	 */
	protected function relevanceSort( string $term ) : string
	{
		return $this->relevanceSource( $term ) . ' * mpro."boost"';
	}


	/**
	 * Returns the search term used for searching by relevance
	 *
	 * @param string $value Search string entered by the user
	 * @return string Search term for the relevance query
	 */
	protected function getRelevanceTerm( string $value ) : string
	{
		$words = $this->getRelevanceWords( $value );
		$parts = array_filter( $words, fn( $word ) => strlen( $word ) > 2 );

		return join( '', array_map( fn( $part ) => $part . '* ', $parts ) ) . '"' . join( ' ', $words ) . '"';
	}
}
