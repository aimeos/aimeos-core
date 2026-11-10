<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\Decorator;


/**
 * Protects shared items against changes depending on the domain owning them
 *
 * Shared items (e.g. prices, texts and media) store the domain owning them in their
 * "<type>.domain" field and list items the domain of the referenced items. If the
 * context contains an authorization policy, items of domains the user isn't allowed
 * to access can be read but not changed. Items are read together with the items
 * referencing them, so restricting the read access is up to the entry points.
 *
 * Unchanged items (e.g. referenced items saved together with their parent item) are
 * not checked when saving, so the setters of the items must not mark them as modified
 * if the passed value equals the current one, even if they convert the value first.
 * Managers must not modify unchanged items before writing them for the same reason.
 *
 * Additional manager methods are passed to the decorated manager without checks.
 * Methods which change items or files before they are saved must be protected by
 * a decorator of the domain extending this one and calling protect() first, like
 * the Owner decorator of the media manager does.
 *
 * @package MShop
 * @subpackage Common
 */
class Owner
	extends \Aimeos\MShop\Common\Manager\Decorator\Base
{
	private ?bool $owned = null;
	private ?string $resource = null;


	/**
	 * Removes multiple items if the user is allowed to change the owning domains
	 *
	 * Deleting shared items changes the items of the owning domain (e.g. the prices of
	 * a product) and is also done when they are removed while saving the owning item,
	 * so it requires the "save" permission of the owning domain like changing them.
	 * Whether the items itself can be deleted is checked by the entry points.
	 *
	 * Only the stored items are checked because the passed items can contain any domain,
	 * so managers removing files must use the stored items too (e.g. the media manager).
	 * Items which aren't found (e.g. of sub-sites or inactive sites) aren't removed
	 * because the domains owning them can't be checked.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $items List of item objects or IDs of the items
	 * @return static Manager object for chaining method calls
	 * @throws \Aimeos\MShop\PolicyException If changing items of the owning domain isn't allowed
	 */
	public function delete( $items ) : static
	{
		if( $policy = $this->guard() )
		{
			// iterators (e.g. generators) can be read only once
			$items = map( $items );
			$fcn = fn( $item ) => (string) ( $item instanceof \Aimeos\MShop\Common\Item\Iface ? $item->getId() : $item );
			$domains = $this->stored( $items->map( $fcn ) );

			$this->check( $policy, $domains );
			$items = $items->filter( fn( $item ) => isset( $domains[$fcn( $item )] ) );
		}

		$this->getManager()->delete( $items );

		return $this;
	}


	/**
	 * Adds or updates the items if the user is allowed to change the owning domains
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|\Aimeos\MShop\Common\Item\Iface $items Item or list of items
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\Map|\Aimeos\MShop\Common\Item\Iface Saved item or items
	 * @throws \Aimeos\MShop\PolicyException If changing items of the owning domain isn't allowed
	 */
	public function save( $items, bool $fetch = true )
	{
		if( $policy = $this->guard() )
		{
			// iterators (e.g. generators) can be read only once, so the list is passed on
			$items = $items instanceof \Traversable ? map( $items ) : $items;

			// unchanged items aren't written, e.g. referenced items saved together with their parent item
			$list = map( $items )->filter( fn( $item ) => $this->changed( $item ) );

			$this->check( $policy, $this->owners( $list ) );
		}

		return $this->getManager()->save( $items, $fetch );
	}


	/**
	 * Returns if the item or its list or property items have been changed
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Item object
	 * @return bool TRUE if the item will be written, FALSE if not
	 */
	protected function changed( \Aimeos\MShop\Common\Item\Iface $item ) : bool
	{
		$fcn = fn( $item ) => $item->isModified();

		return $item->isModified()
			|| $item instanceof \Aimeos\MShop\Common\Item\ListsRef\Iface && ( !$item->getListItemsDeleted()->isEmpty()
				|| $item->getListItems( active: false )->some( $fcn ) )
			|| $item instanceof \Aimeos\MShop\Common\Item\PropertyRef\Iface && ( !$item->getPropertyItemsDeleted()->isEmpty()
				|| $item->getPropertyItems( active: false )->some( $fcn ) );
	}


	/**
	 * Throws an exception if changing items owned by one of the given domains isn't allowed
	 *
	 * Owning domains without rules in the policy have no resource that must be
	 * protected, so changes are only denied if the policy explicitly denies them.
	 *
	 * The message names the required permission and the item type because the user may
	 * have only changed a shared item (e.g. a price) and never the owning domain.
	 *
	 * @param \Aimeos\MShop\PolicyIface $policy Authorization policy
	 * @param \Aimeos\Map $domains Domains owning the items, e.g. "service"
	 * @throws \Aimeos\MShop\PolicyException If the "save" permission of an owning domain is denied
	 */
	protected function check( \Aimeos\MShop\PolicyIface $policy, \Aimeos\Map $domains ) : void
	{
		$context = $this->context();

		foreach( $domains->filter()->unique() as $domain )
		{
			if( $policy->allowed( $context, (string) $domain, 'save' ) === false )
			{
				$msg = $context->translate( 'mshop', 'Permission "%1$s" for "%2$s" is required to change "%3$s" items' );
				throw new \Aimeos\MShop\PolicyException( sprintf( $msg, 'save', $domain, $this->resource() ) );
			}
		}
	}


	/**
	 * Returns the domains owning the passed items
	 *
	 * @param \Aimeos\Map $items List of items managed by this manager
	 * @return \Aimeos\Map List of domains owning the items
	 */
	protected function domains( \Aimeos\Map $items ) : \Aimeos\Map
	{
		$key = static::key( $this->resource() );
		return $items->map( fn( $item ) => $item->get( $key ) );
	}


	/**
	 * Returns the authorization policy if the items must be checked
	 *
	 * Items are only checked if the context contains a policy and the items store
	 * the domain owning them. Items without owning domain (e.g. products) have
	 * nothing to check.
	 *
	 * @return \Aimeos\MShop\PolicyIface|null Authorization policy or NULL if no check is required
	 */
	protected function guard() : ?\Aimeos\MShop\PolicyIface
	{
		return ( $policy = $this->context()->policy() ) && $this->owned() ? $policy : null;
	}


	/**
	 * Returns the key of the domain owning the items
	 *
	 * Shared items store the domain owning them and list items the domain of the referenced
	 * items in this field. The field must be a search attribute to protect the items.
	 *
	 * @param string $resource Resource type of the items, e.g. "price" or "product/lists"
	 * @return string Item key, e.g. "price.domain" or "product.lists.domain"
	 */
	public static function key( string $resource ) : string
	{
		return str_replace( '/', '.', $resource ) . '.domain';
	}


	/**
	 * Returns if the managed items store the domain owning them
	 *
	 * Items whose domain field doesn't contain the owning domain are excluded.
	 *
	 * @return bool TRUE if the items have an owning domain, FALSE if not
	 * @see unowned()
	 */
	protected function owned() : bool
	{
		return $this->owned ??= !$this->unowned()
			&& isset( $this->getManager()->getSearchAttributes( false )[static::key( $this->resource() )] );
	}


	/**
	 * Returns the domains owning the passed items and the stored items with the same IDs
	 *
	 * The stored items are checked too because items must not be taken over from domains
	 * the user isn't allowed to access by changing the owning domain or reusing their IDs.
	 * This requires a query for each check of items with IDs, even for unchanged items,
	 * because fromArray() can set the domain before the ID and setId() resets the
	 * modified flag, so unchanged items can contain a domain which isn't stored.
	 *
	 * Items with IDs which aren't found are rejected because the domains owning them
	 * can't be checked but writing them changes the items of all sub-sites, including
	 * the ones of inactive sites which are never found.
	 *
	 * @param \Aimeos\Map $items List of items managed by this manager
	 * @return \Aimeos\Map List of domains owning the items
	 * @throws \Aimeos\MShop\Exception If an item with ID isn't found
	 */
	protected function owners( \Aimeos\Map $items ) : \Aimeos\Map
	{
		$ids = $items->map( fn( $item ) => (string) $item->getId() );
		$stored = $this->stored( $ids );

		foreach( $ids->filter() as $id )
		{
			if( !isset( $stored[$id] ) ) {
				$msg = $this->context()->translate( 'mshop', 'Item with ID "%2$s" in "%1$s" not found' );
				throw new \Aimeos\MShop\Exception( sprintf( $msg, str_replace( '/', '.', $this->resource() ) . '.id', $id ), 404 );
			}
		}

		return $this->domains( $items )->merge( $stored->values() );
	}


	/**
	 * Throws an exception if changing the passed item isn't allowed
	 *
	 * Used by additional manager methods which change items or files before they are
	 * saved (e.g. the file operations of the media manager). The item is always checked,
	 * even if it's unchanged, because the methods can change the stored files only.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Item managed by this manager
	 * @throws \Aimeos\MShop\PolicyException If changing items of the owning domain isn't allowed
	 */
	protected function protect( \Aimeos\MShop\Common\Item\Iface $item ) : void
	{
		if( $policy = $this->guard() ) {
			$this->check( $policy, $this->owners( map( [$item] ) ) );
		}
	}


	/**
	 * Returns the resource type of the managed items
	 *
	 * @return string Resource type, e.g. "product/lists"
	 */
	protected function resource() : string
	{
		return $this->resource ??= join( '/', $this->getManager()->type() );
	}


	/**
	 * Returns if the domain field of the managed items doesn't contain the owning domain
	 *
	 * @return bool TRUE if the items aren't owned by the domain they store, FALSE if they are
	 */
	protected function unowned() : bool
	{
		/** mshop/common/manager/unowned
		 * Resource types whose items aren't owned by the domain stored in their domain field
		 *
		 * If the context contains an authorization policy, the Owner manager decorator
		 * requires the "save" permission of the domain stored in the "<type>.domain" field
		 * to change shared items like prices, texts and media. Other items use this field
		 * for the domain they belong to or describe, e.g. product reviews, product types
		 * or product attributes. Changing them must not require the permission to change
		 * the products, so their resource types aren't checked if they are listed here.
		 *
		 * The list contains resource types (e.g. "review" or "product/lists") or their last
		 * part, e.g. "type" for the type managers of all domains like "product/type":
		 *
		 *  'unowned' => [
		 *      'review' => 'review',
		 *      'type' => 'type',
		 *  ]
		 *
		 * Extensions add the resource types of their managers in their own configuration.
		 * The names must be used as keys too because configurations are merged by their
		 * keys and numeric keys would replace the entries of other configurations.
		 * Entries can be removed by setting their value to NULL.
		 *
		 * Caution: Items of listed resource types can be changed by all users who are
		 * allowed to access their managers, regardless of the domain they belong to.
		 * Reading the items is still restricted by the entry points depending on the domain.
		 *
		 * @param array Associative list of resource types or their last part as keys and values
		 * @since 2026.10
		 */
		$list = (array) $this->context()->config()->get( 'mshop/common/manager/unowned', [] );
		$type = $this->getManager()->type();

		return in_array( $this->resource(), $list, true ) || in_array( end( $type ), $list, true );
	}


	/**
	 * Returns the domains owning the stored items
	 *
	 * Only the items of the sites the user can retrieve are found, which doesn't include
	 * items of sub-sites by default and never items of inactive sites.
	 *
	 * @param \Aimeos\Map $ids List of item IDs
	 * @return \Aimeos\Map Domains owning the found items with the item IDs as keys
	 */
	protected function stored( \Aimeos\Map $ids ) : \Aimeos\Map
	{
		if( ( $ids = $ids->filter()->unique()->values() )->isEmpty() ) {
			return map();
		}

		$manager = $this->getManager();
		$key = str_replace( '/', '.', $this->resource() ) . '.id';
		$filter = $manager->filter()->add( $key, '==', $ids->all() )->slice( 0, $ids->count() );

		return $this->domains( $manager->search( $filter )->rekey( fn( $item ) => (string) $item->getId() ) );
	}
}
