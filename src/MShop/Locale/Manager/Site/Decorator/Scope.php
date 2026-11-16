<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 * @package MShop
 * @subpackage Locale
 */


namespace Aimeos\MShop\Locale\Manager\Site\Decorator;


/**
 * Restricts changing sites to the site tree of the user
 *
 * Users who aren't super users can only change, insert, move and delete sites within
 * the site tree of their own site if the context contains an authorization policy.
 * The rule which sites are within the site tree of the user is implemented by the
 * \Aimeos\MShop\Locale\SiteScope class.
 *
 * Reading sites isn't restricted because the managers need the parent sites too, e.g.
 * for inherited items. Instead, the entry points limit the sites shown to the users by
 * the restrict() and readable() methods of the policy trait.
 * Which actions the users are allowed to execute is checked by the entry points as well.
 *
 * The decorator is added by the "mshop/locale/manager/site/decorators/local" setting,
 * so configurations replacing that list must keep it. Sites aren't restricted if the
 * context contains no policy, so entry points must set one before using the manager.
 *
 * @package MShop
 * @subpackage Locale
 */
class Scope
	extends \Aimeos\MShop\Common\Manager\Decorator\Base
{
	/**
	 * Removes multiple sites if they are within the site tree of the user
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $items List of site items or IDs of the sites
	 * @return static Manager object for chaining method calls
	 * @throws \Aimeos\MShop\PolicyException If one of the sites isn't within the site tree of the user
	 */
	public function delete( $items ) : static
	{
		// iterators (e.g. generators) can be read only once
		$items = map( $items );
		$this->check( $items->map( fn( $item ) => $item instanceof \Aimeos\MShop\Common\Item\Iface ? $item->getId() : $item ) );

		$this->getManager()->delete( $items );
		return $this;
	}


	/**
	 * Adds a new site below the given parent site if it's within the site tree of the user
	 *
	 * @param \Aimeos\MShop\Locale\Item\Site\Iface $item Site item which should be inserted
	 * @param string|null $parentId ID of the parent site, NULL for a new root site
	 * @param string|null $refId ID of the site the new site should be inserted before
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Inserted site item
	 * @throws \Aimeos\MShop\PolicyException If the parent site isn't within the site tree of the user
	 */
	public function insert( \Aimeos\MShop\Locale\Item\Site\Iface $item, ?string $parentId = null,
		?string $refId = null ) : \Aimeos\MShop\Locale\Item\Site\Iface
	{
		$this->checkParent( $parentId );
		$this->check( map( [$parentId] ) );

		// @phpstan-ignore method.notFound
		return $this->getManager()->insert( $item, $parentId, $refId );
	}


	/**
	 * Moves the site to the new parent site if both are within the site tree of the user
	 *
	 * @param string $id ID of the site which should be moved
	 * @param string|null $oldParentId ID of the old parent site
	 * @param string|null $newParentId ID of the new parent site, NULL for a root site
	 * @param string|null $refId ID of the site the moved site should be inserted before
	 * @return static Manager object for chaining method calls
	 * @throws \Aimeos\MShop\PolicyException If one of the sites isn't within the site tree of the user
	 */
	public function move( string $id, ?string $oldParentId = null, ?string $newParentId = null,
		?string $refId = null ) : static
	{
		$this->checkParent( $newParentId );
		$this->check( map( [$id, $newParentId] ) );

		// @phpstan-ignore method.notFound
		$this->getManager()->move( $id, $oldParentId, $newParentId, $refId );
		return $this;
	}


	/**
	 * Updates the sites if they are within the site tree of the user
	 *
	 * The stored sites are checked because the site IDs of the passed items can be changed,
	 * and the changed site IDs must stay within the site tree of the user too. New sites
	 * have no ID and must be added using insert(), the manager rejects them.
	 *
	 * @param \Aimeos\MShop\Locale\Item\Site\Iface[]|\Aimeos\MShop\Locale\Item\Site\Iface $items Site item or list of site items
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface[]|\Aimeos\MShop\Locale\Item\Site\Iface Saved site item or items
	 * @throws \Aimeos\MShop\PolicyException If one of the sites isn't within the site tree of the user
	 */
	public function save( $items, bool $fetch = true )
	{
		// iterators (e.g. generators) can be read only once, so the list is passed on
		$items = $items instanceof \Traversable ? map( $items ) : $items;
		$list = map( $items );

		if( ( $root = \Aimeos\MShop\Locale\SiteScope::root( $this->context() ) ) !== null
			&& $list->some( fn( $item ) => !\Aimeos\MShop\Locale\SiteScope::within( $root, $item->getSiteId() ) )
		) {
			\Aimeos\MShop\Locale\SiteScope::deny( $this->context() );
		}

		$this->check( $list->map( fn( $item ) => $item->getId() ) );

		return $this->getManager()->save( $items, $fetch );
	}


	/**
	 * Throws an exception if one of the sites isn't within the site tree of the user
	 *
	 * Empty IDs (e.g. of new items) are skipped.
	 *
	 * @param \Aimeos\Map $ids List of site IDs (not the site path IDs like "1.2.")
	 * @throws \Aimeos\MShop\PolicyException If one of the sites isn't within the site tree of the user
	 */
	protected function check( \Aimeos\Map $ids ) : void
	{
		if( \Aimeos\MShop\Locale\SiteScope::root( $this->context() ) === null
			|| ( $ids = $ids->map( fn( $id ) => (string) $id )->filter()->unique()->values() )->isEmpty()
		) {
			return;
		}

		$manager = $this->getManager();
		$filter = $manager->filter()->slice( 0, $ids->count() )->add( 'locale.site.id', '==', $ids->all() );
		\Aimeos\MShop\Locale\SiteScope::limit( $this->context(), $manager, $filter );

		if( $manager->search( $filter )->count() !== $ids->count() ) {
			\Aimeos\MShop\Locale\SiteScope::deny( $this->context() );
		}
	}


	/**
	 * Throws an exception if users who aren't allowed to access all sites add root sites
	 *
	 * New root sites have no parent and are outside of each site tree.
	 *
	 * @param string|null $parentId ID of the parent site, NULL or empty for a root site
	 * @throws \Aimeos\MShop\PolicyException If the user isn't allowed to add root sites
	 */
	protected function checkParent( ?string $parentId ) : void
	{
		if( (string) $parentId === '' && \Aimeos\MShop\Locale\SiteScope::root( $this->context() ) !== null ) {
			\Aimeos\MShop\Locale\SiteScope::deny( $this->context() );
		}
	}
}
