<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2024
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\Decorator;


/**
 * Provides a decorator for managing list items
 *
 * @package MShop
 * @subpackage Common
 */
class Lists
	extends \Aimeos\MShop\Common\Manager\Decorator\Base
	implements \Aimeos\MShop\Common\Manager\ListsRef\Iface
{
	use \Aimeos\MShop\Common\Manager\ListsRef\Traits;

	private string $domain;


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $items List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Attribute\Manager\Iface Manager object for chaining method calls
	 */
	public function delete( $items ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$this->getManager()->delete( $items );
		return $this->deleteRefItems( $items );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		$domain = $this->domain();
		$alias = $this->alias( $domain . '.lists.id' );

		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
		$level = $this->context()->config()->get( 'mshop/' . $domain . '/manager/sitemode', $level );

		return $this->getManager()->getSearchAttributes( $withsub ) + $this->createAttributes( [
			$domain . ':has' => [
				'code' => $domain . ':has()',
				'internalcode' => ':site AND :key AND ' . $alias . '."id"',
				'internaldeps' => [
					'LEFT JOIN "mshop_' . $domain . '_list" AS ' . $alias . ' ON ( ' . $alias . '."parentid" = ' . $this->alias() . '."id" )'
				],
				'label' => 'Has list item, parameter(<domain>[,<list type>[,<reference ID>]])',
				'type' => 'null',
				'public' => false,
				'function' => function( &$source, array $params ) use ( $alias, $level ) {
					$keys = [];

					foreach( (array) ( $params[1] ?? '' ) as $type ) {
						foreach( (array) ( $params[2] ?? '' ) as $id ) {
							$keys[] = substr( $params[0] . '|' . ( $type ? $type . '|' : '' ) . $id, 0, 255 );
						}
					}

					$sitestr = $this->siteString( $alias . '."siteid"', $level );
					$keystr = $this->toExpression( $alias . '."key"', $keys, ( $params[2] ?? null ) ? '==' : '=~' );
					$source = str_replace( [':site', ':key'], [$sitestr, $keystr], $source );

					return $params;
				}
			],
			$domain . ':starts' => [
				'code' => $domain . ':starts()',
				'internalcode' => ':site AND :expr AND ' . $alias . '."id"',
				'internaldeps' => [
					'LEFT JOIN "mshop_' . $domain . '_list" AS ' . $alias . ' ON ( ' . $alias . '."parentid" = ' . $this->alias() . '."id" )'
				],
				'label' => 'Has list item with start date, parameter(<domain>,<list type>,<after>[,<before>])',
				'type' => 'null',
				'public' => false,
				'function' => function( &$source, array $params ) use ( $alias, $level ) {
					$expr = [
						$this->toExpression( $alias . '."domain"', $params[0] ?? '' ),
						$this->toExpression( $alias . '."type"', $params[1] ?? '' ),
						$this->toExpression( $alias . '."start"', $params[2] ?? '', '>=' )
					];

					if( isset( $params[3] ) ) {
						$expr[] = $this->toExpression( $alias . '."start"', $params[3], '<=' );
					}

					$sitestr = $this->siteString( $alias . '."siteid"', $level );
					$source = str_replace( [':site', ':expr'], [$sitestr, join( ' AND ', $expr )], $source );

					return $params;
				}
			],
			$domain . ':ends' => [
				'code' => $domain . ':ends()',
				'internalcode' => ':site AND :expr AND ' . $alias . '."id"',
				'internaldeps' => [
					'LEFT JOIN "mshop_' . $domain . '_list" AS ' . $alias . ' ON ( ' . $alias . '."parentid" = ' . $this->alias() . '."id" )'
				],
				'label' => 'Has list item with end date, parameter(<domain>,<list type>,<after>[,<before>])',
				'type' => 'null',
				'public' => false,
				'function' => function( &$source, array $params ) use ( $alias, $level ) {
					$expr = [
						$this->toExpression( $alias . '."domain"', $params[0] ?? '' ),
						$this->toExpression( $alias . '."type"', $params[1] ?? '' ),
						$this->toExpression( $alias . '."end"', $params[2] ?? '', '>=' )
					];

					if( isset( $params[3] ) ) {
						$expr[] = $this->toExpression( $alias . '."end"', $params[3], '<=' );
					}

					$sitestr = $this->siteString( $alias . '."siteid"', $level );
					$source = str_replace( [':site', ':expr'], [$sitestr, join( ' AND ', $expr )], $source );

					return $params;
				}
			]
		] );
	}


	/**
	 * Saves the dependent items of the item
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Item object
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\Iface Updated item
	 */
	public function saveRefs( \Aimeos\MShop\Common\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Common\Item\Iface
	{
		$this->saveListItems( $item, $this->domain(), $fetch );

		return $this->getManager()->saveRefs( $item );
	}


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

		foreach( $this->getListItems( array_keys( $entries ), $ref, $this->domain() ) as $id => $listItem ) {
			$entries[$listItem->getParentId()]['.listitems'][$id] = $listItem;
		}

		return $entries;
	}


	/**
	 * Returns the domain of the manager
	 *
	 * @return string Domain of the manager
	 */
	protected function domain() : string
	{
		if( !isset( $this->domain ) ) {
			$this->domain = current( $this->getManager()->type() ) ?: '';
		}

		return $this->domain;
	}
}
