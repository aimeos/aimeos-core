<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 * @package MShop
 * @subpackage Locale
 */


namespace Aimeos\MShop\Locale;


/**
 * Rule for the sites users are allowed to access
 *
 * If the context contains an authorization policy, users who aren't super users can
 * only access the sites within the site tree of their own site. Site IDs contain the
 * path of the site in the tree and end with a dot (e.g. "1.2."), so the sites of the
 * user are the ones whose site ID starts with the user's site ID. Without sub-sites,
 * each site has its own site ID and users can only access their site.
 *
 * The site manager enforces the rule for changing sites by the Scope decorator and
 * the entry points for retrieving sites by the restrict() and readable() methods of
 * the policy trait.
 *
 * @package MShop
 * @subpackage Locale
 */
final class SiteScope
{
	private const TYPE = 'locale/site';


	/**
	 * Throws the exception for sites outside the site tree of the user
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object for translating the message
	 * @throws \Aimeos\MShop\PolicyException Always
	 */
	public static function deny( \Aimeos\MShop\ContextIface $context ) : never
	{
		$msg = $context->translate( 'mshop', 'Only sites within your own site tree are allowed' );
		throw new \Aimeos\MShop\PolicyException( $msg );
	}


	/**
	 * Limits the filter for searching sites to the site tree of the user
	 *
	 * Filters of other managers than the site manager are returned unchanged.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object with policy and user
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager the filter is used for
	 * @param \Aimeos\Base\Criteria\Iface $filter Filter for searching items which is limited in place
	 * @return \Aimeos\Base\Criteria\Iface Passed filter object
	 * @throws \Aimeos\MShop\PolicyException If the user has no site ID, which would match all sites
	 */
	public static function limit( \Aimeos\MShop\ContextIface $context, \Aimeos\MShop\Common\Manager\Iface $manager,
		\Aimeos\Base\Criteria\Iface $filter ) : \Aimeos\Base\Criteria\Iface
	{
		if( join( '/', $manager->type() ) === self::TYPE && ( $root = static::root( $context ) ) !== null ) {
			$filter->add( 'locale.site.siteid', '=~', $root );
		}

		return $filter;
	}


	/**
	 * Returns the site ID of the user which all sites of the user must start with
	 *
	 * Sites are only restricted if the context contains a policy, e.g. set by the admin
	 * interfaces, and if the user isn't a super user who can access all sites.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object with policy and user
	 * @return string|null Site ID of the user (e.g. "1.2.") or NULL if all sites are allowed
	 * @throws \Aimeos\MShop\PolicyException If the user has no site ID, which would match all sites
	 */
	public static function root( \Aimeos\MShop\ContextIface $context ) : ?string
	{
		if( $context->policy() === null || $context->access( 'super' ) ) {
			return null;
		}

		if( ( $siteId = (string) $context->user()?->getSiteId() ) === '' ) {
			static::deny( $context );
		}

		return $siteId;
	}


	/**
	 * Returns if the user is allowed to access the item
	 *
	 * Other items than sites are always allowed.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object with policy and user
	 * @param \Aimeos\MShop\Common\Item\Iface $item Item to check
	 * @return bool TRUE if the item isn't a site or the site is within the site tree of the user, FALSE if not
	 * @throws \Aimeos\MShop\PolicyException If the user has no site ID, which would match all sites
	 */
	public static function visible( \Aimeos\MShop\ContextIface $context, \Aimeos\MShop\Common\Item\Iface $item ) : bool
	{
		return $item->getResourceType() !== self::TYPE
			|| ( $root = static::root( $context ) ) === null
			|| static::within( $root, $item->getSiteId() );
	}


	/**
	 * Returns if the site ID is within the site tree starting at the passed site ID
	 *
	 * @param string $root Site ID of the site tree, e.g. "1.2." as returned by root()
	 * @param string|null $siteId Site ID of the site (e.g. "1.2.3.")
	 * @return bool TRUE if the site is within the site tree, FALSE if not
	 */
	public static function within( string $root, ?string $siteId ) : bool
	{
		return str_starts_with( (string) $siteId, $root );
	}
}
