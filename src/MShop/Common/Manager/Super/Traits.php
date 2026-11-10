<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\Super;


/**
 * Protects the "super" group, its memberships and its members from non-super users
 *
 * Members of the "super" group have unrestricted access to all sites, so
 * changing them would allow other users to gain super privileges.
 *
 * @package MShop
 * @subpackage Common
 */
trait Traits
{
	/**
	 * Returns the context object.
	 *
	 * @return \Aimeos\MShop\ContextIface Context object
	 */
	abstract protected function context() : \Aimeos\MShop\ContextIface;


	/**
	 * Throws an exception if one of the customers is a member of the "super" group
	 *
	 * The current user is allowed to change his own account.
	 *
	 * @param iterable $ids List of customer IDs
	 * @throws \Aimeos\MShop\PolicyException If a non-super user tries to change a super user
	 */
	protected function protectCustomers( iterable $ids ) : void
	{
		$context = $this->context();
		$userId = (string) $context->user()?->getId();
		$ids = map( $ids )->filter( fn( $id ) => $id && (string) $id !== $userId )->unique();

		if( $ids->isEmpty() || $context->access( 'super' ) || ( $groupIds = $this->superGroupIds() )->isEmpty() ) {
			return;
		}

		$manager = \Aimeos\MShop::create( $context, 'customer/lists' );
		$filter = $manager->filter()->add( [
			'customer.lists.parentid' => $ids->values()->all(),
			'customer.lists.domain' => 'group',
			'customer.lists.refid' => $groupIds->values()->all(),
		] )->slice( 0, 1 );

		if( !$manager->search( $filter )->isEmpty() ) {
			$this->denySuper();
		}
	}


	/**
	 * Throws an exception if one of the groups is the "super" group
	 *
	 * @param iterable $ids List of group IDs
	 * @throws \Aimeos\MShop\PolicyException If a non-super user tries to change the "super" group
	 */
	protected function protectGroups( iterable $ids ) : void
	{
		$ids = map( $ids )->filter()->unique();

		if( !$ids->isEmpty() && !$this->context()->access( 'super' ) && !$this->superGroupIds( $ids )->isEmpty() ) {
			$this->denySuper();
		}
	}


	/**
	 * Throws the exception for denied changes of super users
	 *
	 * @throws \Aimeos\MShop\PolicyException Always
	 */
	protected function denySuper() : void
	{
		$msg = $this->context()->translate( 'mshop', 'Only super users can change super users and the "super" group' );
		throw new \Aimeos\MShop\PolicyException( $msg );
	}


	/**
	 * Returns the IDs of the passed items or IDs
	 *
	 * @param \Aimeos\Map|\Aimeos\MShop\Common\Item\Iface[]|string[]|\Aimeos\MShop\Common\Item\Iface|string $items Item(s) or ID(s)
	 * @return \Aimeos\Map List of non-empty, unique IDs
	 */
	protected function itemIds( $items ) : \Aimeos\Map
	{
		return map( $items )->map( fn( $item ) => is_object( $item ) ? $item->getId() : $item )->filter()->unique();
	}


	/**
	 * Returns the IDs of the "super" groups
	 *
	 * @param iterable|null $ids List of group IDs to limit the result to or NULL for all
	 * @return \Aimeos\Map List of group IDs
	 */
	protected function superGroupIds( ?iterable $ids = null ) : \Aimeos\Map
	{
		$manager = \Aimeos\MShop::create( $this->context(), 'group' );
		$filter = $manager->filter()->add( 'group.code', '==', 'super' )->slice( 0, 0x7fffffff );

		if( $ids !== null ) {
			$filter->add( 'group.id', '==', map( $ids )->values()->all() );
		}

		return $manager->search( $filter )->keys();
	}
}
