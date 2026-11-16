<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common;


/**
 * Rule for the shared items owned by other domains
 *
 * Shared items (e.g. prices, texts and media) store the domain owning them in their
 * "<type>.domain" field and list items the domain of the referenced items. They can
 * contain sensitive data of the owning domain like service provider credentials, so
 * users may only access them if they are allowed to access the owning domain. Items
 * without owning domain aren't restricted. Sub-domains (e.g. "product/property" of
 * property types) are owned by their top-level domain.
 *
 * The managers enforce the rule for changing shared items by the Owner decorator and
 * the entry points for retrieving them by the restrict() and readable() methods of
 * the policy trait.
 *
 * @package MShop
 * @subpackage Common
 */
final class OwnerScope
{
	/**
	 * Returns the key of the domain owning the items
	 *
	 * The field must be a search attribute to protect the items.
	 *
	 * @param string $resource Resource type of the items, e.g. "price" or "product/lists"
	 * @return string Item key, e.g. "price.domain" or "product.lists.domain"
	 */
	public static function key( string $resource ) : string
	{
		return str_replace( '/', '.', $resource ) . '.domain';
	}


	/**
	 * Limits the filter to the items owned by the passed domains or by no domain
	 *
	 * Filters of managers whose items aren't shared are returned unchanged. The domains
	 * are passed as closure because computing them is only necessary for shared items.
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager the filter is used for
	 * @param \Aimeos\Base\Criteria\Iface $filter Filter for searching the items which is limited in place
	 * @param \Closure(): string[] $domains Returns the top-level domains the user is allowed to access, e.g. ["product", "service"]
	 * @return \Aimeos\Base\Criteria\Iface Passed filter object
	 * @see shared()
	 */
	public static function limit( \Aimeos\MShop\Common\Manager\Iface $manager, \Aimeos\Base\Criteria\Iface $filter,
		\Closure $domains ) : \Aimeos\Base\Criteria\Iface
	{
		if( !static::shared( $manager ) ) {
			return $filter;
		}

		$domains = array_values( $domains() );
		$key = static::key( join( '/', $manager->type() ) );
		$expr = [$filter->compare( '==', $key, array_merge( [''], $domains ) )];

		if( !empty( $domains ) ) {
			// sub-domains of the allowed domains, e.g. "product/property"
			$expr[] = $filter->compare( '=~', $key, array_map( fn( $domain ) => $domain . '/', $domains ) );
		}

		return $filter->add( $filter->or( $expr ) );
	}


	/**
	 * Returns the top-level domain owning the item
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Item to check
	 * @return string Owning domain, e.g. "product" for "product/property", or empty if not owned
	 */
	public static function owner( \Aimeos\MShop\Common\Item\Iface $item ) : string
	{
		$domain = (string) $item->get( static::key( $item->getResourceType() ) );
		return strstr( $domain, '/', true ) ?: $domain;
	}


	/**
	 * Returns if the items of the manager store the domain owning them
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager to check
	 * @return bool TRUE if the items can be owned by other domains, FALSE if not
	 */
	public static function shared( \Aimeos\MShop\Common\Manager\Iface $manager ) : bool
	{
		return isset( $manager->getSearchAttributes( false )[static::key( join( '/', $manager->type() ) )] );
	}
}
