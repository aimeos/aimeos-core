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
		$prefix = rtrim( $this->prefix(), '.' );
		$filter = $this->filterBase( $prefix, $default );

		if( $default !== false )
		{
			$date = $this->context()->datetime();

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
		}

		return $filter;
	}


	/**
	 * Returns the attributes that can be used for saving.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSaveAttributes( bool $withsub = true ) : array
	{
		$prefix = $this->prefix();

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
	 * Search for all list items based on the given critera.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of list items implementing \Aimeos\MShop\Common\Item\Lists\Iface with ids as keys
	 */
	public function search( \Aimeos\Base\Criteria\Iface $search, array $ref = [], ?int &$total = null ) : \Aimeos\Map
	{
		$items = parent::search( $search, $ref, $total );

		if( empty( $ref ) ) {
			return $items;
		}

		$refItemMap = [];
		$refIdMap = $items->groupBy( $this->prefix() . 'domain' );

		foreach( $refIdMap as $domain => $list )
		{
			$manager = \Aimeos\MShop::create( $this->context(), $domain );
			$attr = map( $manager->getSearchAttributes() );

			$key = $attr->get( 'id' )?->getCode() === 'id' ? 'id' : str_replace( '/', '.', $domain ) . '.id';

			$search = $manager->filter()->slice( 0, count( $list ) )
				->add( [$key => map( $list )->getRefId()] );

			$refItemMap[$domain] = $manager->search( $search, $ref );
		}

		foreach( $items as $listItem )
		{
			if( isset( $refItemMap[$listItem->getDomain()][$listItem->getRefId()] ) ) {
				$listItem->setRefItem( $refItemMap[$listItem->getDomain()][$listItem->getRefId()] );
			}
		}

		return $items;
	}


	/**
	 * Returns the name of the used table
	 *
	 * @return string Table name
	 */
	protected function table() : string
	{
		return substr( parent::table(), 0, -1 ); // cuts of the "s" from "lists"
	}


	/**
	 * Returns the domain prefix.
	 *
	 * @return string Domain prefix with sub-domains separated by "."
	 */
	protected function prefix() : string
	{
		return $this->domain() . '.lists.';
	}
}
