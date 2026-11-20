<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2024-2026
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
	 * @return static Manager object for chaining method calls
	 */
	public function delete( $items ) : static
	{
		$this->getManager()->delete( $items );
		// @phpstan-ignore argument.type
		return $this->deleteRefItems( $items );
	}


	/**
	 * Creates objects from the given array
	 *
	 * @param iterable $entries List of associative arrays with key/value pairs
	 * @param array $refs List of domains to retrieve list items and referenced items for
	 * @param array $excludes List of keys which shouldn't be used when creating the items
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Common\Item\Iface
	 */
	public function from( iterable $entries, array $refs = [], array $excludes = [] ) : \Aimeos\Map
	{
		$context = $this->context();
		// @phpstan-ignore argument.type
		$keys = array_flip( $excludes );
		$excludes[] = 'lists';

		$items = $this->getManager()->from( $entries, $refs, $excludes );

		foreach( $entries as $key => $entry )
		{
			if( isset( $entry['lists'] ) && ( $item = $items->get( $key ) ) )
			{
				foreach( $entry['lists'] as $domain => $list )
				{
					foreach( $list as $data )
					{
						$data = array_diff_key( (array) $data, $keys );
						$listItem = $this->createListItem()->fromArray( $data, true );
						$manager = \Aimeos\MShop::create( $context, (string) $domain );

						if( $refItem = $manager->from( [$data] )->first() ) {
							$item->addListItem( $domain, $listItem, $refItem );
						}
					}
				}
			}
		}

		// @phpstan-ignore return.type
		return $items;
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

		$level = $this->getSiteMode();

		$join = ['LEFT JOIN "mshop_' . $domain . '_list" AS ' . $alias . ' ON ( ' . $alias . '."parentid" = ' . $this->alias() . '."id" )'];

		$dateFcn = fn( string $col ) => function( array $params ) use ( $alias, $level, $col ) {
			$expr = [
				$this->toExpression( $alias . '."domain"', $params[0] ?? '' ),
				$this->toExpression( $alias . '."type"', $params[1] ?? '' ),
				$this->toExpression( $alias . '."' . $col . '"', $params[2] ?? '', '>=' )
			];

			if( isset( $params[3] ) ) {
				$expr[] = $this->toExpression( $alias . '."' . $col . '"', $params[3], '<=' );
			}

			return [$this->siteString( $alias . '."siteid"', $level ), ...$expr, $alias . '."id"'];
		};

		return $this->getManager()->getSearchAttributes( $withsub ) + $this->createAttributes( [
			$domain . ':has' => [
				'code' => $domain . ':has()',
				'internaldeps' => $join,
				'label' => 'Has list item, parameter(<domain>[,<list type>[,<reference ID>]])',
				'type' => 'null',
				'public' => false,
				'function' => $this->hasFunction( $alias, $level )
			],
			$domain . ':starts' => [
				'code' => $domain . ':starts()',
				'internaldeps' => $join,
				'label' => 'Has list item with start date, parameter(<domain>,<list type>,<after>[,<before>])',
				'type' => 'null',
				'public' => false,
				'function' => $dateFcn( 'start' )
			],
			$domain . ':ends' => [
				'code' => $domain . ':ends()',
				'internaldeps' => $join,
				'label' => 'Has list item with end date, parameter(<domain>,<list type>,<after>[,<before>])',
				'type' => 'null',
				'public' => false,
				'function' => $dateFcn( 'end' )
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
		// @phpstan-ignore argument.type
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

		// @phpstan-ignore argument.type
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
