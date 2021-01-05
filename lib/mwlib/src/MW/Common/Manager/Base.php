<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Common
 */


namespace Aimeos\MW\Common\Manager;


/**
 * Common methods for all manager objects.
 *
 * @package MW
 * @subpackage Common
 */
abstract class Base extends \Aimeos\MW\Common\Base
{
	private $filterFcn = [];


	/**
	 * Adds a filter callback for an item type
	 *
	 * @param string $iface Interface name of the item to apply the filter to
	 * @param \Closure $fcn Anonymous function receiving the item to check as first parameter
	 */
	public function addFilter( string $iface, \Closure $fcn )
	{
		if( !isset( $this->filterFcn[$iface] ) ) {
			$this->filterFcn[$iface] = [];
		}

		$this->filterFcn[$iface][] = $fcn;
	}


	/**
	 * Applies the filters for the item type to the item
	 *
	 * @param object $item Item to apply the filter to
	 * @return object|null Object if the item should be used, null if not
	 */
	protected function applyFilter( $item )
	{
		foreach( $this->filterFcn as $iface => $fcnList )
		{
			if( is_object( $item ) && $item instanceof $iface )
			{
				foreach( $fcnList as $fcn )
				{
					if( $fcn( $item ) === null ) {
						return null;
					}
				}
			}
		}

		return $item;
	}


	/**
	 * Returns a sorted list of required criteria keys.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $criteria Search criteria object
	 * @param string[] $required List of prefixes of required search conditions
	 * @return string[] Sorted list of criteria keys
	 */
	protected function getCriteriaKeyList( \Aimeos\MW\Criteria\Iface $criteria, array $required ) : array
	{
		$keys = array_merge( $required, $this->getCriteriaKeys( $required, $criteria->getConditions() ) );

		foreach( $criteria->getSortations() as $sortation ) {
			$keys = array_merge( $keys, $this->getCriteriaKeys( $required, $sortation ) );
		}

		$keys = array_unique( array_merge( $required, $keys ) );
		sort( $keys );

		return $keys;
	}


	/**
	 * Returns the used separator inside the search keys.
	 *
	 * @return string Separator string (default: ".")
	 */
	protected function getKeySeparator() : string
	{
		return '.';
	}


	/**
	 * Returns the attribute helper functions for searching defined by the manager.
	 *
	 * @param \Aimeos\MW\Criteria\Attribute\Iface[] $attributes List of search attribute items
	 * @return array Associative array of attribute code and helper function
	 */
	protected function getSearchFunctions( array $attributes ) : array
	{
		$list = [];
		$iface = \Aimeos\MW\Criteria\Attribute\Iface::class;

		foreach( $attributes as $key => $item )
		{
			if( $item instanceof $iface ) {
				$list[$item->getCode()] = $item->getFunction();
			} else if( isset( $item['code'] ) ) {
				$list[$item['code']] = $item['function'];
			} else {
				throw new \Aimeos\MW\Common\Exception( sprintf( 'Invalid attribute at position "%1$d"', $key ) );
			}
		}

		return $list;
	}


	/**
	 * Returns the attribute translations for searching defined by the manager.
	 *
	 * @param \Aimeos\MW\Criteria\Attribute\Iface[] $attributes List of search attribute items
	 * @return array Associative array of attribute code and internal attribute code
	 */
	protected function getSearchTranslations( array $attributes ) : array
	{
		$translations = [];
		$iface = \Aimeos\MW\Criteria\Attribute\Iface::class;

		foreach( $attributes as $key => $item )
		{
			if( $item instanceof $iface ) {
				$translations[$item->getCode()] = $item->getInternalCode();
			} else if( isset( $item['code'] ) ) {
				$translations[$item['code']] = $item['internalcode'];
			} else {
				throw new \Aimeos\MW\Common\Exception( sprintf( 'Invalid attribute at position "%1$d"', $key ) );
			}
		}

		return $translations;
	}


