<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2026
 * @package MShop
 * @subpackage Index
 */


namespace Aimeos\MShop\Index\Manager\Text;


/**
 * SQL Server based index text for searching in product tables.
 *
 * @package MShop
 * @subpackage Index
 */
class SQLSrv
	extends \Aimeos\MShop\Index\Manager\Text\Standard
{
	/**
	 * Returns additional conditions for searching by relevance
	 *
	 * Filters by the fulltext index first so the rank subquery is only evaluated for matching rows.
	 *
	 * @param string $term Quoted search term
	 * @return string[] List of SQL conditions
	 */
	protected function relevanceConditions( string $term ) : array
	{
		if( $this->fulltext() ) {
			return ['mindte."id" IN ( SELECT "KEY" FROM CONTAINSTABLE("mshop_index_text", "content", ' . $term . ') )'];
		}

		return [];
	}


	/**
	 * Returns the expression for searching by relevance
	 *
	 * @param string $term Quoted search term
	 * @return string SQL expression
	 */
	protected function relevanceSource( string $term ) : string
	{
		if( $this->fulltext() )
		{
			return '(
				SELECT mindte_ft.RANK
				FROM CONTAINSTABLE("mshop_index_text", "content", ' . $term . ') AS mindte_ft
				WHERE mindte."id" = mindte_ft."KEY"
			)';
		}

		return 'CHARINDEX( ' . $term . ', mindte."content" )';
	}


	/**
	 * Returns the expression for sorting by relevance
	 *
	 * @param string $term Quoted search term
	 * @return string SQL expression
	 */
	protected function relevanceSort( string $term ) : string
	{
		if( $this->fulltext() ) {
			return $this->relevanceSource( $term ) . ' * mpro."boost"';
		}

		return parent::relevanceSort( $term );
	}


	/**
	 * Returns if the SQL Server fulltext index should be used
	 *
	 * @return bool TRUE if the fulltext index is available, FALSE if not
	 */
	private function fulltext() : bool
	{
		return (bool) $this->context()->config()->get( 'mshop/index/manager/text/sqlsrv/fulltext', false );
	}


	/**
	 * Returns the search term used for searching by relevance
	 *
	 * @param string $value Search string entered by the user
	 * @return string Search term for the relevance query
	 */
	protected function getRelevanceTerm( string $value ) : string
	{
		if( !$this->fulltext() ) {
			return parent::getRelevanceTerm( $value );
		}

		$words = $this->getRelevanceWords( $value );
		$parts = array_filter( $words, fn( $word ) => strlen( $word ) > 2 );

		// CONTAINSTABLE fails for empty search conditions
		if( empty( $parts ) ) {
			return '"' . join( ' ', $words ) . '"';
		}

		return join( ' | ', array_map( fn( $part ) => '"' . $part . '*"', $parts ) );
	}
}
