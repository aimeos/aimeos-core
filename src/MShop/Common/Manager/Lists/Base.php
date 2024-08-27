<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\Lists;


/**
 * Abstract list manager implementation
 *
 * @package MShop
 * @subpackage Common
 */
abstract class Base
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Common\Manager\Lists\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface New list item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$prefix = $this->prefix();
		$context = $this->context();

		$values['.date'] = $context->datetime();
		$values[$prefix . 'siteid'] = $values[$prefix . 'siteid'] ?? $context->locale()->getSiteId();

		return new \Aimeos\MShop\Common\Item\Lists\Standard( $prefix, $values );
	}


	/**
	 * Creates a filter object.
	 *
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @param bool $site TRUE for adding site criteria to limit items by the site of related items
	 * @return \Aimeos\Base\Criteria\Iface Returns the filter object
	 */
	public function filter( ?bool $default = false, bool $site = false ) : \Aimeos\Base\Criteria\Iface
	{
		if( $default !== false )
		{
			$date = $this->context()->datetime();
			$prefix = rtrim( $this->prefix(), '.' );
			$filter = $this->filterBase( $prefix, $default );

			$filter->add( $filter->and( [
				$filter->or( [
					$filter->compare( '<=', $prefix . '.datestart', $date ),
					$filter->compare( '==', $prefix . '.datestart', null ),
				] ),
				$filter->or( [
					$filter->compare( '>=', $prefix . '.dateend', $date ),
					$filter->compare( '==', $prefix . '.dateend', null ),
				] ),
			] ) );

			return $filter;
		}

		return parent::filter();
	}


	/**
	 * Returns the attributes that can be used for saving.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSaveAttributes( bool $withsub = true ) : array
	{
		$prefix = $this->getDomain() . '.lists.';

		return $this->createAttributes( [
			$prefix . 'parentid' => [
				'internalcode' => 'parentid',
				'label' => 'List parent ID',
				'type' => 'int',
				'public' => false,
			],
			$prefix . 'key' => [
				'internalcode' => 'key',
				'label' => 'List key',
				'public' => false,
			],
			$prefix . 'type' => [
				'internalcode' => 'type',
				'label' => 'List type',
			],
			$prefix . 'refid' => [
				'internalcode' => 'refid',
				'label' => 'List reference ID',
			],
			$prefix . 'datestart' => [
				'internalcode' => 'start',
				'label' => 'List start date',
				'type' => 'datetime',
			],
			$prefix . 'dateend' => [
				'internalcode' => 'end',
				'label' => 'List end date',
				'type' => 'datetime',
			],
			$prefix . 'domain' => [
				'internalcode' => 'domain',
				'label' => 'List domain',
			],
			$prefix . 'position' => [
				'internalcode' => 'pos',
				'label' => 'List position',
				'type' => 'int',
			],
			$prefix . 'status' => [
				'internalcode' => 'status',
				'label' => 'List status',
				'type' => 'int',
			],
			$prefix . 'config' => [
				'internalcode' => 'config',
				'label' => 'List config',
				'type' => 'json',
				'public' => false,
			],
		] );
	}


	/**
	 * Creates a new manager for list extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g type, etc.
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->getSubManagerBase( $this->getDomain(), 'lists/' . $manager, $name );
	}


	/**
	 * Search for all list items based on the given critera.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of list items implementing \Aimeos\MShop\Common\Item\Lists\Iface with ids as keys
	 */
	public function search( \Aimeos\Base\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$items = parent::search( $search, $ref, $total );

		return $this->buildItems( $items, $ref );
	}


	/**
	 * Creates the items with address item, list items and referenced items.
	 *
	 * @param iterable $map Associative list of IDs as keys and the associative array of values
	 * @param string[] $domains List of domains to fetch list items and referenced items for
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Common\Item\Lists\Iface with IDs as keys
	 */
	protected function buildItems( iterable $map, array $domains ) : \Aimeos\Map
	{
		if( empty( $domains ) ) {
			return map( $map );
		}

		$refItemMap = $refIdMap = [];

		foreach( $map as $listItem ) {
			$refIdMap[$listItem->getDomain()][] = $listItem->getRefId();
		}

		foreach( $refIdMap as $domain => $list )
		{
			$manager = \Aimeos\MShop::create( $this->context(), $domain );

			if( ( $attr = current( $manager->getSearchAttributes() ) ) === false )
			{
				$msg = sprintf( 'No search configuration available for domain "%1$s', $domain );
				throw new \Aimeos\MShop\Exception( $msg );
			}

			$search = $manager->filter()->slice( 0, count( $list ) )->add( [$attr->getCode() => $list] );

			foreach( $manager->search( $search, $domains ) as $id => $item ) {
				$refItemMap[$domain][$id] = $item;
			}
		}

		foreach( $map as $id => $listItem )
		{
			if( isset( $refItemMap[$listItem->getDomain()][$listItem->getRefId()] ) ) {
				$listItem->setRefItem( $refItemMap[$listItem->getDomain()][$listItem->getRefId()] );
			}
		}

		return map( $map );
	}


	/**
	 * Returns the name of the used table
	 *
	 * @return string Table name
	 */
	protected function getTable() : string
	{
		return substr( parent::getTable(), 0, -1 ); // cuts of the "s" from "lists"
	}


	/**
	 * Returns the domain prefix.
	 *
	 * @return string Domain prefix with sub-domains separated by "."
	 */
	protected function prefix() : string
	{
		return $this->getDomain() . '.lists.';
	}
}