	/**
	 * Returns the attribute types for searching defined by the manager.
	 *
	 * @param \Aimeos\MW\Criteria\Attribute\Iface[] $attributes List of search attribute items
	 * @return array Associative array of attribute code and internal attribute type
	 */
	protected function getSearchTypes( array $attributes ) : array
	{
		$types = [];
		$iface = \Aimeos\MW\Criteria\Attribute\Iface::class;

		foreach( $attributes as $key => $item )
		{
			if( $item instanceof $iface ) {
				$types[$item->getCode()] = $item->getInternalType();
			} else if( isset( $item['code'] ) ) {
				$types[$item['code']] = $item['internaltype'];
			} else {
				throw new \Aimeos\MW\Common\Exception( sprintf( 'Invalid attribute at position "%1$d"', $key ) );
			}
		}

		return $types;
	}


	/**
	 * Cuts the last part separated by a dot repeatedly and returns the list of resulting string.
	 *
	 * @param string[] $prefix Required base prefixes of the search keys
	 * @param string $string String containing parts separated by dots
	 * @return array List of resulting strings
	 */
	private function cutNameTail( array $prefix, string $string ) : array
	{
		$result = [];
		$noprefix = true;
		$strlen = strlen( $string );
		$sep = $this->getKeySeparator();

		foreach( $prefix as $key )
		{
			$len = strlen( $key );

			if( strncmp( $string, $key, $len ) === 0 )
			{
				if( $strlen > $len && ( $pos = strrpos( $string, $sep ) ) !== false )
				{
					$result[] = $string = substr( $string, 0, $pos );
					$result = array_merge( $result, $this->cutNameTail( $prefix, $string ) );
					$noprefix = false;
				}

				break;
			}
		}

		if( $noprefix )
		{
			if( ( $pos = strrpos( $string, ':' ) ) !== false ) {
				$result[] = substr( $string, 0, $pos );
				$result[] = $string;
			} elseif( ( $pos = strrpos( $string, $sep ) ) !== false ) {
				$result[] = substr( $string, 0, $pos );
			} else {
				$result[] = $string;
			}
		}

		return $result;
	}


	/**
	 * Returns a list of unique criteria names shortend by the last element after the ''
	 *
	 * @param string[] $prefix Required base prefixes of the search keys
	 * @param \Aimeos\MW\Criteria\Expression\Iface|null Criteria object
	 * @return array List of shortend criteria names
	 */
	private function getCriteriaKeys( array $prefix, \Aimeos\MW\Criteria\Expression\Iface $expr = null ) : array
	{
		if( $expr === null ) { return []; }

		$result = [];

		foreach( $this->getCriteriaNames( $expr ) as $item )
		{
			if( strncmp( $item, 'sort:', 5 ) === 0 ) {
				$item = substr( $item, 5 );
			}

			if( ( $pos = strpos( $item, '(' ) ) !== false ) {
				$item = substr( $item, 0, $pos );
			}

			$result = array_merge( $result, $this->cutNameTail( $prefix, $item ) );
		}

		return $result;
	}


	/**
	 * Returns a list of criteria names from a expression and its sub-expressions.
	 *
	 * @param \Aimeos\MW\Criteria\Expression\Iface Criteria object
	 * @return array List of criteria names
	 */
	private function getCriteriaNames( \Aimeos\MW\Criteria\Expression\Iface $expr ) : array
	{
		if( $expr instanceof \Aimeos\MW\Criteria\Expression\Compare\Iface ) {
			return array( $expr->getName() );
		}

		if( $expr instanceof \Aimeos\MW\Criteria\Expression\Combine\Iface )
		{
			$list = [];
			foreach( $expr->getExpressions() as $item ) {
				$list = array_merge( $list, $this->getCriteriaNames( $item ) );
			}
			return $list;
		}

		if( $expr instanceof \Aimeos\MW\Criteria\Expression\Sort\Iface ) {
			return array( $expr->getName() );
		}

		return [];
	}
}
