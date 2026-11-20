<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2026
 * @package MShop
 * @subpackage Index
 */


namespace Aimeos\MShop\Index\Manager\Text;


/**
 * PostgreSQL based index text for searching in product tables.
 *
 * @package MShop
 * @subpackage Index
 */
class PgSQL
	extends \Aimeos\MShop\Index\Manager\Text\Standard
{
	/**
	 * Returns additional conditions for searching by relevance
	 *
	 * The condition must match the "idx_msindte_content" GIN index exactly to use it.
	 *
	 * @param string $term Quoted search term
	 * @return string[] List of SQL conditions
	 */
	protected function relevanceConditions( string $term ) : array
	{
		return ['to_tsvector(\'english\', mindte."content") @@ to_tsquery(\'english\', ' . $term . ')'];
	}


	/**
	 * Returns the expression for searching by relevance
	 *
	 * @param string $term Quoted search term
	 * @return string SQL expression
	 */
	protected function relevanceSource( string $term ) : string
	{
		return 'CAST( to_tsvector(\'english\', mindte."content") @@ to_tsquery(\'english\', ' . $term . ') AS integer )';
	}


	/**
	 * Returns the expression for sorting by relevance
	 *
	 * @param string $term Quoted search term
	 * @return string SQL expression
	 */
	protected function relevanceSort( string $term ) : string
	{
		return 'ts_rank(to_tsvector(\'english\', mindte."content"), to_tsquery(\'english\', ' . $term . ')) * mpro."boost"';
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
		$parts = array_map( fn( $part ) => $part . ':*', $parts );

		return join( ' | ', [...$parts, '"' . join( ' <-> ', $words ) . '"'] );
	}
}
